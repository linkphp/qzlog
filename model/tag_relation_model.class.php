<?php
/**
 * TAG标签关系表
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class tag_relation extends model{
	// hook tag_relation_model_before.php
	function __construct(){
		$this->table = 'tag_relation';		// 表名
		$this->pri = array('tagid', 'id');	// 主键
	}

	//获取标签列表
	public function list_arr($tagid, $orderway, $start, $limit, $total,$life){
		// 优化大数据量翻页
		if($start > 1000 && $total > 2000 && $start > $total/2){
			$orderway = -$orderway;
			$newstart = $total-$start-$limit;
			if($newstart < 0){
				$limit += $newstart;
				$newstart = 0;
			}
			$list_arr = $this->find_fetch(array('tagid' => $tagid), array('id' => $orderway), $newstart, $limit ,$life);
			return array_reverse($list_arr, TRUE);
		}else{
			return $this->find_fetch(array('tagid' => $tagid), array('id' => $orderway), $start, $limit ,$life);
		}
	}
	// hook tag_relation_model_after.php
}