<?php
/**
 * 扩展字段模型
 * @author: caisonglin <595785872@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class field_project extends model{
	// hook field_project_model_before.php
	function __construct(){
		$this->table = 'field_project';		// 表名
		$this->pri = array('projectid');	// 主键
		$this->maxid = 'projectid';			// 自增字段
	}

	public function addField($data,&$result){
		if($data){
			$result = $this->create($data);
		}
	}
	// hook field_project_model_after.php
}