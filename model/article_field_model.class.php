<?php
/**
 * 文章对应扩展字段设计模型
 * @author: caisonglin <595785872@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class article_field extends model{
	// hook article_field_model_begin.php
 	function __construct(){
 		// hook article_field_model_construct_begin.php
		$this->table = 'article_field';	// 表名
		$this->pri = array('id');		// 主键
		$this->maxid = 'id';			// 自增字段
	}

	/*
	 * 添加文章对应扩展字段
	 * param int $id 文章id
	 * param array() $data
	 */
	public function addArtField($id,&$data){
		// hook article_field_model_addArtField_begin.php
		$extfield_arr = array();
		$this->field->getVarBycid($data['cid'],$extfield_arr);
		foreach($extfield_arr as $k => $v){
			$extfield_arr[$k]['value'] = trim($data[$v['var']]);
		}
		$this->article_field->create(array('article_id'=>$id,'cid'=>$data['cid'],'data'=>serialize($extfield_arr)));
		// hook article_field_model_addArtField_after.php
	}


	/**
	 * 更新文章对应扩展字段
	 * param int $id 文章id
	 * param array() $data
	 **/
	public function updateArtField($id,&$data){
		// hook article_field_model_updateArtField_begin.php
		$field_data = $this->find_fetch(array('article_id'=>$id));
		if(!empty($field_data)){
			$field_data = array();
			$this->field->getVarBycid($data['cid'],$field_data);
			if($field_data){
				foreach($field_data as $k => $v){
					$field_data[$k]['value'] = trim($data[$v['var']]);
				}
				$data_field = array('cid'=>$data['cid'],'data'=>serialize($field_data));
				$this->article_field->find_update(array('article_id'=>$id),$data_field);
			}
		}else{
			$this->addArtField($id,$data);
		}
		// hook article_field_model_updateArtField_after.php
	}

	/**
	 * 获取文章关联扩展字段
	 * param int $id 文章id
	 * param int $cid 文章所属栏目id
	 * return array $result
	 **/
	public function getfieldById($id,$cid = null){
		// hook article_field_model_getfieldById_begin.php
		$field_data = array();
		if($id){
			$fields = $this->find_fetch(array('article_id'=>$id));
			if($fields && is_array($fields)){
				$fields = current($fields);
				$data = unserialize($fields['data']);
			}
			if(isset($fields['cid']) && $fields['cid'] == $cid && !empty($data)){
				$field_data = $this->getfieldData($data);
			}else{
				$this->field->getVarBycid($cid,$field_data);
			}
		}
		// hook article_field_model_getfieldById_after.php
		return $field_data;
	}

	private function getfieldData($data){
		// hook article_field_model_getfieldData_begin.php
		if($data){
			$fieldid = array();
			foreach($data as $k => $v){
				$tmp_arr = explode('-',$k);
				$fieldid[] = $tmp_arr[2];
			}
			if(!empty($fieldid)){
				$list = $this->field->find_fetch(array('fieldid'=>array('IN'=>$fieldid)));
				foreach($list as $k => $v){
					$tmp_arr = unserialize($v['setting']);
					$data[$k]['fieldname'] = $tmp_arr['fieldname'];
					$data[$k]['var'] = $tmp_arr['var'];
					$data[$k]['field'] = $v['field'];
				}
			}
			// hook article_field_model_getfieldData_after.php
			return $data;
		}
	}
	// hook article_field_model_after.php
}