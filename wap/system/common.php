<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

//前后台加载的函数库
require_once 'system_init.php';

 
//获取真实路径
function get_real_path()
{
	return APP_ROOT_PATH;
}

//获取GMTime
function get_gmtime()
{
	return (time() - date('Z'));
}

function to_date($utc_time, $format = 'Y-m-d H:i:s') {
	if (empty ( $utc_time )) {
		return '';
	}
	$timezone = intval(app_conf('TIME_ZONE'));
	$time = $utc_time + $timezone * 3600; 
	return date ($format, $time );
}

function to_timespan($str, $format = 'Y-m-d H:i:s')
{
	$timezone = intval(app_conf('TIME_ZONE'));
	//$timezone = 8; 
	$time = intval(strtotime($str));
        
	if($time!=0)
	$time = $time - $timezone * 3600;
    return $time;
}


//获取客户端IP
function get_client_ip() {
	if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return ($ip);
}

//过滤注入
function filter_injection(&$request)
{
	$pattern = "/(select[\s])|(insert[\s])|(update[\s])|(delete[\s])|(from[\s])|(where[\s])/i";
	foreach($request as $k=>$v)
	{
				if(preg_match($pattern,$k,$match))
				{
						die("SQL Injection denied!");
				}
		
				if(is_array($v))
				{					
					filter_injection($request[$k]);
				}
				else
				{					
 					if(preg_match($pattern,$v,$match))
					{
						die("SQL Injection denied!");
					}					
				}
	}
	
}

function filter_ma_request(&$str){
	$search = array("../","\n","%","\r","\t","\r\n","'","<",">","\"");
 	return str_replace($search,"",$str);
}

//过滤请求
function filter_request(&$request)
{
		if(MAGIC_QUOTES_GPC)
		{
			foreach($request as $k=>$v)
			{
				if(is_array($v))
				{
					filter_request($v);
				}
				else
				{
					$request[$k] = stripslashes(trim($v));
				}
			}
		}
		
}

function adddeepslashes(&$request)
{

			foreach($request as $k=>$v)
			{
				if(is_array($v))
				{
					adddeepslashes($v);
				}
				else
				{
					$request[$k] = addslashes(trim($v));
				}
			}		
}


function quotes($content)
{
	//if $content is an array
	if (is_array($content))
	{
		foreach ($content as $key=>$value)
		{
			$content[$key] = mysql_real_escape_string($value);
		}
	} else
	{
		//if $content is not an array
		mysql_real_escape_string($content);
	}
	return $content;
}


//request转码
function convert_req(&$req)
{
	foreach($req as $k=>$v)
	{
		if(is_array($v))
		{
			convert_req($req[$k]);
		}
		else
		{
			if(!is_u8($v))
			{
				$req[$k] = iconv("gbk","utf-8",$v);
			}
		}
	}
}

function is_u8($string)
{
	return preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]            # ASCII
	   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $string);
}


//清除缓存
function clear_cache()
{
		//系统后台缓存
		clear_dir_file(get_real_path()."public/runtime/admin/Cache/");	
		clear_dir_file(get_real_path()."public/runtime/admin/Data/_fields/");		
		clear_dir_file(get_real_path()."public/runtime/admin/Temp/");	
		clear_dir_file(get_real_path()."public/runtime/admin/Logs/");	
		@unlink(get_real_path()."public/runtime/admin/~app.php");
		@unlink(get_real_path()."public/runtime/admin/~runtime.php");
		@unlink(get_real_path()."public/runtime/admin/lang.js");
		@unlink(get_real_path()."public/runtime/app/config_cache.php");	
		
		
		//数据缓存
		clear_dir_file(get_real_path()."public/runtime/app/data_caches/");				
		clear_dir_file(get_real_path()."public/runtime/app/db_caches/");
		$GLOBALS['cache']->clear();
		clear_dir_file(get_real_path()."public/runtime/data/");

		//模板页面缓存
		clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");		
		clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");
		@unlink(get_real_path()."public/runtime/app/lang.js");	
		
		//脚本缓存
		clear_dir_file(get_real_path()."public/runtime/statics/");		
			
				
		
}
function clear_dir_file($path)
{
   if ( $dir = opendir( $path ) )
   {
            while ( $file = readdir( $dir ) )
            {
                $check = is_dir( $path. $file );
                if ( !$check )
                {
                    @unlink( $path . $file );                       
                }
                else 
                {
                 	if($file!='.'&&$file!='..')
                 	{
                 		clear_dir_file($path.$file."/");              			       		
                 	} 
                 }           
            }
            closedir( $dir );
            rmdir($path);
            return true;
   }
}


function check_install()
{
	if(!file_exists(get_real_path()."public/install.lock"))
	{
	    clear_cache();
		header('Location:'.APP_ROOT.'/install');
		exit;
	}
}


/**发邮件与短信的示例*/

function send_demo_mail()
{
	if(app_conf("MAIL_ON")==1)
	{		
			//$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg = "测试邮件";
			$msg_data['dest'] = "demo@demo.com";
			$msg_data['send_type'] = 1;
			$msg_data['title'] = "测试邮件";
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = get_gmtime();
			$msg_data['user_id'] = 0;
			$msg_data['is_html'] = 1;
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入

	}
}


function send_demo_sms()
{
	if(app_conf("SMS_ON")==1)
	{
		
			//$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg = "测试短信";
			$msg_data['dest'] = "13333333333";
			$msg_data['send_type'] = 0;
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['title'] = "测试短信";
			$msg_data['create_time'] = get_gmtime();
			$msg_data['user_id'] = 0;
			$msg_data['is_html'] = 0;
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入

	}
}


//utf8 字符串截取
function msubstr($str, $start=0, $length=15, $charset="utf-8", $suffix=true)
{
	if(function_exists("mb_substr"))
    {
        $slice =  mb_substr($str, $start, $length, $charset);
        if($suffix&$slice!=$str) return $slice."…";
    	return $slice;
    }
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix&&$slice!=$str) return $slice."…";
    return $slice;
}


//字符编码转换
if(!function_exists("iconv"))
{	
	function iconv($in_charset,$out_charset,$str)
	{
		require 'libs/iconv.php';
		$chinese = new Chinese();
		return $chinese->Convert($in_charset,$out_charset,$str);
	}
}

//JSON兼容
if(!function_exists("json_encode"))
{	
	function json_encode($data)
	{
		require_once 'libs/json.php';
		$JSON = new JSON();
		return $JSON->encode($data);
	}
}
if(!function_exists("json_decode"))
{	
	function json_decode($data)
	{
		require_once 'libs/json.php';
		$JSON = new JSON();
		return $JSON->decode($data,1);
	}
}

//邮件格式验证的函数
function check_email($email)
{
	if(!preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/",$email))
	{
		return false;
	}
	else
	return true;
}

//验证手机号码
function check_mobile($mobile)
{
	if(!empty($mobile) && !preg_match("/^([0-9]{11})?$/",$mobile))
	{
		return false;
	}
	else
	return true;
}
//验证邮编
function check_postcode($postcode)
{
	if(!empty($postcode) && !preg_match("/^([0-9]{6})(-[0-9]{5})?$/",$postcode))
	{
		return false;
	}
	else
	return true;
}
//验证验证码
function check_verify_coder($verify_coder){
	if(!empty($verify_coder) && !preg_match("/^([0-9]{4})?$/",$verify_coder))
	{
		return false;
	}
	else
	return true;
}
//跳转
function app_redirect($url,$time=0,$msg='')
{
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);    
    if(empty($msg))
        $msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if(0===$time) {
        	if(substr($url,0,1)=="/")
        	{        		
        		header("Location:".get_domain().$url);
        	}
        	else
        	{
        		header("Location:".$url);
        	}
            
        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0)
            $str   .=   $msg;
        exit($str);
    }
}



/**
 * 验证访问IP的有效性
 * @param ip地址 $ip_str
 * @param 访问页面 $module
 * @param 时间间隔 $time_span
 * @param 数据ID $id
 */
function check_ipop_limit($ip_str,$module,$time_span=0,$id=0)
{
		$op = es_session::get($module."_".$id."_ip");
    	if(empty($op))
    	{
    		$check['ip']	=	 get_client_ip();
    		$check['time']	=	get_gmtime();
    		es_session::set($module."_".$id."_ip",$check);    		
    		return true;  //不存在session时验证通过
    	}
    	else 
    	{   
    		$check['ip']	=	 get_client_ip();
    		$check['time']	=	get_gmtime();    
    		$origin	=	es_session::get($module."_".$id."_ip");
    		
    		if($check['ip']==$origin['ip'])
    		{
    			if($check['time'] - $origin['time'] < $time_span)
    			{
    				return false;
    			}
    			else 
    			{
    				es_session::set($module."_".$id."_ip",$check);
    				return true;  //不存在session时验证通过    				
    			}
    		}
    		else 
    		{
    			es_session::set($module."_".$id."_ip",$check);
    			return true;  //不存在session时验证通过
    		}
    	}
    }

function gzip_out($content)
{
	header("Content-type: text/html; charset=utf-8");
    header("Cache-control: private");  //支持页面回跳
	$gzip = app_conf("GZIP_ON");
	if( intval($gzip)==1 )
	{
		if(!headers_sent()&&extension_loaded("zlib")&&preg_match("/gzip/i",$_SERVER["HTTP_ACCEPT_ENCODING"]))
		{
			$content = gzencode($content,9);	
			header("Content-Encoding: gzip");
			header("Content-Length: ".strlen($content));
			echo $content;
		}
		else
		echo $content;
	}else{
		echo $content;
	}
	
}


/**
	 * 保存图片
	 * @param array $upd_file  即上传的$_FILES数组
	 * @param array $key $_FILES 中的键名 为空则保存 $_FILES 中的所有图片
	 * @param string $dir 保存到的目录
	 * @param array $whs
	 	可生成多个缩略图
		数组 参数1 为宽度，
			 参数2为高度，
			 参数3为处理方式:0(缩放,默认)，1(剪裁)，
			 参数4为是否水印 默认为 0(不生成水印)
	 	array(
			'thumb1'=>array(300,300,0,0),
			'thumb2'=>array(100,100,0,0),
			'origin'=>array(0,0,0,0),  宽与高为0为直接上传
			...
		)，
	 * @param array $is_water 原图是否水印
	 * @return array
	 	array(
			'key'=>array(
				'name'=>图片名称，
				'url'=>原图web路径，
				'path'=>原图物理路径，
				有略图时
				'thumb'=>array(
					'thumb1'=>array('url'=>web路径,'path'=>物理路径),
					'thumb2'=>array('url'=>web路径,'path'=>物理路径),
					...
				)
			)
			....
		)
	 */
//$img = save_image_upload($_FILES,'avatar','temp',array('avatar'=>array(300,300,1,1)),1);
function save_image_upload($upd_file, $key='',$dir='temp', $whs=array(),$is_water=false,$need_return = false)
{
		require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
		$image = new es_imagecls();
		$image->max_size = intval(app_conf("MAX_IMAGE_SIZE"));
		
		$list = array();

		if(empty($key))
		{
			foreach($upd_file as $fkey=>$file)
			{
				$list[$fkey] = false;
				$image->init($file,$dir);
				if($image->save())
				{
					$list[$fkey] = array();
					$list[$fkey]['url'] = $image->file['target'];
					$list[$fkey]['path'] = $image->file['local_target'];
					$list[$fkey]['name'] = $image->file['prefix'];
				}
				else
				{
					if($image->error_code==-105)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'上传的图片太大');
						}
						else
						echo "上传的图片太大";
					}
					elseif($image->error_code==-104||$image->error_code==-103||$image->error_code==-102||$image->error_code==-101)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'非法图像'.$image->error_code);
						}
						else
						echo "非法图像";
					}
					exit;
				}
			}
		}
		else
		{
			$list[$key] = false;
			$image->init($upd_file[$key],$dir);
			if($image->save())
			{
				$list[$key] = array();
				$list[$key]['url'] = $image->file['target'];
				$list[$key]['path'] = $image->file['local_target'];
				$list[$key]['name'] = $image->file['prefix'];
			}
			else
				{
					if($image->error_code==-105)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'上传的图片太大');
						}
						else
						echo "上传的图片太大";
					}
					elseif($image->error_code==-104||$image->error_code==-103||$image->error_code==-102||$image->error_code==-101)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'非法图像'.$image->error_code);
						}
						else
						echo "非法图像";
					}
					exit;
				}
		}

		$water_image = APP_ROOT_PATH.app_conf("WATER_MARK");
		$alpha = app_conf("WATER_ALPHA");
		$place = app_conf("WATER_POSITION");
		
		foreach($list as $lkey=>$item)
		{
				//循环生成规格图
				foreach($whs as $tkey=>$wh)
				{
					$list[$lkey]['thumb'][$tkey]['url'] = false;
					$list[$lkey]['thumb'][$tkey]['path'] = false;
					if($wh[0] > 0 || $wh[1] > 0)  //有宽高度
					{
						$thumb_type = isset($wh[2]) ? intval($wh[2]) : 0;  //剪裁还是缩放， 0缩放 1剪裁
						if($thumb = $image->thumb($item['path'],$wh[0],$wh[1],$thumb_type))
						{
							$list[$lkey]['thumb'][$tkey]['url'] = $thumb['url'];
							$list[$lkey]['thumb'][$tkey]['path'] = $thumb['path'];
							if(isset($wh[3]) && intval($wh[3]) > 0)//需要水印
							{
								$paths = pathinfo($list[$lkey]['thumb'][$tkey]['path']);
								$path = $paths['dirname'];
				        		$path = $path."/origin/";
				        		if (!is_dir($path)) { 
						             @mkdir($path);
						             @chmod($path, 0777);
					   			}   	    
				        		$filename = $paths['basename'];
								@file_put_contents($path.$filename,@file_get_contents($list[$lkey]['thumb'][$tkey]['path']));      
								$image->water($list[$lkey]['thumb'][$tkey]['path'],$water_image,$alpha, $place);
							}
						}
					}
				}
			if($is_water)
			{
				$paths = pathinfo($item['path']);
				$path = $paths['dirname'];
        		$path = $path."/origin/";
        		if (!is_dir($path)) { 
		             @mkdir($path);
		             @chmod($path, 0777);
	   			}   	    
        		$filename = $paths['basename'];
				@file_put_contents($path.$filename,@file_get_contents($item['path']));        		
				$image->water($item['path'],$water_image,$alpha, $place);
			}
		}			
		return $list;
}

function empty_tag($string)
{	
	$string = preg_replace(array("/\[img\]\d+\[\/img\]/","/\[[^\]]+\]/"),array("",""),$string);
	if(trim($string)=='')
	return $GLOBALS['lang']['ONLY_IMG'];
	else 
	return $string;
	//$string = str_replace(array("[img]","[/img]"),array("",""),$string);
}


/**
 * utf8字符转Unicode字符
 * @param string $char 要转换的单字符
 * @return void
 */
function utf8_to_unicode($char)
{
	switch(strlen($char))
	{
		case 1:
			return ord($char);
		case 2:
			$n = (ord($char[0]) & 0x3f) << 6;
			$n += ord($char[1]) & 0x3f;
			return $n;
		case 3:
			$n = (ord($char[0]) & 0x1f) << 12;
			$n += (ord($char[1]) & 0x3f) << 6;
			$n += ord($char[2]) & 0x3f;
			return $n;
		case 4:
			$n = (ord($char[0]) & 0x0f) << 18;
			$n += (ord($char[1]) & 0x3f) << 12;
			$n += (ord($char[2]) & 0x3f) << 6;
			$n += ord($char[3]) & 0x3f;
			return $n;
	}
}

/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @param string $depart 分隔,默认为空格为单字
 * @return string
 */
function str_to_unicode_word($str,$depart=' ')
{
	$arr = array();
	$str_len = mb_strlen($str,'utf-8');
	for($i = 0;$i < $str_len;$i++)
	{
		$s = mb_substr($str,$i,1,'utf-8');
		if($s != ' ' && $s != '　')
		{
			$arr[] = 'ux'.utf8_to_unicode($s);
		}
	}
	return implode($depart,$arr);
}


/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @return string
 */
function str_to_unicode_string($str)
{
	$string = str_to_unicode_word($str,'');
	return $string;
}

//分词
function div_str($str)
{
	require_once APP_ROOT_PATH."system/libs/words.php";
	$words = words::segment($str);
	$words[] = $str;	
	return $words;
}



/**
 * 
 * @param $tag  //要插入的关键词
 * @param $table  //表名
 * @param $id  //数据ID
 * @param $field		// tag_match/name_match/cate_match/locate_match
 */
function insert_match_item($tag,$table,$id,$field)
{
	if($tag=='')
	return;
	
	$unicode_tag = str_to_unicode_string($tag);
	$sql = "select count(*) from ".DB_PREFIX.$table." where match(".$field.") against ('".$unicode_tag."' IN BOOLEAN MODE) and id = ".$id;	
	$rs = $GLOBALS['db']->getOne($sql);
	if(intval($rs) == 0)
	{
		$match_row = $GLOBALS['db']->getRow("select * from ".DB_PREFIX.$table." where id = ".$id);
		if($match_row[$field]=="")
		{
				$match_row[$field] = $unicode_tag;
				$match_row[$field."_row"] = $tag;
		}
		else
		{
				$match_row[$field] = $match_row[$field].",".$unicode_tag;
				$match_row[$field."_row"] = $match_row[$field."_row"].",".$tag;
		}
		$GLOBALS['db']->autoExecute(DB_PREFIX.$table, $match_row, $mode = 'UPDATE', "id=".$id, $querymode = 'SILENT');	
		
	}	
}
/**同步索引的示例
function syn_supplier_match($supplier_id)
{
	$supplier = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".$supplier_id);
	if($supplier)
	{
		$supplier['name_match'] = "";
		$supplier['name_match_row'] = "";
		$GLOBALS['db']->autoExecute(DB_PREFIX."supplier", $supplier, $mode = 'UPDATE', "id=".$supplier_id, $querymode = 'SILENT');	
		
		
		//同步名称
		$name_arr = div_str(trim($supplier['name'])); 
		foreach($name_arr as $name_item)
		{
			insert_match_item($name_item,"supplier",$supplier_id,"name_match");
		}
		
	}
}
*/

//封装url

function url($route="index",$param=array())
{
	$key = md5("URL_KEY_".$route.serialize($param));
	if(isset($GLOBALS[$key]))
	{
		$url = $GLOBALS[$key];
		return $url;
	}
	
	$url = load_dynamic_cache($key);
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	
	$route_array = explode("#",$route);
	
	if(isset($param)&&$param!=''&&!is_array($param))
	{
		$param['id'] = $param;
	}

	$module = strtolower(trim($route_array[0]));
	$action = strtolower(trim($route_array[1]));

	if(!$module||$module=='index')$module="";
	if(!$action||$action=='index')$action="";
	
	if(app_conf("URL_MODEL")==0)
	{
	//原始模式
		$url = APP_ROOT."/index.php";
		if($module!=''||$action!=''||count($param)>0) //有后缀参数
		{
			$url.="?";
		}
	
		if($module&&$module!='')
		$url .= "ctl=".$module."&";
		if($action&&$action!='')
		$url .= "act=".$action."&";
		if(count($param)>0)
		{
			foreach($param as $k=>$v)
			{
				if($k&&$v)
				$url =$url.$k."=".urlencode($v)."&";
			}
		}
		if(substr($url,-1,1)=='&'||substr($url,-1,1)=='?') $url = substr($url,0,-1);
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	else
	{
		//重写的默认
		$url = APP_ROOT;

		if($module&&$module!='')
		$url .= "/".$module;
		if($action&&$action!='')
		$url .= "-".$action;
		
		if(count($param)>0)
		{
			$url.="/";
			foreach($param as $k=>$v)
			{
				$url =$url.$k."-".urlencode($v)."-";
			}
		}
		
		$route = $module."#".$action;
		switch ($route)
		{
				case "xxx":
					break;
				default:
					break;
		}
				
		if(substr($url,-1,1)=='/'||substr($url,-1,1)=='-') $url = substr($url,0,-1);		
		
		if($url=='')$url="/";
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	
	
}
//封装url

function url_mapi($route="index",$param=array())
{
	$key = md5("URL_KEY_".$route.serialize($param));
	if(isset($GLOBALS[$key]))
	{
		$url = $GLOBALS[$key];
		return $url;
	}
	
	$url = load_dynamic_cache($key);
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	
	$route_array = explode("#",$route);
	
	if(isset($param)&&$param!=''&&!is_array($param))
	{
		$param['id'] = $param;
	}

	$module = strtolower(trim($route_array[0]));
	$action = strtolower(trim($route_array[1]));

	if(!$module||$module=='index')$module="";
	if(!$action||$action=='index')$action="";
	
	if(app_conf("URL_MODEL")==0)
	{
	//原始模式
		$url = get_domain().APP_ROOT."/index.php";
		if($module!=''||$action!=''||count($param)>0) //有后缀参数
		{
			$url.="?";
		}
	
		if($module&&$module!='')
		$url .= "ctl=".$module."&";
		if($action&&$action!='')
		$url .= "act=".$action."&";
		if(count($param)>0)
		{
			foreach($param as $k=>$v)
			{
				if($k&&$v)
				$url =$url.$k."=".urlencode($v)."&";
			}
		}
		if(substr($url,-1,1)=='&'||substr($url,-1,1)=='?') $url = substr($url,0,-1);
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	else
	{
		//重写的默认
		$url = get_domain().APP_ROOT;

		if($module&&$module!='')
		$url .= "/".$module;
		if($action&&$action!='')
		$url .= "-".$action;
		
		if(count($param)>0)
		{
			$url.="/";
			foreach($param as $k=>$v)
			{
				$url =$url.$k."-".urlencode($v)."-";
			}
		}
		
		$route = $module."#".$action;
		switch ($route)
		{
				case "xxx":
					break;
				default:
					break;
		}
				
		if(substr($url,-1,1)=='/'||substr($url,-1,1)=='-') $url = substr($url,0,-1);		
		
		if($url=='')$url="/";
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	
	
}
//手机端 访问根目录的url
function url_root($route="index",$param=array())
{
	$key = md5("URL_KEY_".$route.serialize($param));
	if(isset($GLOBALS[$key]))
	{
		$url = $GLOBALS[$key];
		return $url;
	}
	
	$url = load_dynamic_cache($key);
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	
	$route_array = explode("#",$route);
	
	if(isset($param)&&$param!=''&&!is_array($param))
	{
		$param['id'] = $param;
	}

	$module = strtolower(trim($route_array[0]));
	$action = strtolower(trim($route_array[1]));

	if(!$module||$module=='index')$module="";
	if(!$action||$action=='index')$action="";
	
	if(app_conf("URL_MODEL")==0)
	{
	//原始模式
		$url = get_domain().REAL_APP_ROOT."/index.php";
		if($module!=''||$action!=''||count($param)>0) //有后缀参数
		{
			$url.="?";
		}
	
		if($module&&$module!='')
		$url .= "ctl=".$module."&";
		if($action&&$action!='')
		$url .= "act=".$action."&";
		if(count($param)>0)
		{
			foreach($param as $k=>$v)
			{
				if($k&&$v)
				$url =$url.$k."=".urlencode($v)."&";
			}
		}
		if(substr($url,-1,1)=='&'||substr($url,-1,1)=='?') $url = substr($url,0,-1);
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	else
	{
		//重写的默认
		$url = get_domain().REAL_APP_ROOT;

		if($module&&$module!='')
		$url .= "/".$module;
		if($action&&$action!='')
		$url .= "-".$action;
		
		if(count($param)>0)
		{
			$url.="/";
			foreach($param as $k=>$v)
			{
				$url =$url.$k."-".urlencode($v)."-";
			}
		}
		
		$route = $module."#".$action;
		switch ($route)
		{
				case "xxx":
					break;
				default:
					break;
		}
				
		if(substr($url,-1,1)=='/'||substr($url,-1,1)=='-') $url = substr($url,0,-1);		
		
		if($url=='')$url="/";
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	
	
}

function unicode_encode($name) {//to Unicode
    $name = iconv('UTF-8', 'UCS-2', $name);
    $len = strlen($name);
    $str = '';
    for($i = 0; $i < $len - 1; $i = $i + 2) {
        $c = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0) {// 两个字节的字
            $cn_word = '\\'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
            $str .= strtoupper($cn_word);
        } else {
            $str .= $c2;
        }
    }
    return $str;
}

function unicode_decode($name) {//Unicode to
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches)) {
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++) {
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0) {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            } else {
                $name .= $str;
            }
        }
    }
    return $name;
}


//载入动态缓存数据
function load_dynamic_cache($name)
{
	if(isset($GLOBALS['dynamic_cache'][$name]))
	{
		return $GLOBALS['dynamic_cache'][$name];
	}
	else
	{
		return false;
	}
}

function set_dynamic_cache($name,$value)
{
	if(!isset($GLOBALS['dynamic_cache'][$name]))
	{
		if(count($GLOBALS['dynamic_cache'])>MAX_DYNAMIC_CACHE_SIZE)
		{
			array_shift($GLOBALS['dynamic_cache']);
		}
		$GLOBALS['dynamic_cache'][$name] = $value;		
	}
}

function load_auto_cache($key,$param=array())
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$result = $obj->load($param);
	}
	else
	$result = false;
	return $result;
}

function rm_auto_cache($key,$param=array())
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$obj->rm($param);
	}
}


function clear_auto_cache($key)
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$obj->clear_all();
	}
}


/*ajax返回*/
function ajax_return($data)
{
		header("Content-Type:text/html; charset=utf-8");
        echo(json_encode($data));
        exit;	
}



function is_animated_gif($filename){
 $fp=fopen($filename, 'rb');
 $filecontent=fread($fp, filesize($filename));
 fclose($fp);
 return strpos($filecontent,chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0')===FALSE?0:1;
}



function update_sys_config()
{
	$filename = APP_ROOT_PATH."public/sys_config.php";
	if(!file_exists($filename))
	{
		//定义DB
		require APP_ROOT_PATH.'system/db/db.php';
		$dbcfg = require APP_ROOT_PATH."public/db_config.php";
		define('DB_PREFIX', $dbcfg['DB_PREFIX']); 
		if(!file_exists(APP_ROOT_PATH.'public/runtime/app/db_caches/'))
			mkdir(APP_ROOT_PATH.'public/runtime/app/db_caches/',0777);
		$pconnect = false;
		$db = new mysql_db($dbcfg['DB_HOST'].":".$dbcfg['DB_PORT'], $dbcfg['DB_USER'],$dbcfg['DB_PWD'],$dbcfg['DB_NAME'],'utf8',$pconnect);
		//end 定义DB

		$sys_configs = $db->getAll("select * from ".DB_PREFIX."conf");
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
		}
		$config_str.=");\n ?>";	
		file_put_contents($filename,$config_str);
		$url = APP_ROOT."/";
		app_redirect($url);
	}
}


function gen_qrcode($str,$size = 5)
{

	require_once APP_ROOT_PATH."system/phpqrcode/qrlib.php";

	$root_dir = APP_ROOT_PATH."public/images/qrcode/";
 	if (!is_dir($root_dir)) {
            @mkdir($root_dir);               
            @chmod($root_dir, 0777);
     }
     
     $filename = md5($str."|".$size);
     $hash_dir = $root_dir. '/c' . substr(md5($filename), 0, 1)."/";
     if (!is_dir($hash_dir))
     {
        @mkdir($hash_dir);
        @chmod($hash_dir, 0777);
     }   
	
	$filesave = $hash_dir.$filename.'.png';

	if(!file_exists($filesave))
	{
		QRcode::png($str, $filesave, 'Q', $size, 2); 
	}	
	return APP_ROOT."/public/images/qrcode/c". substr(md5($filename), 0, 1)."/".$filename.".png";       
}

function format_price($v)
{
	return "¥".number_format($v,2);
}

function syn_deal($deal_id)
{
	$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	
	if($deal_info)
	{
		$deal_info['comment_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_comment where deal_id = ".$deal_info['id']." and log_id = 0"));
		$deal_info['support_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_support_log where deal_id = ".$deal_info['id']));
		$deal_info['focus_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_focus_log where deal_id = ".$deal_info['id']));
		$deal_info['view_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_visit_log where deal_id = ".$deal_info['id']));
		$deal_info['support_amount'] = doubleval($GLOBALS['db']->getOne("select sum(price) from ".DB_PREFIX."deal_support_log where deal_id = ".$deal_info['id']));
		$deal_info['delivery_fee_amount'] = doubleval($GLOBALS['db']->getOne("select sum(delivery_fee) from ".DB_PREFIX."deal_order where deal_id = ".$deal_info['id']." and order_status=3 "));
		
		if($deal_info['pay_radio'] > 0){
			$deal_info['pay_amount'] = ($deal_info['support_amount']*(1-$deal_info['pay_radio']))+$deal_info['delivery_fee_amount'];
		}
		else
		{
			$deal_info['pay_amount'] = ($deal_info['support_amount']*(1-app_conf("PAY_RADIO")))+$deal_info['delivery_fee_amount'];
		
		}
	
		//$deal_info['pay_amount'] = ($deal_info['support_amount']*(1-app_conf("PAY_RADIO")))+$deal_info['delivery_fee_amount'];
		

		if($deal_info['support_amount']>=$deal_info['limit_price'])
		{
			$deal_info['is_success'] = 1;
		}
		else
		{
			$deal_info['is_success'] = 0;
		}
		
		$deal_info['tags_match'] = "";
		$deal_info['tags_match_row'] = "";
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal", $deal_info, $mode = 'UPDATE', "id=".$deal_info['id'], $querymode = 'SILENT');	
		
		$tags_arr = preg_split("/[, ]/",$deal_info["tags"]);

		foreach($tags_arr as $tgs){
			if(trim($tgs)!="")
			insert_match_item(trim($tgs),"deal",$deal_info['id'],"tags_match");
		}
		
		$name_arr = div_str($deal_info['name']);
		foreach($name_arr as $name_item){
			if(trim($name_item)!="")
			insert_match_item(trim($name_item),"deal",$deal_info['id'],"name_match");
		}

	}


}



//发密码验证邮件
function send_user_password_mail($user_id)
{

		$verify_code = rand(111111,999999);
		$GLOBALS['db']->query("update ".DB_PREFIX."user set password_verify = '".$verify_code."' where id = ".$user_id);
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);			
		if($user_info)
		{
			$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_USER_PASSWORD'");
			$tmpl_content=  $tmpl['content'];
			$user_info['logo']=app_conf("SITE_LOGO");
			$user_info['site_name']=app_conf("SITE_NAME");
			$time=get_gmtime();
			$user_info['send_time']=to_date($time,'Y年m月n日');
			$user_info['send_time_ms']=to_date($time,'Y年m月n日 h时i分');
			
			$user_info['password_url'] = get_domain().url("settings#password", array("code"=>$user_info['password_verify'],"id"=>$user_info['id']));			
			$GLOBALS['tmpl']->assign("user",$user_info);
			$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg_data['dest'] = $user_info['email'];
			$msg_data['send_type'] = 1;
			$msg_data['title'] = "重置密码";
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = get_gmtime();
			$msg_data['user_id'] = $user_info['id'];
			$msg_data['is_html'] = $tmpl['is_html'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
		}

}

function strim($str)
{
	return quotes(htmlspecialchars(trim($str)));
}
function btrim($str)
{
	return quotes(trim($str));
}
function valid_tag($str)
{
	
	return preg_replace("/<(?!div|ol|ul|li|sup|sub|span|br|img|p|h1|h2|h3|h4|h5|h6|\/div|\/ol|\/ul|\/li|\/sup|\/sub|\/span|\/br|\/img|\/p|\/h1|\/h2|\/h3|\/h4|\/h5|\/h6|blockquote|\/blockquote|strike|\/strike|b|\/b|i|\/i|u|\/u)[^>]*>/i","",$str);
}

//$type = 1(添加) 2(删除)
function update_user_weibo($user_id,$weibo_url,$type=1)
{
	if($weibo_url!="")
	{
		if($type==1)
		{
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_weibo where weibo_url = '".$weibo_url."' and user_id = ".$user_id)==0)
			{
				$weibo_data['user_id'] = $user_id;
				$weibo_data['weibo_url'] = $weibo_url;				
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_weibo",$weibo_data);
			}
		}
		if($type==2)
		{
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_weibo where user_id = ".$user_id." and weibo_url = '".$weibo_url."'");
		}		
	}
}


//返回array: status:0:未支付 1:已支付(过期) 2:已支付(无库存) 3:成功  money:剩余需支付金额 4:已支付但未判定（锁住订单）
function pay_order($order_id)
{
	require_once APP_ROOT_PATH."system/libs/user.php";	
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
	$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set order_status = 4 where id = ".$order_id." and (credit_pay+online_pay>=total_price) and order_status = 0");
	if($GLOBALS['db']->affected_rows()>0) //订单已成功支付
	{
		$order_info['order_status'] = 4;
		if($order_info['credit_pay']+$order_info['online_pay']>$order_info['total_price'])
		{
			$more_money = $order_info['credit_pay']+$order_info['online_pay'] - $order_info['total_price'];
			modify_account(array("money"=>$more_money),$order_info['user_id'],$order_info['deal_name']."超额支付，转存入会员帐户");
		}
		
		$order_info['pay_time'] = get_gmtime();
		$GLOBALS['db']->query("update ".DB_PREFIX."deal set support_count = support_count + 1,support_amount = support_amount + ".$order_info['deal_price'].",pay_amount = pay_amount + ".$order_info['total_price'].",delivery_fee_amount = delivery_fee_amount + ".$order_info['delivery_fee']." where id = ".$order_info['deal_id']." and is_effect = 1 and is_delete = 0 and begin_time < ".get_gmtime()." and (end_time > ".get_gmtime()." or end_time = 0)");
		if($GLOBALS['db']->affected_rows()>0)
		{
			//记录支持日志
			$support_log['deal_id'] = $order_info['deal_id'];
			$support_log['user_id'] = $order_info['user_id'];
			$support_log['create_time'] = get_gmtime();
			$support_log['price'] = $order_info['deal_price'];
			$support_log['deal_item_id'] = $order_info['deal_item_id'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_support_log",$support_log);
			$support_log_id = intval($GLOBALS['db']->insert_id());
			
			
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_item set support_count = support_count + 1,support_amount = support_amount +".$order_info['deal_price']." where (support_count + 1 <= limit_user or limit_user = 0) and id = ".$order_info['deal_item_id']);
			if($GLOBALS['db']->affected_rows()>0)
			{
				$result['status'] = 3;
				$order_info['order_status'] = 3;	

				
				$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$order_info['deal_id']." and is_effect = 1 and is_delete = 0");
				//下单项目成功，准备加入准备队列
				if($deal_info['is_success'] == 0)
				{
					//未成功的项止准备生成队列
					$notify['user_id'] = $GLOBALS['user_info']['id'];
					$notify['deal_id'] = $deal_info['id'];
					$notify['create_time'] = get_gmtime();
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_deal_notify",$notify,"INSERT","","SILENT");

				}
				
				//更新用户的支持数
				$GLOBALS['db']->query("update ".DB_PREFIX."user set support_count = support_count + 1 where id = ".$order_info['user_id']);
				//同步deal_log中的deal_info_cache
				
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_log set deal_info_cache = '' where deal_id = ".$deal_info['id']);
				$GLOBALS['db']->query("update ".DB_PREFIX."deal set deal_extra_cache = '' where id = ".$deal_info['id']);
				
				//同步项目状态
				syn_deal_status($order_info['deal_id']);				
				syn_deal($order_info['deal_id']);
				
				
				
			}
			else
			{
				$result['status'] = 2;
				$order_info['order_status'] = 2;
				$order_info['is_refund'] =1;
				$GLOBALS['db']->query("update ".DB_PREFIX."deal set support_count = support_count - 1,support_amount = support_amount - ".$order_info['deal_price'].",pay_amount = pay_amount - ".$order_info['total_price'].",delivery_fee_amount = delivery_fee_amount - ".$order_info['delivery_fee']." where id = ".$order_info['deal_id']);
				$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_support_log where id = ".$support_log_id);
				modify_account(array("money"=>$order_info['total_price']),$order_info['user_id'],$order_info['deal_name']."限额已满，转存入会员帐户");
			}
		}
		else
		{
			$result['status'] =1;
			$order_info['order_status'] =1;
			$order_info['is_refund'] =1;
			modify_account(array("money"=>$order_info['total_price']),$order_info['user_id'],$order_info['deal_name']."已过期，转存入会员帐户");
		}
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set order_status = ".intval($order_info['order_status']).",pay_time = ".$order_info['pay_time'].",is_refund = ".$order_info['is_refund']." where id = ".$order_info['id']);
		
	}
	else
	{
		$result['status'] = 0;
		$result['money'] = $order_info['total_price'] - $order_info['online_pay'] - $order_info['credit_pay'];
	}
	return $result;
}

function syn_deal_status($deal_id)
{
	$GLOBALS['db']->query("update ".DB_PREFIX."deal set is_success = 1,success_time = ".get_gmtime()." where id = ".$deal_id." and is_effect=  1 and is_delete = 0 and support_amount >= limit_price and begin_time <".get_gmtime()." and (end_time > ".get_gmtime()." or end_time = 0)");
	if($GLOBALS['db']->affected_rows()>0)
	{		
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_success = 1 where deal_id = ".$deal_id);
		//项目成功，加入项目成功的待发队列
		$deal_notify['deal_id'] = $deal_id;
		$deal_notify['create_time'] = get_gmtime();
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_notify",$deal_notify,"INSERT","","SILENT");	
	}
}

//同步到微博
function syn_weibo($data)
{
	$api_list = $GLOBALS['db']->getAllCached("select * from ".DB_PREFIX."api_login where is_weibo = 1");
	foreach($api_list as $k=>$v)
	{
		if($GLOBALS['user_info'][strtolower($v['class_name'])."_id"]==""||$GLOBALS['user_info'][strtolower($v['class_name'])."_token"]=="")
		{
			unset($api_list[$k]);
		}
		else
		{
			$class_name = $v['class_name']."_api";
			require_once APP_ROOT_PATH."system/api_login/".$class_name.".php";
			$o = new $class_name($v);
			$o->send_message($data);
		}
	}
}


//发送给用户通知
function send_notify($user_id,$content,$url_route,$url_param)
{
	$notify = array();
	$notify_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($user_id));
	if($notify_user)
	{
		$notify['user_id'] = $user_id;
		$notify['log_info'] = $content;
		$notify['log_time'] = get_gmtime();
		$notify['url_route'] = $url_route;
		$notify['url_param'] = $url_param;
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_notify",$notify,"INSERT","","SILENT");
	}
	
}
//发注册验证邮件
function send_user_verify_mail($user_id)
{
	if(app_conf("MAIL_ON")==1)
	{
		$verify_code = rand(111111,999999);
		$GLOBALS['db']->query("update ".DB_PREFIX."user set verify = '".$verify_code."' where id = ".$user_id);
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);			
		if($user_info)
		{
			$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_USER_VERIFY'");
			$tmpl_content=  $tmpl['content'];
			
			$user_info['logo']=app_conf("SITE_LOGO");
			$user_info['site_name']=app_conf("SITE_NAME");
			$time=get_gmtime();
			$user_info['send_time']=to_date($time,'Y年m月n日');
			$user_info['send_time_ms']=to_date($time,'Y年m月n日 h时i分');
			$user_info['verify_url'] = get_domain().url_wap("user#verify",array("id"=>$user_info['id'],"code"=>$user_info['verify']));			
			
			$GLOBALS['tmpl']->assign("user",$user_info);
			$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
                        
			$msg_data['dest'] = $user_info['email'];
			$msg_data['send_type'] = 1;
			$msg_data['title'] = app_conf("SITE_NAME")."帐号-帐号激活";
			$msg_data['content'] = addslashes($msg);;
			$msg_data['send_time'] = 0; 
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = get_gmtime();
			$msg_data['user_id'] = $user_info['id'];
			$msg_data['is_html'] = $tmpl['is_html'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
		}
	}
}

//发短信验证码
function send_verify_sms($mobile,$code,$type="nomal")
{
	if(app_conf("SMS_ON")==1)
	{
										
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_VERIFY_CODE'");				
				$tmpl_content = $tmpl['content'];
				$verify['mobile'] = $mobile;
				$verify['code'] = $code;
				$GLOBALS['tmpl']->assign("verify",$verify);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $mobile;
				$msg_data['send_type'] = 0;
				$msg_data['title'] =  "短信验证码";
				$msg_data['content'] = addslashes($msg);
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = get_gmtime();
 				$msg_data['user_id'] = $GLOBALS['user_info']['id'];
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入		
	}
}

//项目成功发送短信、回报短信(所有成功项目的支持人、项目创立者）
function send_pay_success($log_info){
	if(app_conf("SMS_ON")==0){
		return false;
	}
	//项目成功发起者短信
	$deal_s_user=$GLOBALS['db']->getAll("select *,u.mobile from ".DB_PREFIX."deal d LEFT JOIN ".DB_PREFIX."user u ON u.id = d.user_id where d.is_success='1' and d.is_has_send_success='0' and d.is_delete = 0 ");
	$tmpl3=$GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name='TPL_SMS_USER_S'");
	$tmpl_content3 = $tmpl3['content'];
	
	foreach ($deal_s_user as $k=>$v){
		if($v['id']){
		$user_s_msg['user_name']=$v['user_name'];
		$user_s_msg['deal_name']=$v['name'];
	
		$GLOBALS['tmpl']->assign("user_s_msg",$user_s_msg);
		$msg3=$GLOBALS['tmpl']->fetch("str:".$tmpl_content3);
		$msg_data3['dest']=$v['mobile'];
		$msg_data3['send_type']=0;
		$msg_data3['content']=addslashes($msg3);
		$msg_data3['send_time']=0;
		$msg_data['title']='项目成功发起者-'.$v['name'];
		$msg_data3['is_send']=0;
		$msg_data3['create_time'] = NOW_TIME;
		$msg_data3['user_id'] = $v['user_id'];
		$msg_data3['is_html'] = $tmpl3['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data3); //插入
	
		}
	}
	

	
}
//获取系统运行上传的值
function get_max_file_size(){
	$system_size=intval(ini_get("post_max_size"))<intval(ini_get("upload_max_filesize"))?intval(ini_get("post_max_size"))*1024*1024:intval(ini_get("upload_max_filesize"))*1024*1024;
	$config_size=app_conf("MAX_IMAGE_SIZE");
	return $system_size>$config_size?number_format($config_size/(1024*1024),1):number_format($system_size/(1024*1024),1);
}

function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
        return true;
    }
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
    //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array (
                                'nokia',
                                'sony',
                                'ericsson',
                                'mot',
                                'samsung',
                                'htc',
                                'sgh',
                                'lg',
                                'sharp',
                                'sie-',
                                'philips',
                                'panasonic',
                                'alcatel',
                                'lenovo',
                                'iphone',
                                'ipod',
                                'blackberry',
                                'meizu',
                                'android',
                                'netfront',
                                'symbian',
                                'ucweb',
                                'windowsce',
                                'palm',
                                'operamini',
                                'operamobi',
                                'openwave',
                                'nexusone',
                                'cldc',
                                'midp',
                                'wap',
                                'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
}
 
 //更新当前项目的姿态
 function set_deal_status($deal){
 	$now_time=NOW_TIME;
 	if($deal['end_time']<$now_time){
 		$GLOBALS['db']->query("update ".DB_PREFIX."investment_list set investor_money_status=2 where deal_id=$deal_id and investor_money_status=0 ");
 	}
 	if($deal['pay_end_time']<$now_time){
 		$GLOBALS['db']->query("update ".DB_PREFIX."investment_list set investor_money_status=4 where deal_id=$deal_id and investor_money_status=1 ");
 	}
 }
 
 /*将会员余额转化为诚意金*/
 function get_mortgate(){
 	require_once APP_ROOT_PATH."system/libs/user.php";
 	$list=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."payment_notice where is_mortgate=1 and is_paid=1 and order_id=0 ");
 	foreach($list as $k=>$row){
 		if($row['money']>0){
 			$user_id=$row['user_id'];
 			$money=$row['money'];
 			$row['money']="-".$row['money'];
 			modify_account($row,$user_id,'冻结为诚意金');
 			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user set mortgage_money=mortgage_money+".$money." where id=".$user_id." ");
 			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."payment_notice set is_mortgate=2 where id=".$row['id']."  ");
 			$mortgate_info['user_id']=$user_id;
 			$mortgate_info['notice_id']=$row['id'];
 			$mortgate_info['money']=$row['money'];
 			$mortgate_info['status']=1;
 			$GLOBALS['db']->autoExecute(DB_PREFIX."mortgate",$mortgate_info);
 		}
 	}
 } 	 
 	/*判断用户当前需要的诚意金数量*/
 	function user_need_mortgate(){
 		get_mortgate();
 		$now_time=NOW_TIME;
 		$GLOBALS['db']->query("update ".DB_PREFIX."investment_list set investor_money_status=4 where investor_money_status=1 and deal_id in (select d.pay_end_time from ".DB_PREFIX."deal as d where d.type=1 and d.pay_end_time<".$now_time."  )  ");
 		$num=$GLOBALS['db']->getRow("select count(*) from ".DB_PREFIX."investment_list where investor_money_status=4 group by deal_id ");
 		$num=intval($num)+1;
 		$money=need_mortgate();
 		return $money*$num;
 	}
 	
 	/*获取询价次数*/
 	function get_ask(){
 		return app_conf("ENQUIER_NUM");
 	}
 	//用户违约时，扣掉违约金，并增加一次违约次数，同时记录数据库
 	function deal_invest_break($invest_id){
 		$invest=$GLOBALS['db']->getRow("select invest.*,d.end_time,d.pay_end_time from ".DB_PREFIX."investment_list as invest left join ".DB_PREFIX."deal as d on d.id=invest.deal_id  where invest.id=$invest_id ");
 		if($invest['investor_money_status']==1&&$invest['pay_end_time']<NOW_TIME){
 			//用户违约
 			//$money=need_mortgate();
 			//$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user set mortgage_money=mortgage_money-".$money." where id=".$invest['user_id']." and mortgage_money>".$money);
 			//if($GLOBALS['db']->affected_rows()>0){
 			$GLOBALS['db']->query("UPDATE  ".DB_PREFIX."investment_list set investor_money_status=4 where id= ".$invest_id);
 			//$data=array('money'=>"-".$money);
 			//add_log($data,$invest['user_id'],"用户违约，ID为".$invest_id."的申请，扣除诚意金$money元");
 			//}
 		}
 	}
 	
 	//判断是否够当前项目的资金
 	function mini_investor_money($money,$deal_id){
 		$invote_mini_money=$GLOBALS['db']->getOne("select invote_mini_money from ".DB_PREFIX."deal where id=$deal_id ");
 		$return=array('status'=>0,'info'=>'');
 		if($money<$invote_mini_money){
 			$return['info']='投资的金额不能低于'.($invote_mini_money/10000).'万元';
 		}else{
 			$return['status']=1;
 		}
 		return $return;
 	}
 	
 	/*当认购资金大于项目的筹资是，判断项目成功*/
 	function investor_is_success($deal_id){
 		$deal=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal where id=$deal_id ");
 		syn_deal($deal_id);
 		if($deal){
 			$yu_money=$GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."investment_list where deal_id=$deal_id ");
 			if($yu_money>=$deal['limit_price']){
 				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal set is_success=1 where id=$deal_id ");
 			}
 		}
 	}
 	
 	//申请领投
 	function investor_applicate_leader($deal_id,$user_id){
 		if(!$deal_id){
 			showErr("您查找的页面不存在");
 		}
 		$cates=$GLOBALS['db']->getAllCached("SELECT name,id FROM ".DB_PREFIX."deal_cate ORDER BY sort ASC");
 		$row=array();
 		$row=$GLOBALS['db']->getRow("SELECT * from ".DB_PREFIX."investment_list where deal_id=$deal_id and user_id=$user_id and type=1 ");
 	
 		if($row){
 			if($row['status']==1){
 				showSuccess("您的申请已经通过");
 			}
 			$row['cates']=unserialize($row['cates']);
 			foreach($row['cates'] as $k=>$v){
 				foreach($cates as $k1=>$v1){
 					if($k==$v1['id']){
 						$cates[$k1]['selected']=1;
 					}
 				}
 			}
 		}
 		$GLOBALS['tmpl']->assign("item",$row);
 		$GLOBALS['tmpl']->assign("cates",$cates);
 		$GLOBALS['tmpl']->assign("deal_id",$deal_id);
 		$GLOBALS['tmpl']->display("user_applicate_leader.html");
 	}
 	
 	//领投ajax判断
 	function  investor_leader_ajax($user_id,$deal_id,$ajax='',$from='wap'){
 		$invest=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."investment_list where user_id=".$user_id." and type=1 and deal_id=".$deal_id);
 		//0表示错误，1表示进行投资，2表示申请领头权限，3表示支付诚意金,4 追加投资 5追加资金审核不通过 6申请领头权限不通过 7已经“领投”,无法再跟投
 		//8项目已经结束无法再进行投资 9判断“投资者认证成功”
 		$result=array('status'=>1,'info'=>'','url'=>'','html'=>'');
 		//项目结束出提示“不能再进行投资！”
 		$deal_ifo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal WHERE id=".$deal_id);
 		if($deal_ifo['user_id']==$user_id){
 			$result['status']=0;
 			$result['info']="不能投资自己的项目！";
 			return $result;
 		}
 		if($deal_ifo['end_time']<NOW_TIME){
 			$result['status']=0;
 			$result['info']="项目已经结束无法投资！";
 			return $result;
 		}
 		//判断“投资者认证成功”
 		if($GLOBALS['user_info']['is_investor']==0){
 			$result['status']=0;
 			$result['info']="您是普通用户,请申请投资人";
 			if($from=='web'){
 				$result['url']=url("investor#index");
 			}elseif($from=='wap'){
 				$result['url']=url_wap("investor#index");
 			}
 				
 			return $result;
 		}else{
 				
 			if($GLOBALS['user_info']['investor_status']==0){
 				$result['status']=0;
 				$result['info']="投资者认证在审核中";
 				return $result;
 			}elseif($GLOBALS['user_info']['investor_status']==2){
 				$result['status']=0;
 				$result['info']="投资者认证未通过，请重新提交";
 				if($from=='web'){
 					$result['url']=url("investor#index");
 				}elseif($from=='wap'){
 					$result['url']=url_wap("investor#index");
 				}
 				return $result;
 			}
 			//判断是否有“跟投”
 			$continue_investor_status=$GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."investment_list WHERE user_id=".$user_id." AND deal_id=".$deal_id." AND type=2");
 			if($continue_investor_status>0){
 				//已经“领投”,无法再跟投
 				$result['status']=0;
 				$result['info']="已经跟投，无需再进行领投！";
 				return $result;
 			}
 			$num=$GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."investment_list where user_id!=".$user_id." and status=1 and type=1 and deal_id=".$deal_id);
 			if($num>0){
 				//领投未审核
 				$result['status']=0;
 				$result['info']="领投用户已经存在,无法申请!";
 				return $result;
 			}
 	
 			if(!$invest){
 				$result['status']=0;
 				$result['info']="进行领投申请";
 				if($from=='web'){
 					$result['url']=url("investor#applicate_leader",array("deal_id"=>$deal_id));
 				}elseif($from=='wap'){
 					$result['url']=url_wap("investor#applicate_leader",array("deal_id"=>$deal_id));
 				}
 				return $result;
 			}
 	
 			if($invest['status']==0){
 				//领投未审核
 				$result['status']=0;
 				$result['info']="您的申请已在审核中!";
 				return $result;
 			}
 	
 			if($invest['status']==1){
 				//判断是否有诚意金
 				$margator_money=$GLOBALS['user_info']['mortgage_money'];
 					
 				if($margator_money>=user_need_mortgate()){
 					//是否已经支付过了(判断钱)
 					$num=$GLOBALS['db']->getOne("SELECT count(*) from ".DB_PREFIX."investment_list WHERE user_id=".$user_id." AND deal_id=$deal_id AND money!=0");
 						
 					if($num>0){
 						if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."investment_list WHERE user_id=".$user_id." AND money>0 AND (type IN (1)) AND investor_money_status=0 AND deal_id=".$deal_id)>0){
 							$result['status']=1;
 							$result['boxid']="append_one_money";
 							$result['title']="追加投资";
 							//调用追加资金页面
 							$GLOBALS['tmpl']->assign("user_id",$user_id);
 							$GLOBALS['tmpl']->assign("deal_id",$deal_id);
 							$result['html'] = $GLOBALS['tmpl']->fetch("inc/append_money.html");
 							return $result;
 						}else{
 							$result['status']=0;
 							$result['info']="您的资金已经无法再追加了！";
 							return $result;
 						}
 					}else{
 						//调用领投资金页面
 						$result['status']=1;
 						$result['boxid']="append_money";
 						$result['title']="领投投资";
 						$GLOBALS['tmpl']->assign("user_id",$user_id);
 						$GLOBALS['tmpl']->assign("deal_id",$deal_id);
 						$result['html'] = $GLOBALS['tmpl']->fetch("inc/append_one_money.html");
 						return $result;
 					}
 				}else{
 	
 					//支付诚意金
 					$result['status']=0;
 					$result['info']="您的诚意金不足，请支付!";
 					if($from=='web'){
 						$result['url']=url("account#mortgate_pay");
 					}elseif($from=='wap'){
 						$result['url']=url_wap("account#mortgate_pay");
 					}
 					return $result;
 	
 				}
 			}
 			if($invest['status']==2){
 				//申请领头权限不通过
 				$result['status']=2;
 				$result['info']="申请领头权限不通过,是否继续申请！";
 				if($from=='web'){
 					$result['url']=url("investor#applicate_leader",array('deal_id'=>$deal_id));
 				}elseif($from=='wap'){
 					$result['url']=url_wap("investor#applicate_leader",array('deal_id'=>$deal_id));
 				}
 				return $result;
 			}
 		}
 	}
 	
 	//领投(首次、追加)投资金额
 	function investor_save_money($user_id,$deal_id,$money,$is_partner,$ajax=''){
 		if($money<=0)
 		{
 			$result['status']=0;
 			$result['info']="请输入正确的目标金额！";
 			return $result;
 		}
 	
 		if($is_partner==2){
 			$result['status']=0;
 			$result['info']="请选择愿意担任！";
 			return $result;
 		}
 		//投资金额不能低于 单次投资的金额
 		$mini_info=mini_investor_money($money,$deal_id);
 		if($mini_info['status']==0){
 			return $mini_info;
 		}
 		$data=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."investment_list WHERE user_id=".$user_id." AND type=1 AND deal_id=".$deal_id);
 		$result=array('status'=>1,'info'=>'','url'=>'','html'=>'');
 	
 		if($data['id']>0){
 			//原来的钱
 			$investment_money=$data['money']+$money;
 			$deal=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."deal where id=$deal_id");
 			if($investment_money>$deal['limit_price']){
 				$result['status']=0;
 				$result['info']="您的投资金额超过融资金额！";
 				return $result;
 			}
 			if($data['investor_money_status']>0){
 				$result['status']=0;
 				$result['info']="无法进行资金添加！";
 				return $result;
 			}
 	
 			if($GLOBALS['db']->query("UPDATE ".DB_PREFIX."investment_list SET money=".$investment_money.",is_partner=".$is_partner.",create_time=".NOW_TIME." WHERE user_id=".$user_id." AND type=1 AND deal_id=".$deal_id)>0){
 				investor_is_success($deal_id);
 				$result['status']=1;
 				$result['info']="投资成功！";
 				return $result;
 			}else{
 				$result['status']=0;
 				$result['info']="提交失败！";
 				return $result;
 			}
 		}else{
 			$result['status']=0;
 			$result['info']="提交失败！";
 			return $result;
 		}
 	}
 	
 	//申请领投信息入库
 	function investor_save_leader($deal_id,$user_id,$cates,$investor_id,$invest,$introduce,$from='wap'){
 		if($investor_id>0){
 			if($invest['status']==2){
 				$invest['status']=0;
 			}
 			if($GLOBALS['db']->query("UPDATE ".DB_PREFIX."investment_list set introduce='".$introduce."',cates='".$cates."',create_time=".NOW_TIME.",status=".$invest['status']." where id=$investor_id ")){
 				$data['status'] = 1;
 				$data['info'] = "申请修改成功！";
 				if($from=='web'){
 					$data['url'] =url("deal#show",array("id"=>$deal_id));
 				}elseif($from=='wap'){
 					$data['url'] =url_wap("deal#show",array("id"=>$deal_id));
 				}
 				return $data;
 			}
 		}else{
 			$re=$GLOBALS['db']->query("INSERT INTO ".DB_PREFIX."investment_list (type,introduce,user_id,deal_id,cates,create_time) VALUES (1,'".$introduce."',$user_id,$deal_id,'".$cates."',".NOW_TIME.")");
 			if($re){
 				$data['status'] = 1;
 				$data['info'] = "已经提交申请！";
 				if($from=='web'){
 					$data['url'] =url("deal#show",array("id"=>$deal_id));
 				}elseif($from=='wap'){
 					$data['url'] =url_wap("deal#show",array("id"=>$deal_id));
 				}
 				return $data;
 			}else{
 				echo "INSERT INTO ".DB_PREFIX."investment_list (type,introduce,user_id,deal_id,cates,create_time) VALUES(1,'".$introduce."',$user_id,$deal_id,'".$cates."',".NOW_TIME.")";
 			}
 		}
 	}

 	//跟投ajax判断
 	function investor_continue($user_id,$deal_id,$from='wap'){
 		//领投是否申请、领投申请状态
 		$whether_apply=$GLOBALS['db']->getRow("SELECT count(*) as count,status FROM ".DB_PREFIX."investment_list WHERE user_id=".$user_id." AND type=1 AND deal_id=$deal_id");
 		//status:0用户未交纳诚意金 1交纳诚意金 3进入询价认筹 4申请领投未通过 5申请领投已通过 6申请领投审核中
 		//7:已经申请“领投”，但是未审核   8:已经申请“领投”，并通过  9:已经申请“领投”，但是审核不通过
 		//10:项目已经结束无法投资！ 11投资者认证未通过
 		$result=array('status'=>'','info'=>'','url'=>'','html'=>'');
 	
 		//项目结束出提示“不能再进行投资！”
 		$deal_ifo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal WHERE id=".$deal_id);
 		if($deal_ifo['user_id']==$user_id){
 			$result['status']=0;
 			$result['info']="不能投资自己的项目！";
 			return $result;
 		}
 		if($deal_ifo['end_time']<NOW_TIME){
 			$result['status']=0;
 			$result['info']="项目已经结束无法投资！";
 			return $result;
 		}
 	
 		//判断“投资者认证成功”
 		if($GLOBALS['user_info']['is_investor']==0){
 			$result['status']=0;
 			$result['info']="您是普通用户,请申请投资人";
 			if($from=='web'){
 				$result['url']=url("investor#index");
 			}elseif($from=='wap'){
 				$result['url']=url_wap("investor#index");
 			}
 			return $result;
 		}else{
 			if($GLOBALS['user_info']['investor_status']!=1){
 				$result['status']=0;
 				$result['info']="投资者认证在审核中";
 				return $result;
 			}
 	
 			$margator_money=$GLOBALS['user_info']['mortgage_money'];
 			if($margator_money<user_need_mortgate()){
 				//支付诚意金
 				$result['status']=0;
 				$result['info']="您的诚意金不足，请支付!";
 				if($from=='web'){
 					$result['url']=url("account#mortgate_pay");
 				}elseif($from=='wap'){
 					$result['url']=url_wap("account#mortgate_pay");
 				}
 				return $result;
 			}
 			$result['status']=1;
 			//剩余询价次数(type=0表示询价)
 			$num=$GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."investment_list WHERE deal_id=".$deal_id." AND user_id=".$user_id." AND type=0");
 			$inquiry_num=get_ask()-$num;
 			$result['boxid']="enquiry_index_form";
 			$result['title']="询价认筹";
 	
 			$GLOBALS['tmpl']->assign("user_id",$user_id);
 			$GLOBALS['tmpl']->assign("deal_id",$deal_id);
 			$GLOBALS['tmpl']->assign("inquiry_num",$inquiry_num);
 			$result['html'] = $GLOBALS['tmpl']->fetch("inc/enquiry_index.html");
 			return $result;
 		}
 	}
 	
 	function investor_enquiry_page($user_id,$deal_id){
 		//询价次数
 		$inquiry_num=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."investment_list where user_id=$user_id and deal_id=$deal_id and type=0 ");
 		//enquiry:1询价  0不参与询价无条件接受项目最终估值
 		$enquiry=intval($_POST['enquiry']);
 		$num=intval(get_ask());
 		$left_num=$num-$inquiry_num;
 		$result=array('status'=>'','info'=>'','url'=>'','html'=>'');
 		if($enquiry==1){
 			/*需要判断次数是否还有*/
 			if($left_num>0){
 				// (次数大于0)
 				$result['status']=1;
 				$GLOBALS['tmpl']->assign("inquiry_num",$left_num);
 				$GLOBALS['tmpl']->assign("user_id",$user_id);
 				$GLOBALS['tmpl']->assign("deal_id",$deal_id);
 				$result['html'] = $GLOBALS['tmpl']->fetch("inc/enquiry_two.html");
 				return $result;
 			}else{
 				// (次数小于0)
 				$result['status']=3;
 				$result['info']="询价次数已经用完！";
 				return $result;
 			}
 	
 		}elseif($enquiry==0){
 			/*enquiry:0不参与询价无条件接受项目最终估值*/
 			//判断是否可以追加资金
 			$leader=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."investment_list where deal_id=$deal_id and user_id=$user_id and type=1 and (status=0 or status=1) ");
 			if($leader){
 				if($leader['status']==0){
 					$result['status']=7;
 					$result['info']="您已申请领投,是否取消!";
 					return $result;
 				}elseif($leader['status']==1){
 					//“领投申请”通过
 					$result['status']=8;
 					$result['info']="您已为领投人,无需进行跟投！";
 					return $result;
 				}
 			}
 			/*需要判断是第一次还是追加*/
 			//判断是不是第一次“跟投”
 			$enquiry=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."investment_list WHERE user_id=".$user_id." AND deal_id=".$deal_id." AND type=2");
 			if($enquiry['money']==null){
 				//第一次“跟投”
 				$result['status']=2;
 				$GLOBALS['tmpl']->assign("user_id",$user_id);
 				$GLOBALS['tmpl']->assign("deal_id",$deal_id);
 				$result['html'] = $GLOBALS['tmpl']->fetch("inc/enquiry_one_money.html");
 				return $result;
 			}else{
 				if($enquiry['investor_money_status']==0){
 					//后续“跟投”追加【要判断是否可以追加2条件】
 					$result['status']=4;
 					$GLOBALS['tmpl']->assign("user_id",$user_id);
 					$GLOBALS['tmpl']->assign("deal_id",$deal_id);
 					$result['html'] = $GLOBALS['tmpl']->fetch("inc/enquiry_two_money.html");
 					return $result;
 				}else{
 					$result['status']=5;
 					$result['info']="您的资金已经无法再次追加！";
 					return $result;
 				}
 			}
 		}
 	}
 	
 	//跟投出资保存
 	function investor_enquiry_money_save($user_id,$deal_id,$is_partner,$money){
 		if($is_partner==2){
 			$result['status']=0;
 			$result['info']="请选择愿意担任！";
 			return $result;
 		}
 		if($money<=0){
 			$result['status']=0;
 			$result['info']="请输入正确的金额！";
 			return $result;
 		}
 		//投资金额不能低于 单次投资的金额
 		$mini_info=mini_investor_money($money,$deal_id);
 		if($mini_info['status']==0){
 			return $mini_info;
 		}
 		$deal=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id=$deal_id");
 		//status:0投资成功 1投资失败 2追加投资成功 3追加投资失败  4不可以再追加资金
 		$result=array('status'=>'','info'=>'','url'=>'','html'=>'');
 		//判断是不是第一次“跟投”
 		$enquiry=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."investment_list WHERE user_id=".$user_id." AND deal_id=".$deal_id." AND type=2");
 		if($enquiry['money']==null){
 			if($deal['limit_price']<$money){
 				$result['status']=0;
 				$result['info']="您的投资大于融资的金额！";
 				return $result;
 			}
 			//第一次“跟投”插入数据
 			if($GLOBALS['db']->query("INSERT INTO ".DB_PREFIX."investment_list (user_id,deal_id,money,is_partner,type,create_time) VALUES($user_id,$deal_id,$money,$is_partner,2,".NOW_TIME.")")>0){
 				investor_is_success($deal_id);
 				$result['status']=1;
 				$result['info']="投资成功！";
 				return $result;
 			}else{
 				$result['status']=0;
 				$result['info']="投资失败！";
 				return $result;
 			}
 		}else{
 			//项目原来的钱
 			$enquiry_old_money=$GLOBALS['db']->getOne("SELECT money FROM ".DB_PREFIX."investment_list WHERE user_id=".$user_id." AND type=2 AND deal_id=".$deal_id);
 			$enquiry_money=$money+$enquiry_old_money;
 			if($deal['limit_price']<$enquiry_money){
 				$result['status']=0;
 				$result['info']="您的投资大于融资的金额！";
 				return $result;
 			}
 			//追加“跟投”更新数据
 			if($GLOBALS['db']->query("UPDATE ".DB_PREFIX."investment_list SET money=".$enquiry_money.",is_partner=".$is_partner.",create_time=".NOW_TIME." WHERE user_id=".$user_id." AND type=2 AND deal_id=".$deal_id)>0){
 				investor_is_success($deal_id);
 				$result['status']=1;
 				$result['info']="追加投资成功！";
 				return $result;
 			}else{
 				$result['status']=0;
 				$result['info']="追加投资失败！";
 				return $result;
 			}
 		}
 	}
 	
 	//"跟投"询价表单信息保存
 	function investor_enquiry_save($user_id,$deal_id,$stock_value,$money,$investment_reason,$funding_to_help,$is_partner){
 		if($money<=0){
 			$result['status']=0;
 			$result['info']="请输入正确的金额！";
 			return $result;
 		}
 		if($is_partner==2){
 			$result['status']=0;
 			$result['info']="请选择愿意担任！";
 			return $result;
 		}
 		if($stock_value<$money){
 			$result['status']=0;
 			$result['info']="您的出资不能大于估值！";
 			return $result;
 		}
 		//投资金额不能低于 单次投资的金额
 		$mini_info=mini_investor_money($money,$deal_id);
 		if($mini_info['status']==0){
 			return $mini_info;
 		}
 		$result=array('status'=>'','info'=>'','url'=>'','html'=>'');
 		if($GLOBALS['db']->query("INSERT INTO ".DB_PREFIX."investment_list (user_id,deal_id,stock_value,money,investment_reason,funding_to_help,is_partner,type,create_time) VALUES($user_id,$deal_id,$stock_value,$money,'".$investment_reason."','".$funding_to_help."',$is_partner,0,".NOW_TIME.")")>0){
 			investor_is_success($deal_id);
 			$result['status']=1;
 			$result['info']="出资成功！";
 			return $result;
 		}else{
 			$result['status']=0;
 			$result['info']="出资失败！";
 			return $result;
 		}
 	}
 	
 	function investor_save($id,$ajax='',$identify_name,$identify_number,$image1,$image2){
 		if($identify_name==null){
 			$data['status'] = 0;
 			$data['info'] = "真实姓名不能为空！";
 			return $data;
 		}
 		if($identify_number==null){
 			$data['status'] = 0;
 			$data['info'] = "身份证号码不能为空！";
 			return $data;
 		}
 		if($image1['url']==null){
 			$data['status'] = 0;
 			$data['info'] = "请上传身份证正面照片！";
 			return $data;
 		}
 		if($image2['url']==null){
 			$data['status'] = 0;
 			$data['info'] = "请上传身份证背面照片！";
 			return $data;
 		}
 		if($GLOBALS['db']->query("update ".DB_PREFIX."user set identify_name='".$identify_name."',identify_number='".$identify_number."',identify_positive_image='".$image1['url']."',identify_nagative_image='".$image2['url']."',investor_status=0 where id=".$id)>0){
 			$data['status'] = 1;
 			$data['info'] = "认证信息入录成功！";
 			$GLOBALS['db']->query("update ".DB_PREFIX."user set identify_business_name='',identify_business_licence='',identify_business_code='',identify_business_tax='' where id=".$id);
 			return $data;
 		}else{
 			$data['status'] = 0;
 			$data['info'] = "认证信息入录失败！";
 			return $data;
 		}
 		return false;
 	}
 	function investor_agency_save($id,$ajax='',$identify_business_name,$identify_business_licence,$identify_business_code,$identify_business_tax,$image1,$image2,$image3)
 	{
 		if($identify_business_name==null){
 			$data['status'] = 0;
 			$data['info'] = "机构名称不能为空！";
 			return $data;
 		}
 		if($identify_business_licence==null){
 			$data['status'] = 0;
 			$data['info'] = "请上传营业执照！";
 			return $data;
 		}
 		if($identify_business_code==null){
 			$data['status'] = 0;
 			$data['info'] = "请上传组织机构代码证照片！";
 			return $data;
 		}
 		if($identify_business_tax==null){
 			$data['status'] = 0;
 			$data['info'] = "请上传税务登记证照片！";
 			return $data;
 		}
 		if($GLOBALS['db']->query("update ".DB_PREFIX."user set identify_business_name='".$identify_business_name."',identify_business_licence='".$image1['url']."',identify_business_code='".$image2['url']."',identify_business_tax='".$image3['url']."',investor_status=0 where id=".$id)>0){
 			$data['status'] = 1;
 			$data['info'] = "认证信息入录成功！";
 			$GLOBALS['db']->query("update ".DB_PREFIX."user set identify_name='',identify_number='',identify_positive_image='',identify_nagative_image='' where id=".$id);
 			return $data;
 		}else{
 			$data['status'] = 0;
 			$data['info'] = "认证信息入录失败！";
 			return $data;
 		}
 		return false;
 	}
?>