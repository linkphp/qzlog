<?php
/**
 * 登录控制器
 * @author: link <612012@qq.com>
 * @copyright 轻舟CMS(QZLOG) http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class login_control extends admin_control{
    // hook admin_login_control_begin.php
	//后台登陆
	public function index(){
		// hook admin_login_control_index_begin.php
		if(IS_POST){
            $username =  trim(R('username','P'));
            $password =  R('password','P');
            // hook admin_login_control_index_post_begin.php
            if(empty($username) || empty($password)) E(0, '用户名或密码不能为空.');
            $userinfo = $this->member->get_info_by_username($username);
            if(empty($userinfo)) E(0, '用户名不存在.');
            if($userinfo['status'] <> 1) E(0, '您的账号被禁用,如有疑问，请联系管理员.');
            if(md5(md5($password).$userinfo['salt']) != $userinfo['password']) E(0, '密码错误.');
            // 写入 cookie
            $user_qzlog = str_auth("$userinfo[uid]\t$userinfo[username]\t$userinfo[password]\t$userinfo[role_id]\t$userinfo[login_count]\t$userinfo[last_login_time]\t$userinfo[last_login_ip]\t$userinfo[regdate]\t$userinfo[regip]\t$userinfo[nickname]", 'ENCODE');
            _setcookie('user_qzlog', $user_qzlog, time()+86400*7 , '', '', false, true);
            $data['uid'] = $userinfo['uid'];
            $data['last_login_time'] = time();
            $data['last_login_ip'] = ip();
            $data['login_count'] = $userinfo['login_count'] + 1;
            $login_rs = $this->member->update($data);
            // hook admin_login_control_index_post_after.php
            if($login_rs){
                E(1, '登陆成功,正在跳转...');
                //$this->redirect('index.php?u=index-index');
            }
		}
        // hook admin_login_control_index_after.php
        $this->display('login/index.htm');
	}

	//注销退出
	public function logout(){
		// hook admin_login_control_logout_begin.php
		if(_setcookie('user_qzlog', '', 1)){
			$this->message(1,'退出成功!','index.php?login-index');
		}
		// hook admin_login_control_logout_after.php
	}
    // hook admin_login_control_after.php
}