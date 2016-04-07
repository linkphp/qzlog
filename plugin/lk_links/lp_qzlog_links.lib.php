<?php
/**
 * 友情链接插件
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_links($conf){
	global $run;

	// hook lp_qzlog_links_before.php

	$arr = list_sort_by($run->kv->xget('lk_links'),'num');

	// hook lp_qzlog_links_after.php

	return $arr;
}