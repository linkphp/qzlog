<?php
/**
 * 主页控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class index_control extends admin_control{
    // hook admin_index_control_begin.php
	//后台首页
	public function index(){
		// hook admin_index_control_index_begin.php
		$this->display('index/index.htm');
	}

	//服务器信息
	public function get_os(){
        // hook admin_index_control_get_os_begin.php
		$info = array();
		$is_ini_get = function_exists('ini_get');
		$info['os'] = function_exists('php_uname') ? php_uname() : '未知';
		$info['software'] = R('SERVER_SOFTWARE', 'S');
		$info['php'] = PHP_VERSION;
		$config_db = C('db');
		$info['db_type'] = $config_db['type'];
		$info['mysql'] = $this->member->db->version();
		$info['filesize'] = $is_ini_get ? ini_get('upload_max_filesize') : '未知';
		$info['exectime'] = $is_ini_get ? ini_get('max_execution_time') : '未知';
		$info['safe_mode'] = $is_ini_get ? (ini_get('safe_mode') ? 'Yes' : 'No') : '未知';
		$info['url_fopen'] = $is_ini_get ? (ini_get('allow_url_fopen') ? 'Yes' : 'No') : '未知';
		$info['other'] = $this->get_other();
        // hook admin_index_control_get_os_after.php
        $this->assign('info',$info);
        $this->display('index/info.htm');
	}

	// 获取其他信息
	private function get_other(){
        // hook admin_index_control_get_other_begin.php
		$s = '';
		if(function_exists('extension_loaded')){
			if(extension_loaded('gd')){
				function_exists('imagepng') && $s .= 'png ';
				function_exists('imagejpeg') && $s .= 'jpg ';
				function_exists('imagegif') && $s .= 'gif ';
			}
			extension_loaded('iconv') && $s .= 'iconv ';
			extension_loaded('mbstring') && $s .= 'mbstring ';
			extension_loaded('zlib') && $s .= 'zlib ';
			extension_loaded('ftp') && $s .= 'ftp ';
			function_exists('fsockopen') && $s .= 'fsockopen';
		}
        // hook admin_index_control_get_other_after.php
		return $s;
	}

	//PHP错误日志
	public function error(){
        // hook admin_index_control_error_begin.php
		$phperror = file_exists(LOG_PATH.'php_error.php')?file(LOG_PATH.'php_error.php'):null;
		$phperror404 = file_exists(LOG_PATH.'php_error404.php')?file(LOG_PATH.'php_error404.php'):null;
		$this->assign('phperror',$phperror);
		$this->assign('phperror404',$phperror404);
		$this->display('index/error.htm');
	}

	//删除PHP错误日志
	public function phpErrordel(){
        // hook admin_index_control_errordel_begin.php
		if(!file_exists(LOG_PATH.'php_error.php')){
			$this->message(1,'暂无PHP错误日志');
		}else{
			$line = LOG_PATH.'php_error.php';
			if(unlink($line)){
				// hook admin_index_control_errordel_after.php
				$this->message(1,'删除成功');
			}else{
				$this->message(0,'删除失败,请检查 runtime/log/php_error.php 是否有可写权限');
			}
		}
	}

	//删除404错误日志
	public function notFoundErrordel(){
        // hook admin_index_control_notFoundErrordel_begin.php
		if(!file_exists(LOG_PATH.'php_error404.php')){
			$this->message(1,'暂无PHP404错误日志');
		}else{
			$line = LOG_PATH.'php_error404.php';
			if(unlink($line)){
				// hook admin_index_control_notFoundErrordel_after.php
				$this->message(1,'删除成功');
			}else {
				$this->message(0,'删除失败,请检查 runtime/log/php_error404.php 是否有可写权限');
			}
		}
	}
    // hook admin_index_control_after.php
}