<?php
/**
 * 类似博客列表（带分页）
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int pagenum 每页显示条数
 * @param int titlelen 标题长度
 * @param int infolen 简介长度
 * @param string dateformat 时间格式
 * @param string orderby 排序方式
 * @param int orderway 降序(-1),升序(1)
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_global_blog($conf){
	global $run;
	// hook lp_qzlog_global_blog_before.php
	$pagenum = empty($conf['pagenum']) ? 20 : max(1, (int)$conf['pagenum']);
	$titlelen = isset($conf['titlelen']) ? (int)$conf['titlelen'] : 0;
	$infolen = isset($conf['infolen']) ? (int)$conf['infolen'] : 0;
	$dateformat = empty($conf['dateformat']) ? 'Y-m-d H:i:s' : $conf['dateformat'];
	$orderby = isset($conf['orderby']) && in_array($conf['orderby'], array('nid', 'dateline')) ? $conf['orderby'] : 'nid';
	$orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
	$life = empty($conf['life']) ? 0 : max(1, (int)$conf['life']);
	$total = $run->article->find_count();
	//分页相关
	$maxpage = max(1, ceil($total/$pagenum));
	$page = min($maxpage, max(1, intval(R('page'))));
	$pages = pages($page, $maxpage, $run->article->index_url());
	//读取内容列表
	$list_arr = $run->article->list_arr(array(), 'nid', -1, ($page-1)*$pagenum, $pagenum, $total,$life);
	$category_urlname= $run->category->get_category_db();
	if($list_arr){
		foreach($list_arr as &$v){
			$run->article_content->format($v,$category_urlname[$v['cid']],$dateformat, $titlelen, $infolen,$life);
			$cate_arr = $run->category->read($v['cid']);
			$v['cate_name'] = $cate_arr['name'];
			$v['cate_url'] = $run->category->category_url($v['cid'],$cate_arr['urlname']);
		}
	}
	// hook lp_qzlog_global_blog.php
	return array('total'=> $total, 'pages'=> $pages, 'list'=> $list_arr);
}