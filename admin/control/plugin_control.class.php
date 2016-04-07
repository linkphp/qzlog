<?php
/**
 * 插件管理控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class plugin_control extends admin_control{
    // hook admin_plugin_control_begin.php
	//插件管理
	public function index(){
        // hook admin_plugin_control_index_begin.php
		$plugins = core::get_plugins();
		// 检查是否有图标和设置功能
		foreach($plugins as &$arr){
			if(isset($arr) && is_array($arr)){
				foreach($arr as $dir => &$v){
					is_file(PLUGIN_PATH.$dir.'/icon.png') && $v['is_show'] = 1;
				}
			}
		}
		// hook admin_plugin_control_index_after.php
		$this->assign('plugins', $plugins);
		$this->display('plugin/list.htm');
	}

	//插件启用
	public function enable(){
		$dir = R('dir', 'P');
        // hook admin_plugin_control_enable_begin.php
		$this->check_plugin($dir);
		$plugins = $this->get_plugin_config();
		isset($plugins[$dir]) || E(1, '启用出错，插件未安装');
		//如果是编辑器插件，卸载其他编辑器插件
		if(substr($dir, 0, 7) == 'editor_'){
			foreach($plugins as $k => $v){
				substr($k, 0, 7) == 'editor_' && $plugins[$k]['enable'] = 0;
			}
		}
		$plugins[$dir]['enable'] = 1;
		if($this->set_plugin_config($plugins)){
			// hook admin_plugin_control_enable_after.php
			$this->clear_cache();
			E(1, '启用完成');
		}else{
			E(0, '写入 plugin.inc.php 文件失败');
		}
	}

	//插件停用
	public function disabled(){
		$dir = R('dir', 'P');
        // hook admin_plugin_control_disabled_begin.php
		$this->check_plugin($dir);
		$plugins = $this->get_plugin_config();
		isset($plugins[$dir]) || E(0, '停用出错，插件未安装');
		$plugins[$dir]['enable'] = 0;
		if($this->set_plugin_config($plugins)){
			// hook admin_plugin_control_disabled_after.php
			$this->clear_cache();
			E(1, '停用完成');
		}else{
			E(0, '写入 plugin.inc.php 文件失败');
		}
	}

	//插件删除
	public function delete(){
		$dir = R('dir', 'P');
        // hook admin_plugin_control_delete_begin.php
		$this->check_plugin($dir);
		$plugins = $this->get_plugin_config();
		//只允许删除停用或未安装的插件
		if(empty($plugins[$dir]['enable'])){
			//检测有 uninstall.php 文件，则执行卸载
			$uninstall = PLUGIN_PATH.$dir.'/uninstall.php';
			if(is_file($uninstall)){
				include $uninstall;
			}
			if(_rmdir(PLUGIN_PATH.$dir)){
				if(isset($plugins[$dir])){
					unset($plugins[$dir]);
					if(!$this->set_plugin_config($plugins)){
						E(1, '写入 plugin.inc.php 文件失败');
					}
				}
				// hook admin_plugin_control_delete_after.php
				E(1, '删除完成');
			}else{
				E(0, '删除出错');
			}
		}else{
			E(0, '已启用的插件不允许删除');
		}
	}

	//本地插件安装
	public function install(){
		$dir = R('dir', 'P');
        // hook admin_plugin_control_install_begin.php
		$this->check_plugin($dir);
		$plugins = $this->get_plugin_config();
		isset($plugins[$dir]) && E(0, '插件已经安装过');
		$cms_version = $this->get_version($dir);
		$cms_version && version_compare($cms_version, C('version'), '>') && E(0, '无法安装，最低版本要求 '.$cms_version);
		$install = PLUGIN_PATH.$dir.'/install.php';// 检测有 install.php 文件，则执行安装
		if(is_file($install)) include $install;
		$plugins[$dir] = array('enable' => 0);
		if(!$this->set_plugin_config($plugins)) E(0, '写入 plugin.inc.php 文件失败');
		// hook admin_plugin_control_enable_after.php
		E(1, '安装完成');
	}

	//检查是否为合法的插件名
	private function check_plugin($dir){
		if(empty($dir)){
			E(0, '插件目录名不能为空');
		}elseif(preg_match('/\W/', $dir)){
			E(0, '插件目录名不正确');
		}elseif(!is_dir(PLUGIN_PATH.$dir)){
			E(0, '插件目录名不存在');
		}
	}

	//检查版本
	private function get_version($dir){
		$cfg = is_file(PLUGIN_PATH.$dir.'/conf.php') ? (array)include(PLUGIN_PATH.$dir.'/conf.php') : array();
		return isset($cfg['cms_version']) ? $cfg['cms_version'] : 0;
	}

	//获取插件配置信息
	private function get_plugin_config(){
		return is_file(CONFIG_PATH.'plugin.inc.php') ? (array)include(CONFIG_PATH.'plugin.inc.php') : array();
	}

	//设置插件配置信息
	private function set_plugin_config($plugins){
		$file = CONFIG_PATH.'plugin.inc.php';
		!is_file($file) && _is_writable(dirname($file)) && file_put_contents($file, '');
		if(!_is_writable($file)) return FALSE;
		return file_put_contents($file, "<?php\nreturn ".var_export($plugins, TRUE).";\n?>");
	}
}