<?php
/**
 * 幻灯控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class banner_control extends admin_control{
	// hook admin_banner_control_before.php
	//幻灯片主页
	public function index(){
		// hook admin_banner_control_index_before.php
		$count = $this->banner->find_count();
		$list = $this->banner->find_fetch(array(),array('num'=>1));
		// hook admin_banner_control_index_before.php
		$this->assign_value('count',$count);
		$this->assign('list', $list);
		$this->display('banner/list.htm');
	}

	//增加
	public function add(){
		// hook admin_banner_control_add_before.php
		if(IS_POST){
			$data['name'] = htmlspecialchars(R('name', 'P'));
			$data['url'] = htmlspecialchars(R('url', 'P'));
			$data['picurl'] = htmlspecialchars(R('picurl', 'P'));
			$data['num'] = (int)R('num', 'P');
			!$data['name'] && E(0, '标题不能为空');
			!$data['url'] && E(0, '跳转URL不能为空');
			!$data['picurl'] && E(0, '图片地址不能为空');
			// hook admin_banner_control_add_post_before.php
			$rs = $this->banner->create($data);
			// hook admin_banner_control_add_post_after.php
			$rs ? E(1, '添加成功') : E(0, '操作失败,请重试');
		}
		// hook admin_banner_control_add_after.php
		$this->display('banner/add.htm');
	}

	//删除
	public function del(){
		$id = (int)R('id','P');
		// hook admin_banner_control_del_before.php
		$rs = $this->banner->delete($id);
		// hook admin_banner_control_del_after.php
		$rs ? E(1,'删除成功') : E(0,'删除失败,请重试');
	}

	//编辑
	public function edit(){
		// hook admin_banner_control_edit_before.php
		if(IS_POST){
			$data['id'] = (int)(R('id', 'P'));
			$data['name'] = htmlspecialchars(R('name', 'P'));
			$data['url'] = htmlspecialchars(R('url', 'P'));
			$data['picurl'] = htmlspecialchars(R('picurl', 'P'));
			!$data['name'] && E(0, '标题不能为空');
			!$data['url'] && E(0, '跳转URL不能为空');
			!$data['picurl'] && E(0, '图片地址不能为空');
			// hook admin_banner_control_add_post_before.php
			$rs = $this->banner->update($data);
			// hook admin_banner_control_add_post_after.php
			$rs ? E(1, '修改成功') : E(0, '修改失败,请重试');
		}
		$id = (int)R('id');
		$banner = $this->banner->read($id);
		// hook admin_banner_control_edit_after.php
		$this->assign('banner',$banner);
		$this->display('banner/edit.htm');
	}

	//排序
	public function upSort(){
        // hook admin_banner_control_upSort_begin.php
		if(IS_POST){
			$list_sort = R('list_sort','P');
            // hook admin_banner_control_upSort_post_begin.php
			foreach ($list_sort as $id => $sort){
				$data['id'] = $id;
				$data['num'] = $sort;
	            $this->banner->update($data);
        	}
        	// hook admin_banner_control_upSort_post_after.php
			E(1,'更新成功');
		}
        // hook admin_banner_control_upSort_after.php
	}

	// hook admin_banner_control_after.php
}
