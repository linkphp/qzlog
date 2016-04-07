<?php
/**
 * 友情链接插件
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */
defined('LINK_PATH') or exit;

//增加到已安装
array_push($menuList,
    array(
    	'id'=>'link',
        'name'=>'友情链接',
        'pid'=>'80',
        'control'=>'links',
        'function' => 'index',
        'menu' =>'1',
        'sort'=>'1',
        'status'=>'1',
    )
);