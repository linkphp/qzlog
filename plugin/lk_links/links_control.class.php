<?php
/**
 * 友情链接插件
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class links_control extends admin_control{

	public function index(){
		$links = $this->kv->xget('lk_links');
		$this->assign('links', $links);
		$this->display();
	}

	// 增加
	public function add(){
		$name = htmlspecialchars(R('name', 'P'));
		$url = htmlspecialchars(R('url', 'P'));
		$num = (int)R('num', 'P');

		!$name && E(0, '网站名称不能为空');
		!$url && E(0, '网站 URL不能为空');

		$arr = $this->kv->xget('lk_links');
		$row = array('name' => $name, 'url' => $url, 'num' => $num);

		$arr[] = $row;
		$this->kv->set('lk_links', $arr);
		end($arr);
		$key = key($arr);
		E(1, '添加成功', $key);
	}

	// 删除链接
	public function del(){
		$key = (int) R('key', 'P');
		$arr = $this->kv->xget('lk_links');
		unset($arr[$key]);
		$this->kv->set('lk_links', $arr);
		E(1, '删除完成');
	}

	// 编辑
	public function edit(){
		$num = (int)R('num', 'P');
		$key = (int)R('key', 'P');
		$name = htmlspecialchars(R('name', 'P'));
		$url = htmlspecialchars(R('url', 'P'));

		$row = array('name' => $name, 'url' => $url, 'num' => $num);
		$arr = $this->kv->xget('lk_links');

		$arr[$key] = $row;
		$this->kv->set('lk_links', $arr);
		E(1, '修改成功');
	}
}
