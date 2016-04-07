<?php
/**
 * 网站首页
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class index_control extends base_control {

	public $_cfg = array();	//全站参数
	public $_var = array();	//首页参数

	// hook index_control_begin.php
	public function index($html = null) {
		// hook index_control_index_begin.php
		if(!$html) {
			$this->display('index.htm');
		}else {
			$this->view->index_display('index.htm');
		}
		// hook index_control_index_after.php
	}
	// hook index_control_after.php
}