<?php
/**
 * 	数据库缓存模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') or exit;

class runtime extends model{
	private $data = array();			// 保证唯一性
	private $changed = array();			// 表示修改过的key
	// hook runtime_model_before.php
	function __construct(){
		$this->table = 'runtime';	// 表名
		$this->pri = array('k');	// 主键
		// hook runtime_model_construct_after.php
	}

	//读取缓存
	public function get($k, $life = 0){
		$arr = parent::get($k, $life);
		return !empty($arr) && (empty($arr['expiry']) || $arr['expiry'] > $_ENV['_time']) ? _json_decode($arr['v']) : array();
	}

	//写入缓存
	public function set($k, $s, $life = 0){
		$s = json_encode($s);
		$arr = array();
		$arr['k'] = $k;
		$arr['v'] = $s;
		$arr['expiry'] = $life ? $_ENV['_time'] + $life : 0;
		return parent::set($k, $arr);
	}

	//读取
	public function xget($key = 'cfg'){
		if(!isset($this->data[$key])){
			$this->data[$key] = $this->get($key);
		}
		return $this->data[$key];
	}

	//修改
	public function xset($k, $v, $key = 'cfg'){
		if(!isset($this->data[$key])){
			$this->data[$key] = $this->get($key);
		}
		if($v && is_string($v) && ($v[0] == '+' || $v[0] == '-')){
			$v = intval($v);
			$this->data[$key][$k] += $v;
		}else{
			$this->data[$key][$k] = $v;
		}
		$this->changed[$key] = 1;
	}

	//保存
	public function xsave($key = 'cfg'){
		$this->set($key, $this->data[$key]);
		$this->changed[$key] = 0;
	}

	//保存所有修改过的key
	public function save_changed(){
		foreach($this->changed as $key=>$v){
			$v && $this->xsave($key);
		}
	}
	// hook runtime_model_after.php
}