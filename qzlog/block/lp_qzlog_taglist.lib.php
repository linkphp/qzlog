<?php
/**
 * TAG列表标签
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param string orderby 排序方式 (参数有 tagid count)
 * @param int orderway 降序(-1),升序(1)
 * @param int limit 显示几条标签
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_taglist($conf){
	global $run;
	// hook lp_qzlog_taglist_before.php
	$orderby = isset($conf['orderby']) && in_array($conf['orderby'], array('tagid', 'count')) ? $conf['orderby'] : 'count';
	$orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
	$limit = isset($conf['limit']) ? (int)$conf['limit'] : 10;
	$life = empty($conf['life']) ? 0 : max(1, (int)$conf['life']);
	$list_arr = $run->tag->find_fetch(array(), array($orderby => $orderway), 0, $limit,$life);
	foreach($list_arr as &$v){
		$v['url'] = $run->article->tag_url($v['name']);
	}
	// hook lp_qzlog_taglist_after.php
	return array('list'=>$list_arr);
}