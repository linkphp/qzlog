<?php
/**
 * 404错误
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class error404_control extends admin_control{

    // hook admin_error404_control_begin.php

    public function index(){
        // hook admin_error404_control_index_begin.php
		$this->display('404.htm');
    }

    // hook admin_error404_control_after.php
}