<?php
/**
 * 文章附件模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class article_attachment extends model{
	// hook article_attachment_model_begin.php
	function __construct(){
		// hook article_attachment_model_construct_begin.php
		$this->table = 'article_attachment';		// 表名
		$this->pri = array('id');					// 主键
		$this->maxid = 'id';						// 自增字段
	}

	//通过文档id获取图集内容
	function get_attachment_by_id($id){
		// hook article_attachment_model_get_attachment_by_id_begin.php
		return $this->find_fetch(array('nid'=>$id));
	}

	//通过文档id获取图集内容 并格式化
	public function get_tuji_attach($id){
		// hook article_attachment_model_get_tuji_attach_begin.php
		$data = $this->find_fetch(array('nid'=>$id));
		return $data ? current($data) : array();
	}
	// hook article_attachment_model_after.php
}