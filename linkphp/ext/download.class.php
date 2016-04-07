<?php
class download { 
	
	private $allow = array(".jpg", ".txt", ".gif", ".png", ".rar"); //允许下载的文件类型
	 
	/**
	 *	文件下载
	 * 	@param  string  $file_name    文件名
	 * 	@param  string  $server_path  文件目录
	 * 	@param  string  $mime_type    传输类型
	 *  @return 
	 */
	public function down($file_name, $server_path = './', $mime_type = 'application/octet-stream') {
		$full_file_name = $server_path . '/' . $file_name;
		$this->check_file_ext($file_name);
		$this->check_file_exists($full_file_name);
		header("Content-Type: {$mime_type}"); 
		$file_name = '"' . htmlspecialchars($file_name) . '"'; 
        $file_size = filesize($full_file_name); 
        header("Content-Disposition: attachment; filename={$file_name}; charset=utf-8"); 
        header("Content-Length: {$file_size}"); 
        readfile($full_file_name); 
        exit;  
	}
	
	/**
	 *	检测文件类型
	 * 	@param  string  $file_name    文件名
	 *  @return 
	 */
	private function check_file_ext($file) {
		$file_ext = strtolower(substr($file, -4)); 
		if (!in_array($file_ext, $this->allow)) exit('this file is deny!');
		return true;
	}
	
	/**
	 *	检测文件是否存在
	 * 	@param  string  $full_file_name  带目录的文件名
	 *  @return 
	 */
	private function check_file_exists($full_file_name) {
		if (!file_exists($full_file_name)) exit('this file does not exit!');
		return true;
	}
} 