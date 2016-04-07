<?php
/**
 * 生成静态Html
 * @author: link <612012@qq.com>
 * @copyright 轻舟CMS(QZLOG) http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class html_control extends admin_control{
	// hook admin_html_control_begin.php

	//更新主页HTML
	public function index(){
		if(IS_POST){
            if(C('ishtml') == 1){
				include APP_PATH.'control/base_control.class.php';
				include APP_PATH.'control/index_control.class.php';
				$obj = new index_control();
				ob_start();
				$obj->index(1);
				$_cache = ob_get_contents();
				ob_end_clean();
				try{
					$rs = FW(ROOT_PATH.'index'.C('html_url_suffix'), $_cache);
				}catch(Exception $e){
					$this->message(0,'写入出错，写入权限不对？');
				}
				if($rs){
					$this->message(1,'成功更新主页HTML','index.php?u=html-index');
				}else{
					$this->message(0,'生成主页HTML失败');
				}
	        }else{
				$this->message(0,'请先开启生成静态功能');
	        }
        }else{
            $this->display('html/index.htm');
        }
	}

	//生成栏目页
	public function makelist(){
        if(IS_POST) {
            //没有选择栏目
            if(!isset($_POST['cid']) || count($_POST['cid']) == 1 && $_POST['cid'][0] == 0) {
                //所有栏目
                $HtmlCategory = $this->category->get_all_ok_category();
            } else {
            	//指定栏目
                $HtmlCategory = $this->category->find_fetch(array('cid'=> array('IN'=>$_POST['cid'])));
            }
            if(empty($HtmlCategory)) {
               $this->message(1,'栏目生成完毕','index.php?u=html-index');
            } else {
                foreach($HtmlCategory as $cat) {
					$total = $cat['count']; //总共多少
					$pagenum = $cat['pagenum']; //每页多少
					$maxpage = max(1, ceil($total/$pagenum)); //最大页
					$page = 1;
					while($page<=$maxpage) {
						$this->category($cat['cid'],$page);
  						$page++;
					}
                }
                $this->message(1,'成功创建栏目 ID：'.$cat['cid'],'index.php?u=html-makelist',1);
            }
        } else {
            $cat = $this->category->get_all_category();
			$list = data::tree($cat, "name", "cid", "pid");
			$this->assign('list',$list);
			$this->display('html/makelist.htm');
        }
	}

   /**
    * 生成单一栏目
    * @param $cid 栏目cid
    * @param $page 生成第几页
    * @return bool
    */
    public function category($cid,$page = 1){
    	$categoryCache = $this->category->find_fetch(array('cid'=> $cid));
        if (!isset($categoryCache['category-cid-'.$cid])) {
            return false;
        }
        $cat = $categoryCache['category-cid-'.$cid];
        if ($cat['attribute'] == 3) {
        		//外部链接栏目不生成
            return true;
        }
		$cat['referpath'] =='parent' ? $path = C('html_dir').'/'.$cat['urlname'].'/':$path = $cat['urlname'].'/';
		$file_name = C('html_category_page_pre').$page.C('html_url_suffix');
		include_once APP_PATH.'control/base_control.class.php';
		include_once APP_PATH.'control/category_control.class.php';
		$obj = new category_control();
		ob_start();
		$obj->index(1,$cid,$page);
		$_cache = ob_get_contents();
		ob_end_clean();
		try{
			$rs = FW(ROOT_PATH.$path.$file_name, $_cache);
	        if ($page == 1) {
				//第1页时复制index.html
				copy(ROOT_PATH.$path.$file_name, ROOT_PATH.$path.'/index'.C('html_url_suffix'));
	        }
		}catch(Exception $e){
			E(0,'生成出错，写入权限不对？');
		}
        return true;
    }

	// hook admin_html_control_after.php
}