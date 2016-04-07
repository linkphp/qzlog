<?php
/**
 * 模板管理控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class theme_control extends admin_control{
    // hook admin_theme_control_before.php
	//主题首页
	public function index(){
		// hook admin_theme_control_index_before.php
		$cfg = $this->kv->xget('cfg');
		$k = &$cfg['theme'];
		$themes = self::get_theme_all();
		//启用的主题放在第一
		if(isset($themes[$k])){
			$tmp = array();
			$tmp[$k] = $themes[$k];
			unset($themes[$k]);
			$themes = $tmp + $themes;
		}
		// hook admin_theme_control_index_after.php
		$this->assign('themes', $themes);
		$this->assign('theme', $cfg['theme']);
		$this->display('theme/index.htm');
	}

	//启用主题
	public function enable(){
        // hook admin_theme_control_enable_before.php
		$theme = R('theme', 'P');
		$this->check_theme($theme);
		$this->kv->xset('theme', $theme, 'cfg');
		$this->kv->save_changed();
		$this->runtime->delete('cfg');
		$this->clear_cache();
		// hook admin_theme_control_enable_after.php
		E(1, '启用成功');
	}

	//删除主题
	public function delete(){
		$theme = R('theme', 'P');
        // hook admin_theme_control_delete_before.php
		$this->check_theme($theme);
		if(_rmdir(ROOT_PATH.'theme/'.$theme)){
			// hook admin_theme_control_delete_after.php
			E(1, '删除完成');
		}else {
			E(0, '删除出错，请检查权限');
		}
	}

    //模版标签手册
    public function taghelp(){
    	// hook admin_theme_control_taghelp_begin.php
        $this->display('theme/taghelp.htm');
    }

	//检查是否为合法的主题名
	private function check_theme($dir){
		if(empty($dir)){
			E(0, '主题目录名不能为空');
		}elseif(preg_match('/\W/', $dir)){
			E(0, '主题目录名不正确');
		}elseif(!is_dir(ROOT_PATH.'theme/'.$dir)){
			E(0, '主题目录名不存在');
		}
	}

	//读取所有主题
	private function get_theme_all(){
		$dir = ROOT_PATH.'theme/';
		$files = _scandir($dir);
		$themes = array();
		foreach($files as $file){
			if(preg_match('/\W/', $file)) continue;
			$path = $dir.'/'.$file;
			$info = $path.'/info.ini';
			if(filetype($path) == 'dir' && is_file($info) && $lines = file($info)){
				$themes[$file] = self::get_theme_info($lines);
			}
		}
		return $themes;
	}

	//读取主题信息
	private function get_theme_info($lines){
		$res = array();
		foreach($lines as $str) {
			$arr = explode('=', trim($str));
			$k = trim($arr[0]);
			$v = isset($arr[1]) ? trim($arr[1]) : '';
			if($k == 'brief') {
				$res[$k] = strip_tags($v, '<br>');
			}elseif(in_array($k, array('name', 'version', 'update', 'author', 'authorurl'))) {
				$res[$k] = strip_tags($v);
			}
		}
		return $res;
	}
	// hook admin_theme_control_after.php
}