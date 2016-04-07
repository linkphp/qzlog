<?php
/**
 * 小贴士插件 插件demo
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class tips_control extends admin_control{
	//添加
	public function index(){
		if(IS_POST){
			$tip = trim(safe_str(R('tip','P')));
            $tip == '' && E(0,'不得为空');
			$arr = $this->kv->xget('tips');
			$arr[] = $tip;
			$rs = $this->kv->set('tips', $arr);
			$this->runtime->delete('tips');
            $rs ? E(1,'添加成功') : E(0,'添加失败');
		}
        $this->display();
	}
}