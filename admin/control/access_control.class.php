<?php
/**
 * 权限节点控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class access_control extends admin_control{
	// hook admin_access_control_begin.php
	//角色列表
	public function index(){
		// hook admin_access_control_index_begin.php
		$count = $this->access_role->find_count();
		$list = $this->access_role->find_fetch(array(),array('role_id'=>1));
		$this->assign_value('count',$count);
		$this->assign('list',$list);
		// hook admin_access_control_index_after.php
		$this->display('role/list.htm');
	}

	//添加角色
	public function addrole(){
		// hook admin_access_control_addrole_begin.php
		if(IS_POST){
            $data['role_name'] = trim(R('role_name','P'));
            $data['description'] = trim(R('description','P'));
            trim($data['role_name']) == '' && E(0,'请填写角色名');
            $data['status'] = 1;
            // hook admin_access_control_addrole_post.php
            $this->access_role->create($data) ? E(1,'添加成功') : E(0,'添加失败');
		}

		// hook admin_access_control_addrole_after.php
        $this->display('access/addrole.htm');
	}

	//更新角色
	public function doRole(){
		// hook admin_access_control_doRole_begin.php
		if(IS_POST){
			$data['role_name'] = trim(R('roleName','P'));
			$data['status'] = (int)R('status','P');
			$data['role_id'] = (int)R('role_id','P');
			$data['description'] = trim(R('description','P'));
			// hook admin_access_control_doRole_post.php
			$this->access_role->update($data) ? E(1,'更新成功') : E(0,'更新失败,请重试');
		}
		// hook admin_access_control_doRole_after.php
	}

	//删除角色
	public function roleDelete(){
		// hook admin_access_control_roleDelete_begin.php
		if(IS_POST){
			$role_id = (int)R('roleId','P');
			$role_id == 1 && E(0,'超级管理员不能删除');
            $this->member->find_fetch(array('role_id'=>$role_id)) && E(0,'该角色下存在用户，请先改为其他角色，或者删除用户');
            // hook admin_access_control_roleDelete_post.php
            $this->access_role->delete($role_id) ? E(1,'删除成功') : E(0,'删除失败,请重试');
		}
		// hook admin_access_control_roleDelete_after.php
	}

	//添加菜单/节点
	public function add(){
		// hook admin_access_control_add_begin.php
		if(IS_POST){
            $data['pid'] = (int)R('pid','P');
            $data['name'] = trim(R('name','P'));
            $data['control'] = trim(R('control','P'));
            $data['function'] = trim(R('function','P'));
            $data['menu'] = (int)R('menu','P');
            $data['status'] = (int)R('status','P');
            $data['sort'] = R('sort','P') != '' ? (int)R('sort','P') : 99;
            $data['name'] == '' && E(0,'请输入名字');
            // hook admin_access_control_add_post.php
            $this->access_node->create($data) ? E(1,'添加成功') : E(0,'添加失败');
		}
        $id = (int)R('id');
        $tree = new tree();
        $treenode = $tree->get_tree($this->access_node->get_node(),0,$id);
        // hook admin_access_control_add_after.php
        $this->assign('treenode',$treenode);
        $this->display('access/add.htm');
	}

	//节点列表
	public function node(){
		// hook admin_access_control_node_begin.php
		$nodeList = $this->access_node->get_node();
		$formateNode = data::tree($nodeList, "name", "id", "pid");
		// hook admin_access_control_node_after.php
		$this->assign('list',$formateNode);
		$this->display('access/node.htm');
	}

	//更新菜单节点排序
	public function upNodeSort(){
		// hook admin_access_control_upNodeSort_begin.php
		if(IS_POST){
			$node_sort = R('list_sort','P');
			foreach ($node_sort as $id => $sort){
				$data['id'] = $id;
				$data['sort'] = $sort;
	            $this->access_node->update($data);
        	}
        	// hook admin_access_control_upNodeSort_post.php
			E(1,'更新成功');
		}
		// hook admin_access_control_upNodeSort_after.php
	}

	//编辑节点
	public function edit(){
		// hook admin_access_control_edit_begin.php
		if(IS_POST){
			$data['id'] = (int)R('id','P');
			$data['name'] = trim(R('name','P'));
			$data['icon'] = trim(R('icon','P'));
			$data['pid'] = (int)R('pid','P');
			$data['control'] = trim(R('control','P'));
			$data['function'] = trim(R('function','P'));
			$data['status'] = (int)R('status','P');
			$data['menu'] = (int)R('menu','P');
			$data['sort'] = (int)R('sort','P');
			// hook admin_access_control_edit_post.php
			$this->access_node->update($data) ? E(1,'编辑成功') : E(0,'编辑失败,请重试');
		}
        $id = R('id');
        $node = $this->access_node->read($id);
        $this->assign('node',$node);
        $tree = new tree();
        $treenode = $tree->get_tree($this->access_node->get_node(),0,$node['pid']);
        // hook admin_access_control_edit_after.php
        $this->assign('treenode',$treenode);
        $this->display('access/edit.htm');
	}

	//启用/禁用节点
	public function NodeStatus(){
		// hook admin_access_control_NodeStatus_begin.php
		if(IS_POST){
			$id = (int)R('nodeId','P');
			// hook admin_access_control_NodeStatus_post.php
			$this->access_node->NodeStatus($id) ? E(1,'操作成功') : E(0,'操作失败');
		}
		// hook admin_access_control_NodeStatus_after.php
	}

	//删除节点
	public function delNode(){
		// hook admin_access_control_delNode_begin.php
		$id = (int)R('nodeId','P');
		$this->access_node->find_isset_child($id) && E(0,'请先删除或者转移子节点');
		// hook admin_access_control_delNode_after.php
		$this->access_node->delete($id) ? E(1,'删除成功') : E(0,'删除失败,请重试');
	}

	//用户权限分配
	public function auth(){
		// hook admin_access_control_auth_begin.php
		if(IS_POST){
			$data['role_id'] = (int)R('role_id','P');
			$role_auth = R('authId','P');
			$data['auth']=implode(",",$role_auth);
			// hook admin_access_control_auth_post.php
			$this->access_role->update($data) ? E(1,'操作成功') : E(0,'操作失败');
		}

        $nodeList = $this->access_node->get_node_ok();
        $channelLevel = data::channelLevel($nodeList, 0, '-', 'id');
        $role_id = (int)R('role_id');
        if($role_id == 1){
            $allNode = $this->access_node->get_node();
            foreach($allNode as $v){
                $role_auth[] = $v['id'];
            }
        }else {
            $role_auth = explode(',', $this->access_role->getRoleAuth($role_id));
        }
        // hook admin_access_control_auth_after.php
        $this->assign('list',$channelLevel);
        $this->assign_value('role_id',$role_id);
        $this->assign('role_auth',$role_auth);
        $this->display('access/auth.htm');
	}

	//hook admin_access_control_after.php
}