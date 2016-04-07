<?php
/**
 * 导航标签
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_banner($conf){
	global $run;
	// hook lp_qzlog_banner_before.php
	$arr = list_sort_by($run->kv->xget('lk_banner'),'num');
	// hook lp_qzlog_banner_after.php
	return $arr;
}