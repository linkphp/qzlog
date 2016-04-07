//admin.js
;(function($){
	$.extend({
		'field':{
			'altdialog':function(content,w,h){//alt弹出窗
				var d = dialog({
					title: '新增方案',
					content: content,
					okValue: '确定',
					ok: function () {
						var data = $('#addfield_form').serialize();
						 hd_ajax('index.php?u=extField-addFieldProject', data, 'index.php?u=extField-index');
						this.close();
						return false;
					}
				})
				d.width(w).height(h).showModal();
			},
			'addfield': function(index){

				var data = [{id:'',name:'',description:''}];
				var template = $('#temp').html();
				var _html=_template(template,data);
				$.field.altdialog(_html,400,200);
			},
			'editfield': function(index){
				var id = $(index).parent().parent().find('td').eq(1).html();
				var name = $(index).parent().parent().find('td').eq(2).html();
				var description = $(index).parent().parent().find('td').eq(3).html();

				var data = [{id:id,name:name,description:description}];
				var template = $('#temp').html();
				var _html=_template(template,data);

				var d =dialog({
					title: '编辑方案',
					content: _html,
					okValue: '确定',
					ok: function () {
						 data = $('#addfield_form').serialize();
						 hd_ajax('index.php?u=extField-editFieldProject', data, 'index.php?u=extField-index');
						 this.close();
						 return false;
					}
				})
				d.width(400).height(200).showModal();
				//retrun false;
			},
			'delfield': function(id){
				var d =dialog({
					title: '删除方案',
					content: '您确定要删除吗？',
					okValue: '确定',
					cancelValue: '取消',
					ok: function(){
						hd_ajax('index.php?u=extField-delFieldProject', {id:id}, 'index.php?u=extField-index');
						 this.close();
						 return false;
					},
					cancel:function(){
						this.close();
					}
				})
				d.width(250).height(50).showModal();
			},
			'delfieldall': function(index){
				var type = $(index).closest('form').find("input[type='checkbox']");
				var ids = [];
				type.each(function(i, e){
					if(e.checked == true){
						ids.push(e.value);
					}
				})
				if(ids.length > 0){
					var d = dialog({
						title:'批量删除方案',
						content: '您确定要删除吗？',
						okValue: '确定',
						cancelValue: '取消',
						ok: function(){
							hd_ajax('index.php?u=extField-delAllFieldProject', {ids:ids}, 'index.php?u=extField-index');
						},
						cancel:function(){
							this.close();
						}
					})
					d.width(250).height(50).showModal();
				}else{
					var d = dialog({title: '批量删除方案',content: '请选择要需要删除的方案'});
					d.width(250).height(50).showModal();
				}
			},
			'adddesign':function(field,projectid){

				var data = [{field:field,projectid:projectid,fieldname:'',var:'',defaultvalue:''}];
				var template = $('#temp').html();
				var _html=_template(template,data);
				if(field == 'textarea'){
					var tmp_arr = _html.split('</tbody>');
					var tmp_str = '<tr><td>启用编辑器</td><td><label><input type="radio" name="editor" value="1" />是&nbsp;&nbsp;&nbsp;</label><label><input type="radio" name="editor" value="0" checked="checked" />否</label></td></tr>';
					_html = tmp_arr[0]+tmp_str+tmp_arr[1];
				}
				var d = dialog({
							title: '添加字段',
							content: _html,
							okValue: '确定',
							cancelValue: '取消',
							ok: function(){
								data = $('#adddesign_form').serialize();
								hd_ajax('index.php?u=extField-adddesign', data,'index.php?u=extField-designField-id-'+projectid);
							},
							cancel:function(){
								this.close();
							}
						});
				d.width(300).height(150).showModal();
			},
			'editdesign': function(index,fieldid,projectid){
				var field = $(index).attr('data-field');
				var editor = $(index).attr('data-editor');

				var data = [{
					fieldname:$(index).parent().parent().find('td').eq(0).html(),
					var:$(index).parent().parent().find('td').eq(1).html(),
					defaultvalue:$(index).parent().parent().find('td').eq(3).html()
				}];
				var template = $('#temp').html();
				var _html=_template(template,data);
				var tmp_check1 = '';
				var tmp_check2 = '';
				if(field == 'textarea'){
					editor == 1 ? tmp_check1 = 'checked="checked"' : tmp_check2 = 'checked="checked"';
					var tmp_arr = _html.split('</tbody>');
					var tmp_str = '<tr><td>启用编辑器</td><td><label><input type="radio" name="editor" '+tmp_check1+' value="1" />是&nbsp;&nbsp;&nbsp;</label><label><input type="radio" name="editor" value="0" '+tmp_check2+' />否</label></td></tr>';
					_html = tmp_arr[0]+tmp_str+tmp_arr[1];
				}
				var d = dialog({
							title: '编辑字段',
							content: _html,
							okValue: '确定',
							cancelValue: '取消',
							ok: function(){
								$('#adddesign_form input[name="field"]').remove();
								$('#adddesign_form input[name="projectid"]').remove();
								$('#adddesign_form').append('<input type="hidden" name="fieldid" value="'+fieldid+'" />');
								data = $('#adddesign_form').serialize();
								hd_ajax('index.php?u=extField-editdesign', data,'index.php?u=extField-designField-id-'+projectid);
							},
							cancel:function(){
								this.close();
							}
						});
				d.width(300).height(150).showModal();
			},
			'deldesign':function(id,projectid){
				var d =dialog({
					title: '删除字段',
					content: '您确定要删除吗？',
					okValue: '确定',
					cancelValue: '取消',
					ok: function(){
						hd_ajax('index.php?u=extField-deldesign', {id:id}, 'index.php?u=extField-designField-id-'+projectid);
						 this.close();
						 return false;
					},
					cancel:function(){
						this.close();
					}
				})
				d.width(250).height(50).showModal();
			}
		}
	})
})(jQuery)

function _template(template, data){
    var i = 0,
        len = data.length,
        fragment = '';
    function replace(obj){
        var t, key, reg;
        for(key in obj){
            reg = new RegExp('{{' + key + '}}', 'ig');
            t = (t || template).replace(reg, obj[key]);
        }
        return t;
    }
    for(; i < len; i++){
        fragment += replace(data[i]);
    }
    return fragment;
}