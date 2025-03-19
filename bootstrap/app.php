<?php
declare(strict_types=1);

use App\Exceptions\AttachmentNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: [
			__DIR__.'/../routes/web.php',
			__DIR__.'/../routes/ajax.php'
		],
		api: __DIR__.'/../routes/api.php',
		commands: __DIR__.'/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware) {
		$middleware->remove([
			// Illuminate\Foundation\Http\Middleware\TrimStrings::class,
			Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
		]);
		$middleware->web(
			append: [
				App\Http\Middleware\HandleInertiaRequests::class,
				Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
			],
			// remove: [
			// 	Illuminate\Cookie\Middleware\EncryptCookies::class,
			// 	Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
			// ]
		);
		// $middleware->append(NotFoundMiddleware::class);
	})
	->withExceptions(function (Exceptions $exceptions) {
		$exceptions->stopIgnoring(AuthenticationException::class);
		$exceptions->stopIgnoring(AuthorizationException::class);
		$exceptions->stopIgnoring(HttpException::class);
		$exceptions->stopIgnoring(HttpResponseException::class);
		$exceptions->stopIgnoring(RequestExceptionInterface::class);
		$exceptions->stopIgnoring(TokenMismatchException::class);
		$exceptions->stopIgnoring(ValidationException::class);
		
		$exceptions->shouldRenderJsonWhen(static function(Request $request, Throwable $e): bool {
			$path = $request->getRequestUri();
			$should_json = str_starts_with($path, '/ajax') || str_starts_with($path, '/api');
			return $should_json;
		});
		
		$exceptions->render(static function(AuthorizationException $e, Request $request) {
			$path = $request->getRequestUri();
			if (str_starts_with($path, '/ajax')) {
				if (Auth::check()) {
					return response()->json(status: 403); // clearly forbidden
				}
				return response()->json(status: 404); // hiding instead of forbidden: not found
			} elseif (str_starts_with($path, '/api')) {
				return response()->json(status: 404); // hiding instead of forbidden: not found
			}
			if (Auth::check()) {
				return response()->view('errors.403', [], 403); // clearly forbidden
			}
			return response()->view('errors.404', [], 404); // hiding instead of forbidden: not found
		});
		
		$exceptions->render(static function(RuntimeException $e, Request $request) {
			$path = $request->getRequestUri();
			if (str_starts_with($path, '/ajax') || str_starts_with($path, '/api')) {
				return response()->json(status: 400, data: $e->getMessage());
			}
			return response()->view('errors.400', [$e->getMessage()], 400);
		});
		
		$exceptions->render(static function(CouchNotFoundException $e, Request $request) {
			$path = $request->getRequestUri();
			if (str_starts_with($path, '/ajax') || str_starts_with($path, '/api')) {
				return response()->json(status: 404);
			}
			return response()->view('errors.404', [], 404);
		});
		$exceptions->render(static function(AttachmentNotFoundException $e, Request $request) {
			$path = $request->getRequestUri();
			if (str_starts_with($path, '/ajax') || str_starts_with($path, '/api')) {
				return response()->json(status: 404);
			}
			return response()->view('errors.404', [], 404);
		});
	})->create();
