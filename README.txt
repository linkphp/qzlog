轻舟CMS(QZLOG)，基于PHP+MySQL的技术架构。

QZLOG定位于高安全、高性能、高扩展、高SEO、高傻瓜化。

QZLOG2.0目录结构
	|--admin					管理后台
		|--control				后台控制器
		|--theme				后台模板
		| index.php				后台入口
	|--config					配置文件
	|--install					安装程序
	|--model					数据库模型
	|--plugin					插件目录
	|--runtime					运行缓存
		|--log					日志文件
	|--theme					前台模板
	|--static					静态文件
		|--cal					日历
		|--css					CSS
		|--hdjs					HDJS框架
		|--images				图片
		|--js					JS
		|--Kindeditor编辑器	 	Kindeditor编辑器
		|--ztree				目录树插件
	|--qzlog					前台核心
		|--block				模板调用标签
		|--control				前台控制器
	|--linkphp					系统框架
	|--upload					上传目录
	| .htaccess					apache伪静态文件
	| index.php					主入口文件

QZLOG2.0简易模板引擎(共8个标签) 更多详细标签请查看后台模板标签手册
1，{inc:header.htm}					包含模板
2，{hook:header_before.htm}			模板钩子(方便插件修改模板)
3，{php}{/php}						模板支持PHP代码 (不支持<??><?php?>的写法)
4，{block:}{/block}					模板模块
5，{loop:}{/loop}					数组遍历
6，{if:} {else} {elseif:} {/if}	逻辑判断
7，{$变量}							显示变量
8，{@$k+1}							显示逻辑变量 (用于运算时的输出，一般用的很少)
