<?php
/**
 * 标签控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class tag_control extends admin_control{
	// hook admin_tag_control_begin.php
	//TAG标签
	public function index(){
        // hook admin_tag_control_index_begin.php
		$keyword = R('keyword');
		if($keyword){
			$keyword = urldecode($keyword);
			$keyword = safe_str($keyword);
		}
		if(isset($keyword) && !empty($keyword)){
			$where = array('name'=>array('LIKE'=>$keyword));
			$count = $this->tag->find_count($where);
			$urlstr = '-keyword-'.urlencode($keyword);
		}else{
			$where = array();
			$count = $this->tag->find_count();
			$urlstr = '';
		}
        $count = $this->tag->find_count($where);
        $page = new page();
        $limit = 20;
        $show = $page->pager($count,$limit, 'index.php?u=tag-index'.$urlstr, true);
        $pbegin = R('page') ? R('page') : 1;
        $arr = $this->tag->find_fetch($where,array('tagid'=>0),$limit*($pbegin-1),$limit,0);
        // hook admin_tag_control_index_after.php
        $this->assign('page',$show);
        $this->assign('keyword',$keyword);
        $this->assign('list',$arr);
        $this->assign_value('count',$count);
		$this->assign_value('limit',$limit);
        $this->display('tag/list.htm');
	}

	//添加TAG标签
	public function add(){
        // hook admin_tag_control_add_begin.php
		if(IS_POST){
            $data['name'] = trim(safe_str(R('name', 'P')));
            $data['content'] = htmlspecialchars(trim(R('content', 'P')));
            // hook admin_tag_control_add_post_begin.php
			empty($data['name']) && E(0, '标签名不能为空');
			strlen($data['name'])>30 && E(0, '标签名称太长');
			$tag_arr = $this->tag->find_fetch(array('name'=>$data['name']));
			if(is_array($tag_arr) && !empty($tag_arr)){
				E(0,$data['name'].'已经存在');
			}else {
				$data['count'] = 0;
				$rs = $this->tag->create($data);
				// hook admin_tag_control_add_post_after.php
				$rs ? E(1,'添加成功') : E(0,'添加失败,请重试');
			}
		}
		 // hook admin_tag_control_add_after.php
        $this->display('tag/add.htm');
	}

	//编辑TAG标签
	public function edit(){
        // hook admin_tag_control_edit_begin.php
		if(IS_POST){
            $tagid = (int)R('tagid', 'P');
            $name = trim(safe_str(R('name', 'P')));
            $content = htmlspecialchars(trim(R('content', 'P')));
            // hook admin_tag_control_edit_post_begin.php
			empty($name) && E(0, '标签名不得为空');
			strlen($name)>30 && E(0, '标签名称太长了');
			$data = $this->tag->read($tagid);
			//修改文章表
			if($data['name'] != $name){
				$list_arr = $this->tag_relation->find_fetch(array('tagid'=>$tagid));
				foreach($list_arr as $v){
					$data2 = $this->article->read($v['id']);
					empty($data2) && E(0, '读取文章表出错！');
					$row = _json_decode($data2['tags']);
					$row[$tagid] = $name;
					$data2['tags'] = _json_encode($row);
					!$this->article->update($data2) && E(0, '写入文章表出错');
				}
			}
			$data['name'] = $name;
			$data['content'] = $content;
			$rs = $this->tag->update($data);
			// hook admin_tag_control_edit_post_after.php
			$rs ? E(1, '编辑成功') : E(0, '编辑失败');
		}
        $tagid = (int)R('tagid');
        $tag = $this->tag->read($tagid);
        // hook admin_tag_control_edit_after.php
        $this->assign('tag',$tag);
        $this->display('tag/edit.htm');
	}

	//删除标签
	public function del(){
		$tagid = (int) R('tagid', 'P');
		empty($tagid) && E(0, '标签ID不能为空');
        // hook admin_tag_control_del_begin.php
		$rs = $this->tag->xdelete($tagid);
        // hook admin_tag_control_del_after.php
		$rs ? E(0, $rs) : E(1, '删除成功');
	}

	//批量删除
	public function batchDel(){
		$tagid = R('tagid', 'P');
		empty($tagid) && E(0, '请选择标签');
        // hook admin_tag_control_batchDel_before.php
		foreach ($tagid as $id){
			$this->tag->xdelete($id);
		}
		// hook admin_tag_control_batchDel_after.php
		E(1, '删除成功');
	}
	// hook admin_tag_control_after.php
}