<?php
/**
 * 后台基类
 * @author: link <612012@qq.com>
 * @copyright 轻舟CMS(QZLOG) http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class admin_control extends control{
	 // hook admin_admin_control_begin.php
	//初始化
	function __construct(){
		// hook admin_admin_control_construct_begin.php
		$_ENV['_config']['FORM_HASH'] = form_hash();
		$cfg = $this->kv->xget('cfg');
		$SITE_URL = $cfg['webdomain'].$cfg['webdir'];
		$cookie_admin_value = $this->get_cookie_admin();
		$this->assign('_cookie', $cookie_admin_value);
		$this->assign('C', $_ENV['_config']);
		$this->assign('cfg', $cfg);
		$this->assign_value('front', F_APP_NAME);
		$this->assign('SITE_URL', $SITE_URL);
		$this->menu();
		$result = $this->check_access();
		switch ($result){
			case 0:
				$this->redirect('index.php?u=login-index');
				break;
			case -1:
				$this->message(0,'安全考虑，请在配置文件的“创始人”一项上增加您的UID，您才有权限操作!','index.php?u=login-index');
				break;
			case -2:
				$this->message(0,'您没有访问权限!','history.back()');
				break;
		}
		// hook admin_admin_control_construct_after.php
	}

    /**
     * 检查用户是否有访问权限
     * 返回 1，表示（可以继续往下操作）
     * 返回 0，表示（您没登陆，请先登陆）
     * 返回 -1，表示（您好，超级管理员。出于安全考虑，请联系创始人在配置文件的“创始人”一项上增加您的UID，您才有权限操作！）
     * 返回 -2，表示（您没有该操作的访问权限）
     * @return number
     */
    public function check_access(){
    	// hook admin_admin_control_check_access_begin.php
		$control = &$_GET['control'];
		$function = &$_GET['action'];
		$action = $control.'/'.$function;
        $node = $this->access_node->get_nodeId_by_c_f($control,$function);
		if(!in_array($action, C('guest'))){
            //1、如果没有登陆，返回 0（没有登录）
			if($this->check_login() == FALSE){
				return 0;
			}
            //2、获取创始人配置
			$founder_arr = explode(',', C('founder'));
            $arr_user_qzlog_cookie = $this->get_cookie_admin();
			if(isset($arr_user_qzlog_cookie[0]) && !empty($arr_user_qzlog_cookie[0])){
				$uid = $arr_user_qzlog_cookie[0];
                $role_id = $arr_user_qzlog_cookie[3];
			}else{
				$uid = 0;
                $role_id = 0;
			}
            // 3、如果是超级管理员角色，但不是创始人，将不能进行操作
            //if( ($role_id == 1) && !in_array($uid, $founder_arr) ){
            //    _setcookie('user_qzlog', '', 1);
            //    return -1;
            //}
           // 4、如果是站点创始人，跳过权限验证，否则判断权限。
			if(!in_array($uid, $founder_arr) && ($role_id <> 1)){
				if($role_id <= 0){
					return -2;
				}
                //获取角色权限信息，返回-2表示（您没有该操作的访问权限）
				$row = $this->access_role->getRoleAuth($role_id);
				if(empty($row)){
					return -2;
				}
				$allow_nodeId = explode(',', $row);
				unset($role_id, $row);
				//检查访问权限
				if(!in_array(isset($node['id'])?$node['id']:'', $allow_nodeId)){
					return -2;
				}
			}
	}
	unset($action);
	return 1;
	}

	//判断用户是否登陆
	public function check_login(){
		$user_qzlog = R($_ENV['_config']['cookie_pre'].'user_qzlog', 'R');
		// hook admin_admin_control_check_login_begin.php
		if(isset($user_qzlog) && !empty($user_qzlog)){
			return true;
		}else{
			return false;
		}
	}

	//获取后台cookie
	public function get_cookie_admin(){
		$user_qzlog_cookie = R($_ENV['_config']['cookie_pre'].'user_qzlog', 'R');
		// hook admin_admin_control_get_cookie_admin_begin.php
		if(isset($user_qzlog_cookie) && !empty($user_qzlog_cookie)){
			$user_qzlog_cookie = str_auth($user_qzlog_cookie);
			$arr_user_qzlog_cookie = explode("\t", $user_qzlog_cookie);
			// hook admin_admin_control_get_cookie_admin_after.php
			return $arr_user_qzlog_cookie;
		}else{
			return false;
		}
	}

	//菜单组
	private function menu(){
        // hook admin_admin_control_menu_begin.php
		$menuList = $this->access_node->get_all_menu();
		// hook admin_admin_control_menu_setting.php
		array_push($menuList,
			array(
		    	'id'=>'bbs',
		        'name'=>'交流论坛',
		        'pid'=>'2',
		        'url' => 'http://www.qzlog.com/bbs/',
		        'menu' =>'1',
		        'sort'=>'2',
		        'status'=>'1',
		    ),
		    array(
		    	'id'=>'onlinePlugin',
		        'name'=>'在线插件',
		        'pid'=>'79',
		        'url' => 'http://www.qzlog.com/bbs/forum-46-1.html',
		        'menu' =>'1',
		        'sort'=>'99',
		        'status'=>'1',
		    ),
		    array(
		    	'id'=>'onlineTheme',
		        'name'=>'在线模板',
		        'pid'=>'81',
		        'url' => 'http://www.qzlog.com/bbs/forum-45-1.html',
		        'menu' =>'1',
		        'sort'=>'99',
		        'status'=>'1',
		    )
		);
        $list = data::channelLevel($menuList, 0, '', 'id');
        // hook admin_admin_control_menu_after.php
		$this->assign('menuList',$list);
	}

	//清除缓存
	public function clear_cache(){
		// hook admin_admin_control_clear_cache_begin.php
		try{
			unlink(RUNTIME_PATH.'_runtime.php');
		}catch(Exception $e) {}
		$tpmdir = array('_control', '_model', '_view');
		foreach($tpmdir as $dir) _rmdir(RUNTIME_PATH.APP_NAME.$dir);
		foreach($tpmdir as $dir) _rmdir(RUNTIME_PATH.F_APP_NAME.$dir);
		_rmdir(RUNTIME_PATH.'temp');
		// hook admin_admin_control_clear_cache_after.php
		return TRUE;
	}

	//重新统计
	public function rebuild_go(){
		// hook admin_admin_control_rebuild_go_begin.php
		$cids = $this->category->get_all_attribute_category(); //重新统计分类的内容数量
		if($cids){
			foreach($cids as $row){
			$count = $this->article->find_count(array('cid'=>$row['cid']));
			$this->category->update(array('cid'=>$row['cid'], 'count'=>$count));
		}
		}
		//清空数据表的 count max 值，让其重新统计
		$this->db->truncate('framework_count');
		$this->db->truncate('framework_maxid');
		//E(1, '重新统计完成！');
		// hook admin_admin_control_rebuild_go_after.php
		return true;
	}
	 // hook admin_admin_control_after.php
}