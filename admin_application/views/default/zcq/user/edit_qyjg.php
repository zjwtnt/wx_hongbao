<?php
/**
 * 2016年8月3日09:19:02
 * 编辑企业或者服务机构的页面
 */
if (!defined('BASEPATH')) {
    exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>用户编辑__<?php echo $model['username']; ?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/Css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/Css/bootstrap-responsive.css"/>
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/Css/style.css"/>

    <!--    <script src="//cdn.bootcss.com/jquery/1.9.1/jquery.min.js"></script>-->
    <script type="text/javascript"
            src="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
    <!--script type="text/javascript" src="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/assets/js/bui-min.js"></script-->
    <script type="text/javascript"
            src="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/Js/validate/validator.js"></script>
    <script src="/home/views/static/js/layer/layer.js?v=2.1"></script>
    <script type="text/javascript" src="/home/views/static/js/uploadfile/jquery.uploadify-3.1.min.js"></script>

    <link rel="stylesheet" type="text/css" href="/home/views/static/js/uploadfile/uploadify.css"/>
    <link href="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/assets/css/dpl-min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/assets/css/bui-min.css" rel="stylesheet"
          type="text/css"/>
    <link href="/admin_application/views/static/Js/mailAutoComplete/mailAutoComplete.css" rel="stylesheet"
          type="text/css">
    <style type="text/css">
        body {
            padding-bottom: 40px;
        }

        .sidebar-nav {
            padding: 9px 0;
        }

        @media (max-width: 980px) {
            /* Enable use of floated navbar text */
            .navbar-text.pull-right {
                float: none;
                padding-left: 5px;
                padding-right: 5px;
            }
        }

        .img_ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .img_ul li {
            list-style: none;
            padding: 0;
            float: left;
            margin: 0 0 0 2px;
        }

        .clear {
            clear: both;
        }

        #myform .tableleft {
            width: 135px;
        }

        .td_label1 {
            float: left;
            margin-right: 5px;
        }
    </style>
</head>
<body class="definewidth">
<!--<div class="form-inline definewidth m20">-->
<!---->
<!---->
<!--</div>-->
<form enctype="multipart/form-data" action="<?php echo site_url("user/edit"); ?>" onsubmit="return postform()"
      method="post" name="myform" id="myform">
    <input type="hidden" name="action" value="doedit">
    <input type="hidden" name="id" value="<?php echo $model['uid']; ?>">

    <table width="99%" bgcolor="#FFFFFF" border="0" cellpadding="3" cellspacing="1"
           class="table table-bordered table-hover definewidth">
        <!-- 单位属于：	企业用户 机构用户-->
        <tr>
            <td width="100" class="tableleft">
                *单位属于：
            </td>
            <td>
                <?php
                foreach ($usertype_list as $item) {
                    $selected = $item["id"] == $model["usertype"] ? "checked" : "";
                    echo "<label class='td_label1'>";
                    echo "<input type='radio' name='mysql_usertype' value='{$item['id']}' {$selected}> {$item['name']}";
                    echo "</label>";
                };
                ?>
            </td>
            <td></td>
        </tr>
        <!--1.企业全称-->
        <tr>
            <td width="100" class="tableleft">
                <span id="fullname"><?php echo $usertype_name; ?></span>全称
            </td>
            <td>
                <input type="text" name="mysql_company" value="<?php echo $model['company']; ?>"
                       minLength="2" valtype="my_require"
                       style="width:400px;">
            </td>
            <td></td>
        </tr>
        <!--2.企业简称-->
        <tr>
            <td width="100" class="tableleft">
                <span id="Abbreviation">
                </span>简称
            </td>
            <td>
                <input type="text" name="mysql_username" value="<?php echo $model['username']; ?>"
                       minLength="2" valtype="my_require"
                       style="width:400px;">
            </td>
            <td></td>
        </tr>
        <!-- 3.密码-->
        <tr>
            <td class="tableleft">*密　码：</td>
            <td><input name="pwd" type="password" id="pwd" placeholder="长度6位到18位，字母和数字组合" style="width:400px;"
                       value=""
                       autocomplete="off" size="36" valtype="mm">
            </td>
            <td>6-18位字母数字组合</td>
        </tr>
        <tr>
            <td class="tableleft">*确认密码：</td>
            <td><input name="pwd2" type="password" id="pwd2" placeholder="长度6位到18位，字母和数字组合" style="width:400px;"
                       value=""
                       autocomplete="off" size="36" valtype="mm">
            </td>
            <td>6-18位字母数字组合</td>
        </tr>
        <!--4.工商注册地址-->
        <tr id="gongshang_addr">
            <td class="tableleft">工商注册地址：</td>
            <td>
                <input type="text" id="mysql_addr" name="mysql_addr" placeholder="工商注册地址"
                       value="<?php echo $model["addr"]; ?>"
                       minLength="2" valtype="my_require" style="width:400px;">
            </td>
            <td></td>
        </tr>
        <!--5.社会信用代码(18位码)-->
        <tr>
            <td class="tableleft">社会信用代码(18位码)</td>
            <td>
                <input type="text" name="mysql_xinyongzheng" valtype="int" minlength="18" maxlength="18"
                       value="<?php echo $model['xinyongzheng']; ?>"
                       style="width:400px;">
            </td>
            <td></td>
        </tr>
        <!--6.组织机构代码(9位码)-->
        <tr>
            <td class="tableleft">组织机构代码(9位码)</td>
            <td>
                <input type="text" name="mysql_zuzhijigou" valtype="int" minlength="9" maxlength="9"
                       value="<?php echo $model['zuzhijigou']; ?>"
                       style="width:400px;">
            </td>
            <td></td>
        </tr>
        <!--7.联系人-->
        <tr>
            <td class="tableleft"><span>*</span>联系人：</td>
            <td><input type="text" name="mysql_realname" required size="10" maxlength="50" placeholder="输入姓名"
                       style="width:400px;"
                       valtype="my_require"
                       value="<?php echo $model["realname"]; ?>"/>
            </td>
            <td></td>
        </tr>
        <!--8.电话（座机、手机）-->
        <tr>
            <td class="tableleft">*电话（座机、手机）：</td>
            <td><input type="text"
                       name="mysql_tel" id="mysql_tel" required size="10" maxlength="11" valtype='mobile'
                       style="width:400px;"
                       remoteurl="<?php echo site_url("user/chktel"); ?>?id=<?php echo $model["uid"]; ?>"
                       remote_notValidValue="<?php echo $model["tel"]; ?>"
                       value="<?php echo $model["tel"]; ?>"
                /></td>
            <td></td>
        </tr>
        <!--9.邮箱-->
        <tr>
            <td class="tableleft"><input name="mysql_email" style="display:none;" type="password"/> *邮箱：</td>
            <td>
                <input type="text" name="mysql_email" size="50" maxlength="50" placeholder="建议：邮箱"
                       value="<?php echo $model['email']; ?>"
                       remoteurl="<?php echo site_url("user/chkusername"); ?>?id=<?php echo $model["uid"]; ?>"
                       remote_notValidValue="<?php echo $model['email']; ?>"
                       valtype='email' minlength="6" style="width:400px;"
                       autocomplete="off">
            </td>
            <td>6到50位字母、数字、下划线组合</td>
        </tr>
        <!--10.工商营业执照副本-->
        <tr>
            <td class="tableleft">工商营业执照副本：</td>
            <td>
                <div id="fj1" style="display:block;">
                    <table class="uploading">
                        <tr>
                            <!--选择文件-->
                            <td style="border:none;">
                                <input type="file" name="business_license_copy" id="business_license_copy"/>
                            </td>
                            <!--上传按钮-->
                            <td style="border:none;">
                                <input type="button" value="上传" class="button button-danger"
                                       onClick="uploadity_start('business_license_copy')"/>
                            </td>
                        </tr>
                    </table>
                    <table class="upload_ok" style="display:none;">
                        <tr>
                            <td style="border:none;">
                                <div>
                                    <ul class="gallery1" id="upload1_updateimg">
                                    </ul>
                                </div>
                                <input type="button" value="重新上传" style="margin-top:3px;"
                                       class="button button-danger"
                                       onClick="cansel('business_license_copy_real','fj1')"/>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <!--保存信息时用到，真正的上传input-->
                <input type="hidden" name="mysql_fujian_gongshang" id="business_license_copy_real"
                       value="<?php echo $model['fujian_gongshang']; ?>">
                <span style="border:none;">文件大小不超过2M，格式为：jpg/png/gif/bmp</span>
            </td>
        </tr>

        <tr>
            <td class="tableleft">是否三证合一：</td>
            <td>
                <input type="radio" name="mysql_sanzheng" value="1">是
                <input type="radio" name="mysql_sanzheng" value="2">否
            </td>
        </tr>

        <tr>
            <td class="tableleft">三证合一：</td>
            <td>
                <div class="upload_div" style="display:block;">
                    <table class="upload_one_uploading">
                        <tr>
                            <!--选择文件-->
                            <td style="border:none;">
                                <input type="file"/>
                            </td>
                            <!--上传按钮-->
                            <td style="border:none;">
                                <input type="button" value="上传" class="button button-danger upload_one_start"/>
                            </td>
                        </tr>
                    </table>
                    <table class="upload_one_finish" style="display:none;">
                        <tr>
                            <td style="border:none;">
                                <div>
                                    <ul class="gallery1">
                                    </ul>
                                </div>
                                <input type="button" value="重新上传" style="margin-top:3px;"
                                       class="button button-danger upload_one_re"/>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="mysql_fujian_sanzheng" class="upload_one_real"
                           value="<?php echo $model['fujian_sanzheng']; ?>">
                </div>
            </td>
            <td>
                <!--保存信息时用到，真正的上传input-->
                <span style="border:none;">文件大小不超过2M，格式为：jpg/png/gif/bmp</span>
            </td>
        </tr>


        <!--11.企业境外投资证书-->
        <tr id="tr_certificate">
            <td class="tableleft">企业境外投资证书：</td>
            <td>
                <div id="fj2" style="display:block;">
                    <table class="uploading">
                        <tr>
                            <!--选择文件-->
                            <td style="border:none;">
                                <input type="file" name="overseas_investment_certificate"
                                       id="overseas_investment_certificate"/>
                            </td>
                            <!--上传按钮-->
                            <td style="border:none;">
                                <input type="button" value="上传" class="button button-danger"
                                       onClick="uploadity_start('overseas_investment_certificate')"/>
                            </td>
                        </tr>
                    </table>
                    <table class="upload_ok" style="display:none;">
                        <tr>
                            <td style="border:none;">
                                <!--input type="button" value="查看"  class="button button-info" onClick="lookpic($('#social_organization_registration_certificate_id').val())"/-->
                                <div>
                                    <ul class="gallery1" id="upload1_updateimg">
                                    </ul>
                                </div>
                                <input type="button" value="重新上传" style="margin-top:3px;"
                                       class="button button-danger"
                                       onClick="cansel('overseas_investment_certificate_real','fj2')"/>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <!--保存信息时用到，真正的上传input-->
                <input type="hidden" name="mysql_fujian_touzizhengshu" id="overseas_investment_certificate_real"
                       value="<?php echo $model['fujian_touzizhengshu']; ?>">
                <span style="border:none;">文件大小不超过2M，格式为：jpg/png/gif/bmp</span>
            </td>
        </tr>

        <!--12.其他资料-->
        <tr id="tr_other_ziliao">
            <td class="tableleft"> 其他资料</td>
            <td colspan="2">
                <div class="zliao_img_box">
                    <ul class="img_ul">
                        <?php foreach ($ziliaos as $key => $value): ?>
                            <li>
                                <a href="/<?php echo $value->path; ?>" target="_blank">
                                    <img src="/<?php echo $value->path; ?>" alt="" style="width:152px;height:112px;">
                                </a>
                                <a href="javascript:void(0);" fpath="<?php echo $value->path; ?>"
                                   class="old_img_ul_li_a">删除</a>
                                <br>描述：<input placeholder="请输入描述" name="ziliao_des[]"
                                              value="<?php echo $value->des; ?>"
                                              style="width:100px;" type="text">
                                <input type="hidden" name="ziliao_uploads[]" value="<?php echo $value->path; ?>">
                            </li>
                        <?php endforeach ?>
                    </ul>
                    <div class="clear"></div>
                </div>
                <input type="file" name="yq_ziliao" id="yq_ziliao"/>&nbsp;&nbsp;
                <span style="color:#ccc;font-size:12px;">每个文件大小不超过2M,一次最多只允许上传10个文件</span>
                <input type="button" value="上传" class="button button-danger"
                       onClick="uploadity_start('yq_ziliao')"/><br>
                &nbsp;&nbsp;<span style="color:#ccc;font-size:12px;">(文件支持格式：jpg、png、gif)</span>
            </td>
        </tr>

        <!--        <tr>
            <td class="tableleft">QQ号码：</td>
            <td><input type="text" style="width:400px;" name="mysql_qq" valtype="int" size="10" maxlength="100"
                       value="<?php /*echo $model["qq"]; */ ?>"/></td>
            <td>&nbsp;</td>
        </tr>-->
        <!--tr>
  <td class="tableleft">微信账号：</td>
  <td><input type="text"  style="width:400px;" name="mysql_weixin_account"  size="10" maxlength="100" value="<?php echo $model["weixin_account"]; ?>"  ></td>
  <td>&nbsp;</td>
</tr-->

        <!--13.服务类型 留学 旅游-->
        <tr id="tr_servertype">
            <td class="tableleft">服务类型：</td>
            <td>
                <?php
                foreach ($server_list as $item) {
                    $pos = strpos($model["server_type"], $item["id"]);
                    $selected = "";//是否选中
                    if ($pos !== false && $pos >= 0) {
                        $selected = "checked";
                    }
                    echo "<label class='td_label1'>";
                    echo "<input type='checkbox' name='server_type[]' value='{$item['id']}' {$selected}> {$item['name']}";
                    echo "</label>";
                } ?>
            </td>
            <td></td>
        </tr>

        <tr>
            <td class="tableleft">冻结：</td>
            <td>
                <input type="radio" name="mysql_status"
                       value="1" <?php echo $model["status"] == "1" ? "checked" : ""; ?>/>否
                &nbsp;
                <input type="radio" name="mysql_status"
                       value="0" <?php echo $model["status"] == "0" ? "checked" : ""; ?>/>是
            </td>
            <td>冻结后，会员不能登录系统</td>
        </tr>
        <tr>
            <td class="tableleft">&nbsp;</td>
            <td>

                <input type="submit" class="btn button-warning" name="btn_save" value="保存"/>

                <a class="btn btn-primary" id="addnew"
                   onClick="parent.flushpage('<?php echo empty($_GET["backurl"]) ? "" : $_GET["backurl"] ?>');top.topManager.closePage();">关闭</a>
            </td>
            <td></td>
        </tr>
    </table>

    <input type="hidden" name="backurl" value="<?php echo empty($_GET["backurl"]) ? "" : $_GET["backurl"] ?>"/>
</form>


<!-- script start-->
<!--邮箱自动完成-->
<script src="/admin_application/views/static/Js/mailAutoComplete/jquery.mailAutoComplete-4.0.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        /*上传一张图片专用*/
        var $div = $("div.upload_div");

        //初始化
        $div.each(function () {
            //最外层的div
            var $this = $(this);
            //上传table
            var $table1 = $this.find('table.upload_one_uploading');
            //上传成功的table
            var $table_finish = $this.find("table.upload_one_finish");
            //装图片的ul
            var $gallery = $table_finish.find("ul.gallery1");

            //上传按钮
            var $btn_start = $this.find(".upload_one_start");
            //找到input file元素
            var $input_file = $this.find("input[type=file]");
            //找到重新上传按钮
            var $btn_restart = $this.find(".upload_one_re");
            //真实的保存文件路径的input
            var input_real_path = $this.find("input.upload_one_real");

            //要有id，uploadify才不会报错
            $input_file.attr("id", "uploadify" + input_real_path.attr("name"));

            //是否刚刚上传
            var isCurrentUp = false;

            /**
             * 上传成功调用的函数
             */
            function uploadsuccess(image_path) {
                $table1.hide();
                $table_finish.show();
                $gallery.append("<li style='padding:1px;'><a href='/" + image_path + "' target='_blank'>" +
                    "<img style='width:92px;height:70px;' src='/" + image_path + "'/></a></li>");
            }

            //初始化uploadify
            $input_file.uploadify({
                'auto': false,//关闭自动上传
                'removeTimeout': 1,//文件队列上传完成1秒后删除
                'swf': '/home/views/static/js/uploadfile/uploadify.swf',
                'uploader': '<?php echo site_url("zcq_fujian/upload");?>',//上传到哪里
                'method': 'post',//方法，服务端可以用$_POST数组获取数据
                'buttonText': '选择文件',//设置按钮文本
                'multi': false,//允许同时上传多张图片
//            'uploadLimit': 1,//一次最多只允许上传10张图片
                'fileTypeDesc': 'All Files',//只允许上传图像
                'fileTypeExts': '*.gif; *.jpg; *.png;*;*.bmp',//限制允许上传的图片后缀
                'fileSizeLimit': '20000KB',//限制上传的图片不得超过200KB
                'onUploadSuccess': function (file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
                    var result = $.parseJSON(data);
                    if (result.success) {
                        //保存成功的图片路径
                        var filepath = result.filepath;
                        input_real_path.val(filepath);
                        layer.msg('上传成功', {time: 1000});
                        uploadsuccess(filepath);
                        //标识刚刚上传完成
                        isCurrentUp = true;
                    } else {
                        layer.msg('上传失败,请检查上传文件类型或者刷新重试', {time: 1000});
                    }
                },
                'onQueueComplete': function (queueData) {
                    //上传队列全部完成后执行的回调函数
                    //console.log("filepath:" + filepath);
                },
                'onError': function (event, ID, fileObj, errorObj) {
                    if (errorObj.type === "File Size") {
                        alert('超过文件上传大小限制（2M）！');
                        return;
                    }
                    alert(errorObj.type + ', Error: ' + errorObj.info);
                }
                // Put your options here
            });

            //上传按钮被点击
            $btn_start.click(function () {
                $input_file.uploadify('upload');
            });
            //重新上传按钮被点击
            $btn_restart.click(function () {
                $table1.show();
                $table_finish.hide();
                //真正的保存路径
                var fjpath = input_real_path.val();
                //提交请求去删除刚才的附件,需要是本次上传还未提交保存的图片才删除
                if (fjpath != "" && isCurrentUp) {
                    var url = "<?php echo site_url("fujian/del");?>";
                    $.ajax({
                        url: url,
                        type: "GET",
                        datatype: "text",
                        data: {
                            filepath: input_real_path.val()
                        },
                        success: function (rdata, textStatus) {
                            console.log("删除附件" + rdata);
                        }
                    });
                }

                //清除已经显示的图片
                $gallery.empty();
                input_real_path.val("");
            });

            //一开始加载页面显示上传好的图片
            var old_image_path1 = input_real_path.val();
            console.log("图片路径：" + old_image_path1);
            if (old_image_path1 != "") {
                uploadsuccess(old_image_path1);
            }
        });

    })
</script>
<script type="text/javascript">
    function postform() {
        var $pwd = $('#pwd');
        var $pwd2 = $('#pwd2');

        if ($pwd.val() != "" || $pwd2.val()) {
            if ($pwd.val() != $pwd2.val()) {
                layer.msg('两次输入密码不相同', {time: 1000});
                return false;
            }
            if ($pwd.val().length < 6) {
                layer.msg('密码长度至少6位', {time: 1000});
                return false;
            }
        }

        return $("#myform").Valid();
    }

    /**
     * 截取字符串
     * @return {string}
     */
    function SetString(str, len) {
        var strlen = 0;
        var s = "";
        for (var i = 0; i < str.length; i++) {
            /*            if(str.charCodeAt(i) > 128){
             strlen += 1;
             }else{

             }*/
            strlen++;
            s += str.charAt(i);
            if (strlen >= len) {
                return s;
            }
        }
        return s;
    }

    /**
     * 对tr进行有动画的显示和隐藏
     * @param $tr
     */
    function trSlideDown($tr, callback) {
        $tr.find('td').wrapInner('<div class="tr_wrap" style="display: none;" />')
            .parent()
            .find('div.tr_wrap')
            .slideDown(450, function () {
                //是div
                var $set = $(this);
                //用div的内容替换掉div
                $set.replaceWith($set.contents());
                //回调函数
                callback && callback();
            });
    }
    function trSlideUp($tr, callback) {
        $tr.find('td').wrapInner('<div class="tr_wrap" style="display: block;" />')
            .parent()
            .find('div.tr_wrap')
            .slideUp(450, function () {
                //是div
                var $set = $(this);
                //用div的内容替换掉div
                $set.replaceWith($set.contents());
                //$tr.hide();
                $tr.css("display", "none");
                //回调函数
                callback && callback();
            });
    }

    /**
     * uploadify插件开始上传
     */
    function uploadity_start(id) {
        $("#" + id).uploadify('upload');
    }

    /**
     * 重新上传
     * @param fjid input的id
     * @param control_id 要开启的控件ID
     */
    function cansel(fjid, div_id) {
        //td内的整个div
        var $ele = $('#' + div_id);
        //上传table
        var $table1 = $ele.find('table.uploading');
        //上传成功的table
        var $table_ok = $ele.find("table.upload_ok");
        //装图片的ul
        var $table_ok_ul = $table_ok.find("ul.gallery1");
        $table1.show();
        $table_ok.hide();
        //真正保存路径的input
        var $input = $('#' + fjid);
        var fjpath = $input.val();
        //提交请求去删除刚才的附件,需要是本次上传还未提交保存的图片才删除
        if (fjpath != "" && $table1.attr("currentUpload") == "true") {
            console.log("ajax删除附件" + div_id);
            var url = "<?php echo site_url("fujian/del");?>";
            $.ajax({
                url: url,
                type: "GET",
                datatype: "text",
                data: {
                    filepath: $input.val()
                },
                success: function (rdata, textStatus) {
                    console.log("删除附件" + rdata);
                }
            });
        }

        //清除已经显示的图片
        $table_ok_ul.empty();
        $input.val("");
    }

    $(document).ready(function () {

        //全名
        var $fullname = $('#fullname');
        //简称
        var $abb = $('#Abbreviation');
        //工商注册地址那一行
        var $tr_gongshang_addr = $('#gongshang_addr');
        //企业境外投资证书那一行
        var $tr_certificate = $('#tr_certificate');
        //其它资料那一行
        var $tr_other_ziliao = $('#tr_other_ziliao');
        //机构服务类型选择
        var $tr_servertype = $('#tr_servertype');

        //用户类型列表
        var usertype_list = '<?php echo json_encode($usertype_list);?>';
        var usertype_obj = $.parseJSON(usertype_list);
        //console.log(usertype_list);
        //console.log(usertype_obj);

        /**
         * 使表单在企业和机构之间转换
         */
        function toggleChange(obj) {
            var $myform = $('#myform');
            var newname = SetString(obj.name, obj.name.length - 2);
            //console.log(newname);
            //更改名字为机构或者企业
            $fullname.text(newname);
            $abb.text(newname);
            if (obj.id == "45063") {
                /* 企业表单 */
                $tr_gongshang_addr.css("display", "table-row");
                trSlideDown($tr_gongshang_addr);
                trSlideUp($tr_servertype);
                $tr_certificate.css("display", "table-row");
                trSlideDown($tr_certificate);
                $tr_other_ziliao.css("display", "table-row");
                trSlideDown($tr_other_ziliao, function () {
                    //回调
                    $myform.Valid();
                });

                //机构开启验证
                $tr_gongshang_addr.attr("notvalid", false);
            } else {
                /* 机构表单 */
                $tr_servertype.css("display", "table-row");
                trSlideDown($tr_servertype);

                trSlideUp($tr_gongshang_addr);
                //$tr_certificate.css("display","none");
                //$tr_other_ziliao.css("display","none");
                trSlideUp($tr_certificate);
                trSlideUp($tr_other_ziliao, function () {
                    //回调
                    $myform.Valid();
                });

                //机构取消验证
                $tr_gongshang_addr.attr("notvalid", true);
            }

            //也要隐藏validate插件带来的错误显示
            $("div.succ,div.error").slideUp();
        }

        /**
         * 根据用户类型id切换表单
         */
        function changeForm(userTypeId) {
            for (var i = 0; i < usertype_obj.length; i++) {
                if (usertype_obj[i].id == userTypeId) {
                    toggleChange(usertype_obj[i]);
                    break;
                }
            }
        }

        var $curSelection = null;
        //用户类型的单选框
        $("input[type=radio]").each(function () {
            var $this = $(this);
            //点击事件
            $this.click(function () {
                //从单选框获得的用户id
                var uid = $this.attr("value");
                if ($this != $curSelection) {
                    $curSelection = $this;
                    changeForm(uid);
                }

                /*                $.each(usertype_obj, function (n, value) {
                 if (value.id == uid + "") {
                 console.log(value);
                 toggleChange(value);
                 }
                 })*/
            })
        });

        changeForm('<?php echo $model["usertype"];?>');

        //工商营业执照副本上传
        var $fj1 = $('#fj1');//整个div
        var $business_license_copy = $('#business_license_copy');
        var $business_license_copy_real = $('#business_license_copy_real');
        $business_license_copy.uploadify({
            'auto': false,//关闭自动上传
            'removeTimeout': 1,//文件队列上传完成1秒后删除
            'swf': '/home/views/static/js/uploadfile/uploadify.swf',
            'uploader': '<?php echo site_url("zcq_fujian/upload");?>',
            'method': 'post',//方法，服务端可以用$_POST数组获取数据
            'buttonText': '选择文件',//设置按钮文本
            'multi': false,//允许同时上传多张图片
//            'uploadLimit': 1,//一次最多只允许上传10张图片
            'fileTypeDesc': 'All Files',//只允许上传图像
            'fileTypeExts': '*.gif; *.jpg; *.png;*;*.bmp',//限制允许上传的图片后缀
            'fileSizeLimit': '20000KB',//限制上传的图片不得超过200KB
            'onUploadSuccess': function (file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
                var result = $.parseJSON(data);
                if (result.success) {
                    //保存成功的图片路径
                    var filepath = result.filepath;
                    $business_license_copy_real.val(filepath);
                    layer.msg('上传成功', {time: 1000});
                    uploadsuccess($fj1, filepath, true);
                } else {
                    layer.msg('上传失败,请检查上传文件类型或者刷新重试', {time: 1000});
                }
            },
            'onQueueComplete': function (queueData) {
                //上传队列全部完成后执行的回调函数
                //console.log("filepath:" + filepath);
            },
            'onError': function (event, ID, fileObj, errorObj) {
                if (errorObj.type === "File Size") {
                    alert('超过文件上传大小限制（2M）！');
                    return;
                }
                alert(errorObj.type + ', Error: ' + errorObj.info);
            },
            // Put your options here
        });

        //境外投资证书
        var $fj2 = $('#fj2');//整个div
        var $overseas_investment_certificate = $('#overseas_investment_certificate');
        var $overseas_investment_certificate_real = $('#overseas_investment_certificate_real');
        $overseas_investment_certificate.uploadify({
            'auto': false,//关闭自动上传
            'removeTimeout': 1,//文件队列上传完成1秒后删除
            'swf': '/home/views/static/js/uploadfile/uploadify.swf',
            'uploader': '<?php echo site_url("zcq_fujian/upload");?>',
            'method': 'post',//方法，服务端可以用$_POST数组获取数据
            'buttonText': '选择文件',//设置按钮文本
            'multi': false,//允许同时上传多张图片
//            'uploadLimit': 1,//一次最多只允许上传10张图片
            'fileTypeDesc': 'All Files',//只允许上传图像
            'fileTypeExts': '*.gif; *.jpg; *.png;*;*.bmp',//限制允许上传的图片后缀
            'fileSizeLimit': '20000KB',//限制上传的图片不得超过200KB
            'onUploadSuccess': function (file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
                var result = $.parseJSON(data);
                if (result.success) {
                    //保存成功的图片路径
                    var filepath = result.filepath;
                    $overseas_investment_certificate_real.val(filepath);
                    layer.msg('上传成功', {time: 1000});
                    uploadsuccess($fj2, filepath, true);
                } else {
                    layer.msg('上传失败,请检查上传文件类型或者刷新重试', {time: 1000});
                }
            },
            'onQueueComplete': function (queueData) {
                //上传队列全部完成后执行的回调函数
                //console.log("filepath:" + filepath);
            },
            'onError': function (event, ID, fileObj, errorObj) {
                if (errorObj.type === "File Size") {
                    alert('超过文件上传大小限制（2M）！');
                    return;
                }
                alert(errorObj.type + ', Error: ' + errorObj.info);
            }
            // Put your options here
        });
        /**
         * 上传成功调用的函数
         */
        function uploadsuccess($ele, image_path, temp) {
            temp = temp ? temp : false;
            var $table1 = $ele.find('table.uploading');
            var $table_ok = $ele.find("table.upload_ok");
            var $table_ok_ul = $table_ok.find("ul.gallery1");
            $table1.hide();
            $table_ok.show();
            $table_ok_ul.append("<li style='padding:1px;'><a href='/" + image_path + "' target='_blank'>" +
                "<img style='width:92px;height:70px;' src='/" + image_path + "'/></a></li>");
            if (temp) {
                $table1.attr("currentUpload", true);
            }
        }

        //其他资料
        $('#yq_ziliao').uploadify({
            'auto': false,//关闭自动上传
            'removeTimeout': 1,//文件队列上传完成1秒后删除
            'swf': '/<?php echo APPPATH?>/views/static/Js/uploadfile/uploadify.swf',
            'uploader': '<?php echo site_url("zcq_fujian/upload");?>',
            'method': 'post',//方法，服务端可以用$_POST数组获取数据
            'buttonText': '选择文件',//设置按钮文本
            'multi': true,//允许同时上传多张图片
            'uploadLimit': 100,//一次最多只允许上传100张图片
            'fileTypeDesc': 'All Files',//只允许上传图像
            'fileTypeExts': '*.jpg;*.png;*.gif;*',//限制允许上传的图片后缀
            'fileSizeLimit': '2048KB',//限制上传的图片不得超过200KB
            'onUploadSuccess': function (file, data, response) {
                //每次成功上传后执行的回调函数，从服务端返回数据到前端
                var result = $.parseJSON(data);
                if (result.success) {
                    //保存成功的图片路径
                    var filepath = result.filepath;

                    //添加图片显示
                    var html = '<li>' +
                        '<a href="/' + filepath + '" target="_blank"><img src="/' + filepath + '" alt="" style="width:152px;height:112px;"></a>' +
                        '<a href="javascript:void(0);" fpath="' + filepath + '" class="img_ul_li_a">删除</a>' +
                        '<br>描述：<input placeholder="请输入描述" name="ziliao_des[]" value="" style="width:100px;" type="text">' +
                        '<input type="hidden" name="ziliao_uploads[]" value="' + filepath + '">' +
                        '</li>';
                    $('.zliao_img_box .img_ul').append(html);

                    layer.msg('上传成功', {time: 1000});
                } else {
                    layer.msg('上传失败,请检查上传文件类型或者刷新重试', {time: 1000});
                }
            },
            'onQueueComplete': function (queueData) {
                //上传队列全部完成后执行的回调函数

            },
            'onError': function (event, ID, fileObj, errorObj) {
                if (errorObj.type === "File Size") {
                    alert('超过文件上传大小限制（2M）！');
                    return;
                }
                alert(errorObj.type + ', Error: ' + errorObj.info);
            }
            // Put your options here
        });
        //监听删除其他资料事件
        $("body").on("click", ".img_ul_li_a", function () {
            //删除本次上传的图片

            //$this代码a标签本身
            var $this = $(this);
            var filepath = $this.attr("fpath");
            var url = "<?php echo site_url("zcq_fujian/del");?>";
            //ajax 删除附件
            $.ajax({
                url: url,
                type: "GET",
                datatype: "text",
                data: {
                    filepath: filepath
                },
                success: function (rdata, textStatus) {
                    console.log("删除附件" + rdata);
                    $this.parent('li').remove();
                    layer.msg('删除成功', {time: 1000});
                }
            });
        }).on("click", ".old_img_ul_li_a", function () {
            //删除已上传的图片

            //ajax 删除附件
            var $this = $(this);
            var filepath = $this.attr("fpath");
            $this.parent('li').remove();
            layer.msg('删除成功,保存生效', {time: 1000});
        });


        //一开始加载页面显示上传好的图片
        var old_image_path1 = $business_license_copy_real.val();
        console.log("信用证路径：" + old_image_path1);
        if (old_image_path1 != "") {
            uploadsuccess($fj1, old_image_path1);
        }

        var old_image_path2 = $overseas_investment_certificate_real.val();
        console.log("境外投资证书路径：" + old_image_path2);
        if (old_image_path2 != "") {
            uploadsuccess($fj2, old_image_path2);
        }

        //一开始1500ms后就验证一下表单
        setTimeout(function () {
            $("#myform").Valid();
        }, 1500);

        //邮箱自动完成
        $("input[type=email]").mailAutoComplete();//使用方法
    });
</script>
<!-- script end -->

</body>
</html>

