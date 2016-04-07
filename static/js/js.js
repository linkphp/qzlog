/**
 * 页面加载完后点击顶部第一个菜单
 */
$(function () {
    $(".t-l-menu a:eq(0)").trigger('click');
    $(".leftMenuBlock:visible").find('a').eq(0).trigger('click');
})
/**
 * 显示左侧菜单块
 * @param nid 一级菜单id
 */
function topMenu(obj,nid) {
    //改变样式
    $('.t-l-menu a').removeClass('active');
    $(obj).addClass('active');
    //隐藏左侧菜单块
    $(".leftMenuBlock").hide();
    //显示当前nid的菜单块
    $("#" + nid).show().find('a').eq(0).trigger("click");
}
/**
 * 调用动作
 * @param obj a对象
 * @param url 链接
 * @returns {boolean}
 */
function runAction(obj, url, nid) {
    /**
     * 移除所有链接标签点击后的样式属性
     */
    $(".leftMenuBlock").find('a').removeClass('active');
    //为当前链接加上active类更改背景颜色
    $(obj).addClass('active');
    //设置iframe标签src属性，显示链接内容
    $("#frame").attr('src', url);
    /**
     * 添加到历史导航
     */
    if ($("#historyMenuList a[nid='" + nid + "']").length) {
        /**
         * 已经存在菜单
         */
        $("#historyMenuList a[nid='" + nid + "']").trigger('click');
    } else {
        /**
         * 不存在菜单时添加菜单
         */
        $("#historyMenuList a").removeClass('active');
        var a = "<li><a href='javascript:;' onclick=\"historyMenu(this,'" + url + "')\" class='active' nid='" + nid + "'>"
            + $(obj).html() + "</a><span class='close' onclick='closeHistoryMenu(this)'>x</span></li>";
        $("#historyMenuList ul").prepend(a);
        $("#historyMenuList").offset({left: 161});
        var w =$("#historyMenuList li").length*161;
        $("#historyMenuList").css({width:w});
    }

    return true;
}
//-------------------------------------------------历史导航----------------------------------------------------
$(function () {
    //历史导航向左滚动
    $("#leftBtn").click(function () {
        //导航链接父级块
        var obj = $("#historyMenuList");
        //当前滚动左侧位置
        var left = obj.offset().left;
        //减少一个块的宽度
        obj.offset({left: left - 125});
    })
    //历史导航向右滚动
    $("#rightBtn").click(function () {
        //导航链接父级块
        var obj = $("#historyMenuList");
        //当前滚动左侧位置
        var left = obj.offset().left;
        //增加一个块的宽度
        obj.offset({left: left + 125});
    })

})
/**
 * 关闭历史导航
 */
function closeHistoryMenu(obj) {
    $(obj).parent().remove();
    return false;
}
/**
 * 历史导航菜单点击
 */
function historyMenu(obj, url) {
    $("#historyMenuList a").removeClass('active');
    $(obj).addClass('active');
    $('#frame').attr('src', url);
}



























