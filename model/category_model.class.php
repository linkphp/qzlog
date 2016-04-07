<?php
/**
 * 栏目分类模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class category extends model{
	// hook category_model_before.php
	function __construct(){
		$this->table = 'category';		// 表名
		$this->pri = array('cid');		// 主键
		$this->maxid = 'cid';			// 自增字段
	}

	//获取cfg值
	function __get($var){
		if($var == 'cfg'){
			return $this->cfg = $this->kv->xget();
		}else{
			return parent::__get($var);
		}
	}

	//获取所有栏目
	public function get_all_category(){
		return $this->find_fetch('',array('sort'=>1));
	}

    //获取所有列表栏目
    public function get_all_attribute_category(){
        return $this->find_fetch(array('attribute'=>2),array('sort'=>1));
    }

	//获取所有显示的栏目
	public function get_all_ok_category(){
		$arr = $this->find_fetch(array('status'=>1),array('sort'=>1));
		return $arr;
	}

	//查询是否存在下级分类
	public function super_class($cid){
		$rs = $this->find_fetch(array('pid'=>$cid));
		if($rs){
			return true;
		}else {
			return false;
		}
	}

	//获得格式化后的分类（包含当前位置）
	public function get_cate($cid){
		$k = 'cate_'.$cid;
		$arr = $this->runtime->xget($k);
		if(empty($arr)){
			$arr = $this->read($cid);
			$arr['place'] = $this->get_place($cid);
			$rs = $this->find_fetch(array('pid'=>$cid));
			if($rs){
				//$arr['son_cids'] = $rs;
				$arr['son_var_cid'] = array();
				foreach($rs as $c => $v){
					array_push($arr['son_var_cid'],$v['cid']);
				}
			}
			$this->runtime->set($k, $arr);
		}
		return $arr;
	}

	//获取当前位置
	public function get_place($cid){
		$p = array();
		$tmp = $this->get_all_ok_category();

		foreach($tmp as $v){
			$tmp[$v['cid']] = $v;
		}

		while(isset($tmp[$cid]) && $v = &$tmp[$cid]){
			array_unshift($p, array(
				'cid'=> $v['cid'],
				'name'=> $v['name'],
				'url'=> $this->category_url($v['cid'], $v['urlname'])
			));
			$cid = $v['pid'];
		}
		return $p;
	}

	// 更新分类缓存
	public function update_cache($cid){
		$k = 'cate_'.$cid;
		$arr = $this->read($cid);
		if(empty($arr)) return FALSE;
		$arr['place'] = $this->get_place($cid);
		$rs = $this->find_fetch(array('pid'=>$cid));
		if($rs){
			$arr['son_var_cid'] = array();
			foreach($rs as $c => $v) {
				array_push($arr['son_var_cid'],$v['cid']);
			}
		}
		$arr2 = $this->find_fetch(array('status'=>1),array('sort'=>1));
		$this->runtime->set($k, $arr);
		$this->runtime->set('cate_all_ok', $arr2);
		return true;
	}

	//判断是否是列表属性的栏目
	public function is_list($cid){
		$data = $this->read($cid);
		if($data['attribute'] == 2){
			return true;
		}else {
			return false;
		}
	}

	//从数据库获取分类
	public function get_category_db(){
		// hook category_model_get_category_db_before.php
		$newarr = array();
		$arr = $this->get_all_category();
		if($arr){
			foreach($arr as $v){
				$newarr[$v['cid']] = $v['urlname'];
			}
		}
		// hook category_model_get_category_db_after.php
		return $newarr;
	}

	//获取子分类 如果有子分类 就格式化
	public function find_son($cid){
		$data = $this->find_fetch(array('pid'=>$cid));
		// hook category_model_find_son_before.php
		if(!empty($data)){
			foreach($data as &$v){
				$v['url'] = $this->category->category_url($v['cid'],$v['urlname']);
			}
		}
		// hook category_model_find_son_after.php
		return $data;
	}

	//分类链接格式化
	public function category_url(&$cid,$urlname,$page = FALSE){
		// hook category_model_category_url_before.php
		if(empty($_ENV['_config']['qzlog_parseurl'])){
			return $this->cfg['webdir'].'index.php?category-index-cid-'.$cid.($page ? '-page-{page}' : '').C('url_suffix');
		}else{
			if($page){
				return $this->cfg['webdir'].$urlname.C('link_category_page_pre').'{page}'.C('url_suffix');
			}else{
				return $this->cfg['webdir'].$urlname.C('url_suffix');
			}
		}
	}
	// hook category_model_after.php
}