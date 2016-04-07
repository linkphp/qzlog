<?php
/**
 * 前台栏目列表
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class category_control extends base_control{
    public $_cfg = array();	// 全站参数
    public $_var = array();	// 分类页参数
    // hook category_control_before.php
    public function index($html = null,$cid = null,$page = 1){
    	// hook category_control_index_before.php
        $cid = isset($cid) ? $cid : (int)R('cid');
		isset($_GET['page']) ? $_GET['page'] : $_GET['page'] = $page;
        $this->_var = $this->category->get_cate($cid);
        empty($this->_var) && core::error404();
        $this->_cfg['title'] = isset($this->_var['name'])? $this->_var['name'] : ''.(empty($this->_var['seotitle']) ? '' : '/'.$this->_var['seotitle']);
        $this->_cfg['keywords'] = isset($this->_var['seokeywords']) ? $this->_var['seokeywords'] :'';
        $this->_cfg['description'] = isset($this->_var['seodescription']) ? $this->_var['seodescription'] : '';
        $this->assign('qz_var', $this->_var);
        if(isset($this->_var['attribute']) && $this->_var['attribute'] == 1){
			//频道封面
			if(!$html) {
				$this->display($this->_var['att_tpl']);
			}else {
				$this->view->index_display($this->_var['att_tpl']);
			}
        }elseif(isset($this->_var['attribute']) && $this->_var['attribute'] == 2){
			//列表页
			if(!$html) {
				$this->display($this->_var['list_tpl']);
			}else {
				$this->view->index_display($this->_var['list_tpl']);
			}
        }else{ //外部链接
            $this->redirect(isset($this->_var['urlname']) ? $this->_var['urlname'] :null);
        }
		// hook category_control_index_after.php
    }
    // hook category_control_after.php
}