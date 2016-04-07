<?php
/**
 * 文章内容页
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class show_control extends base_control{

	public $_cfg = array();	// 全站参数
	public $_var = array();	// 内容页参数
	// hook show_control_before.php
	public function index(){
		// hook show_control_index_before.php
		$nid = (int)R('id');
		$_show = $this->article->read($nid,3600);
        !empty($_show['redirecturl']) && $this->redirect($_show['redirecturl']);
		if(empty($_show['nid']) || $_show['nid'] != $nid) core::error404();
		$this->_var = $this->category->get_cate($_show['cid']);
		empty($this->_var) && core::error404();
		$_show['urlname'] = $this->_var['urlname'];
        $this->article->find_update(array('nid'=>$nid),array('click'=>$_show['click']+1));
		$this->_cfg['title'] = $_show['title'];
		$this->_cfg['keywords'] = empty($_show['keywords']) ? $_show['title'] : $_show['keywords'];
		$this->_cfg['description'] = $_show['description'];
        $this->assign('qz_var', $this->_var);
		$GLOBALS['_show'] = &$_show;
		// hook show_control_index_after.php
		$this->display($this->_var['show_tpl']);
	}
	// hook show_control_after.php
}