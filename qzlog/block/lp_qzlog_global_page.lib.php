<?php
/**
 * 单页/频道封面模块
 * @author: link <612012@qq.com>
 * @copyright http://www.qzlog.com
 * @param int life 缓存时间/秒 默认不缓存
 * @return array
 */

defined('LINK_PATH') || exit('Access Denied');
function lp_qzlog_global_page($conf){
    global $run;
    // hook lp_qzlog_global_page_before.php
    $life = empty($conf['life']) ? 0 : max(1, (int)$conf['life']);
    $arr = $run->category->read($run->_var['cid'],$life);
    // hook lp_qzlog_global_page_after.php
    return $arr;
}