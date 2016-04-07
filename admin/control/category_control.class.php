<?php
/**
 * 栏目分类控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class category_control extends admin_control{
	// hook admin_category_control_begin.php
	//分类列表
	public function index(){
        // hook admin_category_control_index_begin.php
		$cat = $this->category->get_all_category();
		$list = data::tree($cat, "name", "cid", "pid");
		// hook admin_category_control_index_after.php
		$this->assign('list',$list);
		$this->display('category/list.htm');
	}

	//分类添加
	public function add(){
        // hook admin_category_control_add_begin.php
		if(IS_POST){
			$data['name'] = trim(R('name','P'));
			$data['urlname'] = trim(R('urlname','P'));
			$data['pid'] = (int)R('pid','P');
            $data['attribute'] = (int)R('attribute','P');
			$data['pagenum'] = (int)R('pagenum','P');
			$data['referpath'] = R('referpath','P');
			$data['status'] = (int)R('status','P');
			$data['sort'] = (int)R('sort','P');
			$data['att_tpl'] = trim(R('att_tpl','P'));
            $data['list_tpl'] = trim(R('list_tpl','P'));
			$data['show_tpl'] = trim(R('show_tpl','P'));
			$data['seotitle'] = trim(R('seotitle','P'));
			$data['seokeywords'] = trim(R('seokeywords','P'));
			$data['seodescription'] = trim(R('seodescription','P'));
            $data['content'] = trim(R('content','P'));
			empty($data['name']) && E(0,'请填写分类名');
			$projectid = (int)R('projectid','P');
            // hook admin_category_control_add_post_begin.php
            if($cid = $this->category->create($data)){
            	//扩展字段添加
				if($projectid){
					$this->category_field->create(array('projectid'=>$projectid,'cid'=>$cid));
				}
				// hook admin_category_control_add_post_after.php
            		E(1,'添加成功');
            }else{
            		E(0,'添加失败，请重试');
            }
		}
        $cat = $this->category->get_all_category();
        $list = data::tree($cat, "name", "cid", "pid");
        //扩展字段
        $field_project = $this->field_project->find_fetch();
        // hook admin_category_control_add_after.php
        $this->assign('list',$list);
		$shangji = C('html_dir');
		$this->assign('shangji',$shangji);
        $this->assign('field_project',$field_project);
        $this->display('category/add.htm');
	}

	//编辑分类
	public function edit(){
         // hook admin_category_control_edit_begin.php
		if(IS_POST){
			$data['cid'] = (int)R('cid','P');
			$data['name'] = trim(R('name','P'));
			$data['urlname'] = trim(R('urlname','P'));
			$data['pid'] = (int)R('pid','P');
            $data['attribute'] = (int)R('attribute','P');
			$data['pagenum'] = (int)R('pagenum','P');
			$data['referpath'] = R('referpath','P');
			$data['status'] = (int)R('status','P');
			$data['sort'] = (int)R('sort','P');
            $data['att_tpl'] = trim(R('att_tpl','P'));
			$data['list_tpl'] = trim(R('list_tpl','P'));
			$data['show_tpl'] = trim(R('show_tpl','P'));
			$data['seotitle'] = trim(R('seotitle','P'));
			$data['seokeywords'] = trim(R('seokeywords','P'));
			$data['seodescription'] = trim(R('seodescription','P'));
            $data['content'] = trim(R('content','P'));
			empty($data['name']) && E(0,'分类名不得为空');
            // hook admin_category_control_edit_post_begin.php
			if($this->category->update($data)){
				//扩展字段编辑
				$projectid = (int)R('projectid','P');
				if($projectid){
					$result = $this->category_field->updateByCid($projectid,$data['cid']);
					!$result && E(0,'编辑扩展字段失败');
				}
				// hook admin_category_control_edit_post_after.php
            	E(1,'编辑成功');
			}else{
				E(0,'编辑失败，请重试');
			}
             // hook admin_category_control_edit_post_after.php
		}
        $cid = (int)R('cid');
        $cat = $this->category->get_all_category();
        $list = data::tree($cat, "name", "cid", "pid");
        $category = $this->category->read($cid);
        $this->assign('category',$category);
        $this->assign('list',$list);
		$shangji = C('html_dir');
		$this->assign('shangji',$shangji);
        $rs = $this->category_field->find_fetch(array('cid'=>$cid));
        if($rs){
			$rs = current($rs);
			$this->assign('projectid',$rs['projectid']);
			$field_project = $this->field_project->find_fetch();
		}
        // hook admin_category_control_edit_after.php
        $this->assign('field_project',$field_project);
        $this->display('category/edit.htm');
	}

	//更新分类排序
	public function upSort(){
        // hook admin_category_control_upSort_begin.php
		if(IS_POST){
			$category_sort = R('list_sort','P');
            // hook admin_category_control_upSort_post_begin.php
			foreach ($category_sort as $id => $sort){
				$data['cid'] = $id;
				$data['sort'] = $sort;
	            $this->category->update($data);
        	}
        	// hook admin_category_control_upSort_post_after.php
			E(1,'更新成功');
		}
        // hook admin_category_control_upSort_after.php
	}

	//删除分类
	public function del(){
        // hook admin_category_control_del_begin.php
		if(IS_POST){
			$cid = (int)R('cid','P');
			$this->category->super_class($cid) && E(0,'请先移动或者删除下级栏目');
            $rs = $this->article->if_has_article($cid);
            !empty($rs) && E(0,'该栏目下存在文章,请先转移文章');
            // hook admin_category_control_del_post_begin.php
			$rs = $this->category->delete($cid);
			// hook admin_category_control_del_post_after.php
			$rs ? E(1,'删除成功') : E(0,'删除失败,请重试');
		}
        // hook admin_category_control_del_after.php
	}

    //移动分类
    public function move(){
    	// hook admin_category_control_move_begin.php
        if(IS_POST){
            $oldCid = (int)R('oldCid','P');
            $to_cid = (int)R('to_cid','P');
            $this->category->super_class($oldCid) && E(0,'请先移动或者删除下级栏目');
			// hook admin_category_control_move_post_begin.php
            $rs = $this->category->find_update(array('cid'=>$oldCid),array('pid'=>$to_cid));
            // hook admin_category_control_move_post_after.php
            $rs ? E(1,'移动成功') : E(0,'移动失败');
        }
        $cid = (int)R('cid');
        $category = $this->category->read($cid);
        $cat = $this->category->get_all_category();
        $list = data::tree($cat, "name", "cid", "pid");
        // hook admin_category_control_move_after.php
        $this->assign('list',$list);
        $this->assign('category',$category);
        $this->display('category/move.htm');
    }
    // hook admin_category_control_after.php
}