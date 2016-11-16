<!doctype html>
<html>
<head>
    <title>系统提示-<?php echo $config["site_fullname"]; ?></title>
    <?php $this->load->view(__TEMPLET_FOLDER__ . '/headerinc.php'); ?>
    <script src="/home/views/static/js/jquery-1.8.1.min.js"></script>
    <script src="/home/views/static/js/validate/validator.js"></script>
    <script src="/home/views/static/js/laydate/laydate.js"></script>
    <script type="text/javascript" src="/home/views/static/js/layer/layer.js"></script>
</head>
<body>
<?php $this->load->view(__TEMPLET_FOLDER__ . '/header.php'); ?>


<div class="pc_list">

    <div style="text-align:center">


        <div style="text-align:center;">
            <?php
            if ($isok == 1) {
                echo "<img src='/home/views/static/images/ok.png'/>";
            } else {
                echo "<img src='/home/views/static/images/err.png'/>";
            }
            ?>
        </div>
        <?php echo $message; ?>
        <?php
        if ($url != "") {
            echo "<div class='pg10'><a href='{$url}'>还剩<span id='timeleft'></span>秒返 回</a></div>";
        }
        ?>


    </div>
</div>
<?php $this->load->view(__TEMPLET_FOLDER__ . '/footer.php'); ?>
<?php if ($url != "") { ?>
    <script type="text/javascript">
        //跳转的url
        var url = '<?php echo $url;?>';
        //多少秒跳转
        var miao = '<?php echo $miao;?>';
        //显示秒的控件
        var $miao = $('#timeleft');

        $miao.text(miao);
        setInterval(function () {
            miao--;
            if (miao>=0) {
                $miao.text(miao);
            }
            if (miao == 0) {
                window.location.href = url;
            }
        }, 1000);
    </script>
<?php } ?>
</body>
</html>