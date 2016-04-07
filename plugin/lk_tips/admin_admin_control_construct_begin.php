<?php
defined('LINK_PATH') or exit;
$tips = $this->kv->xget('tips',3600);
$i = mt_rand(0, count($tips) - 1);
$tip = $tips[$i];
$this->assign_value('tip',$tip);
?>