<?php
/**
 * 导航模块 (最多支持两级)
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_nav($conf){
	global $run;
	$life = _int($conf, 'life');
	// hook lp_block_nav_before.php
	$navigate = $run->navigate->find_fetch('',array('sort'=>1),'','',$life);
	$category = $run->category->get_all_category();
	$cfg = $run->kv->xget('cfg',$life);
	$SITE_URL = $cfg['webdomain'].$cfg['webdir'];
	if($navigate){
		foreach($navigate as $key=>$val){
			if($val['cid'] != 0){
				$navigate[$key]['name'] = isset($category['category-cid-'.$val['cid']]['name'])?$category['category-cid-'.$val['cid']]['name']:'不存在';
				if(isset($category['category-cid-'.$val['cid']]['attribute'])){
					$category['category-cid-'.$val['cid']]['attribute'] == 3 ? $navigate[$key]['url'] = $category['category-cid-'.$val['cid']]['urlname'] : $navigate[$key]['url'] = $SITE_URL.$category['category-cid-'.$val['cid']]['urlname'];
				}else{
					$navigate[$key]['url'] ='javascript:;';
				}
				$navigate[$key]['attribute'] = isset($category['category-cid-'.$val['cid']]['attribute'])?$category['category-cid-'.$val['cid']]['attribute']:'不存在';
			}
		}
	}
	$nav_arr = data::channelLevel($navigate, 0, '','id', 'pid');
	// hook lp_block_nav_after.php
	return $nav_arr;
}