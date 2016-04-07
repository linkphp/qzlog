<?php
/**
 * 扩展字段控制器
 * @author: caisonglin <595785872@qq.com>
 * @copyright http://www.qzlog.com
*/

defined('LINK_PATH') || exit('Access Denied');
class extField_control extends admin_control{
	// hook admin_extField_control_before.php
	public function index(){
		// hook admin_extField_control_index_before.php
		$where = array();
		$count = $this->field_project->find_count();
		$page = new page();
		$limit = 10;
		$show = $page->pager($count,$limit, 'index.php?u=extField-index', true);
		$pbegin = R('page') ? R('page') : 1;
		$list = $this->field_project->find_fetch($where,array('projectid'=>0),$limit*($pbegin-1),$limit,0);
		// hook admin_extField_control_index_after.php
		$this->assign_value('count',$count);
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display('extfield/list.htm');
	}

	//设计扩展字段
	public function designField(){
		$id = (int)R('id');
		// hook admin_extField_control_designField_before.php
		$field_type = array('text'=>'单行文本','file'=>'文件上传','textarea'=>'多行文本','image'=>'图片上传');
		$list = $this->field->find_fetch(array('projectid'=>$id));
		if($list){
			foreach($list as $k => $v){
				if(in_array($v['field'],array('text','file','textarea','image'))){
					$list[$k]['type'] = $field_type[$v['field']];
				}
				if($v['setting']){
					$list[$k]['setting'] = unserialize($v['setting']);
				}
			}
		}
		// hook admin_extField_control_designField_after.php
		$this->assign('list',$list);
		$this->assign('projectid',$id);
		$this->display('extfield/design.htm');
	}

	//添加方案
	public function addFieldProject(){
		$name = trim(R('name','P'));
		$description = trim(R('description','P'));
		empty($name) && E(0,'方案名称不能为空');
		$data = $result = array();
		$data['name'] = $name;
		$data['description'] = $description;
		// hook admin_extField_control_addFieldProject_before.php
		$this->field_project->addField($data,$result);
		if($result){
			// hook admin_extField_control_addFieldProject_after.php
			E(1, '添加成功');
		}else{
			E(0, '添加失败');
		}
	}

	//编辑扩展字段方案
	public function editFieldProject(){
		$id = (int)R('id','P');
		$name = trim(R('name','P'));
		$description = trim(R('description','P'));
		empty($name) && E(0,'方案名称不能为空');
		$data = array();
		$data['projectid'] = $id;
		$data['name'] = $name;
		$data['description'] = $description;
		// hook admin_extField_control_editFieldProject_before.php
		$rs = $this->field_project->update($data);
		// hook admin_extField_control_editFieldProject_after.php
		$rs ? E(1, '编辑成功') : E(0, '编辑失败');
	}

	//删除扩展字段方案
	public function delFieldProject(){
		$id = (int)R('id','P');
		// hook admin_extField_control_delFieldProject_before.php
		$rs = $this->field_project->delete($id);
		// hook admin_extField_control_delFieldProject_after.php
		$rs ? E(1, '删除成功') : E(0, '删除失败');
	}

	//批量删除扩展字段方案
	public function delAllFieldProject(){
		$ids = R('ids','P');
		$where['projectid'] = array('IN'=>$ids);
		// hook admin_extField_control_delAllFieldProject_before.php
		$rs = $this->field_project->find_delete($where);
		// hook admin_extField_control_delAllFieldProject_after.php
		$rs ? E(1, '批量删除成功') : E(0, '批量删除失败');
	}

	//添加设计字段
	public function adddesign(){
		$projectid = (int)R('projectid','P');
		$field = trim(R('field','P'));
		$setting['fieldname'] = trim(R('fieldname','P'));
		$setting['var'] = trim(R('var','P'));
		$setting['defaultvalue'] = trim(R('defaultvalue','P'));
		$field == 'textarea' && $setting['editor'] = (int)R('editor','P');
		//验证
		empty($setting['fieldname']) && E(0,'字段名称不能为空');
		empty($setting['var']) && E(0,'变量名不能为空');
		$data = array();
		$projectid && $data['projectid'] = $projectid;
		$field && $data['field'] = $field;
		!empty($setting) && $data['setting'] = serialize($setting);
		// hook admin_extField_control_adddesign_before.php
		$result = array();
		$this->field->addField($data,$result);
		// hook admin_extField_control_adddesign_after.php
		$result ? E(1, '添加成功') : E(0, '添加失败');
	}

	//编辑设计字段
	public function editdesign(){
		$fieldid = (int)R('fieldid','P');
		$editor = trim(R('editor','P'));
		if($fieldid){
			$setting['fieldname'] = trim(R('fieldname','P'));
			$setting['var'] = trim(R('var','P'));
			$setting['defaultvalue'] = trim(R('defaultvalue','P'));
			$editor != NULL && $setting['editor'] = $editor;
			empty($setting['fieldname']) && E(0,'字段名称不能为空');
			empty($setting['var']) && E(0,'变量名不能为空'); //验证
			$data['fieldid'] = $fieldid;
			$data['setting'] = serialize($setting);
			// hook admin_extField_control_editdesign_before.php
			$rs = $this->field->update($data);
			// hook admin_extField_control_editdesign_after.php
			$rs ? E(1, '编辑成功') : E(0, '编辑失败');
		}
	}

	//删除字段
	public function deldesign(){
		$id = (int)R('id','P');
		// hook admin_extField_control_deldesign_before.php
		if($id){
			$rs = $this->field->delete($id);
			// hook admin_extField_control_deldesign_after.php
			$rs ? E(1, '删除成功') : E(0, '删除失败');
		}
	}
	// hook admin_extField_control_after.php
}