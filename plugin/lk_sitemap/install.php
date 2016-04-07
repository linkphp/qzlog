<?php
/**
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');

if(!file_exists(ROOT_PATH.'sitemap.xml')){

	$sitemap_list = $this->article->find_fetch('',array('addtime'=>1),0,500);
	$sitemap_taglist = $this->tag->find_fetch('',array('tagid'=>1),0,500);
	$cfg = $this->kv->xget('cfg');
	$cfg['webdir'] != '' && $cfg['webdir'] = substr($cfg['webdir'],0,strlen($cfg['webdir'])-1);
	$SITE_URL = 'http://'.$cfg['webdomain'].$cfg['webdir'];

	$sitemap_tpl = '<?xml version="1.0" encoding="UTF-8"?><urlset>';
	$sitemap_tpl .= '<url>';
	$sitemap_tpl .= '<loc>'.$SITE_URL.'</loc>';
	$sitemap_tpl .= '<lastmod>'.date('Y-m-d').'</lastmod>';
	$sitemap_tpl .= '<changefreq>always</changefreq>';
	$sitemap_tpl .= '<priority>1.0</priority>';
	$sitemap_tpl .= '</url>';

	//文档
	foreach ($sitemap_list as $sitemap_v){
		$sitemap_tpl .= '<url>';
		if(empty($_ENV['_config']['qzlog_parseurl'])){
			$sitemap_tpl .= $SITE_URL.'index.php?u=show-index-id-'.$sitemap_v['nid'].$_ENV['_config']['url_suffix'];
		}else{
			$sitemap_tpl .= $SITE_URL.'/show_'.$sitemap_v['nid'].$_ENV['_config']['url_suffix'];
		}
		$sitemap_tpl .= '<lastmod>'.date('Y-m-d',$sitemap_v['addtime']).'</lastmod>';
		$sitemap_tpl .= '<changefreq>always</changefreq>';
		$sitemap_tpl .= '<priority>1.0</priority>';
		$sitemap_tpl .= '</url>';
	}

	//tag
	foreach ($sitemap_taglist as $sitemap_tagv){
		$sitemap_tpl .= '<url>';
		if(empty($_ENV['_config']['qzlog_parseurl'])){
            $sitemap_tpl .= $SITE_URL.'index.php?u=tag-index-name-'.$sitemap_tagv['name'].$_ENV['_config']['url_suffix'];
        }else{
           $sitemap_tpl .= $SITE_URL.'/tag_'.$sitemap_tagv['name'].$_ENV['_config']['url_suffix'];
        }
		$sitemap_tpl .= '<lastmod>'.date('Y-m-d').'</lastmod>';
		$sitemap_tpl .= '<changefreq>always</changefreq>';
		$sitemap_tpl .= '<priority>1.0</priority>';
		$sitemap_tpl .= '</url>';
	}

	$sitemap_tpl.= '</urlset>';
	file_put_contents(ROOT_PATH.'sitemap.xml' , $sitemap_tpl );
}

?>