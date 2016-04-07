<?php
/**
 * 幻灯片（Banner）模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class banner extends model{
	// hook banner_model_before.php

	function __construct(){
		// hook banner_model_construct_before.php
		$this->table = 'banner';		// 表名
		$this->pri = array('id');		// 主键
		$this->maxid = 'id';			// 自增字段
	}

	// hook banner_model_after.php
}