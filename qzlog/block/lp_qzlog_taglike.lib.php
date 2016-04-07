<?php
/**
 * 相关内容模块 (只能用于内容页)
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int titlelen 标题长度
 * @param int infolen 简介长度
 * @param string dateformat 时间格式
 * @param int type 相关内容类型 (1为显示第一个tag相关内容，2为随机显示一个tag相关内容)
 * @param int orderway 降序(-1),升序(1)
 * @param int start 开始位置
 * @param int limit 显示几条
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_taglike($conf){
	global $run, $_show;
	// hook lp_qzlog_taglike_before.php
	if(empty($_show['tags'])) return array('list'=> array());
	$titlelen = isset($conf['titlelen']) ? (int)$conf['titlelen'] : 0;
	$infolen = isset($conf['infolen']) ? (int)$conf['infolen'] : 0;
	$dateformat = empty($conf['dateformat']) ? 'Y-m-d H:i:s' : $conf['dateformat'];
	$type = max(1, _int($conf, 'type'));
	$orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
	$start = _int($conf, 'start');
	$limit = _int($conf, 'limit', 10);
	$life = empty($conf['life']) ? 0 : max(1, (int)$conf['life']);
	if($type == 2){
		$tagid = array_rand($_show['tags']);
	}else{
		$tagid = key($_show['tags']);
	}
	$tag_arr = $run->tag_relation->find_fetch(array('tagid'=>$tagid), array('id'=>$orderway), $start, $limit,$life);
	$keys = array();
	foreach($tag_arr as $v){
		$keys[] = $v['id'];
	}
	$list_arr = $run->article->mget($keys);
	$category_urlname= $run->category->get_category_db();
	foreach($list_arr as &$v){
		$run->article_content->format($v, $category_urlname[$v['cid']],$dateformat, $titlelen, $infolen);
	}
	// hook lp_qzlog_taglike_after.php
	return array('list'=> $list_arr);
}