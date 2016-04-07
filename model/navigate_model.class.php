<?php
/**
 * 导航模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class navigate extends model{
	// hook navigate_model_begin.php
	function __construct(){
		$this->table = 'navigate';		// 表名
		$this->pri = array('id');		// 主键
		$this->maxid = 'id';			// 自增字段
	}
	// hook navigate_model_after.php
}