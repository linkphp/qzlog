<?php

defined('LINK_PATH') || exit;
class cache_file implements cache_interface{
	private $conf;
	public $pre;	//缓存前缀 （防止同一台缓存服务器，有多套程序，键名冲突问题）

	public function __construct(&$conf){
		$this->conf = &$conf;
		$this->pre = $conf['pre'];
		$this->temp = !empty($conf['temp']) ? $conf['temp'] : RUNTIME_PATH.'temp/';
		$this->init();
	}

	/**
	 * 初始化检查
	 */
	private function init(){
		!is_dir($this->temp) && mkdir($this->temp);
	}

	/**
	 * 获取key对应存储文件名称
	 * @param string $key 缓存变量名
	 * @return string
	 */
	private function filename($key){

		return $this->temp.$this->pre.$key.'.php';
	}

	/**
	 * 读取一条数据
	 * @param string $key	键名
	 * @return array
	 */
	public function get($key){
		$filename = $this->filename($key);
		$data = array();
		if(is_file($filename)){
			$_data = file_get_contents($filename);
			$life = (int)substr($_data,8, 12);
			if($life != 0 && time() <= filemtime($filename) + $life)
				$data = unserialize(substr($_data,20, -3));
		}
		return $data;
	}

	/**
	 * 读取多条数据
	 * @param array $keys	键名数组
	 * @return array
	 */
	public function multi_get($keys){
		$data = array();
		if(!empty($keys)){
			foreach($keys as $k) {
				if(is_file($this->filename($k))){
					$tmp_data = $this->get($k);
					$data[$k] = !empty($tmp_data) ? $tmp_data : false;
				}else{
					$data[$k] = false;
				}
			}
		}
		return $data;
	}

	/**
	 * 写入一条数据
	 * @param string $key	键名
	 * @param array $data	数据
	 * @param int  $life	缓存时间 (默认为永久)
	 * @return bool
	 */
	public function set($key, $data, $life = 0){

		$filename = $this->filename($key);
		$data = serialize($data);
		$data = "<?php\n//".sprintf('%012d',$life).$data."\n?>";
		$result  =  file_put_contents($filename,$data);
		return $result;
	}

	/**
	 * 更新一条数据
	 * @param string $key	键名
	 * @param array $data	数据
	 * @param int  $life	缓存时间 (默认为永久)
	 * @return bool
	 */
	public function update($key, $data, $life = 0){
		$arr = $this->get($key);
		if($arr !== FALSE) {
			is_array($arr) && is_array($data) && $arr = array_merge($arr, $data);
			return $this->set($key, $arr, $life);
		}
		return FALSE;
	}

	/**
	 * 删除一条数据
	 * @param string $key	键名
	 * @return bool
	 */
	public function delete($key){

		$filename = $this->filename($key);
		if(is_file($filename)){
			return unlink($filename);
		}
	}

	/**
	 * 获取/设置最大ID
	 * @param string $table	表名
	 * @param boot/int $val	值	（为 FALSE 时为获取）
	 * @return int
	 */
	public function maxid($table, $val = FALSE){
		$key = $table.'-Auto_increment';
		if($val === FALSE) {
			return intval($this->get($key));
		}else{
			 $this->set($key, $val);
			 return $val;
		}
	}

	/**
	 * 获取/设置总条数
	 * @param string $table	表名
	 * @param boot/int $val	值	（为 FALSE 时为获取）
	 * @return int
	 */
	public function count($table, $val = FALSE){
		$key = $table.'-Rows';
		if($val === FALSE) {
			return intval($this->get($key));
		}else{
			$this->set($key, $val);
			return $val;
		}
	}

	/**
	 * 清空缓存
	 * @param string $pre	前缀
	 * @return boot
	 */
	public function truncate($pre = ''){
		$files = _scandir($this->temp);
		if($files){
			foreach($files as $file){
				if ($file != '.' && $file != '..' && is_dir($this->temp.$file)){
					array_map( 'unlink', glob( $this->temp.$file.'/*.*' ) );
				}elseif(is_file($this->temp.$file)){
					unlink($this->temp.$file );
				}
			}
		}
	}

	/**
	 * 读取一条二级缓存
	 * @param string $l2_key	二级缓存键名
	 * @return boot
	 */
	public function l2_cache_get($l2_key){

		$result = $this->get($l2_key);
		return !empty($result) ? $result : false;
	}

	/**
	 * 写入一条二级缓存
	 * @param string $l2_key	二级缓存键名
	 * @param string $keys		键名数组
	 * @return boot
	 */
	public function l2_cache_set($l2_key, $keys, $life = 0){
		return $this->set($l2_key, $keys, $life);
	}
}
?>
