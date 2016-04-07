<?php
/**
 * 系统配置文件
 * 自动生成的 基本不需要自己修改
 * @author: link <612012@qq.com>
 * @copyright 轻舟CMS(QZLOG) http://www.qzlog.com
 */

$_ENV['_config'] = array(

	//时区
	'zone' => 'Asia/Shanghai',

	//是否开启GZIP压缩
	'gzip' => 1,

	//加密KEY
	'auth_key' => 'QrlMm1vaqDm3duMH7RikMUQPoIk1Xht9',

	//COOKIE前缀
	'cookie_pre' => 'lk_',

	//cookie路径
	'cookie_path' => '/',
	'cookie_domain' => '',

	//数据库配置
	'db' => array(
		'type' => 'pdo', //数据库连接方式支持mysql pdo
		//主数据库
		'master' => array(
			'host' => 'localhost',
			'user' => 'root',
			'password' => '123456',
			'name' => 'qzlog',
			'charset' => 'utf8',
			'tablepre' => 'mc_',
			'engine'=>'MyISAM',
		),

		//从数据库(可以是从数据库服务器群，如果不设置将使用主数据库)
		/*
		'slaves' => array(
			array(
				'host' => 'localhost',
				'user' => 'root',
				'password' => '123456',
				'name' => 'qzcms',
				'charset' => 'utf8',
				'engine'=>'MyISAM',
			),
		),
		*/
	),

	'cache' => array(
		'enable'=>1,
		'l2_cache'=>1,
		'type'=>'file', //支持file，memcache
		'pre' => 'qzlog_',
		/*'memcache'=>array (
			'multi'=>1,
			'host'=>'127.0.0.1',
			'port'=>'11211',
		)*/
	),

	//游客来宾允许访问的页面
	'guest' => array(
		//格式:（模块/动作）
		'login/index',
		'login/logout',
		'login/authlogin',
		),

	//是否禁止掉所有插件
	'plugin_disable' => 0,

	//是否开启伪静态
	'qzlog_parseurl' => 0,

	//URL伪静态后缀
	'url_suffix' => '.shtml',

	//分类URL伪静态前缀
	'link_category_page_pre' => '/page_',

	//TAG伪静态前缀
	'link_tag_pre' => 'tag_',

	//搜索结果页前缀
	'link_search_pre' => 'keyword_',

	//上传图片允许类型
	'up_img_ext' => 'jpg,jpeg,gif,png',

	//上传图片最大限制
	'up_img_max_size' => 3074,//KB

	//上传附件允许类型
	'up_file_ext' => 'zip,gz,rar,iso,xsl,doc,ppt,wps,wav,txt',

	//上传附件最大限制
	'up_file_max_size' => 10240, //KB

	//文档缩略图宽
	'thumb_article_w' => 300,

	//文档缩略图高
	'thumb_article_h' => 200,

	//裁剪方式
	'thumb_type' => 2, //1为补白裁剪 2为居中裁剪 3为上左裁剪 4为按等比缩放

	//缩略图质量
	'thumb_quality' => 100,

	//水印的位置
	'watermark_pos' => 9, //可选参数0-10  其中0关闭，10随机

	//水印透明度
	'watermark_pct' => 95,

	//水印的路径
	'watermark_path' => 'static/images/watermark.png',

	//自动生成文章摘要长度
	'auto_intro' => 255,

	//是否生成静态
	'ishtml' => 1,

	//静态文件目录
	'html_dir' => 'html',

	//URL生成静态后缀
	'html_url_suffix' => '.html',

	//列表URL静态前缀
	'html_category_page_pre' => 'list_',

	//TAG静态前缀
	'html_tag_pre' => 'tag_',

	//版本号
	'version' => '2.0',

	//发布日期
	'release' => '20151212',
);