<?php
/**
 * 用户管理模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class member extends model{
	// hook member_model_before.php
	function __construct(){
		$this->table = 'member';		// 表名
		$this->pri = array('uid');		// 主键
		$this->maxid = 'uid';			// 自增字段
	}

	//查询用户名是否存在
	public function checkUsername($username){
		$arr = $this->find_fetch(array('username'=>$username));
		if(!empty($arr)){
			return true;
		}else{
			return false;
		}
	}

	//根据username查询用户的信息
	public function get_info_by_username($username){
		$data = $this->find_fetch(array('username'=>$username), array(), 0, 1);
		return $data ? current($data) : array();
	}

    //判断密码是否正确
    public function verify_password($oldpw, $salt, $password){
        if(md5(md5($oldpw).$salt) != $password){
            return false;
        }else {
            return true;
        }
    }
    // hook member_model_after.php
}