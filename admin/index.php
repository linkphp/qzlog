<?php
/**
 * 后台主入口文件
 * @author: link <612012@qq.com>
 * @copyright 轻舟CMS(QZLOG) http://www.qzlog.com
 */

define('DEBUG', 2); //0 关闭调试; 1 开启调试; 2 开发调试
define('APP_NAME', 'admin'); //后台名称
define('F_APP_NAME', 'qzlog'); //前台名称
define('ADM_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/'); //后台目录
define('CONTROL_PATH', ADM_PATH.'control/'); //后台控制器目录
define('ROOT_PATH', dirname(ADM_PATH).'/'); //根目录
define('CONFIG_PATH', ROOT_PATH.'config/'); //配置目录
define('RUNTIME_PATH', ROOT_PATH.'runtime/'); //运行缓存目录
define('LOG_PATH', RUNTIME_PATH.'log/'); //日志目录
define('MODEL_PATH', ROOT_PATH.'model/'); //模型目录
define('APP_PATH', ROOT_PATH.F_APP_NAME.'/'); //前台APP目录
define('PLUGIN_PATH', ROOT_PATH.'plugin/'); //插件目录
define('VIEW_PATH', ADM_PATH.'theme/'); //后台模板目录
define('LINK_PATH', ROOT_PATH.'linkphp/'); //系统框架目录
require LINK_PATH.'linkphp.php'; //引入框架主入口文件