<?php
/**
 * 角色模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class access_role extends model{
	// hook access_role_model_begin.php
	function __construct(){
		// hook access_role_model_construct_begin.php
		$this->table = 'access_role';	// 表名
		$this->pri = array('role_id');	// 主键
		$this->maxid = 'role_id';		// 自增字段
	}

	//获取所有用户角色
	public function get_all_user_role(){
		// hook access_role_model_get_all_user_role_begin.php
		return $this->find_fetch('',array('role_id'=>1));
	}

	//获取未禁用的用户角色
	public function get_ok_user_role(){
		// hook access_role_model_get_ok_user_role_begin.php
		return $this->find_fetch(array('status'=>1));
	}

	//角色所有操作权限
	public function getRoleAuth($id){
		// hook access_role_model_getRoleAuth_begin.php
		$arr = $this->read($id);
		return $arr['auth'];
	}
	// hook access_role_model_after.php
}