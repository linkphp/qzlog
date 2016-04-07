<?php
/**
 * Tag标签模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class tag extends model{
	// hook tag_model_before.php
	function __construct(){
		$this->table = 'tag';			// 表名
		$this->pri = array('tagid');	// 主键
		$this->maxid = 'tagid';			// 自增字段
	}

	//标签关联删除 (需要删除三个表: tag tag_relation article)
	public function xdelete($tagid){
		// 删除 article 表的内容
		try{
			// 如果内容数太大，会删除失败。（这时程序需要改进做分批删除设计）
			$list_arr = $this->tag_relation->find_fetch(array('tagid'=>$tagid));
			foreach($list_arr as $v){
				$data = $this->article->read($v['id']);
				if(empty($data)) return '读取文档表出错';
				$row = _json_decode($data['tags']);
				unset($row[$tagid]);
				$data['tags'] = _json_encode($row);
				if(!$this->article->update($data)) return '写入文档表出错';
			}
		}catch(Exception $e){
			return '修改文档表出错';
		}
		// 删除 tag_relation 表的内容
		try{
			$this->tag_relation->find_delete(array('tagid'=>$tagid));
		}catch(Exception $e){
			return '删除标签关系表出错';
		}
		return $this->delete($tagid) ? '' : '删除失败';
	}
	// hook tag_model_after.php
}