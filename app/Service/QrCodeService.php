<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Exhibit;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use RuntimeException;

final class QrCodeService {
	
	private function determinate_user_file_name(Exhibit $exhibit, string $ext): string {
		$user_file_name = trim($exhibit->get_name());
		$user_file_name = mb_ereg_replace('\s+', '_', $user_file_name);
		$user_file_name = mb_ereg_replace('[/\\\\]', '', $user_file_name);
		return 'qr_code_' . $user_file_name . '.' . $ext;
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
		
		$user_file_name = $this->determinate_user_file_name($exhibit, 'png');
		
		return [
			'tmp_file_path' => $tmp_file_path,
	 		'user_file_name' => $user_file_name,
	 		'content_type' => 'image/png',
		];
	}
	
	/**
	 * @param \App\Models\Exhibit $exhibit
	 * @return array{
	 *     tmp_file_path: string,
	 *     user_file_name: string,
	 *     content_type: string,
	 *     charset: string,
	 * }
	 */
	public function create_qr_code_basic_script(Exhibit $exhibit): array {
		$result_code = 255;
		
		$cwd = '/var/python';
		$arg = $exhibit->get_id();
		$command = ['bin/python3', 'src/qrbas.py', $arg];
		$file_descriptors = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
			2 => ["pipe", "w"],
		];
		$pipes = [];
		
		$process = proc_open($command, $file_descriptors, $pipes, $cwd);
		if (!is_resource($process)) {
			throw new RuntimeException('Python script qrbas.py failed.');
		}
		$stdout = stream_get_contents($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		$result_code = proc_close($process);
		
		if ($result_code !== 0 || !is_string($stdout) || $stdout === '' || $stderr !== '') {
			throw new RuntimeException('Python script qrbas.py failed.');
		}
		
		$tmp_file_path = trim($stdout);
		$user_file_name = $this->determinate_user_file_name($exhibit, 'bas');
		
		return [
			'tmp_file_path' => $tmp_file_path,
			'user_file_name' => $user_file_name,
			'content_type' => 'text/x-basic',
			'charset' => 'windows-1252',
		];
	}
}
