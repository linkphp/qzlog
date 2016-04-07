<?php
/**
 * 文章控制器
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class article_control extends admin_control{
	//hook admin_article_control_begin.php
	//模块主页
    public function index(){
		//hook admin_article_control_index_begin.php
        $this->display('article/index.htm');
    }

	//文章列表
	public function indexList(){
		//hook admin_article_control_indexList_begin.php
		$cid = R('cid') ? R('cid') : null;
		$keyword = R('keyword');
		isset($keyword) && $keyword = safe_str(urldecode($keyword));
		$this->assign('keyword', $keyword);
		if(isset($keyword) && !empty($keyword)){
			$where = array('title'=>array('LIKE'=>$keyword));
			$count = $this->article->find_count($where);
			$urlstr = '-keyword-'.urlencode($keyword);
		}else if(isset($cid) && !empty($cid)){
			$where = array('cid'=>$cid);
			$categorys = $this->category->read($cid);
			$count = isset($categorys['count']) ? $categorys['count'] : 0;
			$urlstr = '-cid-'.$cid;
		}else {
			$where = array();
			$count = $this->article->find_count();
			$urlstr = '';
		}
		$page = new page();
		$limit = 20;
		$show = $page->pager($count,$limit, 'index.php?u=article-indexList'.$urlstr, true);
		$pbegin = R('page') ? R('page') : 1;
		$arr = $this->article->find_fetch($where,array('nid'=>0),$limit*($pbegin-1),$limit,0);
		$arr = $this->article->list_format($arr);
		//hook admin_article_control_indexList_after.php
		$this->assign_value('limit',$limit);
		$this->assign_value('count',$count);
		$this->assign('page',$show);
		$this->assign('cid',$cid);
		$this->assign('list',$arr);
		$this->display('article/list.htm');
	}

    //异步获得目录树，内容左侧目录列表
    public function ajaxCategoryZtree(){
        $allCat = $this->category->get_all_category();
        $category = data::tree($allCat, "name", "cid", "pid");
		//hook admin_article_control_ajaxCategoryZtree_begin.php
        if ($category){
            foreach ($category as $n => $cat){
                $data = array();
                //过滤掉外部链接栏目
                if ($cat['attribute'] != 3){
                    if($cat['attribute'] == 2){
                        $url = 'index.php?u=article-indexList-cid-'.$cat['cid'];
                    }else{
                        $url = 'javascript:';
                    }
                    $data['id'] = $cat['cid'];
                    $data['pId'] = $cat['pid'];
                    $data['url'] = $url;
                    $data['target'] = 'content';
                    $data['open'] = true;
                    if ($cat['attribute'] == 1){
                        $data['name'] = $cat['name'] . '(封)';
                    }else{
                        $data['name'] = $cat['name'];
                    }
                    $category_new[] = $data;
                }
            }
        }
		//hook admin_article_control_ajaxCategoryZtree_after.php
        $this->ajax($category_new);
    }


    //文章添加
	public function add(){
		//hook admin_article_control_add_begin.php
		if(IS_POST){
            //hook admin_article_control_add_post_begin.php
            $content = R('content','P');
			$data['thumb'] = R('thumb','P')?ltrim(R('thumb','P'),'/'):null;
			$flag = R('flag','P');
			$data['flag'] = arr_to_string($flag);
			$data['title'] = trim(R('title','P'));
			$data['color'] = trim(R('color','P'));
			$data['new_window'] = (int)R('new_window','P');
			$data['cid'] = (int)R('cid','P');
			$data['isremote'] = (int)R('isremote','P');
			$data['keywords'] = trim(R('keywords','P'));
			$data['description'] = $this->auto_intro(trim(R('description','P')), $content);
			$data['redirecturl'] = trim(R('redirecturl','P'));
			$data['source'] = trim(R('source','P'));
			$data['addtime'] = strtotime(R('addtime','P'));
			$data['click'] = (int)R('click','P');
			$data['status'] = (int)R('status','P');
            $gallery_arr = R('gallery','P')?R('gallery','P'):null;
			!empty($gallery_arr) && count($gallery_arr)>0 ? $data['tuji']  = 1: $data['tuji']  = 0;
			empty($data['title']) && E(0, '亲，标题忘了填哦');
			empty($data['cid']) && E(0, '亲，您没有选择分类哦');
			if(strlen($content) < 5) E(0, '亲，内容字数太少了哦');
			$categorys = $this->category->read($data['cid']);
			if(empty($categorys)) E(0, '分类ID不存在！');
			$this->category->is_list($data['cid']) == false && E(0, '不可以发布到当前栏目,请重新选择栏目');
            $cookie_user = $this->get_cookie_admin(); //获取cookie
			$data['editor_uid'] = $cookie_user[0];
			$editusername = R('editor_username','P');
			if(isset($editusername) && !empty($editusername)){
				$data['editor_username'] = $editusername;
			}else{
				$data['editor_username'] = $cookie_user[1];
			}
			//TAG标签
			$tags = trim(R('tag', 'P'), ", \t\n\r\0\x0B");
			$tags_arr = explode(',', $tags);
			$tagdatas = $tags = array();
			for($i=0; isset($tags_arr[$i]) && $i<count($tags_arr); $i++){
				$name = trim($tags_arr[$i]);
				if($name){
					$tagdata = $this->tag->find_fetch(array('name'=>$name), array(), 0, 1);
					if($tagdata){
						$tagdata = current($tagdata);
					}else{
						$tagid = $this->tag->create(array('name'=>$name, 'count'=>0, 'content'=>''));
						if(!$tagid) E(0, '写入TAG标签表出错');
						$tagdata = $this->tag->get($tagid);
					}
					$tagdata['count']++;
					$tagdatas[] = $tagdata;
					$tags[$tagdata['tagid']] = $tagdata['name'];
				}
			}
			$data['tags'] =  _json_encode($tags);
			if($nid = $this->article->create($data)){ //写入文档主表
				$data_content['nid'] = $nid;
				$data_content['content'] = $content;
				$this->article_content->create($data_content); //写入文章内容表
				//把nid反更新到文档附件表
				 if(!empty($gallery_arr) && count($gallery_arr)>0){
					foreach($gallery_arr as $gallery){
						$data_attachment['id'] = $gallery;
						$data_attachment['nid'] = $nid;
						$this->article_attachment->update($data_attachment);
					}
				}
			}
			//写入标签和标签关系
			foreach($tagdatas as $tagdata){
				$this->tag->update($tagdata);
				$this->tag_relation->set(array($tagdata['tagid'], $nid), array('id'=>$nid));
			}
			//更新分类内容数
			$categorys['count']++;
			$this->category->update($categorys);
			$this->category->update_cache($data['cid']);
			//写入扩展字段
			$this->article_field->addArtField($nid,$_POST);
			//hook admin_article_control_add_post_after.php
			E(1, '添加成功');
		}
		$cat = $this->category->get_all_ok_category();
		$list = data::tree($cat, "name", "cid", "pid");
		//hook admin_article_control_add_after.php
		$this->assign('list',$list);
		$this->display('article/add.htm');
	}

	//文章编辑
	public function edit(){
		//hook admin_article_control_edit_begin.php
		if(IS_POST){
			$nid = (int)R('nid','P'); //文章id
			$data['nid'] = $nid;
			$data = $this->article->get($nid);
            $cid = $data['cid'];
			if(empty($data)) E(0, '文档不存在');
			//hook admin_article_control_edit_post_begin.php
			$content = R('content','P');
			$data['thumb'] = R('thumb','P')?ltrim(R('thumb','P'),'/'):null;
			if($data['thumb'] == '/static/images/upload_pic.png'){
				$data['thumb'] = null;
			}
			$flag = R('flag','P');
			$data['flag'] = arr_to_string($flag);
			$data['title'] = trim(R('title','P'));
			$data['color'] = trim(R('color','P'));
			$data['new_window'] = (int)R('new_window','P');
			$data['cid'] = (int)R('cid','P');
			$data['keywords'] = trim(R('keywords','P'));
			$data['description'] = $this->auto_intro(trim(R('description','P')), $content);
			$data['redirecturl'] = trim(R('redirecturl','P'));
			$data['source'] = trim(R('source','P'));
			$data['updatetime'] = time();
			$data['click'] = (int)R('click','P');
			$data['status'] = (int)R('status','P');
			$gallery_arr = R('gallery','P')?R('gallery','P'):null;
			!empty($gallery_arr) && count($gallery_arr)>0 ? $data['tuji']  = 1: $data['tuji']  = 0;
			$this->category->is_list($cid) == false && E(0, '不可以发布到当前栏目,请重新选择栏目');
			empty($data['title']) && E(0, '亲，标题忘了填哦');
			empty($cid) && E(0, '亲，您没有选择分类哦');
			if(strlen($content) < 5) E(0, '亲，内容字数太少了哦');
			$categorys = $this->category->read($cid);
			if(empty($categorys)) E(0, '分类ID不存在');
			$editusername = R('editor_username','P');
            $cookie_user = $this->get_cookie_admin();
			if(isset($editusername) && !empty($editusername)){
				$data['editor_username'] = $editusername;
			}else{
				$data['editor_username'] = $cookie_user[1];
			}
			//比较标签变化
			$tags = trim(R('tag', 'P'), ", \t\n\r\0\x0B");
			$tags_new = explode(',', $tags);
			$tags_old = (array)_json_decode($data['tags']);
			$tags_arr = $tags = array();
			foreach($tags_new as $tagname){
				$key = array_search($tagname, $tags_old);
				if($key === false){
					$tags_arr[] = $tagname;
				}else{
					$tags[$key] = $tagname;
					unset($tags_old[$key]);
				}
			}
			//标签
			$tagdatas = array();
			for($i=0; isset($tags_arr[$i]) && $i<count($tags_arr); $i++){
				$name = trim($tags_arr[$i]);
				if($name){
					$tagdata = $this->tag->find_fetch(array('name'=>$name), array(), 0, 1);
					if($tagdata){
						$tagdata = current($tagdata);
					}else{
						$tagid = $this->tag->create(array('name'=>$name, 'count'=>0, 'content'=>''));
						if(!$tagid) E(0, '写入标签表出错');
						$tagdata = $this->tag->get($tagid);
					}
					$tagdata['count']++;
					$tagdatas[] = $tagdata;
					$tags[$tagdata['tagid']] = $tagdata['name'];
				}
			}
			$data['tags'] =  _json_encode($tags);
			if($this->article->update($data)){
				$data_content['nid'] = $nid;
				$data_content['content'] = $content;
				$this->article_content->find_update(array('nid'=>$nid),$data_content);
			}
			//写入内容标签表
			foreach($tagdatas as $tagdata){
				$this->tag->update($tagdata);
				$this->tag_relation->set(array($tagdata['tagid'], $nid), array('id'=>$nid));
			}
			//删除不用的标签
			foreach($tags_old as $tagid => $tagname){
				$tagdata = $this->tag->get($tagid);
				$tagdata['count']--;
				$this->tag->update($tagdata);
				$this->tag_relation->delete($tagid, $nid);
			}
			//比较图集的变化
			 $gallery_old = $this->article_attachment->find_fetch(array('nid'=>$nid)); //图集附件表中的图片
			 $gallery_old_attachment = array();
			 if(!empty($gallery_old)){
				//如果数据库中存在图集附件
				foreach($gallery_old as $gallery_old_v){
					$gallery_old_attachment[] = $gallery_old_v['id'];
				}
			 }
			 if(!empty($gallery_arr)){
				$intersect = array_intersect($gallery_old_attachment,$gallery_arr); //交集
				$new_gallery = array_diff($gallery_arr,$intersect); //不同
				if(!empty($new_gallery)){
					foreach($new_gallery as $new_gallery_v){
						$data_attachment_new['id'] = $new_gallery_v;
						$data_attachment_new['nid'] = $nid;
						$this->article_attachment->update($data_attachment_new);
					}
				}
			 }
			//如果分类ID发生变化，更新分类内容数
			if($data['cid'] != $cid){
				// 旧的分类内容数减1
				$categorys_old = $this->category->read($data['cid']);
				$categorys_old['count'] = max(0, $categorys_old['count']-1);
				$this->category->update($categorys_old);
				$this->category->update_cache($data['cid']);
				//新的分类内容数加1
				$categorys['count']++;
				$this->category->update($categorys);
				$this->category->update_cache($cid);
			}
			//更新扩展字段
			$this->article_field->updateArtField($nid,$_POST);
			//hook admin_article_control_edit_post_after.php
			E(1, '编辑成功');
		}
        $nid = (int) R('nid');
        $data = $this->article->read($nid);
        $data_content = $this->article_content->get_content_by_id($nid);
        $data = array_merge($data,$data_content);
        $data['content'] = htmlspecialchars($data['content']);
        $data['tags'] = implode(',', (array)_json_decode($data['tags']));
        $data['thumb'] = $data['thumb'];
        $data['flag'] = explode(',', $data['flag']);
        $data['addtime'] = date('Y-m-d H:i:s', $data['addtime']);
        $data['images'] = $this->article_attachment->get_attachment_by_id($nid);
        $data['field'] = $this->article_field->getfieldById($nid,$data['cid']);
        $this->assign('data', $data);//print_r($data);
        $cat = $this->category->get_all_ok_category();
        $list = data::tree($cat, "name", "cid", "pid");
        //hook admin_article_control_edit_after.php
        $this->assign('list',$list);
        $this->display('article/edit.htm');
	}

	//删除文章
	public function del(){
		$id = (int) R('nid', 'P');
		empty($id) && E(0, '文章ID不能为空');
        // hook admin_article_control_del_before.php
		$rs = $this->article->xdelete($id);
		// hook admin_article_control_del_after.php
		$rs ? E(0, $rs) : E(1, '删除成功');
	}

	//批量删除
	public function batchDel(){
		$nid = R('nid', 'P');
		empty($nid) && E(0, '请选择文章');
        // hook admin_article_control_batchDel_before.php
		foreach ($nid as $id){
			$this->article->xdelete($id);
		}
		// hook admin_article_control_batchDel_after.php
		E(1, '删除成功');
	}

	//移动文章
	public function move(){
		// hook admin_article_control_move_before.php
		if(IS_POST){
			// hook admin_article_control_move_post_begin.php
			$cid = (int)R('cid','P');
            $to_cid = (int)R('to_cid','P');
			$nid = trim(R('nid','P'));
			$nid = explode(",", $nid);
		    if ($nid && is_array($nid)){
		    		$flag_num = 1;
		    		$flag = '';
		        foreach ($nid as $id){
		            if (is_numeric($id)){
		                if(!$this->article->find_update(array("nid" => $id),array("cid" => $to_cid))){
		                		$flag .= $id.',';
							$flag_num = 0;
		                }
		            }
		        }
			// 分类ID发生变化，更新分类内容数
			if($cid != $to_cid){
				$num = count($nid);
				// 旧的分类内容数减1
				$categorys_old = $this->category->read($cid);
				$categorys_old['count'] = max(0, $categorys_old['count']- $num);
				$this->category->update($categorys_old);
				$this->category->update_cache($cid);
				// 新的分类内容数加1
				$categorys = $this->category->read($to_cid);
				$categorys['count'] = $categorys['count'] + $num;
				$this->category->update($categorys);
				$this->category->update_cache($to_cid);
			}
			// hook admin_article_control_move_post_after.php
				$flag .= !empty($flag) ? '文章移动失败' : '文章移动成功';
				E($flag_num,$flag);
		    }
        }
        $nid = trim(R('nid'));
		$cid = (int)(R('cid'));
		$allCat = $this->category->get_all_category();
        $category = data::tree($allCat, "name", "cid", "pid");
        if ($category){
            foreach ($category as $n => $cat){
				if ($cat['attribute'] == 1 || $cat['attribute'] == 3){
					$category[$n]['disabled'] = 'disabled';
				}
				if ($cid == $cat['cid']) {
                    $category[$n]['selected'] = "selected";
                }
            }
        }
		// hook admin_article_control_move_after.php
		$this->assign('nid',$nid);
		$this->assign('cid',$cid);
		$this->assign('list',$category);
		$this->display('article/move.htm');
	}

	//删除图集图片及缩略图
	public function del_attachment(){
		$id = (int) R('id', 'P');
		// hook admin_article_control_del_attachment_begin.php
		empty($id) && E(0, 'ID不能为空');
		$filedata = $this->article_attachment->read($id);
		$file = ROOT_PATH.'upload/'.$filedata['filepath'];
		is_file($file) && unlink($file);
		if($filedata['isimage'] == 1){
			$file_thumb = ROOT_PATH.'upload/'. rtrim($filedata['filepath'],'.'.$filedata['fileext']).'_thumb'.'.jpg';
			is_file($file_thumb) && unlink($file_thumb);
		}
		$rs = $this->article_attachment->delete($id);
		// hook admin_article_control_del_attachment_after.php
		$rs ? E(1, '删除成功') : E(0, '删除失败,请刷新重试');
	}

	//自动生成摘要
	private function auto_intro($intro, &$content){
		// hook admin_article_control_auto_intro_begin.php
		if(empty($intro)){
			$intro = preg_replace('/\s{2,}/', ' ', strip_tags($content));
			return trim(utf8::cutstr_cn($intro, C('auto_intro'), ''));
		}else {
			return str_replace(array("\r\n", "\r", "\n"), '<br />', strip_tags($intro));
		}
	}

	//文章扩展字段
	public function fieldList(){
		$cid = (int)R('cid','P');
		// hook admin_article_control_fieldList_begin.php
		if($cid){
			$list = array();
			$this->field->getFieldsBycid($cid,$list);
			// hook admin_article_control_fieldList_after.php
			echo json_encode(array('res'=>1,'info'=>$list));
			exit;
		}
	}

	//草稿列表页
	public function draft(){
		// hook admin_article_control_draft_begin.php
		$where = array('status'=>0);
		$count = $this->article->find_count($where);
		$urlstr = '';
		$page = new page();
		$limit = 20;
		$show = $page->pager($count,$limit, 'index.php?u=article-draft'.$urlstr, true);
		$pbegin = R('page') ? R('page') : 1;
		$arr = $this->article->find_fetch($where,array('nid'=>0),$limit*($pbegin-1),$limit,0);
		$arr = $this->article->list_format($arr);
		// hook admin_article_control_draft_after.php
		$this->assign_value('count',$count);
		$this->assign('page',$show);
		$this->assign('list',$arr);
		$this->display('article/draft.htm');
	}

	//发布
	public function online(){
		$id = (int) R('nid', 'P');
		// hook admin_article_control_online_begin.php
		empty($id) && E(0, '文章ID不能为空');
		$data['status'] = 1;
		$where = array('nid'=>$id);
		$rs = $this->article->find_update($where,$data);
		// hook admin_article_control_online_after.php
		$rs ? E(1, '发布成功') : E(0, '操作失败');
	}

	//批量发布
	public function batchOnline(){
		$nid = R('nid', 'P');
		// hook admin_article_control_batchOnline_begin.php
		empty($nid) && E(0, '请选择文章');
		foreach ($nid as $id){
			$data['status'] = 1;
			$where = array('nid'=>$id);
			$rs = $this->article->find_update($where,$data);
		}
		// hook admin_article_control_batchOnline_after.php
		E(1, '发布成功');
	}
	// hook admin_article_control_after.php
}