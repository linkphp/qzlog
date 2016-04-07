<?php
/**
 * 分类调用模块
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int cid 分类ID
 * @param string type 显示类型  同级(sibling) 子级(child)
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_class($conf){
	global $run;
	// hook lp_qzlog_class_before.php
	$cid = intval($conf['cid']);
	$type = isset($conf['type']) && in_array($conf['type'], array('sibling', 'child')) ? $conf['type'] : 'child';
	$cate_arr = $run->category->get_all_ok_category();
	switch($type){
		case 'sibling':
			$pid = isset($cate_arr[$cid]) ? $cate_arr[$cid]['pid'] : 0;
			break;
		case 'child':
			$pid = $cid;
			break;
	}
	foreach($cate_arr as $k => &$v){
		if($v['pid'] != $pid){
			unset($cate_arr[$k]);
		}else{
			$v['url'] = $run->category->category_url($v['cid']);
			$v['son'] = $run->category->find_son($v['cid']);
		}
	}
	// hook lp_qzlog_class_after.php
	return $cate_arr;
}