<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />   
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script> 	
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/bui-min.js"></script>
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/validate/validator.js"></script>
	<link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
    
    
    
    
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
	
	.bui-dialog .bui-stdmod-body{
		padding:0px;
	}
    </style>
</head>
<body class="definewidth">


<form action="" onsubmit="return postform()" method="post" name="myform" id="myform">
<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="newitem" id="newitem" value="" />
<input type="hidden" name="olditem" id="olditem" value="" />
<input type="hidden" name="pid" id="pid" value="0"/>
<input type="hidden" name="guid" id="guid" value="<?php echo create_guid();?>"/>
<input type="hidden" name="bigitem" id="bigitem" value=""/>

<table class="table table-bordered table-hover m10">
    <tr>
        <td class="tableleft">试卷主题：</td>
        <td>

<input type="text" style="width:200px;" 
name="title"
id="title" 
value="" required="true" 
valType="check"
remoteUrl="/admin.php/fenzitiku/chktitle.shtml?id=0"

        
        />        
*  

<span class="icon-plus" style="cursor:pointer" onclick="show_additem('',-1);"></span>

        </td>
    </tr>
   <tr>
        <td class="tableleft">答题项：</td>
        <td>
        
<div id="dati_item">

</div>


        </td>
    </tr> 
   <tr>
        <td class="tableleft">是否显示：</td>
        <td>
<input type="radio" name="isshow" value="1" checked/>显示
<input type="radio" name="isshow" value="0" />隐藏（前台不显示）
        </td>
   </tr>  
   <tr>
        <td class="tableleft">介绍内容：</td>
        <td>

<textarea name="content" id="content" style="width:200px;height:40px;"></textarea>       
        
        </td>
    </tr>    

   <tr>
        <td class="tableleft">备注（对内）：</td>
        <td>

<textarea name="beizhu" id="beizhu" style="width:200px;height:40px;"></textarea>       
        
        </td>
    </tr>
 



    <tr>
        <td class="tableleft"></td>
        <td>
<button type="submit" class="btn btn-primary"  id="btnSave">保存</button>            
<button type="button" class="btn btn-primary" onclick="window.location.href='<?php echo $backurl;?>';" >返回列表</button>
        </td>
    </tr>
</table>

</form>
</body>
</html>
<script type="text/javascript">
function postform(){
	//
	var fenshu_start = $("#fenshu_start").val();
	var fenshu_end = $("#fenshu_end").val();
	if(fenshu_start<0){
		
		parent.parent.tip_show('开始分数不能少于零。',2,3000);
		$("#fenshu_start").focus();
		return false;	
	}
	if(fenshu_end<=0){
		parent.parent.tip_show('结束分数不能少于零。',2,3000);
		$("#fenshu_end").focus();
		return false;		
	}
	
	if(parseInt(fenshu_start)>=parseInt(fenshu_end)){
		parent.parent.tip_show('开始分数必须少于结束分数。',2,3000);
		$("#fenshu_start").focus();				
		return false;
	}
	
	if($("#myform").Valid()) {
		return true;
	}
	else{	
		return false;
	}
}

var dialog2
function show_additem(item,i){
	item = encodeURIComponent(item);
	//alert(item);
	//alert(document.getElementById("sendtype2").checked);
	//if(document.getElementById("sendtype2").checked){
		var w = 600;
		var h = 400;
		 BUI.use('bui/overlay',function(Overlay){
	         dialog2 = new Overlay.Dialog({
	           title:'新增答题',
	           width:w,
	           height:h,
	           //配置文本
	           bodyContent:'<iframe id="ifr_additem" name="ifr_additem" src="<?php echo site_url("fenzitiku/additem")?>?item='+item+'&i='+i+'" width="'+(w-10)+'"  height="'+(h-60)+'" frameborder="0" marginwidth="0" marginheight="0"  ></iframe>',
	           buttons:[
	                    
	                  ]
	         });
	       dialog2.show();
	       	
		});
	//}
}

//将子项写入到大项中
function getitem(json,index){
	//alert(json);
	var bgitem = $("#bigitem").val();
	json = eval("["+json+"]");
	//alert(json);
	var tmpjson = "{\"dati\":"+"\""+json[0].dati+"\",";
	tmpjson += "\"seltype\":"+"\""+json[0].seltype+"\",\"subitem\":";
	
	for(i=0;i<json.length;i++){		
		var subitem = "";
		var subobj = eval(json[i].subitem);
		for(j=0;j<subobj.length;j++)
		if(subitem ==""){
			subitem = "{\"title\":\""+subobj[j].title+"\",\"istrue\":\""+subobj[j].istrue+"\"}";
		}
		else{
			subitem += ",{\"title\":\""+subobj[j].title+"\",\"istrue\":\""+subobj[j].istrue+"\"}";
		} 		
	}
	tmpjson+="["+subitem+"]";
	tmpjson+="}";
	//alert(tmpjson);
	if(bgitem==""){
		bgitem = tmpjson;
	}
	else{
		if(index>=0){
			//替换
			//alert(tmpjson);
			bgitem = tihuan(bgitem,tmpjson,index);
			//alert(bgitem);
			var arr = eval(bgitem);
			var tmp = "";
			//alert("arr.length="+arr.length);
			for(j=0;j<arr.length;j++){
				if(j==0){
					tmp=BUI.JSON.stringify(arr[j]);
					//alert(tmp);
				}
				else{		
					tmp+=","+BUI.JSON.stringify(arr[j]);
				}
			}			
			bgitem = tmp;			
		}
		else{
			bgitem += ","+tmpjson;
		}
	}	

	//alert(index);
	//alert(bgitem);
	$("#bigitem").val(bgitem);
	//alert($("#bigitem").val());	
	getlist();	
}

function tihuan(all,json,i){
	
	all = eval("["+all+"]");
	/*
	var bgjson = "";
	for(j=0;j<bgitem.length;j++){
		bgjson += "{dati:"+"\""+bgitem[j].title+"\",";
		tmpjson += "seltype:"+"\""+bgitem[j].seltype+"\",subitem:";		
	}
	*/
	
	for(j=0;j<all.length;j++){
		if(j==i){
			//alert(json);
			json = eval("["+json+"]");
			all[j] = json[0];
		}
	}
	//all.splice((i+1),1,json);
	//alert(BUI.JSON.stringify(all));	
	return BUI.JSON.stringify(all);
}

function getlist(){
	var item = $("#bigitem").val();
	//alert(item);
	json = eval("["+item+"]");	
	var html = "";
	for(i=0;i<json.length;i++){
		html+=json[i].dati;
		html+=' ';
		html+='<span class="icon-edit" onclick="edit('+i+')" style="cursor:pointer"></span>';
		html+=' ';
		html+='<span class="icon-remove" onclick="delitem('+i+')" style="cursor:pointer"></span>';
		html+= "<br/>";
	}	
	$("#dati_item").html(html);
}

function delitem(index){
	var item = $("#bigitem").val();
	var json = eval("["+item+"]");
	//delete json[index];
	//从0开始，第几个位置删除1位
	json.splice((index),1);
	//alert("json.length="+json.length);
	tmp = "";
	for(i=0;i<json.length;i++){
		if(i==0){
			tmp = BUI.JSON.stringify(json[i]);
		}
		else{
			tmp += ","+BUI.JSON.stringify(json[i]);
		}
	}
	//alert(tmp);
	$("#bigitem").val(tmp);
	getlist();	
}

function edit(id){
	var item = $("#bigitem").val();
	json = eval("["+item+"]");	

	var tmpjson = "{\"dati\":"+"\""+json[id].dati+"\",";
	tmpjson += "\"seltype\":"+"\""+json[id].seltype+"\",\"subitem\":";
	
	//for(i=0;i<json.length;i++){
		var subitem = "";
		var subobj = eval(json[id].subitem);
		for(j=0;j<subobj.length;j++)
		if(subitem ==""){
			subitem = "{\"title\":\""+subobj[j].title+"\",\"istrue\":\""+subobj[j].istrue+"\"}";
		}
		else{
			subitem += ",{\"title\":\""+subobj[j].title+"\",\"istrue\":\""+subobj[j].istrue+"\"}";
		} 		
	//}
	tmpjson+="["+subitem+"]";
	tmpjson+="}";
	//alert(tmpjson);
	show_additem(tmpjson,id);
}

var errmsg = "<?php echo !isset($err)?"":$err;?>";
if(errmsg!=""){
	parent.tip_show(errmsg,2,3000);	
}
</script>

