<?php
/**
 * 搜索页模块 (比较占用资源，大站可使用sphinx做搜索引擎)
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int pagenum 每页显示条数
 * @param int titlelen 标题长度
 * @param int infolen 简介长度
 * @param string dateformat 时间格式
 * @param int maxcount 允许最大内容数(数据库搜索) 默认10000条
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_global_search($conf){
	global $run, $keyword;
	// hook lp_qzlog_global_search_before.php
	$pagenum = empty($conf['pagenum']) ? 20 : max(1, (int)$conf['pagenum']);
	$titlelen = isset($conf['titlelen']) ? (int)$conf['titlelen'] : 0;
	$infolen = isset($conf['infolen']) ? (int)$conf['infolen'] : 0;
	$dateformat = empty($conf['dateformat']) ? 'Y-m-d H:i:s' : $conf['dateformat'];
	$maxcount = isset($conf['maxcount']) ? (int)$conf['maxcount'] : 1000000;
	$life = empty($conf['life']) ? 0 : max(1, (int)$conf['life']);
	$where = array('title'=>array('LIKE'=>$keyword));
	//内容数大于100W的网站最好不要使用数据库搜索
	if($run->article->find_count() > $maxcount) return array('total'=> 0, 'pages'=> '', 'list'=> array());
	//初始分页
	$total = $run->article->find_count($where);
	$maxpage = max(1, ceil($total/$pagenum));
	$page = min($maxpage, max(1, intval(R('page'))));
	$pages = pages($page, $maxpage, 'index.php?search-index-keyword-'.urlencode($keyword).'-page-{page}'.C('url_suffix'));
	//读取内容列表
	$list_arr = $run->article->list_arr($where, 'nid', -1, ($page-1)*$pagenum, $pagenum, $total,$life);
	$category_urlname= $run->category->get_category_db();
	if($list_arr){
		foreach($list_arr as &$v){
			$run->article_content->format($v,$category_urlname[$v['cid']], $dateformat, $titlelen, $infolen);
			//$v['title'] = str_ireplace($keyword, '<font color="red">'.$keyword.'</font>', $v['title']);
			$v['description'] = str_ireplace($keyword, '<font color="red">'.$keyword.'</font>', $v['description']);
		}
	}
	// hook lp_qzlog_global_search_after.php
	return array('total'=> $total, 'pages'=> $pages, 'list'=> $list_arr);
}