<?php
/**
 * 前台基类
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class base_control extends control{

	public $_cfg = array();	// 全站参数
	public $_var = array();	// 首页参数
	// hook base_control_begin.php
	public function __construct(){
		// hook base_control_construct_begin.php
		$GLOBALS['run'] = &$this;
		$cfg = $this->_cfg = $this->kv->xget('cfg');
		//$cfg['webdir'] != '' && $cfg['webdir'] = substr($cfg['webdir'],0,strlen($cfg['webdir'])-1); //去除网址后面的/
		$this->_cfg['siteurl'] = $cfg['webdomain'].$cfg['webdir'];
		$this->_cfg['tplpath'] = $cfg['webdomain'].$cfg['webdir'].'/theme/'.$this->_cfg['theme'];
        $this->assign('cfg', $this->_cfg); //系统配置信息
        $_ENV['_theme'] = &$this->_cfg['theme'];
		// hook base_control_construct_after.php
	}
	// hook base_control_after.php
}