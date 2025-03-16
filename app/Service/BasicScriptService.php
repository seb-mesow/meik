<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Exhibit;
use RuntimeException;

final class BasicScriptService {
	
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
		
		$user_file_name = trim($exhibit->get_name());
		$user_file_name = mb_ereg_replace('\s+', '_', $user_file_name);
		$user_file_name = mb_ereg_replace('[/\\\\]', '', $user_file_name);
		$user_file_name = 'QR-Code_' . $user_file_name . '.bas';
		$tmp_file_path = trim($stdout);
		
		return [
			'tmp_file_path' => $tmp_file_path,
			'user_file_name' => $user_file_name,
			'content_type' => 'text/x-basic',
			'charset' => 'windows-1252',
		];
	}
}
