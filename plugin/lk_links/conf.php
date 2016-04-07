<?php
/**
 * 友情链接插件
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
return array(
	'name' => '友情链接',														// 插件名
	'brief' => '友情链接插件，只支持文字链接，更有益搜索引擎优化。',				//介绍
	'version' => '1.0.0',													// 插件版本
	'cms_version' => '1.0',													// 插件支持的 QZLOG 版本
	'update' => '2015-07-26',												// 插件最近更新
	'author' => 'Link',														// 插件作者
	'authorurl' => 'http://www.ludajie.com',									// 插件作者主页
	'setting' => 'index.php?u=links-index',									// 插件设置URL
	'rank' => 2,																//排序
);
