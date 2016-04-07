<?php
/**
 * 标签TAG
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class tag_control extends base_control{

    public $_cfg = array(); // 全站参数
    public $_var = array(); // 标签页参数
    // hook tag_control_before.php
    public function index(){
        // hook tag_control_index_before.php
        $name = R('name');
        empty($name) && core::error404();
        $name = substr(urldecode($name), 0, 30);
        $name = safe_str($name);
        $tags = $this->tag->find_fetch(array('name'=>$name), array(), 0, 1);
        empty($tags) && core::error404();
        $tags = current($tags);
		$this->_cfg['title'] = $tags['name'];
		$this->_cfg['keywords'] = $tags['name'];
		$this->_cfg['description'] = $tags['name'];
        $this->assign('qz_var', $this->_var);
        $GLOBALS['tags'] = &$tags;
        // hook tag_control_index_after.php
        $this->display('tag_list.htm');
    }

	//热门标签
	public function hot(){
		// hook tag_control_top_before.php
		$this->_cfg['title'] = '热门标签';
		$this->assign('qz_var', $this->_var);
		// hook tag_control_top_after.php
		$this->display('tag_hot.htm');
	}
    // hook tag_control_after.php
}
