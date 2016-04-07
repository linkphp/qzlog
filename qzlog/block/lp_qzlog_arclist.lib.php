<?php
/**
 * 内容列表标签
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int cid 分类ID 支持多个,英文逗号隔开
 * @param int flag 属性ID (默认为0) [1=热门 2=置顶 3=推荐 4=图片 5=精华 6=幻灯]
 * @param string dateformat 时间格式
 * @param int titlelen 标题长度
 * @param int infolen 简介长度
 * @param string orderby 排序方式
 * @param int orderway 降序(-1),升序(1)
 * @param int start 开始位置
 * @param int limit 显示几条
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_arclist($conf){
	global $run;
	// hook lp_qzlog_arclist_before.php
	$flag = _int($conf, 'flag');
	$cid = isset($conf['cid']) ? $conf['cid'] : 0;
	if($cid != 0){
		if(strstr($cid,",")){ //判断是否是多个cid
			$cid = explode(',',$cid); //多个切割成数组
		}else{
			$cid = (int)$cid;
		}
	}
	$dateformat = empty($conf['dateformat']) ? 'Y-m-d H:i:s' : $conf['dateformat'];
	$titlelen = _int($conf, 'titlelen');
	$infolen = _int($conf, 'infolen');
	$orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
	$start = _int($conf, 'start');
	$limit = _int($conf, 'limit', 10);
	$life = empty($conf['life']) ? 0 : max(1, (int)$conf['life']);
	if($cid == 0){
		//没有填写cid
		$cate_name = 'No Title';
		$cate_url = 'javascript:;';
		isset($flag) && !empty($flag) ? $where = array('flag' => $flag) : $where = array();
	}else{
		if(!is_array($cid)){
			//如果是单个且不为0的cid 调用出当前栏目的名字和URL
			$cate_arr = $run->category->get_cate($cid);
			if(empty($cate_arr)) return;
			$cate_name = !empty($cate_arr['name']) ? $cate_arr['name'] : null;
			$cate_urlname = !empty($cate_arr['urlname']) ? $cate_arr['urlname'] : null;
			$cate_url = $run->category->category_url($cid,$cate_urlname);
			if(isset($cate_arr['son_var_cid']) && is_array($cate_arr['son_var_cid']) && !empty($cate_arr['son_var_cid'])){
				array_push($cate_arr['son_var_cid'],$cid);
			}
		}else{
			//多个cid
			$cate_arr = array();
			$cate_name = 'No Title';
			$cate_url = 'javascript:;';
			$cate_arr['son_var_cid'] = array();
			foreach($cid as $key => $value){
				$rs = $run->category->find_fetch(array('pid'=>$value),'','','',$life);
				if($rs){
					foreach($rs as $c => $v){
						array_push($cate_arr['son_var_cid'],$v['cid']);
					}
				}
			}
			$cate_arr['son_var_cid'] = array_merge($cate_arr['son_var_cid'],$cid);
		}
		//拼接where语句
		if(isset($cate_arr['son_var_cid']) && !empty($cate_arr['son_var_cid'])){
			isset($flag) && !empty($flag) ? $where = array('flag' => $flag,'cid' => array("IN" => $cate_arr['son_var_cid'])) : $where = array('cid' => array("IN" => $cate_arr['son_var_cid']));
		}else{
			isset($flag) && !empty($flag) ? $where = array('flag' => $flag,'cid' => $cid) : $where = array('cid' => $cid);
		}
		$list_arr = $run->article->find_fetch($where, array('nid' => $orderway), $start, $limit,$life);
		$category_urlname= $run->category->get_category_db();
		if($list_arr){
			foreach($list_arr as &$v){
				$run->article_content->format($v, $category_urlname[$v['cid']],$dateformat, $titlelen, $infolen,$life);
			}
		}
		// hook lp_qzlog_arclist_after.php
		return array('cate_name'=> $cate_name, 'cate_url'=> $cate_url, 'list'=> $list_arr);
	}
}