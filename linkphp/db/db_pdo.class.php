<?php
/**
 * pdo驱动
 * @author: caisonglin <612012@qq.com>
 */
defined('LINK_PATH') || exit;
class db_pdo implements db_interface{

	private $conf = array(); // 配置，可以支持主从
	public $tablepre;	// 数据表前缀
	private $wlink = NULL; //写连接
	private $rlink = NULL; //读连接
	private $link = NULL; //最后一次使用的连接
	private $pconnect = FALSE; //是否开启长连接

	public function __construct(&$conf) {
		$this->conf = &$conf;
		$this->tablepre = $conf['master']['tablepre'];
		$this->content();
	}

	// 根据配置文件连接
	public function content(){
		$this->wlink = $this->connect_master();
		$this->rlink = $this->connect_slave();
	}

	// 连接主(写)服务器
	public function connect_master(){
		if($this->wlink) return $this->wlink;
		$conf = $this->conf['master'];
		$this->wlink = $this->real_connect($conf['host'], $conf['user'], $conf['password'], $conf['name'], $conf['charset'], $conf['engine']);
		return $this->wlink;
	}

	// 连接从服务器
	public function connect_slave(){
		if($this->rlink) return $this->rlink;
		if(empty($this->conf['slaves'])){
			if(!$this->wlink) $this->wlink = $this->connect_master();
			$this->rlink = $this->wlink;
		}else{
			$n = array_rand($this->conf['slaves']);
			$conf = $this->conf['slaves'][$n];
			$this->rlink = $this->real_connect($conf['host'], $conf['user'], $conf['password'], $conf['name'], $conf['charset'], $conf['engine']);
		}
		return $this->rlink;
	}


	public function real_connect($host, $user, $password, $name, $charset = 'utf8', $engine = ''){
		$params = array();
		if(strpos($host, ':') !== FALSE){
			list($host, $port) = explode(':', $host);
		}else{
			$port = 3306;
		}

		if($this->pconnect) $params[PDO::ATTR_PERSISTENT] = TRUE;
		if(version_compare(PHP_VERSION,'5.3.6','<=')){
			//禁用模拟预处理语句
			$params[PDO::ATTR_EMULATE_PREPARES] = FALSE;
		}

		try{
			$link = new PDO("mysql:host=$host;dbname=$name;port=$port",$user,$password,$params);
		}catch(Exception $e){
			$this->error(-10000, '连接数据库服务器失败:'.$e->getMessage());
		}

		$charset && $link->query("SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary, sql_mode=''");
		return $link;
	}

	public function query($sql){
		if(!$this->rlink && !$this->connect_slave()) return FALSE;
		$link = $this->link = $this->rlink;
		$query = $link->query($sql);
		if($query === FALSE) $this->error();
		//if(count($this->sqls) < 1000) $this->sqls[] = $sql;
		return $query;
	}

	public function exec($sql){
		if(!$this->wlink && !$this->connect_master()) return FALSE;
		$link = $this->link = $this->wlink;
		$n = $link->exec($sql); // 返回受到影响的行，插入的 id ?
		if($n !== FALSE) {
			$pre = strtoupper(substr(trim($sql), 0, 7));
			if($pre == 'INSERT ' || $pre == 'REPLACE') {
				return $this->last_insert_id();
			}
		} else {
			$this->error();
		}
		//if(count($this->sqls) < 1000) $this->sqls[] = $sql;
		return $n;
	}

	public function last_insert_id() {
		return $this->wlink->lastinsertid();
	}

	public function error($errno = 0, $errstr = ''){
		$error = $this->link ? $this->link->errorInfo() : array(0, $errno, $errstr);
		$errno = $errno ? $errno : (isset($error[1]) ? $error[1] : 0);
		$errstr = $errstr ? $errstr : (isset($error[2]) ? $error[2] : '');
		throw new Exception($errno.':'.$errstr);
		return FALSE;
	}

	public function __destruct() {
		if($this->wlink) $this->wlink = NULL;
		if($this->rlink) $this->rlink = NULL;
	}

	public function get($key){
		list($table, $keyarr, $keystr) = $this->key2arr($key);
		$sql = "SELECT * FROM {$this->tablepre}$table WHERE $keystr LIMIT 1";
		$query = $this->query($sql);
		$query->setFetchMode(PDO::FETCH_ASSOC);
		return $query->fetch();
	}

	public function multi_get($keys){
		$where = '';
		$ret = array();
		foreach($keys as $k) {
			$ret[$k] = array();	// 按原来的顺序赋值，避免后面的 OR 条件取出时顺序混乱
			list($table, $keyarr, $keystr) = $this->key2arr($k);
			$where .= "$keystr OR ";
		}
		$where = substr($where, 0, -4);
		if($where){
			$sql = "SELECT * FROM {$this->tablepre}$table WHERE $where";
			$query = $this->query($sql);
			$query->setFetchMode(PDO::FETCH_ASSOC);
			while($row = $query->fetch()) {
				$keyname = $table;
				foreach($keyarr as $k=>$v) {
					$keyname .= "-$k-".$row[$k];
				}
				$ret[$keyname] = $row;
			}
			return $ret;
		}
	}

	public function set($key, $data){
		if(!is_array($data)) return FALSE;
		list($table, $keyarr) = $this->key2arr($key);
		$data += $keyarr;
		$s = $this->arr2sql($data);
		$exists = $this->get($key);
		if(empty($exists)) {
			return $this->exec("INSERT INTO {$this->tablepre}$table SET $s");
		}else{
			return $this->update($key, $data);
		}
	}

	public function update($key, $data){
		list($table, $keyarr, $keystr) = $this->key2arr($key);
		$s = $this->arr2sql($data);
		$sql = "UPDATE {$this->tablepre}$table SET $s WHERE $keystr LIMIT 1";
		return $this->exec($sql);
	}

	public function delete($key){
		list($table, $keyarr, $keystr) = $this->key2arr($key);
		return $this->exec("DELETE FROM {$this->tablepre}$table WHERE $keystr LIMIT 1");
	}

	public function maxid($key, $val = FALSE){
		$maxid = $this->table_maxid($key);
		list($table, $col) = explode('-', $key);
		if($val === FALSE) {
			return $maxid;
		}elseif(is_string($val)) {
			$val = max(0, $maxid + intval($val));
		}
		$this->exec("UPDATE {$this->tablepre}framework_maxid SET maxid='$val' WHERE name='$table' LIMIT 1");
		return $val;
	}

	/**
	 * 读取表最大ID (如果不存在自动创建表和设置最大ID)
	 * @param string $key	键名 只能是表名+一个字段 如：'user-uid'(uid一般为主键)
	 * @return int
	 */
	public function table_maxid($key) {
		list($table, $col) = explode('-', $key);
		$maxid = FALSE;
		if(!$this->rlink && !$this->connect_slave()) return FALSE;
		$this->link = $this->rlink;
		$query = $this->link->query("SELECT maxid FROM {$this->tablepre}framework_maxid WHERE name='$table' LIMIT 1");
		if($query) {
			$maxid = $query->fetchColumn();
		}elseif($this->link->errorCode() == '42S02') {
			$sql = "CREATE TABLE `{$this->tablepre}framework_maxid` (";
			$sql .= "`name` char(32) NOT NULL default '',";
			$sql .= "`maxid` int(10) unsigned NOT NULL default '0',";
			$sql .= "PRIMARY KEY (`name`)";
			$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
			$this->exec($sql);
		}else{
			$this->error();
		}
		if($maxid === FALSE) {
			$query = $this->query("SELECT MAX($col) FROM {$this->tablepre}$table");
			$maxid = $query->fetchColumn();
			$this->exec("INSERT INTO {$this->tablepre}framework_maxid SET name='$table', maxid='$maxid'");
		}
		return $maxid;
	}

	public function count($table, $val = FALSE){
		$count = $this->table_count($table);
		if($val === FALSE) {
			return $count;
		}elseif(is_string($val)) {
			if($val[0] == '+') {
				$val = $count + intval($val);
			}elseif($val[0] == '-') {
				$val = max(0, $count + intval($val));
			}
		}
		$this->exec("UPDATE {$this->tablepre}framework_count SET count='$val' WHERE name='$table' LIMIT 1");
		return $val;
	}

	/**
	 * 读取表的总行数 (如果不存在自动创建表和设置总行数)
	 * @param string $table	表名
	 * @return int
	 */
	public function table_count($table) {
		$count = FALSE;
		if(!$this->rlink && !$this->connect_slave()) return FALSE;
		$this->link = $this->rlink;
		$query = $this->link->query("SELECT count FROM {$this->tablepre}framework_count WHERE name='$table' LIMIT 1");

		if($query) {
			$count = $query->fetchColumn();
		}elseif($this->link->errorCode() == '42S02') {
			$sql = "CREATE TABLE {$this->tablepre}framework_count (";
			$sql .= "`name` char(32) NOT NULL default '',";
			$sql .= "`count` int(10) unsigned NOT NULL default '0',";
			$sql .= "PRIMARY KEY (`name`)";
			$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
			$this->exec($sql);
		}else{
			$this->error();
		}
		if($count === FALSE) {
			$query = $this->query("SELECT COUNT(*) FROM {$this->tablepre}$table");
			$count = $query->fetchColumn();
			$this->exec("INSERT INTO {$this->tablepre}framework_count SET name='$table', count='$count'");
		}
		return $count;
	}

	public function truncate($table){
		try {
			if($this->exec("TRUNCATE {$this->tablepre}$table"))
				return TRUE;
		} catch(Exception $e) {
			$this->error();
		}

	}

	public function version(){
		$query = $this->query("SELECT VERSION() AS v");
		$query->setFetchMode(PDO::FETCH_ASSOC);
		$r = $query->fetch();
		return $r['v'];
	}

	public function find_fetch($table, $pri, $where = array(), $order = array(), $start = 0, $limit = 0){

	}

	public function find_fetch_key($table, $pri, $where = array(), $order = array(), $start = 0, $limit = 0){
		$pris = implode(',', $pri);
		$s = "SELECT $pris FROM {$this->tablepre}$table";
		$s .= $this->arr2where($where);
		if(!empty($order)) {
			$s .= ' ORDER BY ';
			$comma = '';
			foreach($order as $k=>$v) {
				$s .= $comma."$k ".($v == 1 ? ' ASC ' : ' DESC ');
				$comma = ',';
			}
		}
		$s .= ($limit ? " LIMIT $start,$limit" : '');
		$ret = array();
		$query = $this->query($s);
		$query->setFetchMode(PDO::FETCH_ASSOC);
		while($row = $query->fetch()) {
			$keystr = '';
			foreach($pri as $k) {
				$keystr .= "-$k-".$row[$k];
			}
			$ret[] = $table.$keystr;
		}
		return $ret;
	}

	public function find_update($table, $where, $data, $lowprority = FALSE){

		$where = $this->arr2where($where);
		$data = $this->arr2sql($data);
		$lpy = $lowprority ? 'LOW_PRIORITY' : '';
		return $this->exec("UPDATE $lpy {$this->tablepre}$table SET $data $where");
	}

	public function find_delete($table, $where, $lowprority = FALSE){

		$where = $this->arr2where($where);
		$lpy = $lowprority ? 'LOW_PRIORITY' : '';
		return $this->exec("DELETE $lpy FROM {$this->tablepre}$table $where");
	}

	public function find_maxid($key){

		list($table, $maxid) = explode('-', $key);
		$query = $this->query("SELECT MAX($maxid) AS num FROM {$this->tablepre}$table");
		return $query->fetchColumn();
	}

	public function find_count($table, $where = array()){
		$where = $this->arr2where($where);
		$query = $this->query("SELECT COUNT(*) FROM {$this->tablepre}$table $where");
		return $query->fetchColumn();
	}

	/**
	 * 创建索引
	 * @param string $table	表名
	 * @param array $index	键名数组	// array('uid'=>1, 'dateline'=>-1, 'unique'=>TRUE, 'dropDups'=>TRUE) 为了配合 mongodb 的索引才这样设计的
	 * @return boot
	 */
	public function index_create($table, $index){

		$keys = implode(',', array_keys($index));
		$keyname = implode('_', array_keys($index));
		return $this->exec("ALTER TABLE {$this->tablepre}$table ADD INDEX $keyname($keys)");
	}

	/**
	 * 删除索引
	 * @param string $table	表名
	 * @param array $index	键名数组
	 * @return boot
	 */
	public function index_drop($table, $index){

		$keys = implode(',', array_keys($index));
		$keyname = implode('_', array_keys($index));
		return $this->exec("ALTER TABLE {$this->tablepre}$table DROP INDEX $keyname");
	}

	/**
	 * 将键名转换为数组
	 * @param string $key	键名
	 * @return array
	 * in: article-cid-1-aid-2
	 * out: array('article', array('cid'=>1, 'aid'=>2), 'cid=1 AND aid=2')
	 */
	private function key2arr($key) {
		$arr = explode('-', $key);
		if(empty($arr[0])) throw new Exception('table name is empty.');

		$table = $arr[0];
		$keyarr = array();
		$keystr = '';
		$len = count($arr);
		for($i = 1; $i < $len; $i = $i + 2) {
			if(isset($arr[$i + 1])) {
				$v = $arr[$i + 1];
				$keyarr[$arr[$i]] = is_numeric($v) ? intval($v) : $v;	// 因为 mongodb 区分数字和字符串
				$keystr .= ($keystr ? ' AND ' : '').$arr[$i]."='".addslashes($v)."'";
			} else {
				$keyarr[$arr[$i]] = NULL;
			}
		}
		if(empty($keystr)) throw new Exception('keystr name is empty.');

		return array($table, $keyarr, $keystr);
	}

	/**
	 * 将数组转换为 where 语句
	 * @param array $arr 数组
	 * @return string
	 * in: array('id'=> array('>'=>'10', '<'=>'200'))
	 * out: WHERE id>'10' AND id<'200'
	 * 支持: '>=', '<=', '>', '<', 'LIKE', 'IN' (尽量少用，能不用则不用。'LIKE' 会导致全表扫描，大数据时不要使用)
	 * 注意1: 为考虑多种数据库兼容和性能问题，其他表达式不要使用，如：!= 会导致全表扫描
	 * 注意2: 高性能准则要让SQL走索引，保证查询至少达到range级别
	 */
	private function arr2where($arr) {
		$s = '';
		if(!empty($arr)) {
			foreach($arr as $key=>$val) {
				if(is_array($val)) {
					foreach($val as $k=>$v) {
						if(is_array($v)) {
							if($k === 'IN' && $v) {
								foreach($v as $i) {
									$i = addslashes($i);
									$s .= "$key='$i' OR "; // 走索引时，OR 比 IN 快
								}
								$s = substr($s, 0, -4).' AND ';
							}
						}else{
							$v = addslashes($v);
							if($k === 'LIKE') {
								$s .= "$key LIKE '%$v%' AND ";
							}else{
								$s .= "$key$k'$v' AND ";
							}
						}
					}
				}else{
					$val = addslashes($val);
					$s .= "$key='$val' AND ";
				}
			}
			$s && $s = ' WHERE '.substr($s, 0, -5);
		}
		return $s;
	}

	/**
	 * 将数组转换为SQL语句
	 * @param array $arr 数组
	 * @return string
	 * in: array('cid'=>1, 'aid'=>2)
	 * out: cid='1',aid='2'
	 */
	private function arr2sql($arr) {
		$s = '';
		foreach($arr as $k=>$v) {
			$v = addslashes($v);
			$s .= "$k='$v',";
		}
		return rtrim($s, ',');
	}
}