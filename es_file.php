<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

define("USERNAME","admin");   //远程同步访问的用户名
define("PASSWORD","fanwe2014");	  //远程同步访问的密码
define('APP_ROOT_PATH', str_replace('es_file.php', '', str_replace('\\', '/', __FILE__)));

function mk_dir($dir, $mode = 0755)
{
  if (is_dir($dir) || @mkdir($dir,$mode)) return true;
  if (!mk_dir(dirname($dir),$mode)) return false;
  return @mkdir($dir,$mode);
}

$username = trim(addslashes($_REQUEST['username']));
$password = trim(addslashes($_REQUEST['password']));
$file = trim(addslashes($_REQUEST['file']));
$path = trim(addslashes($_REQUEST['path']));
$name = trim(addslashes($_REQUEST['name']));
$act = intval($_REQUEST['act']);

if($username==USERNAME&&$password==PASSWORD)
{
	if($act==0) //上传
	{
		$file_data = @file_get_contents($file);
		$img = @imagecreatefromstring($file_data);
		if($img!==false)
		{
			$save_path = APP_ROOT_PATH."public/".$path;
			if(!is_dir($save_path))
			{
				@mk_dir($save_path);			
			}
			@file_put_contents($save_path.$name,$file_data);
		}
	}
	else
	{
		//删除
		$save_path = APP_ROOT_PATH.$path;  //删除时直接传入相应的位置与名称
		$file_data = @file_get_contents($save_path);
		$img = @imagecreatefromstring($file_data);
		if($img!==false)
		{
			@unlink($save_path);
		}
	}
}
else
{
	die("invalid access");
}

?>