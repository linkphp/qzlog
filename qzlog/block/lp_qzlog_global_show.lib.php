<?php
/**
 * 内容页模块
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param string dateformat 时间格式
 * @param int show_prev_next 显示上下篇
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_global_show($conf){
	global $run, $_show;
	// hook lp_qzlog_global_show_before.php
	$dateformat = empty($conf['dateformat']) ? 'Y-m-d H:i:s' : $conf['dateformat'];
	$show_prev_next = isset($conf['show_prev_next']) && (int)$conf['show_prev_next'] ? true : false;
	$urlname = $_show['urlname'];
	$run->article_content->format($_show,$urlname,$dateformat);
	$id = &$_show['nid'];
	$data = $run->article_content->get_content_by_id($id);
	$data_attachment = $run->article_attachment->get_attachment_by_id($id);
	if($data) $_show += $data;
	if($data_attachment) $_show['images'] = $data_attachment;
	if($show_prev_next){
		//上一篇
		$_show['prev'] = $run->article->find_fetch(array('cid' => $run->_var['cid'], 'nid'=>array('<'=> $id)), array('nid'=>-1), 0 , 1);
		$_show['prev'] = $_show['prev'] && current($_show['prev']);
		$run->article_content->format($_show['prev'],$urlname, $dateformat);
		//下一篇
		$_show['next'] = $run->article->find_fetch(array('cid' => $run->_var['cid'], 'nid'=>array('>'=> $id)), array('nid'=>1), 0 , 1);
		$_show['next'] = $_show['next'] && current($_show['next']);
		$run->article_content->format($_show['next'],$urlname, $dateformat);
	}
	// hook lp_qzlog_global_show_after.php
	return $_show;
}