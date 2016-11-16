<?php
/**
 * KindEditor PHP
 *
 * 本PHP程序是演示程序，建议不要直接在实际项目中使用。
 * 如果您确定直接使用本程序，使用之前请仔细确认相关安全设置。
 *
 */


/*$php_path = substr(dirname(__FILE__), 0, -59) . '/webroot/upload/news/';
$php_url = dirname($_SERVER['PHP_SELF']) . '/';

//文件保存目录路径
$save_path = $php_path  ; 
//文件保存目录URL
$save_url = $php_url . '../attached/';*/


//$php_path = config_item("news_attach_path");
//$save_path = $php_path  ; 
//$save_url = config_item('news_visit_url');

//定义允许上传的文件扩展名
$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
	'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
);
//最大文件大小
$max_size = 1000000000;

$save_path = realpath($save_path) . '/';

//PHP上传失败
if (!empty($_FILES['imgFile']['error'])) {
	switch($_FILES['imgFile']['error']){
		case '1':
			$error = '超过php.ini允许的大小。';
			break;
		case '2':
			$error = '超过表单允许的大小。';
			break;
		case '3':
			$error = '图片只有部分被上传。';
			break;
		case '4':
			$error = '请选择图片。';
			break;
		case '6':
			$error = '找不到临时目录。';
			break;
		case '7':
			$error = '写文件到硬盘出错。';
			break;
		case '8':
			$error = 'File upload stopped by extension。';
			break;
		case '999':
		default:
			$error = '未知错误。';
	}
	alert($error);
}

//有上传文件时
if (empty($_FILES) === false) {
	//原文件名
	$file_name = $_FILES['imgFile']['name'];
	//服务器上临时文件名
	$tmp_name = $_FILES['imgFile']['tmp_name'];
	//文件大小
	$file_size = $_FILES['imgFile']['size'];
	//检查文件名
	if (!$file_name) {
		alert("请选择文件。");
	}
	//检查目录
	if (@is_dir($save_path) === false) {
		alert("上传目录不存在。");
	}
	//检查目录写权限
	if (@is_writable($save_path) === false) {
		alert("上传目录没有写权限。");
	}
	//检查是否已上传
	if (@is_uploaded_file($tmp_name) === false) {
		alert("上传失败。");
	}
	//检查文件大小
	if ($file_size > $max_size) {
		alert("上传文件大小超过限制。");
	}
	//检查目录名
	$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
	if (empty($ext_arr[$dir_name])) {
		alert("目录名不正确。");
	}
	//获得文件扩展名
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	//检查扩展名
	if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
		alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
	}
	//创建文件夹
	if ($dir_name !== '') {
		$save_path .= $dir_name . "/";
		$save_url .= $dir_name . "/";
		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
	}
	$ymd = date("Ymd");
	$save_path .= $ymd . "/";
	$save_url .= $ymd . "/";
	if (!file_exists($save_path)) {
		mkdir($save_path);
	}
	//新文件名
	$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
	//移动文件
	$file_path = $save_path . $new_file_name;
	if (move_uploaded_file($tmp_name, $file_path) === false) {
		alert("上传文件失败。");
	}
	@chmod($file_path, 0644);
	$file_url = $save_url . $new_file_name;
	//取图片长高
	$source_img = getimagesize($file_url);
	//生成大中小三张缩略图	
	$config['image_library'] = 'gd2';
	$width_arr[] = $this->m_57sy_common_sysconfig->GetConfig("cfg_thumb_small");
	$width_arr[] = $this->m_57sy_common_sysconfig->GetConfig("cfg_thumb_mid");
	$width_arr[] = $this->m_57sy_common_sysconfig->GetConfig("cfg_thumb_big");
	
	$height_arr[] = $this->m_57sy_common_sysconfig->GetConfig("cfg_thumb_small_height");
	$height_arr[] = $this->m_57sy_common_sysconfig->GetConfig("cfg_thumb_mid_height");
	$height_arr[] = $this->m_57sy_common_sysconfig->GetConfig("cfg_thumb_big_height");
	
	
	//file_put_contents("e:aa.txt",);
	//先创建大图、再中图、再小图
	if($source_img[0]>$width_arr[2]){
		$this->load->library('image_lib');
		$config['source_image'] = realpath(".".str_replace(base_url(),"",$file_url));
		$config['create_thumb'] = FALSE;	
		$config['quality'] = 100;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $width_arr[2];
		$config['height'] = $height_arr[2];		
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		$this->image_lib->clear();
	}
	
	if($source_img[0]>$width_arr[1]){	
		$this->load->library('image_lib');
		$config=array();
		$newfilepath = $save_url ."mid_". $new_file_name;
		$newfilepath = str_replace(base_url(),"",$newfilepath);		
		$config['source_image'] = realpath(".".str_replace(base_url(),"",$file_url));
		$config['create_thumb'] = TRUE;
		//$config['new_image'] = realpath(".".$newfilepath);
		$config['thumb_marker'] = "_mid";
		$config['quality'] = 100;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $width_arr[1];
		$config['height'] = $height_arr[1];
		$this->image_lib->initialize($config); 
		$this->image_lib->resize();
		$this->image_lib->clear();
	}
	
	if($source_img[0]>$width_arr[0]){
		$this->load->library('image_lib');
		$config=array();
		$newfilepath = $save_url ."small_". $new_file_name;
		$newfilepath = str_replace(base_url(),"",$newfilepath);	
		$config['source_image'] = realpath(".".str_replace(base_url(),"",$file_url));
		$config['create_thumb'] = TRUE;
		//$config['new_image'] = realpath(".".$newfilepath);
		$config['quality'] = 100;
		$config['thumb_marker'] = "_small";
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $width_arr[0];
		$config['height'] = $height_arr[0];
		$this->image_lib->initialize($config); 
		$this->image_lib->resize();
		$this->image_lib->clear();
	}
	//结束缩略图
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 0, 'url' => $file_url));
	exit;
}

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}
