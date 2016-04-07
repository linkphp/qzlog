<?php
/**
 * 搜索结果
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class search_control extends base_control{

	public $_cfg = array();	// 全站参数
	public $_var = array();	// 搜索页参数
	// hook search_control_before.php
	public function index() {
		// hook search_control_index_before.php
		$keyword = urldecode(R('keyword'));
		$keyword = safe_str($keyword);
		$this->_cfg['title'] = $keyword;
		$this->_cfg['keywords'] = $keyword;
		$this->_cfg['description'] = $keyword;
        $this->assign('qz_var', $this->_var);
		$this->assign('keyword', $keyword);
		$GLOBALS['keyword'] = &$keyword;
		// hook search_control_index_after.php
		$this->display('search.htm');
	}
	// hook search_control_after.php
}