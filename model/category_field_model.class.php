<?php
/**
 * 扩展字段与栏目关联模型
 * @author: caisonglin <595785872@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class category_field extends model{
	// hook category_field_model_before.php
	function __construct(){
		// hook category_field_model_construct_before.php
		$this->table = 'category_field';	// 表名
		$this->pri = array('id');			// 主键
		$this->maxid = 'id';				// 自增字段
	}

	/**
	 * 更新栏目扩展字段
	 * param int $projectid 扩展字段项目id
	 * param int $cid 栏目cid
	 * return bool
	 **/
	public function updateByCid($projectid,$cid){
		$result = false;
		if($cid && $projectid){
			// hook category_field_model_updateByCid_before.php
			//获取主键
			$cate = $this->find_fetch(array('cid'=>$cid));
			if(!empty($cate)){
				$cate = current($cate);
				$result = $this->update(array('id'=>$cate['id'],'projectid'=>$projectid));
			}else{
				$result = $this->create(array('projectid'=>$projectid,'cid'=>$cid));
			}
		}
		return $result;
	}
	// hook category_field_model_after.php
}