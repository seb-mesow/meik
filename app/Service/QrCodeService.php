<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Exhibit;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use RuntimeException;

final class QrCodeService {
	
	private function determinate_user_file_name(Exhibit $exhibit, string $suffix): string {
		$user_file_name = trim($exhibit->get_name());
		$user_file_name = mb_ereg_replace('\s+', '_', $user_file_name);
		$user_file_name = mb_ereg_replace('[/\\\\]', '', $user_file_name);
		return 'qr_code_' . $user_file_name . $suffix;
	}
	
	/**
	 * @param \App\Models\Exhibit $exhibit
	 * @return array{
	 *     tmp_file_path: string,
	 *     user_file_name: string,
	 *     content_type: string,
	 * }
	 */
	public function create_qr_code(Exhibit $exhibit): array {
		$tmp_dir_path = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'meik', 'qr_code', 'png']);
		if (!is_dir($tmp_dir_path)) {
			mkdir($tmp_dir_path, recursive: true);
		}
		$tmp_file_path = tempnam($tmp_dir_path, '');
		
		$data = (string) $exhibit->get_id();
		
		$qrCode = new QrCode($data);
		$writer = new PngWriter();
		$result = $writer->write($qrCode);
		// $writer->validateResult($result, $data);
		
		$result->saveToFile($tmp_file_path);
		
		$user_file_name = $this->determinate_user_file_name($exhibit, '.png');
		
		return [
			'tmp_file_path' => $tmp_file_path,
	 		'user_file_name' => $user_file_name,
	 		'content_type' => 'image/png',
		];
	}
	
	/**
	 * @param \App\Models\Exhibit $exhibit
	 * @param string $type_basic
	 * @return array{
	 *     tmp_file_path: string,
	 *     user_file_name: string,
	 *     content_type: string,
	 *     charset: string,
	 * }
	 */
	public function create_qr_code_basic_script(Exhibit $exhibit, string $type_basic): array {
		if ($type_basic !== 'q' && $type_basic !== 'gw') {
			return throw new RuntimeException("Parameter type_basic must be either 'q' or 'gw'");
		}
		$type_arg = $type_basic === 'q' ? 'Q' : 'G';
		$type_suffix = $type_basic === 'q' ? 'Q' : 'GW';
		
		$result_code = 255;
		
		$cwd = '/var/python/src';
		$arg = $exhibit->get_id();
		$command = ['/var/python/bin/python3', 'qrbasgen.py', $type_arg, $arg];
		$file_descriptors = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
			2 => ["pipe", "w"],
		];
		$pipes = [];
		
		$process = proc_open($command, $file_descriptors, $pipes, $cwd);
		if (!is_resource($process)) {
			throw new RuntimeException('Python script qrbasgen.py failed.');
		}
		$stdout = stream_get_contents($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		$result_code = proc_close($process);
		
		if ($result_code !== 0 || !is_string($stdout) || $stdout === '' || $stderr !== '') {
			throw new RuntimeException('Python script qrbasgen.py failed.');
		}
		
		$tmp_file_path = trim($stdout);
		$user_file_name = mb_strtoupper($this->determinate_user_file_name($exhibit, '__' . $type_suffix . '.bas'));
		
		return [
			'tmp_file_path' => $tmp_file_path,
			'user_file_name' => $user_file_name,
			'content_type' => 'text/x-basic',
			'charset' => 'ISO-8859-1', # === latin-1 in Python
		];
	}
}
