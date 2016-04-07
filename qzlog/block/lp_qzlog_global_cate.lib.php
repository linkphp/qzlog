<?php
/**
 * 列表页模块
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int pagenum 每页显示条数 默认20
 * @param int titlelen
 * @param int infolen 简介长度
 * @param string dateformat 时间格式
 * @param string orderby 排序方式
 * @param int orderway 降序(-1),升序(1)
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_global_cate($conf){
	global $run;
	// hook lp_qzlog_global_cate_before.php
	$pagenum = empty($conf['pagenum']) ? 20 : max(1, (int)$conf['pagenum']);
	$titlelen = isset($conf['titlelen']) ? (int)$conf['titlelen'] : 0;
	$infolen = isset($conf['infolen']) ? (int)$conf['infolen'] : 0;
	$dateformat = empty($conf['dateformat']) ? 'Y-m-d H:i:s' : $conf['dateformat'];
	$orderby = isset($conf['orderby']) && in_array($conf['orderby'], array('nid', 'addtime')) ? $conf['orderby'] : 'nid';
	$orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
	$life = empty($conf['life']) ? 0 : max(1, (int)$conf['life']);
	$cid = &$run->_var['cid'];
	$where = array('cid' => $cid);
	$total = &$run->_var['count'];
	$maxpage = max(1, ceil($total/$pagenum));
	$page = min($maxpage, max(1, intval(R('page'))));
	$pages = pages($page, $maxpage, $run->category->category_url($cid,$run->_var['urlname'],TRUE));
	$list_arr = $run->article->list_arr($where, $orderby, $orderway, ($page-1)*$pagenum, $pagenum, $total,$life);
	if($list_arr){
		foreach($list_arr as &$v){
			$run->article_content->format($v,$run->_var['urlname'],$dateformat, $titlelen, $infolen,$life);
			$v['cate_name'] = &$run->_var['name'];
			$v['cate_url'] = $run->category->category_url($cid,$run->_var['urlname'],$run->_var['urlname']);
		}
	}
	// hook lp_qzlog_global_cate_after.php
	return array('total'=> $total, 'pages'=> $pages, 'list'=> $list_arr);
}