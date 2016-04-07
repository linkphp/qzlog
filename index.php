<?php
/**
 * 前台主入口文件
 * @author: link <612012@qq.com>
 * @copyright 轻舟CMS(QZLOG) http://www.qzlog.com
 */

define('DEBUG', 2); //0 关闭调试; 1 开启调试; 2 开发调试 上线后建议关闭
define('APP_NAME', 'qzlog'); //APP名称
define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/'); //前台根目录
if(!is_file(ROOT_PATH.'config/config.inc.php')){ //如未安装，执行安装
    exit('<html><body><script>location="'.ROOT_PATH.'/install/'.'"</script></body></html>');
}
define('APP_PATH', ROOT_PATH.APP_NAME.'/'); //APP目录
define('CONFIG_PATH', ROOT_PATH.'config/'); //配置目录
define('MODEL_PATH', ROOT_PATH.'model/'); //模型目录
define('VIEW_PATH', ROOT_PATH.'theme/'); //视图目录
define('PLUGIN_PATH', ROOT_PATH.'plugin/'); //插件目录
define('RUNTIME_PATH', ROOT_PATH.'runtime/'); //运行缓存目录
define('LOG_PATH', RUNTIME_PATH.'log/'); //日志目录
define('LINK_PATH', ROOT_PATH.'linkphp/'); //系统框架目录
require LINK_PATH .'linkphp.php'; //引入框架主入口文件