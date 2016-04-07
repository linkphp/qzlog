<?php
//卸载  删除根目录下的sitemap.xml
defined('LINK_PATH') || exit;

if(file_exists(ROOT_PATH.'sitemap.xml')){
	unlink(ROOT_PATH.'sitemap.xml' );
}

?>