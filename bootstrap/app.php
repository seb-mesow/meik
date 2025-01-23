<?php
declare(strict_types=1);

use App\Exceptions\AttachmentNotFoundException;
use App\Http\Middleware\NotFoundMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;
use PHPOnCouch\Exceptions\CouchNotFoundException;

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
			ConvertEmptyStringsToNull::class
		]);
		$middleware->web(append: [
			\App\Http\Middleware\HandleInertiaRequests::class,
			\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
		]);
		// $middleware->append(NotFoundMiddleware::class);
	})
	->withExceptions(function (Exceptions $exceptions) {
		$exceptions->render(static function(CouchNotFoundException $e, Request $request) {
			$path = $request->getRequestUri();
			if (str_starts_with($path, '/ajax') || str_starts_with($path, '/api')) {
				return response(status: 404);
			}
			return response()->view('errors.404', [], 404);
		});
		$exceptions->render(static function(AttachmentNotFoundException $e, Request $request) {
			$path = $request->getRequestUri();
			if (str_starts_with($path, '/ajax') || str_starts_with($path, '/api')) {
				return response(status: 404);
			}
			return response()->view('errors.404', [], 404);
		});
	})->create();
