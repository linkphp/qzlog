<?php
/**
 * 附件控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class attachment_control extends admin_control{
    // hook admin_attachment_control_before.php
	//附件列表
	public function index(){
        // hook admin_attachment_control_index_before.php
        $count = $this->article_attachment->find_count();
        $page = new page();
        $limit = 20;
        $show = $page->pager($count,$limit, 'index.php?attachment-index', true);
        $pbegin = R('page') ? R('page') : 1;
        $arr = $this->article_attachment->find_fetch(array(),array('id'=>0),$limit*($pbegin-1),$limit,0);
        // hook admin_attachment_control_index_after.php
        $this->assign('page',$show);
		$this->assign_value('limit',$limit);
        $this->assign_value('count',$count);
        $this->assign('list',$arr);
		$this->display('attachment/list.htm');
	}

	//上传附件
	public function add(){
		// hook admin_attachment_control_add_begin.php
		$this->display('attachment/add.htm');
	}

	//批量删除
	public function batchDel(){
		$ids = R('attachid', 'P');
		empty($ids) && E(0, '请选择附件');
        // hook admin_attachment_control_batchDel_before.php
		foreach ($ids as $id){
			$filedata = $this->article_attachment->read($id);
			$file = ROOT_PATH.'upload/'.$filedata['filepath'];
			is_file($file) && unlink($file);
			if($filedata['isimage'] == 1){
				$file_thumb = ROOT_PATH.'upload/'. rtrim($filedata['filepath'],'.'.$filedata['fileext']).'_thumb'.'.jpg';
				is_file($file_thumb) && unlink($file_thumb);
			}
			$this->article_attachment->delete($id);
		}
		// admin_attachment_control_batchDel_after.php
		E(1, '删除成功');
	}

     // hook admin_attachment_control_after.php
}