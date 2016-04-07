<?php
/**
 * 友情链接插件
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
$arr = array();
$arr[] = array('name' => 'QZLOG', 'url' => 'http://www.qzlog.com');
$arr[] = array('name' => '撸大姐', 'url' => 'http://www.ludajie.com' );
$this->kv->set('lk_links', $arr);