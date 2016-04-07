<?php defined('LINK_PATH') || exit; ?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="/static/css/success_error.css"/>
    <title>QZLOG 操作提示信息</title>
</head>

<div class="wrap">
    <div class="title">
        QZLOG 提示信息！
    </div>
    <div class="content">
        <div class="icon"></div>
        <div class="message">
            <p>
                <?php echo $message;?>
            </p>
            <a id="jump" href="javascript:<?php echo $jumpurl == 'history.back()' ? 'history.back()' : 'location.href = "'.$jumpurl.'"';?>;" class="hd-cancel">
                返回
            </a>
        </div>
    </div>
</div>
<?php if($jumpurl != -1) { ?>
    <script type="text/javascript">
        var dot = '', t;
        var jump = document.getElementById("jump");
        var time = <?php echo $delay;?>;
        function jumpurl(){
            <?php echo $jumpurl == 'history.back()' ? 'history.back()' : 'location.href = "'.$jumpurl.'"';?>;
        }
        function display(){
            dot += '.';
            if(dot.length > 6) dot = '.';
            jump.innerHTML = (time--) + '秒后自动跳转' + dot + ' <a href="javascript:jumpurl();">立即跳转</a>';
            if(time == -1) {
                clearInterval(t);
                jumpurl();
            }
        }
        display();
        t = setInterval(display, 1000);
    </script>
<?php } ?>
</body>
</html>

