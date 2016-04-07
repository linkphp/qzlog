<?php
/**
 * 权限节点模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class access_node extends model{
	// hook access_node_model_begin.php
	function __construct(){
		// hook access_node_model_construct_begin.php
		$this->table = 'access_node';	// 表名
		$this->pri = array('id');		// 主键
		$this->maxid = 'id';			// 自增字段
	}

	//获取所有(正常+禁止)的菜单
	public function get_menu(){
		// hook access_node_model_get_menu_begin.php
		return $this->find_fetch(array('menu'=>1),array('sort'=>1));
	}

	//获取正常的菜单
	public function get_all_menu(){
		// hook access_node_model_get_all_menu_begin.php
		return $this->find_fetch(array('status'=>1,'menu'=>1),array('sort'=>1,'id'=>1));
	}

	//获取所有(正常+禁止)二级菜单
	public function get_all_child($id){
		// hook access_node_model_get_all_child_begin.php
		return $this->find_fetch(array('pid'=>$id,'menu'=>1),array('sort'=>1));
	}

	//获取所有正常的二级菜单
	public function get_child($id){
		// hook access_node_model_get_child_begin.php
		return $this->find_fetch(array('pid'=>$id,'status'=>1,'menu'=>1),array('sort'=>1));
	}

	//获取所有显示的一级菜单
	public function get_all_parent_menu(){
		// hook access_node_model_get_all_parent_menu_begin.php
		return $this->find_fetch(array('pid'=>0,'status'=>1));
	}

	//查询是否存在子菜单
	public function find_isset_child($id){
		// hook access_node_model_find_isset_child_begin.php
		return $this->find_fetch(array('pid'=>$id));
	}

	//获取所有(正常+禁止)的节点
	public function get_node(){
		// hook access_node_model_get_node_begin.php
		return $this->find_fetch(array(),array('sort'=>1,'id'=>1));
	}

	//启用/禁止节点
	public function NodeStatus($id){
		$arr = $this->read($id);
		$data['id'] = $id;
		$arr['status'] == 1?$data['status'] = 0:$data['status'] = 1;
		// hook access_node_model_NodeStatus_begin.php
		return $this->update($data);
	}

	//获取所有正常的节点
	public function get_node_ok(){
		// hook access_node_model_get_node_ok_begin.php
		return $this->find_fetch(array('status'=>1),array('sort'=>1,'id'=>1));
	}

    //通过control和action获取节点id
    public function get_nodeId_by_c_f($control,$function){
    	// hook access_node_model_get_nodeId_by_c_f_begin.php
        $node = $this->find_fetch(array('control'=>$control,'function'=>$function));
        // hook access_node_model_get_nodeId_by_c_f_after.php
        return $node ? current($node) : array();
    }
    // hook access_node_model_after.php
}