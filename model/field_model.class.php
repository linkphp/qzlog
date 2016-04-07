<?php
/**
 * 扩展字段设计模型
 * @author: caisonglin <595785872@qq.com>
 * @copyright http://www.qzlog.com
 */

defined('LINK_PATH') || exit('Access Denied');
class field extends model{
	// hook field_model_before.php
	private $field_html;

	function __construct(){
		$this->table = 'field';			// 表名
		$this->pri = array('fieldid');	// 主键
		$this->maxid = 'fieldid';		// 自增字段

		//扩展字段
		$this->field_html = array(
			'text' => '$fieldname$ : <input type="text" class="hd-w300" name="$name$" value="$value$" />',
			'file' => '$fieldname$ : <input type="text" class="hd-w300" id="$name$url" name="$name$" value="$value$" readonly="readonly" /> <input type="button" class="hd-btn hd-btn-sm" onclick="$.category.insertfile(\'$name$\',\'file\');" value="选择文件" />',
			'textarea' => '$fieldname$ : <textarea name="$name$" $class$ style="width: 90%">$value$</textarea>',
			'image' => '$fieldname$ : <input type="text" class="hd-w300" id="$name$url" name="$name$" value="$value$" readonly="readonly" /> <input type="button" class="hd-btn hd-btn-sm" onclick="$.category.insertfile(\'$name$\',\'image\');" value="选择图片" />',
		);
	}

	//创建字段
	public function addField($data,&$result){
		if($data){
			$result = $this->create($data);
		}
	}

	/**
	 * 获取字段列表
	 * @parems int $cid 栏目id
	 * return array $list
	**/
	public function getFieldsBycid($cid,&$list){
		if($cid){
			$rs = $this->category_field->find_fetch(array('cid'=>$cid));
			$rs ? $rs = current($rs) : $rs = array();
			if(isset($rs['projectid'])){
				$list = $this->field->find_fetch(array('projectid'=>$rs['projectid']));
				if($list){
					foreach($list as $k => $v){
						$tmp_setting = unserialize($v['setting']);
						$tmp_html = $this->field_html;
						if(isset($tmp_setting['editor'])){
							$tmp_html[$v['field']] = $tmp_setting['editor'] == 1 ? str_replace('$class$','class="editor"',$tmp_html[$v['field']]) : str_replace('$class$','',$tmp_html[$v['field']]);
						}
						$list[$k]['html'] = str_replace(array('$fieldname$','$name$','$value$'),array($tmp_setting['fieldname'],$tmp_setting['var'],$tmp_setting['defaultvalue']),$tmp_html[$v['field']]);
					}
				}
			}
		}
	}

	/**
	 * 获取对应字段名称列表
	 * @param int $cid 栏目id
	 * return array $list
	**/
	public function getVarBycid($cid,&$list){
		$rs = $this->category_field->find_fetch(array('cid'=>$cid));
		$rs && $rs = current($rs);
		$proid = isset($rs['projectid'])?$rs['projectid']:null;
		$arr = $this->field->find_fetch(array('projectid'=>$proid));
		if($arr){
			foreach($arr as $k => $v){
				$tmp_setting = unserialize($v['setting']);
				$list[$k]['var'] = $tmp_setting['var'];
				$list[$k]['value'] = $tmp_setting['defaultvalue'];
				$list[$k]['fieldname'] = $tmp_setting['fieldname'];
				$list[$k]['field'] = $v['field'];
			}
		}
	}
	// hook field_model_after.php
}