<?php
if (! defined('BASEPATH')) {
	exit('Access Denied');
}

class Weixin extends CI_Model{
	var $TOKEN;
	var $Appkey;
	var $AppSecret;
	private $sysconfig_cache_path = '';
	
	function Weixin(){
		parent::__construct();
		$this->load->model('M_57sy_common_sysconfig','sysconfig');
		$this->load->model('M_zzb_acc','acc');
		$this->load->model('M_weixin_jsapi_ticket','jssdk');
		$this->sysconfig_cache_path = config_item("sysconfig_cache");
		$configfile = $this->sysconfig_cache_path . "/sysconfig.inc.php";
		if(file_exists($configfile)){
			require_once $configfile;
				
	
			if(isset($weixin_token) &&
					isset($weixin_appid) &&
					isset($weixin_appsecret))
			{
				$this->TOKEN = $weixin_token;
				$this->Appkey = $weixin_appid;
				$this->AppSecret = $weixin_appsecret;
			}
			else{
				//没有变量，就读数据库
				$this->TOKEN = $this->sysconfig->GetConfig("weixin_token");
				$this->Appkey = $this->sysconfig->GetConfig("weixin_appid");
				$this->AppSecret = $this->sysconfig->GetConfig("weixin_appsecret");
			}
		}
	
	
	}
	function index(){
		$this->valid();
		//echo $this->getacc();
		//echo $this->getopenid();
	}
	
	function valid()
	{
		$echoStr = $this->input->get("echostr");
		if($this->checkSignature()){
			echo $echoStr;
			exit;
		}
	}
	
	function getopenid(){
		
		//1订阅号，2服务号
		if(!__WXKF__){
			$weixin_type = $this->sysconfig->GetConfig("cfg_weixin_type");
			if($weixin_type==2){		
				$get = $this->input->get();
				$code = empty($get['code'])?"":$get['code'];
				$openid = "";
				//取得OPENID
				$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->Appkey."&secret=".$this->AppSecret."&code=".$code."&grant_type=authorization_code";
				//echo $url;
				$ch = curl_init($url);
				//设置选项，包括URL
				//curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
				//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,1);
				//执行并获取HTML文档内容
				$output = curl_exec($ch);				
				curl_close($ch);
				if($output!="")
				{
					$arr = json_decode($output);
					if(empty($arr->openid))
					{
						$openid = "";
					}
					else{
						$openid = $arr->openid;
					}
				}
				else{
					$openid = "";
				}
			}
			else{
				//订阅号，用GUID
				$openid = create_guid();
			}
		}
		else{
			//当WXKF为TURE时，直接用模拟OPENID
			$openid=__OPENID__;
		}
		return $openid;
	}
	
	function getacc(){
		$model = $this->acc->GetModel2();
		$iscreate = false;
		$access = "";
		if($model==""){
			$iscreate = true;
		}
		else{
			if(is_array($model)){
				//少于1.5小时，就用旧的ACC
				if( (time()-$model["createtime"])<=5400 ){
					$access = $model["acc"];
				}
				else{
					$iscreate = true;
				}
			}
			else{
				$iscreate = true;
			}
			//创建新的ACC并写入数据库
			if($iscreate){
				$access = $this->GetNewAcc();
				if($access!=""){
					$model = array(
							"acc"=>$access,
							"createtime"=>time()
					);
					$this->acc->del("1=1");
					$this->acc->add($model);
				}
			}
		}
		return $access;
	}
	
	function createmenu()
	{

		//cfg_weixin_type
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->GetNewAcc();
		$menu = '
		{
		     "button":[
			  {
				   "name":"登录/注册",
				   "sub_button":[
									{
									   "type":"view",
									   "name":"注册",
									   "url":"'.($this->getwxurl(site_url("/home_wx/reg"))).'"
									},
									{
									   "type":"view",
									   "name":"登录",
									   "url":"'.($this->getwxurl(site_url("/home_wx/login"))).'"
									}								   											 							
								]
	
			  },
			 {
				   "name":"我的推广",
				   "sub_button":[
								   {
									   "type":"view",
									   "name":"我的名片",
									   "url":"'.($this->getwxurl(site_url("home_wx/mingpian"))).'"
									},									   		
								   {
									   "type":"view",
									   "name":"商城",
									   "url":"'.($this->getwxurl(site_url("home_wx/wxmall"))).'"
									},
									{
									   "type":"view",
									   "name":"资讯",
									   "url":"'.($this->getwxurl(site_url("huiyuan_wx/category"))).'"
									}											
								]
			  },
			  {
				   "name":"管理中心",
				   "sub_button":[
								   {
									   "type":"view",
									   "name":"修改密码",
										"url":"'.(site_url("huiyuan_wx/pwd")).'"
									},
									{
										"type":"view",
										"name":"我的介绍",
										"url":"'.(site_url("huiyuan_wx/js")).'"
									},	
									{
									   "type":"view",
									   "name":"上传头像",
									   "url":"'.(site_url("huiyuan_wx/phone_logo")).'"
									},													
									{
										"type":"view",
										"name":"我的订单",
										"url":"'.(site_url("huiyuan_wx/dingdan")).'"
									}											
								]
			  }
		]
	}
	';
		//http://map.wap.soso.com/x/?type=infowindow&X=113.37996990680697&Y=22.50075670865048&Z=16&name=%E4%B8%AD%E5%B1%B1%E5%AE%8C%E7%BE%8E%E4%B8%93%E5%8D%96%E5%BA%97&address=%E4%B8%AD%E5%9B%BD%E5%B9%BF%E4%B8%9C%E7%9C%81%E4%B8%AD%E5%B1%B1%E5%B8%82%E6%B2%99%E7%9F%B3%E5%85%AC%E8%B7%AF&open=1&welcomeChange=1&welcomeClose=1&hideAdvert=hide&referer=weixinmp_profile
	
		//初始化
		$ch = curl_init($url);
		//设置选项，包括URL
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $menu); // Post提交的数据包
	
	
		//curl_setopt($ch, CURLOPT_HEADER, 0);
		//执行并获取HTML文档内容
		$output = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);
		//打印获得的数据	
		$arr = json_decode($output);
		return  print_r($arr,true).("<meta charset=\"utf-8\"><pre>$menu</pre>");
	
	}
	//组合链接，用于跳转获取用户信息
	function getwxurl($url){
		//1订阅号，2服务号
		$weixin_type = $this->sysconfig->GetConfig("cfg_weixin_type");
		if(!__WXKF__){
			if($weixin_type==2){
				$wxurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->Appkey."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_base&state=xxx#wechat_redirect";
			}
			else{
				$wxurl = $url;
			}
		}
		else{
			//WXKF设为TRUE时，代表在本地开发，就不跳去微信端认端
			$wxurl = $url;
		}
		
		return $wxurl;
	}
	
	private function GetNewAcc(){
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->Appkey."&secret=".$this->AppSecret;
		//初始化
		$ch = curl_init($url);
		//设置选项，包括URL
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($ch, CURLOPT_HEADER, 0);
		//执行并获取HTML文档内容
		$output = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);
		//打印获得的数据
		//print_r($output);
		$arr = json_decode($output);
		$access_token = "";
		if(!empty($arr->access_token))
		{
			$access_token = $arr->access_token;
			//file_put_contents(realpath($txt),$access_token."|".strtotime(date("Y-m-d H:i:s")));
		}
		return $access_token;
	}
	private function checkSignature()
	{
		$signature = $this->input->get("signature");
		$timestamp = $this->input->get("timestamp");
		$nonce = $this->input->get("nonce");
		//file_put_contents("./aa.txt",$signature);// print_r($this->input->get(),true)
		$token = $this->TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
	
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}	
	
	public function getjssdk(){
		if(!__WXKF__){
			$jsapiTicket = $this->getJsApiTicket();
			
			// 注意 URL 一定要动态获取，不能 hardcode.
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			
			$timestamp = time();
			$nonceStr = $this->createNonceStr();
			
			// 这里参数的顺序要按照 key 值 ASCII 码升序排序
			$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
			
			$signature = sha1($string);
			
			$signPackage = array(
					"appId"     => $this->Appkey,
					"nonceStr"  => $nonceStr,
					"timestamp" => $timestamp,
					"url"       => $url,
					"signature" => $signature,
					"rawString" => $string
			);
		}
		else{
			$signPackage = array(
					"appId"     => $this->Appkey,
					"nonceStr"  => "",
					"timestamp" => "",
					"url"       => "",
					"signature" => "",
					"rawString" => ""	
			);		
		}
		return $signPackage;
	}
	//js sdk
	public function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
	//js sdk
	private function getJsApiTicket() {
		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$jssdk_model = $this->jssdk->GetModel2();
		$data = "";
		if(count($jssdk_model)>0){
			$data= $jssdk_model;
		}
		if($data!=""){		
			if ($data["expire_time"] < time()) {
				$accessToken = $this->getacc();
				// 如果是企业号用以下 URL 获取 ticket
				// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
				$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
				$res = json_decode($this->httpGet($url));
				$ticket = $res->ticket;
				if ($ticket) {		
					$jssdk_model["jsapi_ticket"] = $ticket;
					$jssdk_model["expire_time"] =  time() + 7000;
					$this->jssdk->del("1=1");
					$this->jssdk->add($jssdk_model);
				}
			} else {
				$ticket = $data["jsapi_ticket"];
			}
		}
		else{
			$accessToken = $this->getacc();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode($this->httpGet($url));
			//echo "aaa=".$accessToken;
			//echo "<br/>";
			//print_r($res);
			$ticket = $res->ticket;
			if ($ticket) {
				$jssdk_model["jsapi_ticket"] = $ticket;
				$jssdk_model["expire_time"] =  time() + 7000;
				$this->jssdk->del("1=1");
				$this->jssdk->add($jssdk_model);
			}			
		}
	
		return $ticket;
	}
	
	public function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);
	
		$res = curl_exec($curl);
		curl_close($curl);
	
		return $res;
	}

	//发送post，xml格式请求到微信服务器,$url微信服务器连接，$data：xml内容,返回微信相应内容
	public function httpPostXml($url, $data) {
		// $url = "http://localhost/response.php";  
		// $data = '<xml><AppId>wxf8b4f85f3a794e77</AppId><ErrorType>1001</ErrorType><Description>错误描述</Description><AlarmContent>transaction_id=33534453534</AlarmContent><TimeStamp>1393860740</TimeStamp><AppSignature>f8164781a303f4d5a944a2dfc68411a8c7e4fbea</AppSignature><SignMethod>sha1</SignMethod></xml>';  
		$ch = curl_init();
		$header[] = "Content-type: text/xml";//定义content-type为xml  
		curl_setopt($ch, CURLOPT_URL, $url); //定义表单提交地址  
		curl_setopt($ch, CURLOPT_POST, 1);   //定义提交类型 1：POST ；0：GET  
		curl_setopt($ch, CURLOPT_HEADER, 1); //定义是否显示状态头 1：显示 ； 0：不显示  
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//定义请求类型  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//定义是否直接输出返回流 0 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //定义提交的数据，这里是XML文件  
		$result = curl_exec($ch);  
		curl_close($ch);//关闭 

		return $result;
	}	
	//进行现金红包的签名，$model进行签名的一维数组,$shkey为商户的api密钥,使用md5
	public function getxjhbSign($model, $shkey='') {
		//先将数组以键值升序排序，之后组合成字符串进行md5运算
		//拼接的api密钥在key设置路径：微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置
		ksort($model);
		$str = $sign = '';
		foreach ($model as $key => $value) {//拼接成字符串
			$str .= $key . "=" . $value ."&";
		}
		if ($shkey) {
			$str .= $shkey;//拼接api密钥
		}
		$sign = strtoupper(md5($str));
		return $sign;
	}
	//将数组转成xml格式, $model转换的数组，返回xml格式
	public function formArrToXml($model) {
		$xml = "<xml>";
		foreach ($model as $key => $value) {
			$xml .= "<" . $key . "><![CDATA[" .$value ."]]></". $key .">";
		}
		$xml .= "</xml>";
		return $xml;
	}


    //判断当前用户是否有关注，有：1，无：0
    function getguanzhu($openid){

        if(__WXKF__){
            return 1;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->getacc()."&openid=".$openid;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,1);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //echo $output;
        //echo "<br/>".$this->getacc();
        curl_close($ch);
        $json = json_decode($output,true);
        //print_r($json);
        return isset($json["subscribe"])?$json["subscribe"]:1;
    }
}

?>