<?php
/**
 * 文档模型
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class article extends model{
	// hook article_model_begin.php
	function __construct(){
		// hook article_model_construct_begin.php
		$this->table = 'article';	// 表名
		$this->pri = array('nid');	// 主键
		$this->maxid = 'nid';		// 自增字段
	}

	// 暂时用些方法解决获取 cfg 值
	function __get($var){
		// hook article_model_get_begin.php
		if($var == 'cfg'){
			return $this->cfg = $this->kv->xget();
		}else{
			return parent::__get($var);
		}
	}

	//判断指定栏目下是否存在文章,存在返回true 不存在返回false
	public function if_has_article($cid){
		// hook article_model_if_has_article_begin.php
		return $this->find_fetch(array('cid'=>$cid));

	}

	// 首页分页链接格式化
	public function index_url(){
		// hook cms_content_model_index_url_before.php
		if(empty($_ENV['_config']['qzlog_parseurl'])) {
			return $this->cfg['webdir'].'index.php?index-index-page-{page}'.C('url_suffix');
		}else{
			return $this->cfg['webdir'].'index_{page}'.C('url_suffix');
		}
	}

	// 标签链接格式化
    public function tag_url(&$name,$page = FALSE){
        // hook article_content_model_tag_url_before.php
        if(empty($_ENV['_config']['qzlog_parseurl'])){
            return $this->cfg['webdir'].'index.php?tag-index-name-'.urlencode($name).($page ? '-page-{page}' : '').C('url_suffix');
        }else{
            if($page){
            		return $this->cfg['webdir'].C('link_tag_pre').urlencode($name).'_{page}'.C('url_suffix');
            }else{
            		return $this->cfg['webdir'].C('link_tag_pre').urlencode($name).C('url_suffix');
            }
        }
    }

	// 搜索链接格式化
    public function search_url(&$keyword, $page = FALSE){
        // hook article_content_model_search_url_before.php
        if(empty($_ENV['_config']['qzlog_parseurl'])){
            return $this->cfg['webdir'].'index.php?search-index-keyword-'.urlencode($keyword).'-page-{page}'.C('url_suffix');
        }else{
            if($page){
            		return $this->cfg['webdir'].C('link_search_pre').urlencode($keyword).'_{page}'.C('url_suffix');
            }else{
            		return $this->cfg['webdir'].C('link_search_pre').urlencode($keyword).C('url_suffix');
            }
        }
    }

   // 获取内容列表
	public function list_arr($where, $orderby, $orderway, $start, $limit, $total,$life = 0){
		// 优化大数据量翻页
		if($start > 1000 && $total > 2000 && $start > $total/2){
			$orderway = -$orderway;
			$newstart = $total-$start-$limit;
			if($newstart < 0){
				$limit += $newstart;
				$newstart = 0;
			}
			$list_arr = $this->find_fetch($where, array($orderby => $orderway), $newstart, $limit,$life);
			return array_reverse($list_arr, TRUE);
		}else{
			return $this->find_fetch($where, array($orderby => $orderway), $start, $limit,$life);
		}
	}

	//格式化文章列表 加分类,加属性
	public function list_format($arr){
		if($arr){
			// hook article_model_list_format_begin.php
			$arr = array_values($arr);
			foreach($arr as $k=>$v){
				$arr[$k]['_category'] = $this->category->read($v['cid']); //分类
				$v['_flag'] = '';
				$flag_arr = array(1=>'热门', 2=>'置顶', 3=>'推荐', 4=>'图片', 5=>'精华', 6=>'幻灯片');
				if($v['tuji'] == 1) {
					$v['_flag'] .= ' [图集]';
				}
				if(!empty($v['flag'])){
					$flags = explode(',', $v['flag']);
					foreach($flags as $flag) {
						$flag = intval($flag);
						if($flag) $v['_flag'] .= ' ['.$flag_arr[$flag].']';
					}
				}else{
					$arr[$k]['_flag'] = '';
				}
				if($v['_flag']) $arr[$k]['_flag'] = '<font color="f35f5d">'.$v['_flag'].'</font>';
			}
			// hook article_model_list_format_after.php
			return $arr;
		}
	}

	// 内容关联删除
	public function xdelete($id){
		// hook article_model_xdelete_before.php
		// 文章内容读取
		$data = $this->read($id);
		if(empty($data)) return '内容不存在';
		$content = $this->article_content->get_content_by_id($id);
		// 删除所有图片附件
		$attach_arr = $this->article_attachment->find_fetch(array('nid'=>$id));
		if(!empty($attach_arr)){
			foreach($attach_arr as $v){
				$file = isset($v['attachment']) ? ROOT_PATH.ltrim($v['attachment'],'/') : null;
				is_file($file) && unlink($file);
				$this->article_attachment->delete($v['id']);
			}
		}
		//删除缩略图
		$thumb = ROOT_PATH.ltrim($data['thumb'],'/');
		is_file($thumb) && unlink($thumb);
		//删除文章中的图片
		$content_data = !empty($content['content']) ? $content['content'] : null;
		$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
		@preg_match_all($pattern,$content_data,$match);
		if(!empty($match[1])){
			foreach($match[1] as $vvv){
				if(stripos($vvv,'http://') === false){
					$imgfile = ROOT_PATH.ltrim($vvv,'/');
					is_file($imgfile) && unlink($imgfile);
				}
			}
		}
		// 更新标签表
		if(!empty($data['tags'])){
			$tags_arr = _json_decode($data['tags']);
			foreach($tags_arr as $tagid => $name){
				$this->tag_relation->delete($tagid, $id);
				$tagdata = $this->tag->read($tagid);
				$tagdata['count']--;
				if($tagdata['count'] > 0) $this->tag->update($tagdata);
			}
		}
		// 更新分类表
		$catedata = $this->category->read($data['cid']);
		if(empty($catedata)) return '读取分类表出错';
		if($catedata['count'] > 0) {
			$catedata['count']--;
			if(!$this->category->update($catedata)) return '写入内容表出错';
		}
		$this->category->update_cache($data['cid']);

		// 删除文章内容
		$this->article_content->find_delete(array('nid'=>$id));
		$ret = $this->delete($id);
		// hook article_model_xdelete_after.php
		return $ret ? '' : '删除失败';
	}
	// hook article_model_after.php
}