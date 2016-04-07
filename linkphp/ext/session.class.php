<?php
/**
 * SESSION类
 */

final class session {

	//防止类实例化或被复制
	private function __construct(){}
	private function __clone(){}

	/**
	* 设置SESSION的值
	*
	* @param string $name :SESSION变量名称
	* @param mixed $value :对应值
	*/
	static public function set($name,$value='')
	{
		if(!is_array($name))
		{
			$_SESSION[$name] = $value;
		}else{
			foreach ($name as $k=>$v) $_SESSION[$k] = $v;
		}
	}

	/**
	* 获得 SESSION 数据
	*
	* @param string $name:域名称,如果为空则返回整个 $SESSION 数组
	* @return mixed
	*/
	static public function get($name='')
	{
		//return  $name ? (isset($_SESSION[$name]) ? $_SESSION[$name] : null) : $_SESSION;
		return  isset($_SESSION[$name]) ? $_SESSION[$name] : null;
	}

	/**
	* 删除指定的 SESSION
	*/
	static public function remove($name)
	{
		if(is_array($name)){
			foreach ($name as $n){
				if(isset($_SESSION[$n])){
					unset($_SESSION[$n]);
				}
			}
		}else{
			if(isset($_SESSION[$name])){
				unset($_SESSION[$name]);
			}
		}
		return true;
	}

	/**
	* 清空 SESSION
	*/
	static public function clear()
	{
		session_destroy();
	}
}
