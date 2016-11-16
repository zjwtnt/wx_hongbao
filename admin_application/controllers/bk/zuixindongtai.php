<?php
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
/*
 * 最新动态
*/
class Zuixindongtai extends MY_Controller{
	public $categorylist = array();
	public $upload_path = '';
	public $upload_path_save = '';
	public $isadmin = false;
	
	function Zuixindongtai(){
		parent::__construct();
		$this->upload_path = __ROOT__ . "/data/upload/info/".date("Y")."/";
		$this->upload_path_save = "data/upload/info/".date("Y")."/";
		$this->load->model('M_common');
		$this->load->model('M_website_common_model','wcm');
		$this->load->model('M_website_common_info','wcmi');
		$this->load->model('M_website_category','wc');
		$this->load->model('M_zzb_dy_dati','dati');
		$this->load->model('M_website_model_zuixindongtai_dyart','zxdt_dyart');
		$this->load->model('M_website_model_zuixindongtai','zxdt');
		$this->load->model('M_common_system_user','system_user');
		$this->load->model('M_zzb_dy_dati_history','dydt_history');
		$this->load->model('M_website_model_dangyuandati','dangyuandati');
		$this->load->library('MyText');
		$this->load->library('MyEditor');
		$this->load->library('MyAlbum');
		$this->load->library("pin");
		
		$this->isadmin = is_super_admin();
	}
	function index(){
		
		$this->load->view(__TEMPLET_FOLDER__."/website/category/index");
	}
	
	
	
	//取得所有栏目
	function GetCategory($pid,$tree){
		global $categorylist;
		$model = $this->wc->GetSubList($pid);
		foreach($model as $v){
			$v["tree"] = "├".$tree;
			$categorylist[] = $v;
			$this->GetCategory($v["id"],$v["tree"]);
			
		}
		return $categorylist;
	} 
	
	//读取树LIST
	function treelist(){		
		global $categorylist;
		$categorylist = array();
		$get = $this->input->get();
		if(!empty($get["json"]))
		{
			$this->GetCategory(0,'-');
			$data["categorylist"] = $categorylist;
			
			$tree = array();
			for($i=0;$i<count($categorylist);$i++){
				$v = $categorylist[$i];
				//判断有子节点，须要设置为leaf : false
				if($this->wc->GetSubCount($v["id"])==0){
					$tree[] = array(
							'pid'=>$v["pid"],
							'id'=>$v["id"],
							'text'=>$v["title"]							
					);
				}					
				else
				{
					$tree[] = array(
							'pid'=>$v["pid"],
							'id'=>$v["id"],
							'text'=>$v["title"],
							'leaf'=>false
						);
				}
			}				
			//生成JSON
			echo json_encode($tree);
			//$json = preg_replace('/\"(\w+)\":/is', '$1:', json_encode($tree));
			//echo trim(str_replace("\"","'",$json));
		}
		else{
			
			$this->load->view(__TEMPLET_FOLDER__."/website/category/tree");
		}
	}
	
	function add(){
		/*
		 * 根据栏目ID，判断所属MODEL类型，如果没有栏目ID就默认文章ID
		 * 
		 * 
		 */
		$get = $this->input->get();
		$typeid = 0;
		$typeid = is_numeric($get["typeid"])?$get["typeid"]:"0";		
		$category_model_id = "1";//如果没有栏目ID，默认就是文章模型
		$data = array();		
		$post = $this->input->post();
		if(is_array($post)){
			//保存			
			//先确认MODEL_ID，再读出字段保存
			$post = $this->input->post();
			if($post["common_model_id"]>0){
				
			}
			else{
				echo "<script>
					parent.tip_show('没有模型ID',2,10000);
					top.topManager.closePage();
				 </script>";
				exit();				
			}		
			//先读主表字段
			$main_model = $this->wcmi->GetFields();
			foreach($main_model as $k=>$v){
				$main_model[$k] = empty($post[$k])?"":trim($post[$k]);
			}
			//初始化
			$main_model["post"] = strtotime($main_model["post"]);
			$main_model["thumb"] = "";
			$main_model["clicks"] = 0;			
			
			$main_model["update_time"] = time();
			$main_model["create_user"] = admin_id();
			$main_model["update_user"] = admin_id();
			$main_model["tuijian"] = "";
			if($main_model["istop_str"] !=""){
				switch ($main_model["istop_str"])
				{
					case "一天":
						$main_model["istop"] = time()+(60*60*24);
						break;
					case "一周":
						$main_model["istop"] = time()+(60*60*24*7);
					break;	
					case "两周":
						$main_model["istop"] = time()+(60*60*24*14);
						break;
					case "一个月":
						$main_model["istop"] = time()+(60*60*24*30);
						break;
					case "三个月":
						$main_model["istop"] = time()+(60*60*24*90);
						break;	
					case "半年":
						$main_model["istop"] = time()+(60*60*24*180);
						break;						
					default:
						$main_model["istop"]=0;
						break;
				}
				;
			}
			else{
				$main_model["istop"] ="0";
			}
			//上传缩略图
			//建立新目录
			if(!is_dir($this->upload_path)){
				@mkdir($this->upload_path);
			}			
			$this->load->library("common_upload");
			$thumb = $this->common_upload->upload_path(
					$this->upload_path, 
					'thumb',
					'png|jpg|gif|bmp'
					);			
			if($thumb!=""){
				$thumb = $this->upload_path_save.$thumb;
			}			
			$main_model["thumb"] = $thumb;										
			$result = $this->wcmi->Insert($main_model);
			write_action_log(
			$result['sql'],
			$this->uri->uri_string(),
			login_name(),
			get_client_ip(),
			1,
			"主表信息：" . $main_model["title"] . "添加成功");			
			$insert_id = $result["insert_id"];
			//保存模型
			$common_model = $this->wcm->GetModel($main_model["common_model_id"]);
			$two_model = $this->wcmi->GetFields($common_model["tablename"]);
			foreach($two_model as $k=>$v){
				$two_model[$k] = empty($post[$k])?"":trim($post[$k]);
			}
			//每个模型固定的字段
			$two_model["aid"] = $insert_id;
			//栏目ID
			$two_model['category_id'] = $main_model['category_id'];
			$two_model['category_id2'] = $main_model['category_id2'];
			$two_model['category_id3'] = $main_model['category_id3'];
			
			//副表新增字段
			$two_model["qishu"] = $post["qishu"];
			$two_model["isnew"] = isset($post["isnew"])?"1":"0";
			if($two_model["isnew"]=="1"){
				//往期自动下架
				$this->wcmi->update_sql("update website_model_zuixindongtai set isnew=0 where aid<>".$insert_id);
			}
			//将一对多储到第三张表
			$website_model_dangyuandati_id = $post["website_model_dangyuandati_id"];
			
			if($website_model_dangyuandati_id!=""){
				$website_model_dangyuandati_id = explode(",", $website_model_dangyuandati_id);
				foreach($website_model_dangyuandati_id as $v){
					$tmpmodel = array(
						"website_model_zuixindongtai_id"=>$insert_id,
						"website_model_dangyuandati_id"=>$v															
					);
					$this->zxdt_dyart->add($tmpmodel);
				}
			}
			
			
			$result = $this->wcmi->InsertModel($two_model,$common_model["tablename"]);
			write_action_log(
			$result['sql'],
			$this->uri->uri_string(),
			login_name(),
			get_client_ip(),
			1,
			"副表信息：" . $main_model["title"] . "添加成功");			
			
			echo "<script>
					parent.tip_show('发布成功',1,3000);
					top.topManager.closePage();
				 </script>";
			exit();
			
		}
		else{	
			$fieldhtml = "";
			$modeltitle = "";	
			if($typeid>0){
				$category_model = $this->wc->GetModel($typeid);
				$category_model_id = $category_model["model_id"];							
			}
/*
1.文本框
2.单选
3.多选
4.上传单个文件
5.上传单张图片
6.批量上传图片
7.文本编辑器
8.多行文本框
9.日期
10.日期时分
 */			
			if($category_model_id>0){
				$mainmodel = $this->wcm->GetModel($category_model_id);
				$common_model = $this->wcm->GetSubList($category_model_id);
				$data["common_model"] = $common_model;
				for($i=0;$i<count($common_model);$i++){
					switch ($common_model[$i]["fieldtype"]){
						case 1:
							$fieldhtml.=$this->mytext->Create($common_model[$i]['id']);
							break;
						case 6:
							$fieldhtml.= $this->myalbum->Create($common_model[$i]['id']);
							break;
						case 7:
							$fieldhtml.= $this->myeditor->Create($common_model[$i]['id']);
							break;
						default:
								
							break;
					}
				}
				$data["field"] = $fieldhtml;				
			}
			else{
				echo "<script>
					parent.tip_show('没有模型ID',2,2000);
					top.topManager.closePage();
				 </script>";				
			}//if($category_model_id>0)
			//读所有栏目
			global $categorylist;
			$this->GetCategory(0,'-');
			$data["category_model_id"] = $category_model_id;
			$data["categorylist"] = $categorylist;
			$data["typeid"] = $typeid;
			
			$this->load->view(__TEMPLET_FOLDER__."/website/zuixindongtai/addinfo",$data);
		}
		
		
	}
	

	function edit(){
		$get = $this->input->get();
		$post = $this->input->post();
		//先取主表		
		$main_model = $this->wcmi->GetModel(!empty($post["id"])?$post["id"]:$get["id"]);
		if(!is_array($main_model)){
			echo "<script>
					parent.tip_show('信息不存在',2,3000);
					top.topManager.closePage();
				 </script>";
			exit();			
		}
		//读所有栏目
		global $categorylist;
		$this->GetCategory(0,'-');		
		$data["categorylist"] = $categorylist;				
		$data["main_model"] = $main_model;			
		//读出副表模型字段	
		$fieldhtml = "";
		$common_model = $this->wcm->GetSubList($main_model["common_model_id"]);
		$data["common_model"] = $common_model;
		//读出副表		
		$two_model = $this->wcmi->GetTwoModel($main_model["id"],$common_model[0]["tablename"]);


		
		
		
		if(is_array($post)){
			//先读主表字段
			$main_model_field = $this->wcmi->GetFields();
			foreach($main_model_field as $k=>$v){				
				$main_model[$k] = empty($post[$k])?"":trim($post[$k]);
			}
			//去掉创建信息时才需要的字段
			unset($main_model["create_user"]);			
			//保存信息
			$main_model["post"] = strtotime($post["post"]);							
			$main_model["update_time"] = time();			
			$main_model["update_user"] = admin_id();
			$main_model["tuijian"] = "";

			if($main_model["istop_str"] !=$post["istop_str"]){
				switch ($post["istop_str"])
				{
					case "一天":
						$main_model["istop"] = time()+(60*60*24);
						break;
					case "一周":
						$main_model["istop"] = time()+(60*60*24*7);
						break;
					case "两周":
						$main_model["istop"] = time()+(60*60*24*14);
						break;
					case "一个月":
						$main_model["istop"] = time()+(60*60*24*30);
						break;
					case "三个月":
						$main_model["istop"] = time()+(60*60*24*90);
						break;
					case "半年":
						$main_model["istop"] = time()+(60*60*24*180);
						break;
					default:
						$main_model["istop"]=0;
						break;
				}
				;
			}			
			//上传缩略图
			//建立新目录
			if(!is_dir($this->upload_path)){
				@mkdir($this->upload_path);
			}
			$this->load->library("common_upload");
			$thumb = $this->common_upload->upload_path(
					$this->upload_path,
					'thumb',
					'png|jpg|gif|bmp'
			);
			if($thumb!=""){
				$thumb = $this->upload_path_save.$thumb;
				$main_model["thumb"] = $thumb;
			}					
			$result = $this->wcmi->Update($main_model);
			write_action_log(
			$result['sql'],
			$this->uri->uri_string(),
			login_name(),
			get_client_ip(),
			1,
			"主表信息：" . $main_model["title"] . "修改成功");

			
			//保存模型
			$common_model = $this->wcm->GetModel($main_model["common_model_id"]);
			$two_model_field = $this->wcmi->GetFields($common_model["tablename"]);			
			foreach($two_model_field as $k=>$v){
				$two_model[$k] = empty($post[$k])?"":trim($post[$k]);
			}		
			//栏目ID
			$two_model['aid'] = $main_model['id'];
			$two_model['category_id'] = $main_model['category_id'];
			$two_model['category_id2'] = $main_model['category_id2'];
			$two_model['category_id3'] = $main_model['category_id3'];
			//副表新增字段
			$two_model["qishu"] = $post["qishu"];
			$two_model["isnew"] = isset($post["isnew"])?"1":"0";
			if($two_model["isnew"]=="1"){
				//往期自动下架
				$this->wcmi->update_sql("update website_model_zuixindongtai set isnew=0 where aid<>".$two_model["aid"]);
			}						
			$result = $this->wcmi->UpdateModel($two_model,$common_model["tablename"]);
			write_action_log(
			$result['sql'],
			$this->uri->uri_string(),
			login_name(),
			get_client_ip(),
			1,
			"副表信息：" . $main_model["title"] . "修改成功");
				
			
			//将一对多储到第三张表			
			$website_model_dangyuandati_id = $post["website_model_dangyuandati_id"];			
			if($website_model_dangyuandati_id!=""){
				//清空未选中部分对应的党员答题记录表
				$website_model_dangyuandati_id_notsel = $this->zxdt_dyart->get_website_model_dangyuandati_id("website_model_zuixindongtai_id=".$main_model["id"]." and website_model_dangyuandati_id not in(".$website_model_dangyuandati_id.")");
				if($website_model_dangyuandati_id_notsel!=""){
					$zzb_dy_dati_id = $this->dangyuandati->get_zzb_dy_dati_id("aid in(".$website_model_dangyuandati_id_notsel.")");
					if($zzb_dy_dati_id!=""){
						//删除记录表
						$this->dydt_history->del2("artid=".$main_model["id"]." and zzb_dy_dati_id in(".$zzb_dy_dati_id.")");
					}
				}										
				//清空未选中的部分				
				$this->zxdt_dyart->del2("website_model_zuixindongtai_id=".$main_model["id"]." and website_model_dangyuandati_id not in($website_model_dangyuandati_id) ");
				$website_model_dangyuandati_id = explode(",",$website_model_dangyuandati_id);
				foreach($website_model_dangyuandati_id as $v){
					if($this->zxdt_dyart->count("website_model_zuixindongtai_id=".$main_model["id"]." and website_model_dangyuandati_id=".$v)==0){
						$tmpmodel = array(
								"website_model_zuixindongtai_id"=>$main_model["id"],
								"website_model_dangyuandati_id"=>$v
						);
						$this->zxdt_dyart->add($tmpmodel);
					}
				}			
			}
			else{
				//全部清空
				$this->dydt_history->del2("artid=".$main_model["id"]);
				$this->zxdt_dyart->del2("website_model_zuixindongtai_id=".$main_model["id"]);
			}			
			
			echo "<script>
					parent.tip_show('保存成功',1,3000);
					top.topManager.closePage();
				 </script>";
			exit();			
			
		}
		
		//读出一对多表
		if($two_model["aid"]>0){
			$dati_model = $this->zxdt_dyart->getlist("website_model_zuixindongtai_id=".$two_model["aid"]);
			$dati_id = "";
			for($i=0;$i<count($dati_model);$i++){
				if($dati_id==""){
					$dati_id = $dati_model[$i]["website_model_dangyuandati_id"];
				}
				else{
					$dati_id .=",".$dati_model[$i]["website_model_dangyuandati_id"];
				}
			}
			$data["dati_id"] = $dati_id;
		}
		else{
			$data["dati_id"] = "";
		}
					
		for($i=0;$i<count($common_model);$i++){
			switch ($common_model[$i]["fieldtype"]){
				case 1:
					$fieldhtml.=$this->mytext->Create(
							$common_model[$i]['id'],
							$two_model[$common_model[$i]["field"]]
						);
					break;
				case 6:
					$fieldhtml.= $this->myalbum->Create(
					$common_model[$i]['id'],
					$two_model[$common_model[$i]["field"]]
					);
					break;					
				case 7:
					$fieldhtml.= $this->myeditor->Create(
							$common_model[$i]['id'],
							$two_model[$common_model[$i]["field"]]
						);
					break;					
				default:
		
					break;
			}
		}
		$data["two_model"] = $two_model;
		$data["field"] = $fieldhtml;		
		$this->load->view(__TEMPLET_FOLDER__."/website/zuixindongtai/editinfo",$data);
	}
	
	//删除文章缩略图
	function delinfothumb(){
		$get = $this->input->get();
		if(empty($get["id"])){
			die("err");
		}
		if($get["id"]>0){
			$model = $this->wcmi->GetModel($get["id"]);
			if(is_array($model)){
				@unlink(__ROOT__."/".$model["thumb"]);
				$model["thumb"]="";
				$result = $this->wcmi->Update($model);
				write_action_log(
				$result['sql'],
				$this->uri->uri_string(),
				login_name(),
				get_client_ip(),
				1,
				"删除缩略图：" . $model["title"] . "删除成功");	
				die("yes");			
			}
			die("no");
		}
		die("no");
	}
	
	//删除栏目缩略图
	function delcategorythumb(){
		$get = $this->input->get();
		if(empty($get["id"])){
			die("err");
		}
		if($get["id"]>0){
			$model = $this->wc->GetModel($get["id"]);
			if(is_array($model)){
				@unlink(__ROOT__."/".$model["thumb"]);
				$model["thumb"]="";
				$result = $this->wc->update($model);
				write_action_log(
				$result['sql'],
				$this->uri->uri_string(),
				login_name(),
				get_client_ip(),
				1,
				"删除缩略图：" . $model["title"] . "删除成功");
				die("yes");
			}
			die("no");
		}
		die("no");
	}	
	
	
	
	//用于文 本编辑器上传
	function upload(){
		//file_put_contents("e:aa.txt","bbbb=".print_r($_SESSION,true));		
		$this->myeditor->upload();
	} 
	//用于相册上传后，保存图片
	function uploadAlbum(){
		
	}
	function getAlbum(){
		$get = $this->input->get();
		if(is_numeric($get["id"])){
			
		}
	}
	
	function AlbumDelPic(){		
		echo $this->myalbum->DelPic();
	}
	
	function AlbumSetPic(){
		$post = $this->input->get();
		$pic = empty($post["pic"])?"":$post["pic"];
		$alljson = empty($post["alljson"])?"":$post["alljson"];
		echo $this->myalbum->ShowAlbumPicInfo($pic,$alljson);
	}
	
	function AlbumSavePic(){
		$post = $this->input->post();
		$pic = empty($post["pic"])?"":$post["pic"];
		$beizhu = empty($post["beizhu"])?"":$post["beizhu"];
		$orderby = empty($post["orderby"])?"":$post["orderby"];
		$alljson = empty($post["alljson"])?"":$post["alljson"];	
		echo $this->myalbum->SaveAlbumPicInfo($pic,$beizhu,$orderby,$alljson);
	}
	
	
	function selart(){
		$pageindex= $this->input->get_post("per_page");
		if ($pageindex <= 0) {
			$pageindex = 1;
		}
		$get=$this->input->get();
			
		
		$title = daddslashes(html_escape(strip_tags(trim($this->input->get_post("title",true))))) ;

		$search = array("category_id"=>"6");
	
		$search_val = array(
				"title"=>""				
		);
		if(!empty($title)){
			$search["title"]= $title;
			$search_val["title"] = $title;
		}
		
		$data = array();
		
		$orderby["id"] = "desc";
		$data = $this->wc->GetInfoList($pageindex,5,$search,$orderby);
		/*
		foreach ($data["list"] as $k=>$v){
			if($data["list"][$k]["create_common_system_userid"]>0){
				$sysmodel = $this->system_user->GetModel($data["list"][$k]["create_common_system_userid"]);
				if(count($sysmodel)>0){
					$data["list"][$k]['username'] = $sysmodel["username"];
				}
				else{
					$data["list"][$k]['username'] = "-";
				}			
			}
		}
		*/		
		
		$data["search_val"] = $search_val;			
		$this->load->view(__TEMPLET_FOLDER__."/website/zuixindongtai/selart",$data);		
	}
	
	
	
	//信息列表
	function infolist(){
		$pageindex= $this->input->get_post("per_page");
		if ($pageindex <= 0) {
			$pageindex = 1;
		}
		$get=$this->input->get();
		$category_model_id = 1;
		if(empty($get["typeid"])){
			$get["typeid"]=0;
		}
		$typeid = is_numeric($get["typeid"])? $get["typeid"]:"0";
		$search_title = empty($get["search_title"])?"":trim($get["search_title"]);
	
		$search = array();
		if($search_title!=""){
			$search["title"]=$search_title;
			$search_val["search_title"] = $search_title;			
		}
		else{
			$search_val["search_title"] = "";
		}
		if($get["typeid"]>0){
			$search["category_id"]=$get["typeid"];
		}
		else{
				
		}
	
		$orderby["id"] = "desc";
		$data = $this->wc->GetInfoList($pageindex,10,$search,$orderby);
		$typeModel = $this->wc->GetModel($typeid);
		foreach($data["list"] as $k=>$v){
			$tmpModel = $this->zxdt->GetModel($data["list"][$k]["id"]);			
			if($tmpModel!=""){
				$data["list"][$k]["isnew"] = $tmpModel["isnew"];
				$data["list"][$k]["qishu"] = $tmpModel["qishu"];
			}
		}
		$data["search_val"] = $search_val;
		$parent_list = array();
		if($typeid>0){
			//读取目录全路径
			if($typeModel["pid"]=="0"){
				$parent_list[] = $typeModel;
			}
			else{
				$parent_list = $this->wc->GetList("id in(".$typeModel["parent_path"].")", "field(id,".$typeModel["parent_path"].")");
				$parent_list[] = $typeModel;
			}
	

			for($i=0;$i<count($parent_list);$i++){
				if($parent_list[$i]["model_id"]>0){
					$tmpModel = $this->wcm->GetModel($parent_list[$i]["model_id"]);
					$parent_list[$i]["listpage"] = $tmpModel["listpage"];
				}
				else{
					$parent_list[$i]["listpage"] = "";
				}
			}
		}
		if(count($typeModel)>0){
			if(!is_numeric($typeModel["model_id"])){
				//当栏目没有指定模型，则自动设为文章类型
				$typeModel["model_id"]="1";
				$this->wc->Update($typeModel);
			}
			$model_id = $typeModel["model_id"];
		}
		else{
			$model_id=1;
		}
	
		$mainmodel = $this->wcm->GetModel($model_id);
	
		if(count($mainmodel)==0){
			//模型不存在
			echo "模型不存在，请修改栏目模型。";
			echo "<a href='".site_url("website_category/edit")."?id=$typeid'>[编辑栏目]</a>";
			die();
		}
		$modeltitle = $mainmodel["title"];
	
		$data["parent_list"] = $parent_list;
		if(count($parent_list)>0){
			$data["category_model"] = $parent_list[count($parent_list)-1];
		}
		else{
			$data["category_model"] = "";
		}
		$data["modeltitle"] = $modeltitle;
	
		$data["addpage"] = $mainmodel["addpage"];
		$data["editpage"] = $mainmodel["editpage"];
	
		$data["category_id"] = $typeid;
		$this->load->view(__TEMPLET_FOLDER__."/website/zuixindongtai/list",$data);
	}	
	
	function chkdydt_history(){
		$err = "";
		$where = " 1=1 ";
		$get = $this->input->get();
		$idlist = "";
		if(empty($get["idlist"])){
			die("err");
		}
		else{
			$idlist = $get["idlist"];
		}				
		$count = $this->dydt_history->count("artid in (".$idlist.")");
		$aids = "";
		if($count>0){
			$artlist = $this->dydt_history->getlist("artid in (".$idlist.")");
			$arr = array();
			foreach($artlist as $v){
				$arr[]=$v["artid"];						
			}
			$arr = array_unique($arr);
			$aids = implode(",", $arr);
		}
		if($aids!=""){
			$err = "编号：".$aids."的动态已有答题记录不能删除。";
		}
		echo $err;	
	}
	
	function DelInfo(){
		//管理员能删除任何信息
		$where = " 1=1 ";
		if(!$this->isadmin){
			$where = " create_user=".admin_id();
		}
		$get = $this->input->get();
		$idlist = "";
		if(empty($get["idlist"])){
			die("err");
		}
		else{
			$idlist = $get["idlist"];
		}
	
		$where .= " and id in($idlist)";
		//先处理图片
		$idarr = explode(",",$idlist);
		$list = $this->wcmi->GetList($where);
		foreach($list as $v){
			$infomodel = $v;//$this->wcmi->GetModel($v);
			if(!$this->isadmin){
				if($infomodel["create_user"]!=admin_id()){
					die("err quan xian");
				}
			}
			$fieldlist = "";
			if($infomodel["common_model_id"]>0){
				$fieldlist = $this->wcm->GetSubList($infomodel["common_model_id"]);
			}
				
			//先删除缩略图
			if($infomodel["thumb"]!=""){
				@unlink(realpath("./".$infomodel["thumb"]));
			}
			//处理字段中相册字段及编辑器字段中的图片
			if($infomodel["common_model_id"]>0){
				/*
				 4.上传单个文件
				 5.上传单张图片
				 6.批量上传图片
				 7.文本编辑器
				 */
				if(is_array($fieldlist)){
					$twomodel = $this->wcmi->GetTwoModel($v["id"],$fieldlist[0]["tablename"]);
					if(count($twomodel)>0){
						foreach($fieldlist as $vv){
							switch($vv["fieldtype"]){
								case 4:
									if($twomodel[$vv["field"]]!=""){
										$file = $twomodel[$vv["field"]];
										if(strpos($file, "//")===false){
											if(substr($file,0,1)!="/"){
												$file = "/".$file;
											}
										}
										else{
											$file = str_replace("//","/",$file);
										}
										@unlink(realpath("./".$file));
									}
									break;
								case 5:
									if($twomodel[$vv["field"]]!=""){
										$file = $twomodel[$vv["field"]];
										if(strpos($file, "//")===false){
											if(substr($file,0,1)!="/"){
												$file = "/".$file;
											}
										}
										else{
											$file = str_replace("//","/",$file);
										}
										@unlink(realpath("./".$file));
									}
									break;
								case 6:
									$json = $twomodel[$vv["field"]];
									if($json!=""){
										$json = json_decode("[".$json."]",true);
										foreach($json as $vvv){
											@unlink(realpath(".".$vvv["pic"]));
											//删除中图、小图
											$pic_arr = explode(".",$vvv["pic"]);
												
											$pic_small = $pic_arr[0]."_small".".".$pic_arr[1];
											$pic_mid = $pic_arr[0]."_mid".".".$pic_arr[1];
											@unlink(realpath(".".$pic_small));
											@unlink(realpath(".".$pic_mid));
										}
									}
									break;
								case 7:
									$editor = $twomodel[$vv["field"]];
									$arr = getImgsFormEditor($editor);
									if(is_array($arr)){
										foreach($arr as $vvv){
											$pic = str_replace(base_url(),"",$vvv);
		
											if( strpos($pic, "http://")===false){
												//是本地图片
												if(strpos($pic, "//")===false){
													if(substr($pic,0,1)!="/"){
														$pic = "/".$pic;
													}
												}
												else{
													$pic = str_replace("//","/",$pic);
												}
												@unlink(realpath(".".$pic));
												//echo $pic."<br/>";
												//删除中图、小图
												$pic_arr = explode(".",$pic);
												$pic_small = $pic_arr[0]."_small".".".$pic_arr[1];
												$pic_mid = $pic_arr[0]."_mid".".".$pic_arr[1];
												@unlink(realpath(".".$pic_small));
												@unlink(realpath(".".$pic_mid));
											}
										}
									}
									break;
								default:
									break;
							}
						}
					}
					//先删除副表
					$this->wcmi->deltwo(str_replace("id","aid",$where),$fieldlist[0]["tablename"]);
				}
	
			}
				
		}
		//再删除主表
		$this->wcmi->del($where);
		//再删除第三张表，一对多关系表
		$this->zxdt_dyart->del2(str_replace("id","website_model_zuixindongtai_id",$where));
		echo "yes";
	}
	
	
	
	

}


?>