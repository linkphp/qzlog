<?php
/**
 * @author link
 */
defined('LINK_PATH') or exit;

//增加到已安装
array_push($menuList,
    array(
    	'id'=>'tips',
        'name'=>'添加小贴士',
        'pid'=>'80',
        'control'=>'tips',
        'function' => 'index',
        'menu' =>'1',
        'sort'=>'99',
        'status'=>'1',
    )
);