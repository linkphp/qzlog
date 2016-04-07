<?php
/**
 * 系统设置控制器
 * @author: link <612012@qq.com>
 * @copyright 轻舟CMS(QZLOG) http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class setting_control extends admin_control{
    // hook admin_setting_control_begin.php
	//基本设置
	public function index(){
        // hook admin_setting_control_index_begin.php
		if(IS_POST){
			_trim($_POST);
			$this->kv->xset('webname', R('webname', 'P'), 'cfg');
			$this->kv->xset('webdomain', R('webdomain', 'P'), 'cfg');
			$this->kv->xset('webdir', R('webdir', 'P'), 'cfg');
			$this->kv->xset('webmail', R('webmail', 'P'), 'cfg');
			$this->kv->xset('tongji', R('tongji', 'P'), 'cfg');
			$this->kv->xset('beian', R('beian', 'P'), 'cfg');
            $this->kv->xset('copyright', R('copyright', 'P'), 'cfg');
            $this->kv->xset('tel', R('tel', 'P'), 'cfg');
			// hook admin_setting_control_index_post_before.php
			$this->kv->save_changed();
			$this->runtime->delete('cfg');
			// hook admin_setting_control_index_post_after.php
			E(1,'修改成功');
		}
        $cfg = $this->kv->xget('cfg');
        $input = array();
        $input['webname'] = form::get_text('webname', $cfg['webname'],'hd-w400');
        $input['webdomain'] = form::get_text('webdomain', $cfg['webdomain'],'hd-w400');
        $input['webdir'] = form::get_text('webdir', $cfg['webdir'],'hd-w400');
        $input['webmail'] = form::get_text('webmail', $cfg['webmail'],'hd-w400');
        $input['beian'] = form::get_text('beian', $cfg['beian'],'hd-w400');
        $input['tel'] = form::get_text('tel', $cfg['tel'],'hd-w400');
        $input['copyright'] = form::get_text('copyright', $cfg['copyright'],'hd-w400');
        $input['tongji'] = form::get_textarea('tongji', $cfg['tongji'],'hd-w500 hd-h100');
        // hook admin_setting_control_index_after.php
        $this->assign('input', $input);
        $this->display('setting/index.htm');
	}

	//SEO设置
	public function seo(){
        // hook admin_setting_control_seo_begin.php
		if(IS_POST){
			_trim($_POST);
			$this->kv->xset('seo_title', R('seo_title', 'P'), 'cfg');
			$this->kv->xset('seo_keywords', R('seo_keywords', 'P'), 'cfg');
			$this->kv->xset('seo_description', R('seo_description', 'P'), 'cfg');
			// hook admin_setting_control_seo_post_before.php
			$this->kv->save_changed();
			$this->runtime->delete('cfg');
			// hook admin_setting_control_seo_post_after.php
			E(1,'修改成功');
		}
        $cfg = $this->kv->xget('cfg');
        $input = array();
        $input['seo_title'] = form::get_text('seo_title', $cfg['seo_title'],'hd-w400');
        $input['seo_keywords'] = form::get_text('seo_keywords', $cfg['seo_keywords'],'hd-w400');
        $input['seo_description'] = form::get_textarea('seo_description', $cfg['seo_description'],'hd-w500 hd-h100');
        // hook admin_setting_control_seo_after.php
        $this->assign('input', $input);
        $this->display('setting/seo.htm');
	}

    //伪静态设置
    public function rewrite(){
		if(empty($_POST)){
			// hook admin_setting_control_rewrite_begin.php
			$this->assign('parseurl', $_ENV['_config']['qzlog_parseurl']);
			$this->assign('url_suffix', $_ENV['_config']['url_suffix']);
			$this->assign('link_category_page_pre', $_ENV['_config']['link_category_page_pre']);
			$this->assign('link_tag_pre', $_ENV['_config']['link_tag_pre']);
			$this->assign('link_search_pre', $_ENV['_config']['link_search_pre']);
			$this->display('setting/rewrite.htm');
		}else{
			_trim($_POST);
			$parseurl = (int)R('parseurl', 'P');
			$url_suffix = R('url_suffix', 'P');
			$link_category_page_pre = R('link_category_page_pre', 'P');
			$link_tag_pre = R('link_tag_pre', 'P');
			$link_search_pre = R('link_search_pre', 'P');
			$file = ROOT_PATH.'config/config.inc.php';
			!_is_writable($file) && E(0,'配置文件 /config/config.inc.php 不可写');
			$s = file_get_contents($file);
			$s = preg_replace("#'qzlog_parseurl'\s*=>\s*\d,#", "'qzlog_parseurl' => {$parseurl},", $s);
			$s = preg_replace("#'url_suffix'\s*=> '.*\s*',#", "'url_suffix' => '{$url_suffix}',", $s);
			$s = preg_replace("#'link_category_page_pre'\s*=> '.*\s*',#", "'link_category_page_pre' => '{$link_category_page_pre}',", $s);
			$s = preg_replace("#'link_tag_pre'\s*=> '.*\s*',#", "'link_tag_pre' => '{$link_tag_pre}',", $s);
			$s = preg_replace("#'link_search_pre'\s*=> '.*\s*',#", "'link_search_pre' => '{$link_search_pre}',", $s);
			if(file_put_contents($file, $s)){
				// hook admin_setting_control_rewrite_post_after.php
				$this->clear_cache();
				E(1,'修改成功');
			}else{
				E(0,'写入 config.inc.php 失败');
			}
		}
    }

    //插件设置
    public function plugin(){
    	// hook admin_setting_control_plugin_begin.php
		if(IS_POST){
			_trim($_POST);
			// hook admin_setting_control_plugin_post_begin.php
			$plugin = (int)R('plugin', 'P');
			$file = ROOT_PATH.'config/config.inc.php';
			!_is_writable($file) && E(0,'配置文件 /config/config.inc.php 不可写');
			$s = file_get_contents($file);
			$s = preg_replace("#'plugin_disable'\s*=>\s*\d,#", "'plugin_disable' => {$plugin},", $s);
			!file_put_contents($file, $s) && E(0,'写入 config.inc.php 失败');
			$this->clear_cache();
			// hook admin_setting_control_plugin_post_after.php
			E(1,'修改成功');
		}
		// hook admin_setting_control_plugin_after.php
		$this->assign('plugin', $_ENV['_config']['plugin_disable']);
		$this->display('setting/plugin.htm');
    }

	//图片设置
    public function thumb(){
    	// hook admin_setting_control_thumb_begin.php
		if(IS_POST){
			_trim($_POST);
			// hook admin_setting_control_thumb_post_begin.php
			$thumb_article_w = (int)R('thumb_article_w', 'P');
			$thumb_article_h = (int)R('thumb_article_h', 'P');
			$thumb_type = (int)R('thumb_type', 'P');
			$thumb_quality = (int)R('thumb_quality', 'P');
			$watermark_pos = (int)R('watermark_pos', 'P');
			$watermark_pct = (int)R('watermark_pct', 'P');
			$watermark_path = R('watermark_path', 'P');
			$file = ROOT_PATH.'config/config.inc.php';
			!_is_writable($file) && E(0,'配置文件 /config/config.inc.php 不可写');
			$s = file_get_contents($file);
			$s = preg_replace("#'thumb_article_w'\s*=>.*\s*\d,#", "'thumb_article_w' => {$thumb_article_w},", $s);
			$s = preg_replace("#'thumb_article_h'\s*=>.*\s*\d,#", "'thumb_article_h' => {$thumb_article_h},", $s);
			$s = preg_replace("#'thumb_type'\s*=>.*\s*\d,#", "'thumb_type' => {$thumb_type},", $s);
			$s = preg_replace("#'thumb_quality'\s*=>.*\s*\d,#", "'thumb_quality' => {$thumb_quality},", $s);
			$s = preg_replace("#'watermark_pos'\s*=>.*\s*\d,#", "'watermark_pos' => {$watermark_pos},", $s);
			$s = preg_replace("#'watermark_pct'\s*=>.*\s*\d,#", "'watermark_pct' => {$watermark_pct},", $s);
			$s = preg_replace("#'watermark_path'\s*=> '.*\s*',#", "'watermark_path' => '{$watermark_path}',", $s);
			!file_put_contents($file, $s) && E(0,'写入 config.inc.php 失败');
			$this->clear_cache();
			// hook admin_setting_control_thumb_post_after.php
			E(1,'修改成功');
		}
		$this->assign('thumb_article_w', $_ENV['_config']['thumb_article_w']);
		$this->assign('thumb_article_h', $_ENV['_config']['thumb_article_h']);
		$this->assign('thumb_type', $_ENV['_config']['thumb_type']);
		$this->assign('thumb_quality', $_ENV['_config']['thumb_quality']);
		$this->assign('watermark_pos', $_ENV['_config']['watermark_pos']);
		$this->assign('watermark_pct', $_ENV['_config']['watermark_pct']);
		$this->assign('watermark_path', $_ENV['_config']['watermark_path']);
		$this->display('setting/thumb.htm');
		// hook admin_setting_control_thumb_after.php
    }

	//上传设置
    public function upload(){
    	// hook admin_setting_control_upload_begin.php
		if(IS_POST){
			_trim($_POST);
			// hook admin_setting_control_upload_post_begin.php
			$up_img_ext = R('up_img_ext', 'P');
			$up_img_max_size = (int)R('up_img_max_size', 'P');
			$up_file_ext = R('up_file_ext', 'P');
			$up_file_max_size = (int)R('up_file_max_size', 'P');
			$file = ROOT_PATH.'config/config.inc.php';
			!_is_writable($file) && E(0,'配置文件 /config/config.inc.php 不可写');
			$s = file_get_contents($file);
			$s = preg_replace("#'up_img_ext'\s*=> '.*\s*',#", "'up_img_ext' => '{$up_img_ext}',", $s);
			$s = preg_replace("#'up_img_max_size'\s*=>.*\s*\d,#", "'up_img_max_size' => {$up_img_max_size},", $s);
			$s = preg_replace("#'up_file_ext'\s*=> '.*\s*',#", "'up_file_ext' => '{$up_file_ext}',", $s);
			$s = preg_replace("#'up_file_max_size'\s*=>.*\s*\d,#", "'up_file_max_size' => {$up_file_max_size},", $s);
			!file_put_contents($file, $s) && E(0,'写入 config.inc.php 失败');
			$this->clear_cache();
			// hook admin_setting_control_upload_post_after.php
			E(1,'修改成功');
		}
		$this->assign('up_img_ext', $_ENV['_config']['up_img_ext']);
		$this->assign('up_img_max_size', $_ENV['_config']['up_img_max_size']);
		$this->assign('up_file_ext', $_ENV['_config']['up_file_ext']);
		$this->assign('up_file_max_size', $_ENV['_config']['up_file_max_size']);
		$this->display('setting/upload.htm');
		// hook admin_setting_control_upload_after.php
    }

    //生成静态设置
    public function html(){
		if(empty($_POST)){
			// hook admin_setting_control_html_after.php
			$this->assign('ishtml', $_ENV['_config']['ishtml']);
			$this->assign('html_dir', $_ENV['_config']['html_dir']);
			$this->assign('html_category_page_pre', $_ENV['_config']['html_category_page_pre']);
			$this->assign('html_tag_pre', $_ENV['_config']['html_tag_pre']);
			$this->assign('html_url_suffix', $_ENV['_config']['html_url_suffix']);
			$this->display('setting/html.htm');
		}else{
			_trim($_POST);
			// hook admin_setting_control_html_post_begin.php
			$ishtml = (int)R('ishtml', 'P');
			$html_dir = R('html_dir', 'P');
			$html_url_suffix = R('html_url_suffix', 'P');
			$html_category_page_pre = R('html_category_page_pre', 'P');
			$html_tag_pre = R('html_tag_pre', 'P');
			$file = ROOT_PATH.'config/config.inc.php';
			!_is_writable($file) && E(0,'配置文件 /config/config.inc.php 不可写');
			$s = file_get_contents($file);
			$s = preg_replace("#'ishtml'\s*=>\s*\d,#", "'ishtml' => {$ishtml},", $s);
			$s = preg_replace("#'html_dir'\s*=> '.*\s*',#", "'html_dir' => '{$html_dir}',", $s);
			$s = preg_replace("#'html_url_suffix'\s*=> '.*\s*',#", "'html_url_suffix' => '{$html_url_suffix}',", $s);
			$s = preg_replace("#'html_category_page_pre'\s*=> '.*\s*',#", "'html_category_page_pre' => '{$html_category_page_pre}',", $s);
			$s = preg_replace("#'html_tag_pre'\s*=> '.*\s*',#", "'html_tag_pre' => '{$html_tag_pre}',", $s);
			if(file_put_contents($file, $s)){
				$this->clear_cache();
				E(1,'修改成功');
			}else{
				E(0,'写入 config.inc.php 失败');
			}
			// hook admin_setting_control_html_post_after.php
		}
    }

	//导航设置
	public function navs(){
		// hook admin_setting_control_navs_begin.php
		$navigate = $this->navigate->find_fetch('',array('sort'=>1));
		$category = $this->category->get_all_category();
		if($navigate){
			foreach($navigate as $key=>$val){
				if($val['cid'] != 0){
					$navigate[$key]['name'] = isset($category['category-cid-'.$val['cid']]['name'])?$category['category-cid-'.$val['cid']]['name']:'不存在';
					$navigate[$key]['url'] = isset($category['category-cid-'.$val['cid']]['urlname'])?$category['category-cid-'.$val['cid']]['urlname']:'不存在';
					$navigate[$key]['attribute'] = isset($category['category-cid-'.$val['cid']]['attribute'])?$category['category-cid-'.$val['cid']]['attribute']:'不存在';
				}
			}
		}
        $list = data::tree($navigate, "name", "id", "pid");
        // hook admin_setting_control_navs_after.php
        $this->assign('list',$list);
		$this->display('setting/navs.htm');
	}

	//导航添加
	public function navsAdd(){
        // hook admin_setting_control_navsAdd_begin.php
		if(IS_POST){
			$cid = (int)R('cid','P');
			if(isset($cid) && $cid != 0){
				//栏目类型
				$data['cid'] = $cid;
				$data['pid'] = (int)R('pid','P');
				$data['status'] = (int)R('status','P');
				$data['sort'] = (int)R('sort','P');
				$data['target'] = (int)R('target','P');
			}else{
				//链接类型
				$data['name'] = trim(R('name','P'));
				$data['url'] = trim(R('url','P'));
				$data['pid'] = (int)R('pid','P');
				$data['status'] = (int)R('status','P');
				$data['sort'] = (int)R('sort','P');
				$data['target'] = (int)R('target','P');
			}
            // hook admin_setting_control_navsAdd_post_begin.php
            $rs = $this->navigate->create($data);
            // hook admin_setting_control_navsAdd_post_after.php
            $rs ? E(1,'添加成功') : E(0,'添加失败，请重试');

		}

        $navigate = $this->navigate->find_fetch();
        $cat = $this->category->get_all_category();
		if($navigate){
			foreach($navigate as $key=>$val){
				if($val['cid'] != 0){
					$navigate[$key]['name'] = $cat['category-cid-'.$val['cid']]['name'];
					$navigate[$key]['url'] = $cat['category-cid-'.$val['cid']]['urlname'];
					$navigate[$key]['attribute'] = $cat['category-cid-'.$val['cid']]['attribute'];
				}
			}
		}

		$list = data::tree($navigate, "name", "id", "pid");
        $category = data::tree($cat, "name", "cid", "pid");
        // hook admin_setting_control_navsAdd_after.php
		$this->assign('list',$list);
        $this->assign('category',$category);
		$this->display('setting/navsAdd.htm');
	}

	//导航编辑
	public function navsEdit(){
        // hook admin_setting_control_navsEdit_begin.php
		if(IS_POST){
			$data['id'] = (int)(R('id','P'));
			$data['name'] = trim(R('name','P'));
			$data['url'] = trim(R('url','P'));
			$data['pid'] = (int)R('pid','P');
			$data['status'] = (int)R('status','P');
			$data['sort'] = (int)R('sort','P');
			$data['target'] = (int)R('target','P');
			empty($data['name']) && E(0,'请填写名称');
			empty($data['url']) && E(0,'请填写链接地址');
			empty($data['url']) && E(0,'请填写链接地址');
            // hook admin_setting_control_navsEdit_post_begin.php
            $rs = $this->navigate->update($data);
            // hook admin_setting_control_navsEdit_post_after.php
            $rs ? E(1,'修改成功') : E(0,'修改失败，请重试');
		}
		$id = (int)R('id');
		$navInfo = $this->navigate->read($id);
		$this->assign('navInfo',$navInfo);
        $navigate = $this->navigate->find_fetch();
        $cat = $this->category->get_all_category();
		foreach($navigate as $key=>$val){
			if($val['cid'] != 0){
				$navigate[$key]['name'] = $cat['category-cid-'.$val['cid']]['name'];
				$navigate[$key]['url'] = $cat['category-cid-'.$val['cid']]['urlname'];
				$navigate[$key]['attribute'] = $cat['category-cid-'.$val['cid']]['attribute'];
			}

		}
		$list = data::tree($navigate, "name", "id", "pid");
        $category = data::tree($cat, "name", "cid", "pid");
        // hook admin_setting_control_navsEdit_after.php
		$this->assign('list',$list);
        $this->assign('category',$category);
		$this->display('setting/navsEdit.htm');
	}

	//更新导航排序
	public function navsSort(){
        // hook admin_setting_control_navsSort_begin.php
		if(IS_POST){
			$navs_sort = R('list_sort','P');
            // hook admin_setting_control_navsSort_post_begin.php
			foreach ($navs_sort as $id => $sort){
				$data['id'] = $id;
				$data['sort'] = $sort;
	            $this->navigate->update($data);
        	}
        	// hook admin_setting_control_navsSort_post_after.php
			E(1,'排序更新成功');
		}
        // hook admin_setting_control_navsSort_after.php
	}

	//删除导航
	public function navsDel(){
		$id = (int)R('id','P');
        // hook admin_setting_control_navsDel_before.php
		$rs = $this->navigate->delete($id);
		// hook admin_setting_control_navsDel_after.php
		$rs ? E(1,'删除成功') : E(0,'删除失败,请重试');
	}

	//更新缓存
	public function cache(){
		// hook admin_setting_control_cache_begin.php
        if(!empty($_POST)){
        	// hook admin_setting_control_cache_post_begin.php
			$this->runtime->truncate();
            $this->clear_cache();
			// hook admin_setting_control_cache_post_after.php
            E(1,'清除成功');
        }
        $this->display('setting/cache.htm');
		// hook admin_setting_control_cache_after.php
	}

    //重新统计
    public function rebuild(){
    	// hook admin_setting_control_rebuild_begin.php
        if(!empty($_POST)){
        	// hook admin_setting_control_rebuild_post_begin.php
        	$this->rebuild_go() && E(1,'重新统计成功');
        }
        // hook admin_setting_control_rebuild_after.php
        $this->display('setting/rebuild.htm');
    }
	// hook admin_setting_control_after.php
}