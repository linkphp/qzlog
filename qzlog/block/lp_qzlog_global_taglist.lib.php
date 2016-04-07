<?php
/**
 * tag标签列表页模块
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int pagenum 每页显示条数
 * @param int titlenum 标题长度
 * @param int intronum 简介长度
 * @param string dateformat 时间格式
 * @param int orderway 降序(-1),升序(1)
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_global_taglist($conf){
    global $run, $tags;
    // hook lp_qzlog_global_taglist_before.php
    $pagesize = empty($conf['pagesize']) ? 20 : max(1, (int)$conf['pagesize']);
    $titlelen = isset($conf['titlelen']) ? (int)$conf['titlelen'] : 0;
    $infolen = isset($conf['infolen']) ? (int)$conf['infolen'] : 0;
    $dateformat = empty($conf['dateformat']) ? 'Y-m-d H:i:s' : $conf['dateformat'];
    $orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
    $life = empty($conf['life']) ? 0 : max(1, (int)$conf['life']);
    $tagid = $tags['tagid'];
    $total = $tags['count'];
    $maxpage = max(1, ceil($total/$pagesize));
    $page = min($maxpage, max(1, intval(R('page'))));
    $pages = pages($page, $maxpage, $run->article->tag_url($tags['name'], TRUE));
    $tag_arr = $run->tag_relation->list_arr($tagid, $orderway, ($page-1)*$pagesize, $pagesize, $total,$life);
    $keys = array();
    foreach($tag_arr as $v){
        $keys[] = $v['id'];
    }
	$category_urlname= $run->category->get_category_db();
    $list_arr = $run->article->mget($keys);
    foreach($list_arr as &$v){
        $run->article_content->format($v,$category_urlname[$v['cid']],$dateformat, $titlelen, $infolen);
    }
    // hook lp_qzlog_global_taglist_after.php
    return array('total'=> $total, 'pages'=> $pages, 'list'=>$list_arr);
}