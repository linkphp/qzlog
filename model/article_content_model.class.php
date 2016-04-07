<?php
/**
 * 文章内容模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class article_content extends model{
	// hook article_content_model_begin.php
	function __construct(){
		// hook article_content_model_construct_begin.php
		$this->table = 'article_content';	// 表名
		$this->pri = array('id');			// 主键
		$this->maxid = 'id';				// 自增字段
	}

	//获取cfg值
	function __get($var){
		// hook article_content_model_get_begin.php
		if($var == 'cfg'){
			return $this->cfg = $this->kv->xget();
		}else{
			return parent::__get($var);
		}
	}

	//通过文章id获取文章内容
	function get_content_by_id($id){
		// hook article_content_model_get_content_by_id_begin.php
		$data = $this->find_fetch(array('nid'=>$id), array(), 0, 1);
		return $data ? current($data) : array();
	}

	//格式化内容数组
	public function format(&$v, $urlname, $dateformat = 'Y-m-d H:i:s', $titlenum = 0, $intronum = 0){
		if(!empty($v) && is_array($v)){
			// hook article_content_model_format_before.php
			$v['date'] = date($dateformat, $v['addtime']);
			$v['subtitle'] = $titlenum ? utf8::cutstr_cn($v['title'], $titlenum) : $v['title'];
			if(empty($_ENV['_config']['qzlog_parseurl'])){
				$v['url'] = $this->cfg['webdir'].'index.php?show-index-id-'.$v['nid'].C('url_suffix');;
			}else{
				$v['url'] = $this->cfg['webdir'].$urlname.'/'.$v['nid'].C('url_suffix');;
			}
			$v['tags'] = _json_decode($v['tags']);
			if($v['tags']){
				$v['tag_arr'] = array();
				foreach($v['tags'] as $name){
					$v['tag_arr'][] = array('name'=>$name, 'url'=> $this->article->tag_url($name));
				}
			}
			$intronum && $v['description'] = utf8::cutstr_cn($v['description'], $intronum);
			$v['thumb'] = empty($v['thumb']) ? 'static/images/nopic.gif' : $v['thumb'];
			// hook article_content_model_format_after.php
		}else{
			return FALSE;
		}
	}
	// hook article_content_model_after.php
}