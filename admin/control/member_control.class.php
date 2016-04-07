<?php
/**
 * 用户管理控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class member_control extends admin_control{
    // hook admin_member_control_before.php
	//用户列表
	public function index(){
        // hook admin_member_control_index_before.php
        $srchtype = R('srchtype');
        $keyword = R('keyword');
        isset($keyword) && $keyword = safe_str(urldecode($keyword));
        $this->assign('keyword', $keyword);
        if(isset($keyword) && !empty($keyword)){
            switch ($srchtype)
            {
                case 'username':
                    $where = array('username'=>array('LIKE'=>$keyword));
                    $urlstr = '-username-'.urlencode($keyword);
                    $count = $this->member->find_count($where);
                    break;
                case 'email':
                    $where = array('email'=>array('LIKE'=>$keyword));
                    $urlstr = '-email-'.urlencode($keyword);
                    $count = $this->member->find_count($where);
                    break;
                case 'regip':
                    $where = array('regip'=>$keyword);
                    $urlstr = '-regip-'.urlencode($keyword);
                    $count = $this->member->find_count($where);
                    break;
                default:
                    $where = array('username'=>array('LIKE'=>$keyword));
                    $urlstr = '-username-'.urlencode($keyword);
                    $count = $this->member->find_count($where);
                    break;
            }
        }else{
            $where = array();
            $count = $this->member->find_count();
            $urlstr = '';
        }
        $page = new page();
        $limit = 20;
        $show = $page->pager($count,$limit, 'index.php?member-index'.$urlstr, true);
        $pbegin = R('page') ? R('page') : 1;
        $arr = $this->member->find_fetch($where,array('uid'=>0),$limit*($pbegin-1),$limit,0);
        $role = $this->access_role->get_all_user_role();
        foreach ($arr as $key => $value) {
            $arr[$key]['roleName'] = $role['access_role-role_id-'.$value['role_id']]['role_name'];
        }
        // hook admin_member_control_del_after.php
        $this->assign('page',$show);
		$this->assign_value('limit',$limit);
        $this->assign_value('count',$count);
        $this->assign('list',$arr);
        $this->assign('srchtype',$srchtype);
        $this->display('member/list.htm');
	}

	//添加用户
	public function add(){
        // hook admin_member_control_add_before.php
		if(IS_POST){
			$data['role_id'] = (int)R('role_id','P');
			$data['username'] = trim(R('username','P'));
			$data['nickname'] = trim(R('username','P'));
			$data['email'] = R('email','P');
			$data['regdate'] = time();
			$data['regip'] = ip();
			$data['salt'] = salt();
			$data['password'] = md5(md5(R('password','P')).$data['salt']);
            // hook admin_member_control_add_post_before.php
			if(trim($data['username']) =='' || trim($data['email']) =='' || R('password','P') =='' || R('password_again','P') == ''){
				E(0,'各项不得为空');
			}
			$this->member->checkUsername($data['username']) && E(0,'用户名已存在');
			if(R('password','P') != R('password_again','P')){
                E(0,'两次输入密码不一致');
            }
			$arr_user_qzlog_cookie = $this->get_cookie_admin();
			if($data['role_id'] == 1 && $arr_user_qzlog_cookie[0] != 1){
				E(0,'您不是创始人，不能创建超级管理员角色');
			}
			$rs = $this->member->create($data);
            // hook admin_member_control_add_post_after.php
            $rs ? E(1,'添加成功') : E(0,'添加失败,请重试');
		}
        $roleList = $this->access_role->get_ok_user_role();
        // hook admin_member_control_add_after.php
        $this->assign('roleList',$roleList);
        $this->display('member/add.htm');
	}

	//编辑用户
	public function edit(){
        // hook admin_member_control_edit_before.php
		if(IS_POST){
			$data['role_id'] = (int)R('role_id','P');
			$data['uid'] = (int)R('uid','P');
			$data['email'] = R('email','P');
			$data['nickname'] = R('nickname','P');
			$data['remark'] = R('remark','P');
			$data['status'] = (int)R('status','P');
            // hook admin_member_control_edit_post_before.php
			if(R('password','P') != ''){
				$password = R('password','P');
				$password_again = R('password_again','P');
				if($password != $password_again){
					E(0,'两次密码输入不一致!');
				}
                if(strlen($password) < 6){
                    E(0, '新密码不能小于6位');
                }
				$data['password'] = md5(md5(R('password','P')).R('salt','P'));
			}
            $arr_user_qzlog_cookie = $this->get_cookie_admin();
            if($data['uid'] == 1 && $arr_user_qzlog_cookie[0] != 1){
                E(0,'不可编辑超级管理员');
            }
            $rs = $this->member->update($data);
            // hook admin_member_control_edit_post_after.php
            $rs ? E(1,'编辑成功') : E(0,'编辑失败，请重试');
		}
        $uid = (int)R('uid');
        $userInfo = $this->member->read($uid);
        $roleList = $this->access_role->get_ok_user_role();
        // hook admin_member_control_edit_after.php
        $this->assign('roleList',$roleList);
        $this->assign('userInfo',$userInfo);
        $this->display('member/edit.htm');
	}

	//删除用户
	public function del(){
		$uid = (int)R('uid','P');
        // hook admin_member_control_del_before.php
		$uid == 1 && E(0,'创始人不能删除!');
		$rs = $this->member->delete($uid);
        // hook admin_member_control_del_after.php
        $rs ? E(1,'删除成功') : E(0,'删除失败,请重试');
	}

    //修改我的密码
    public function mypasswd(){
        // hook admin_member_control_mypasswd_before.php
        if(IS_POST){
            $oldpw = trim(R('oldpw', 'P'));
            $newpw = trim(R('newpw', 'P'));
            $confirm_newpw = trim(R('confirm_newpw', 'P'));
            // hook admin_member_control_mypasswd_post_before.php
            $arr_user_qzlog_cookie = $this->get_cookie_admin();
            $uid = $arr_user_qzlog_cookie[0];
            $data = $this->member->read($uid);
            if(empty($oldpw)){
                E(0, '旧密码不能为空');
            }elseif(strlen($newpw) < 6){
                E(0, '新密码不能小于6位');
            }elseif($confirm_newpw != $newpw){
                E(0, '确认密码不等于新密码');
            }elseif($oldpw == $newpw){
                E(0, '新密码不能和旧密码相同');
            }elseif(!$this->member->verify_password($oldpw, $data['salt'], $data['password'])){
                E(0, '旧密码不正确');
            }
            $data['salt'] = salt();
            $data['password'] = md5(md5($newpw).$data['salt']);
            if($this->member->update($data)){
                // hook admin_member_control_mypasswd_post_after.php
                _setcookie('user_qzlog', '', 1);
                E(1,'修改成功');
                $this->redirect('index.php?login-index',2);
            }else{
                E(0,'修改失败,请重试');
            }
        }
        // hook admin_member_control_mypasswd_after.php
        $this->display('member/mypasswd.htm');
    }

    //修改个人信息
    public function myinfo(){
        // hook admin_member_control_myinfo_before.php
        if(IS_POST){
            $arr_user_qzlog_cookie = $this->get_cookie_admin();
            $uid = $arr_user_qzlog_cookie[0];
            $data = $this->member->read($uid);
            $data['email'] = R('email','P');
            $data['nickname'] = R('nickname','P');
            $data['remark'] = R('remark','P');
            // hook admin_member_control_myinfo_post_before.php
            $rs = $this->member->update($data);
            // hook admin_member_control_myinfo_post_after.php
            $rs ? E(1,'编辑成功') : E(0,'编辑失败,请重试');
        }
        $arr_user_qzlog_cookie = $this->get_cookie_admin();
        $uid = $arr_user_qzlog_cookie[0];
        $userInfo = $this->member->read($uid);
        // hook admin_member_control_myinfo_after.php
        $this->assign('userInfo',$userInfo);
        $this->display('member/myinfo.htm');
    }
    // hook admin_member_control_after.php
}