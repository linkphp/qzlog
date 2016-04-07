<?php
/**
 * 伪静态路由
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */
defined('LINK_PATH') or exit;
class parseurl_control extends control{
	// hook parseurl_control_before.php
	public function index(){
		// hook parseurl_control_index_before.php
		if(empty($_GET)) return;
		if(!empty($_ENV['_config']['qzlog_parseurl']) && !empty($_GET['rewrite'])){
			$uri = $_GET['rewrite'];
			unset($_GET['rewrite']);
			$cate_arr = $this->category->get_category_db();
			$cate_arr = array_flip($cate_arr);
			//分类URL未设置后缀
			if(isset($cate_arr[$uri])){
				$_GET['control'] = 'category';
				$_GET['action'] = 'index';
				$_GET['cid'] = $cate_arr[$uri];
				return;
			}
			//分类URL已设置后缀
			$len = strlen(C('url_suffix'));
			if(substr($uri, -$len) == C('url_suffix')){
				$newurl = substr($uri, 0, -$len); //分类别名
				if(isset($cate_arr[$newurl])){
					$_GET['control'] = 'category';
					$_GET['action'] = 'index';
					$_GET['cid'] = $cate_arr[$newurl];
					return;
				}
			}
			//分类URL分页的情况
			if(strpos($uri, C('link_category_page_pre') )!== FALSE){
				$len = strlen(C('url_suffix'));
				if(substr($uri, -$len) == C('url_suffix')){
					$newurl = substr($uri, 0, -$len);
					$u_arr = explode(C('link_category_page_pre'), $newurl);
					if(isset($cate_arr[$u_arr[0]])){
						$_GET['control'] = 'category';
						$_GET['action'] = 'index';
						$_GET['cid'] = $cate_arr[$u_arr[0]];
						isset($u_arr[1]) && $_GET['page'] = $u_arr[1];
						return;
					}
				}
			}
			//标签URL
			$len = strlen(C('link_tag_pre'));//前缀长度
			if(substr($uri, 0, $len) == C('link_tag_pre')){
				$len2 = strlen(C('url_suffix'));//后缀长度
				if(substr($uri, -$len2) == C('url_suffix')){
					$newurl = substr($uri, $len, -$len2); //去除前缀和后缀
					$u_arr = explode('_', $newurl);
					if(count($u_arr) > 0){
						$_GET['control'] = 'tag';
						$_GET['action'] = 'index';
						$_GET['name'] = $u_arr[0];
						isset($u_arr[1]) && $_GET['page'] = $u_arr[1];
						return;
					}
				}
			}
			//首页分页URL
			$len = strlen(C('url_suffix'));
			if(substr($uri, 0, 6) == 'index_' && substr($uri, -$len) == C('url_suffix')){
				$uri = substr($uri, 6, -$len);
				$u_arr = explode('_', $uri);
				if(count($u_arr) > 0) {
					$_GET['control'] = 'index';
					$_GET['action'] = 'index';
					$_GET['page'] = $u_arr[0];
					return;
				}
			}
			//标签排行页
			if($uri == 'tag_hot' || $uri == 'tag_hot/' || $uri == 'tag_hot'.C('url_suffix')){
				$_GET['control'] = 'tag';
				$_GET['action'] = 'hot';
				return;
			}
			//内容页
			$len = strlen(C('url_suffix'));
			if(empty($len) || substr($uri, -$len) == C('url_suffix')){
				$u_arr = explode('/', $len ? substr($uri, 0, -$len) : $uri);
				if(count($u_arr) > 1 && isset($cate_arr[$u_arr[0]])){
					$_GET['control'] = 'show';
					$_GET['action'] = 'index';
					$_GET['id'] = $u_arr[1];
					return;
				}
			}
			// hook parseurl_control_index_after.php

		}

		//伪静态时，如果 $uri 有值，但没有解析到相关 $_GET 时，就提示404
		if(empty($_GET) && !empty($uri)){
			core::error404();
		}

		//上面都不符合到这里解析
		if(!isset($_GET['control'])){
			if(isset($_GET['u'])) {
				$u = $_GET['u'];
				unset($_GET['u']);
			}elseif(!empty($_SERVER['PATH_INFO'])){
				$u = $_SERVER['PATH_INFO'];
			}else{
				$_GET = array();
				$u = $_SERVER["QUERY_STRING"];
			}
			//清除URL后缀
			$url_suffix = C('url_suffix');
			if($url_suffix) {
				$suf_len = strlen($url_suffix);
				if(substr($u, -($suf_len)) == $url_suffix) $u = substr($u, 0, -($suf_len));
			}
			$uarr = explode('-', $u);
			if(count($uarr) < 2) return;

			if(isset($uarr[0])) {
				$_GET['control'] = $uarr[0];
				array_shift($uarr);
			}
			if(isset($uarr[0])) {
				$_GET['action'] = $uarr[0];
				array_shift($uarr);
			}
			$num = count($uarr);
			for($i=0; $i<$num; $i+=2){
				isset($uarr[$i+1]) && $_GET[$uarr[$i]] = $uarr[$i+1];
			}
		}
	}
	// hook parseurl_control_after.php
}
