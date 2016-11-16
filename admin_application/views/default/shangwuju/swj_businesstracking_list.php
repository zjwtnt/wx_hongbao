<?php 
if (! defined('BASEPATH')) {
  exit('Access Denied');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>文件管理</title>
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />   
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
    <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
   <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
   
   <script type="text/javascript" src="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/Js/admin.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/assets/js/bui-min.js"></script>        
   <script type="text/javascript" src="<?php echo base_url(); ?>/<?php echo APPPATH ?>/views/static/assets/js/config-min.js"></script>   
 
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


    /**内容超出 出现滚动条 **/
    .bui-stdmod-body{
      overflow-x : hidden;
      overflow-y : auto;
    }
  </style>      
</head>

<body>
<div class="form-inline definewidth m20" > 
    可视权限
  <select id="condition">
    <option value="">请选择</option>
    <option value="1">公开</option>
    <option value="2">不公开</option>
  </select>
  
  文件名: <input type="text" name="name" id="name"class="abc input-default" placeholder="" value="">&nbsp;&nbsp;  
    <button type="submit" class="btn btn-primary" onclick="common_request(1)">查询</button>&nbsp;&nbsp; 
 <a  class="btn btn-primary" id="addnew" href="<?php echo site_url("Swj_businesstracking/add");?>">新增<span class="glyphicon glyphicon-plus"></span></a>
</div>
<div class="form-inline definewidth m10">
  <a  class="btn btn-info" id="pldel" href="javascript:void(0)" onclick="pldel();">
      批量删除<span class="glyphicon glyphicon-plus"></span>
  </a>&nbsp;&nbsp;
   <a  class="btn btn-info" id="plgk" href="javascript:void(0)" onclick="plgk();">
      批量公开<span class="glyphicon glyphicon-plus"></span>
  </a>&nbsp;&nbsp;
   <a  class="btn btn-info" id="plbgk" href="javascript:void(0)" onclick="plbgk();">
      批量不公开<span class="glyphicon glyphicon-plus"></span>
  </a>
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
    <tr>
        <th style="vertical-align: middle;width:10px;"><input type="checkbox" name="piliang[]" id="piliang" value="-1" /></th>
        <th width="61">可视权限</th>
        <th>业务名称</th>
        <th width="91">经办人</th>
        <th width="91">完成日期</th>
        <th width="76">操作</th>
    </tr>
    </thead>
  <tbody id="result_">
  </tbody> 
</table>
<div id="page_string" class="form-inline definewidth m10">
  
</div>
</body>
</html>
<script>
$(function () {
  $("#table option").eq(1).attr("selected","selected");
  common_request(1);
  //全选反选事件
  $('#piliang').click(function(){
    var obj = $(this);
    if (obj.attr("checked")) {  
        $(":checkbox").attr("checked", true);  
    } else {  
        $(":checkbox").attr("checked", false);  
    }  
  });
});
function common_request(page){
  var url="<?php echo site_url("Swj_businesstracking/index");?>?inajax=1";
  var data_ = {
    'page':page,
    'time':<?php echo time();?>,
    'action':'ajax_data',
    'name':$("#name").val(),
    'condition':$("#condition").val(),
  } ;
  $.ajax({
       type: "POST",
       url: url ,
       data: data_,
       cache:false,
       dataType:"json",
     //  async:false,
       success: function(msg){
      var shtml = '' ;
      var list = msg.resultinfo.list;
      var message = msg.resultinfo.errmsg;
      if(msg.resultcode<0){
        alert("没有权限执行此操作");
        return false ;
      }else if(msg.resultcode == 0 ){
        var s = '<div class="alert alert-warning alert-dismissable"><strong>Tips!</strong>'+message+'</div> ' ;
        $("#result_").html(s);
        return false ;        
      }else{
        
        for(var i in list){
          var isshow = list[i]['audit'];//可视权限
          var keshi = '公开';
          switch (isshow) {
            case '2':
              keshi = '不公开';
              break;
            case '1':
              keshi = '公开';
              break;
            default:
              break;
          }
          shtml+='<tr>';
          shtml+='<td><input type="checkbox" name="piliang[]" value="'+list[i].id+'"></td>'
          shtml+='<td>'+keshi+'</td>';
          shtml+='<td>'+list[i]['name']+'</td>';
          shtml+='<td>'+list[i]['jingbanren']+'</td>';
          shtml+='<td>'+list[i]['complete_date']+'</td>';
          shtml+='<td>';
        shtml+='   <a href="<?php echo site_url('Swj_businesstracking/download');?>?id='+list[i].id+'" target="_blank" class="icon-download"></a>&nbsp;&nbsp;';
        shtml+='   <a href="<?php echo site_url('Swj_businesstracking/edit');?>?id='+list[i].id+'" class="icon-edit"></a>&nbsp;&nbsp;';
        shtml+='    <a href="javascript:void(0)" onclick="del_lm('+list[i].id+',this)" class="icon-remove"></a>';
          shtml+='</td>';
          shtml+='</tr>';
        }
        $("#result_").html(shtml);
        
        $("#page_string").html(msg.resultinfo.obj);
      }
       },
       beforeSend:function(){
        $("#result_").html('<font color="red"><img src="<?php echo base_url();?>/<?php echo APPPATH?>/views/static/Images/progressbar_microsoft.gif"></font>');
       },
       error:function(){
         
       }
      
    });   
  

}
function ajax_data(page){
  common_request(page); 
}

//删除类目
function del_lm(id, object) {
  if (confirm('您确定要删除该数据吗？')) {
    window.location.href = "<?php echo site_url('Swj_businesstracking/del');?>?id=" + id;
  }
}
//批量删除
function pldel() {
  var ids = getIds();
  /*alert(ids);
  return;*/
  if (confirm('您确定要删除选中的数据吗？')) {
    window.location.href = "<?php echo site_url('Swj_businesstracking/del');?>?id=" + ids;
  }
}
//批量公开
function plgk() {
  var ids = getIds();
  if (confirm('您确定要公开选中的数据吗？')) {
    window.location.href = "<?php echo site_url('Swj_businesstracking/show');?>?isshow=1&id=" + ids;
  }
}
//批量不公开
function plbgk() {
  var ids = getIds();
  if (confirm('您确定不要公开选中的数据吗？')) {
    window.location.href = "<?php echo site_url('Swj_businesstracking/show');?>?isshow=2&id=" + ids;
  }
}
//获取所有选中的复选框的id,以逗号分割
function getIds() {
  var ids = '';//初始化变量
  var flag = 1;//判断是否为第一次进入，1代表是
  var obj = document.getElementsByName('piliang[]'); //选择所有name="'test'"的对象，返回数组 
  for(var i = 0; i < obj.length; i++){
    //选中，且其值不为-1（不是点击的那个checkbox）
    if(obj[i].checked&&obj[i].value != -1) {
      if (flag == 1) {
        //第一次进入
        flag = 0;//改变其值
        ids = obj[i].value;//添加到ids变量中
      } else {
        ids = ids + ',' + obj[i].value;//如果选中且值不为-1，将value添加到变量ids中 
      }
      
    }
  }
  return ids;
}
</script>
<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/bui-min.js"></script>
