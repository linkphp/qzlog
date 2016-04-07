;(function($){
    $.extend({
        'category':{
            'extfield':function(cid){
                var extfield = $('.content_left table').find('tr:last th').text();
                if(!cid) return false;
                $.ajax({
                    type:'post',
                    url: 'index.php?u=article-fieldList',
                    data: {'cid':cid},
                    dataType:'json',
                    success:function(data){
                         if(data.res == 1){
                            var da = eval(data.info);
                            var _html = '<tr><th>扩展字段</th><td>';
                            var tmp_editor = false;;
                            $.each(da,function(i,ob){
                                if(ob.html.indexOf('class="editor"') > 0){
                                    tmp_editor = true;
                                }
                                _html += ob.html+'<br/>';
                            })
                            _html += '</td></tr>';
                            if(extfield == '扩展字段'){
                                $('.content_left table').find('tr:last').remove();
                            }
                            $('.content_left table').append(_html);
                            if(tmp_editor) createdEditor('editor');
                         }
                    }
                })
            },
            'insertfile':function(name,type){
                //type为类型
                switch(type){
                    case 'image' : createdImage(name);break;
                    case 'file' : createdFile(name);break;
                }
            }
        }
    })
})(jQuery)

/**
 * 创建一个简易编辑器
 **/
function createdEditor(classs){
    var editor = KindEditor.create('.'+classs, {
    	uploadJson : 'index.php?u=upload-index',
        resizeType : 1,
        allowPreviewEmoticons : false,
        allowImageUpload : true,
        items : [
            'source', '|','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
            'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
            'insertunorderedlist', '|', 'emoticons', 'image', 'link'],
        afterBlur:function(){
            this.sync();
        }
    });
}

/**
 * 创建上传文件框
 **/
 function createdFile(name){
    var editor = KindEditor.create('.editor',{
                    uploadJson : 'index.php?u=upload-index',
                    allowFileUpload : true
                });
    editor.loadPlugin('insertfile', function(){
        editor.plugin.fileDialog({
            fileUrl : KindEditor('#'+name+'url').val(),
            clickFn : function(url, title) {
                KindEditor('#'+name+'url').val(url);
                editor.hideDialog();
            }
        });
    });
 }

 /**
  * 创建上传图片框
  **/
  function createdImage(name){
    var editor = KindEditor.create('.editor',{
                    uploadJson : 'index.php?u=upload-index',
                    allowImageUpload : true
                });
    editor.loadPlugin('image', function(){
        editor.plugin.imageDialog({
            imageUrl : KindEditor('#'+name+'url').val(),
            clickFn : function(url, title) {
                KindEditor('#'+name+'url').val(url);
                editor.hideDialog();
            }
        });
    });
  }

/**
 * 移除缩略图
 */
function removeThumb(){
    $("#thumbImg").attr('src', '../static/images/upload_pic.png');
    $("input[name='thumb']").val('');
}

/**
 * 选择颜色
 * @param obj 颜色选择对象，按钮对象
 * @param _input 颜色name=color表单
 */
function selectColor(obj, _input){
    if ($("div.colors").length == 0) {
        var _div = "<div class='colors' style='width:80px;height:160px;position: absolute;z-index:999;'>";
        //颜色块
        var colors = ["#f00f00", "#272964", "#4C4952", "#74C0C0", "#3B111B", "#147ABC", "#666B7F", "#A95026", "#7F8150", "#F09A21", "#7587AD", "#231012", "#DE745C", "#ED2F8D", "#B57E3E", "#002D7E", "#F27F00", "#B74589"];
        for (var i = 0; i < 16; i++) {
            _div += "<div color='" + colors[i] + "' style='background:" + colors[i] + ";width:20px;height:20px;float:left;cursor:pointer;'></div>"
        }
        _div += "</div>";
        $("body").append(_div);
        $(".colors").css({
            top: $(obj).offset().top + 30,
            left: $(obj).offset().left
        });
    }

    $("div.colors").show();
    $("div.colors div").click(function () {
        $("div.colors").hide();
        var _c = $(this).attr("color");
        $("[name='" + _input + "']").val(_c);
        $("[name='title']").css({
            color: _c
        });
    })
}

/**
 * 标题颜色
 */
function update_title_color() {
    var title = $("[name='title']").css({
        "color": $("[name='color']").val()
    });
}

/**
 * 中文逗号变英文逗号
 */
function ReplaceDot(obj) {
	var oldValue=obj.value;
	while(oldValue.indexOf("，")!=-1)//寻找每一个中文逗号，并替换
	{
		obj.value=oldValue.replace("，",",");
		oldValue=obj.value;
	}
	obj.value = oldValue;
}

/**
 * 编辑文章时更改标题颜色
 */
$(function () {
    //更改颜色文本框时
    $("[name='color']").blur(function () {
        update_title_color();
    })
    update_title_color();
})