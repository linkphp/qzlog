<?php
/**
 * 404错误页
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class error404_control extends control{

	public $_cfg = array();	// 全站参数
	public $_var = array();	// 首页参数
	// hook 404_control_before.php
	function index(){
		// hook 404_control_index_before.php
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		$this->display('404.htm');
		// hook 404_control_index_after.php
	}
	// hook 404_control_after.php
}