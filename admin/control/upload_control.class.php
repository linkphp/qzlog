<?php
/**
 * 上传方法控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class upload_control extends admin_control{
    // hook admin_upload_control_begin.php
    //上传方法
	public function index(){
		// hook admin_upload_control_index_begin.php
		$config = array(
			'maxSize' => R('dir') == 'image' ? C('up_img_max_size') : C('up_file_max_size'),
			'allowExt' => R('dir') == 'image' ? C('up_img_ext') : C('up_file_ext'),
			'upDir'=> ROOT_PATH.'upload/'
		);
		//上传类上传
		$up = new upload($config, 'imgFile');
		$info = $up->getFileInfo();
		// hook admin_upload_control_index_after.php

		/*
		* 返回格式如下示例
		* [state] => SUCCESS
		* [name] => pssafafau.jpeg
		* [size] => 48879
		* [type] => image/jpeg
		* [ext] => jpeg
		* [path] => 201512/05/2039165662daf4b55d2JaX60b.jpeg
		* [isimage] => 1
		*/
		if($info['state'] == 'SUCCESS'){
			//如果是图片就缩略+水印
			if(R('dir') == 'image'){
				//生成缩略图
				$path = 'upload/'.$info['path'];
				$thumb = image::thumb_name($path);
				$src_file = ROOT_PATH.$path;
				image::thumb($src_file, ROOT_PATH.$thumb, C('thumb_article_w'), C('thumb_article_h'), C('thumb_type'), C('thumb_quality'));
				//添加水印
				if(!empty($_ENV['_config']['watermark_pos'])){
					image::watermark(ROOT_PATH.'upload/'.$info['path'], ROOT_PATH.$_ENV['_config']['watermark_path'], null, C('watermark_pos'), C('watermark_pct'));
				}
			}
			//获取发布者id
			$arr_user_qzlog_cookie = $this->get_cookie_admin();
			if(isset($arr_user_qzlog_cookie[0]) && !empty($arr_user_qzlog_cookie[0])){
				$uid = $arr_user_qzlog_cookie[0];
			}else{
				$uid = 0;
			}
			//更新附件表
			$data = array(
				'uid' => $uid,
				'nid' => 0,
				'filename' => $info['name'],
				'filetype' => $info['ext'],
				'filesize' => $info['size'],
				'filepath' => $info['path'],
				'fileext' => $info['ext'],
				'dateline' => $_ENV['_time'],
				'isimage' => $info['isimage'],
			);
			$rs = $this->article_attachment->create($data);
			if(!$rs){
				$info['state'] = '写入附件表失败';
			}
		}
		// hook admin_upload_control_index_success_after.php
		if($info['state'] == 'SUCCESS'){
			$data_ajax['error'] = 0;
			$data_ajax['url'] = '/upload/'.$info['path'];
			$data_ajax['id'] = $rs;
			$this->ajax($data_ajax);
		}else{
			$this->alert($info['state']);
		}
	}

	//弹出提示
	function alert($msg){
		header('Content-type: text/html; charset=UTF-8');
		echo json_encode(array('error' => 1, 'message' => $msg));
		exit;
	}
    // hook admin_upload_control_after.php
}