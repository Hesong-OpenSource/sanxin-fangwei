<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/shop_lip.php';
class userModule extends BaseModule
{
	public function login()
	{
                
                 //links
                $g_links =get_link_by_id(14);
                
                $GLOBALS['tmpl']->assign("g_links",$g_links);
              
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
		if (!$GLOBALS['tmpl']->is_cached('user_login.html', $cache_id))
		{		
			$GLOBALS['tmpl']->assign("page_title","会员登录");
		}
		 
		$GLOBALS['tmpl']->display("user_login.html",$cache_id);
	}
	
	public function do_login()
	{		
		if(!$_POST)
		{
			app_redirect(APP_ROOT."/");
		}
		foreach($_POST as $k=>$v)
		{
			$_POST[$k] = strim($v);
		}
		$ajax = intval($_REQUEST['ajax']);
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		if(check_ipop_limit(get_client_ip(),"user_dologin",5))
		$result = do_login_user($_POST['email'],$_POST['user_pwd']);
		else
		showErr("提交太快",$ajax,url("user#login"));		
		if($result['status'])
		{	
			$s_user_info = es_session::get("user_info");
			if(intval($_POST['auto_login'])==1)
			{
				//自动登录，保存cookie
				$user_data = $s_user_info;
				es_cookie::set("email",$user_data['email'],3600*24*30);			
				es_cookie::set("user_pwd",md5($user_data['user_pwd']."_EASE_COOKIE"),3600*24*30);
				
			}
			if($ajax==0&&trim(app_conf("INTEGRATE_CODE"))=='')
			{
				$redirect = $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:url("index");
				app_redirect($redirect);
			}
			else
			{			
				$jump_url = get_gopreview();				
				if($ajax==1)
				{
					$return['status'] = 1;
					$return['info'] = "登录成功";
					$return['data'] = $result['msg'];
					$return['jump'] = $jump_url;					
					ajax_return($return);
				}
				else
				{
					$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);					
					showSuccess("登录成功",$ajax,$jump_url);
				}
			}
		}
		else
		{
			if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
			{
				$err = "会员不存在";
			}
			if($result['data'] == ACCOUNT_PASSWORD_ERROR)
			{
				$err = "密码错误";
			}
                        if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
			{
				$err = "用户未通过验证";
				if(app_conf("MAIL_ON")==1&&$ajax==0)
				{				
					$GLOBALS['tmpl']->assign("page_title",$err);
					$GLOBALS['tmpl']->assign("user_info",$result['user']);
					$GLOBALS['tmpl']->display("verify_user.html");
					exit;
				}
				
			}
			showErr($err,$ajax);
		}
	}
        public function verify()
	{
		$id = intval($_REQUEST['id']);
		$user_info  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id);
		if(!$user_info)
		{
			showErr("没有该会员");
		}
		$verify = addslashes(trim($_REQUEST['code']));
		if($user_info['verify']!=''&&$user_info['verify'] == $verify)
		{
			//成功
			send_register_success(0,$user_info);
			es_session::set("user_info",$user_info);
			$GLOBALS['db']->query("update ".DB_PREFIX."user set login_ip = '".get_client_ip()."',login_time= ".get_gmtime().",verify = '',is_effect = 1 where id =".$user_info['id']);
			$GLOBALS['db']->query("update ".DB_PREFIX."mail_list set is_effect = 1 where mail_address ='".$user_info['email']."'");									
			showSuccess("验证成功",0,get_gopreview());
		}
                
		elseif($user_info['verify']=='')
		{
			showErr("已验证过",0,get_gopreview());
                        
		}
		else
		{
			showErr("验证失败",0,get_gopreview());
		}
	}
	
	public function loginout()
	{		
		$ajax = intval($_REQUEST['ajax']);
		require_once APP_ROOT_PATH."system/libs/user.php";
		$result = loginout_user();
		if($result['status'])
		{
			es_cookie::delete("email");
			es_cookie::delete("user_pwd");
			es_cookie::delete("hide_user_notify");
			if($ajax==1)
			{
				$return['status'] = 1;
				$return['info'] = "登出成功";
				$return['data'] = $result['msg'];
				$return['jump'] = get_gopreview();					
				ajax_return($return);
			}
			else
			{
				$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);
				if(trim(app_conf("INTEGRATE_CODE"))=='')
				{
					app_redirect_preview();
				}
				else
				showSuccess("登出成功",0,get_gopreview());
			}
		}
		else
		{
			if($ajax==1)
			{
				$return['status'] = 1;
				$return['info'] = "登出成功";
				$return['jump'] = get_gopreview();					
				ajax_return($return);
			}
			else
			app_redirect(get_gopreview());		
		}
	}
	
	public function getpassword()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
		if (!$GLOBALS['tmpl']->is_cached('user_getpassword.html', $cache_id))	
		{			 
			$GLOBALS['tmpl']->assign("page_title","邮件取回密码");
		}
		$GLOBALS['tmpl']->display("user_getpassword.html",$cache_id);
	}
	
	public function do_getpassword()
	{
		
		$email = strim($_REQUEST['email']);
		$ajax = intval($_REQUEST['ajax']);
		if(!check_ipop_limit(get_client_ip(),"user_do_getpassword",5))
		showErr("提交太快",$ajax);	
		if(!check_email($email))
		{
			showErr("邮箱格式有误",$ajax);
		}
		elseif($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email ='".$email."'") == 0)
		{
			showErr("邮箱不存在",$ajax);
		}
		else 
		{
			$user_info = $GLOBALS['db']->getRow('select * from '.DB_PREFIX."user where email='".$email."'");
			send_user_password_mail($user_info['id']);
			showSuccess("邮件已经寄出，请查看您的邮箱!",$ajax);
		}
	}
	
	
	public function register()
	{
                 //links
                $g_links =get_link_by_id(14);
                
         $GLOBALS['tmpl']->assign("g_links",$g_links);
       
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
		if (!$GLOBALS['tmpl']->is_cached('user_register.html', $cache_id))
		{		
			$GLOBALS['tmpl']->assign("page_title","会员注册");
		}
		$GLOBALS['tmpl']->display("user_register.html",$cache_id);
	}
	
	public function register_check()
	{
		$field = strim($_REQUEST['field']);
		$value = strim($_REQUEST['value']);
		require_once APP_ROOT_PATH."system/libs/user.php";
		$result = check_user($field,$value);
		if($result['status']==0)
		{
			if($result['data']['field_name']=='user_name')
			{
				$field_name = "会员帐号";
			}
		
			if($result['data']['field_name']=='email')
			{
				$field_name = "电子邮箱";
			}
			if($result['data']['error']==EMPTY_ERROR)
			{
				$error = "不能为空";
			}
			if($result['data']['error']==FORMAT_ERROR)
			{
				$error = "格式有误";
			}
			if($result['data']['error']==EXIST_ERROR)
			{
				$error = "已存在";
			}
			$return = array('status'=>0,"info"=>$field_name.$error);
			ajax_return($return);
		}
		else
		{
			$return = array('status'=>1);
			ajax_return($return);
		}
		
		
	}
	public function register_check1()
	{
		$field = strim($_REQUEST['field']);
		$value = strim($_REQUEST['value']);
		require_once APP_ROOT_PATH."system/libs/user.php";
		$result = check_user($field,$value);
		if($result['status']==0)
		{
			if($result['data']['field_name']=='user_name')
			{
				$field_name = "会员帐号";
			}
			if($result['data']['field_name']=='mobile')
			{
				$field_name = "会员手机";
			}
			
			if($result['data']['error']==EMPTY_ERROR)
			{
				$error = "不能为空";
			}
			if($result['data']['error']==FORMAT_ERROR)
			{
				$error = "格式有误";
			}
			if($result['data']['error']==EXIST_ERROR)
			{
				$error = "已存在";
			}
			$return = array('status'=>0,"info"=>$field_name.$error);
			ajax_return($return);
		}
		else
		{
			$return = array('status'=>1);
			ajax_return($return);
		}
		
		
	}
	
	private function register_check_all()
	{
		if(app_conf("USER_VERIFY")!=2){
			$user_name = strim($_REQUEST['user_name']);
			$email = strim($_REQUEST['email']);
			
			//	$mobile = strim($_REQUEST['mobile']);
			$user_pwd = strim($_REQUEST['user_pwd']);
			$confirm_user_pwd = strim($_REQUEST['confirm_user_pwd']);
			$data = array();
			require_once APP_ROOT_PATH."system/libs/user.php";
			
			$user_name_result = check_user("user_name",$user_name);
			if($user_name_result['status']==0)
			{
				if($user_name_result['data']['error']==EMPTY_ERROR)
				{
					$error = "不能为空";
					$type = "form_tip";
				}
				if($user_name_result['data']['error']==FORMAT_ERROR)
				{
					$error = "格式有误";
					$type="form_error";
				}
				if($user_name_result['data']['error']==EXIST_ERROR)
				{
					$error = "已存在";
					$type="form_error";
				}
				$data[] = array("type"=>$type,"field"=>"user_name","info"=>"会员帐号".$error);
			}
			else
			{
				$data[] = array("type"=>"form_success","field"=>"user_name","info"=>"");
			}
			
			$email_result = check_user("email",$email);
			if($email_result['status']==0)
			{
				if($email_result['data']['error']==EMPTY_ERROR)
				{
					$error = "不能为空";
					$type = "form_tip";
				}
				if($email_result['data']['error']==FORMAT_ERROR)
				{
					$error = "格式有误";
					$type="form_error";
				}
				if($email_result['data']['error']==EXIST_ERROR)
				{
					$error = "已存在";
					$type="form_error";
				}
				$data[] = array("type"=>$type,"field"=>"email","info"=>"电子邮箱".$error);
			}
			else
			{
				$data[] = array("type"=>"form_success","field"=>"email","info"=>"");
			}
			
			if($user_pwd=="")
			{
				$user_pwd_result['status'] = 0;
				$data[] = array("type"=>"form_tip","field"=>"user_pwd","info"=>"请输入会员密码");
			}
			elseif(strlen($user_pwd)<4)
			{
				$user_pwd_result['status'] = 0;
				$data[] = array("type"=>"form_error","field"=>"user_pwd","info"=>"密码不得小于四位");
			}
			else
			{
				$user_pwd_result['status'] = 1;
				$data[] = array("type"=>"form_success","field"=>"user_pwd","info"=>"");
			}
			
			if($user_pwd==$confirm_user_pwd)
			{
				$confirm_user_pwd_result['status'] = 1;
				$data[] = array("type"=>"form_success","field"=>"confirm_user_pwd","info"=>"");
			}
			else
			{
				$confirm_user_pwd_result['status'] = 0;
				$data[] = array("type"=>"form_error","field"=>"confirm_user_pwd","info"=>"确认密码失败");
			}
			
			if($email_result['status']==1&&$user_name_result['status']==1&&$user_pwd_result['status']==1&&$confirm_user_pwd_result['status']==1)
			{
				$return = array("status"=>1);
			}
			else
			{
				$return = array("status"=>0,"data"=>$data,"info"=>"");
			}
			return $return;
		}
		if(app_conf("USER_VERIFY")==2){
			$user_name = strim($_REQUEST['user_name']);
			$email = strim($_REQUEST['email']);
			
			$mobile = strim($_REQUEST['mobile']);
			$verify_coder=strim($_REQUEST['verify_coder']);
			
			$user_pwd = strim($_REQUEST['user_pwd']);
			$confirm_user_pwd = strim($_REQUEST['confirm_user_pwd']);
			$data = array();
			require_once APP_ROOT_PATH."system/libs/user.php";
				
			$user_name_result = check_user("user_name",$user_name);
			if($user_name_result['status']==0)
			{
				if($user_name_result['data']['error']==EMPTY_ERROR)
				{
					$error = "不能为空";
					$type = "form_tip";
				}
				if($user_name_result['data']['error']==FORMAT_ERROR)
				{
					$error = "格式有误";
					$type="form_error";
				}
				if($user_name_result['data']['error']==EXIST_ERROR)
				{
					$error = "已存在";
					$type="form_error";
				}
				$data[] = array("type"=>$type,"field"=>"user_name","info"=>"会员帐号".$error);
			}
			else
			{
				$data[] = array("type"=>"form_success","field"=>"user_name","info"=>"");
			}
			

			$mobile_result = check_user("mobile",$mobile);
			if($mobile_result['status']==0)
			{
				if($mobile_result['data']['error']==EMPTY_ERROR)
				{
					$error = "不能为空";
					$type = "form_tip";
				}
				if($mobile_result['data']['error']==FORMAT_ERROR)
				{
					$error = "格式有误";
					$type="form_error";
				}
				if($mobile_result['data']['error']==EXIST_ERROR)
				{
					$error = "已存在";
					$type="form_error";
				}
				$data[] = array("type"=>$type,"field"=>"mobile","info"=>"手机号码".$error);
			}
			else
			{
				$data[] = array("type"=>"form_success","field"=>"mobile","info"=>"");
			}
			//=================================================这里面的要验证改y
			$verify_coder_result = check_user("verify_coder",$verify_coder);
			if($verify_coder_result['status']==0)
			{
				if($verify_coder_result['data']['error']==EMPTY_ERROR)
				{
					$error = "不能为空";
					$type = "form_tip";
				}
				if($verify_coder_result['data']['error']==EXIST_ERROR)
				{
					$error = "错误";
					$type="form_error";
				}
				$data[] = array("type"=>$type,"field"=>"verify_coder","info"=>"验证码".$error);
			}
			else
			{
				$data[] = array("type"=>"form_success","field"=>"verify_coder","info"=>"");
			}
				
			$email_result = check_user("email",$email);
			if($email_result['status']==0)
			{
				if($email_result['data']['error']==EMPTY_ERROR)
				{
					$error = "不能为空";
					$type = "form_tip";
				}
				if($email_result['data']['error']==FORMAT_ERROR)
				{
					$error = "格式有误";
					$type="form_error";
				}
				if($email_result['data']['error']==EXIST_ERROR)
				{
					$error = "已存在";
					$type="form_error";
				}
				$data[] = array("type"=>$type,"field"=>"email","info"=>"电子邮箱".$error);
			}
			else
			{
				$data[] = array("type"=>"form_success","field"=>"email","info"=>"");
			}
				
			if($user_pwd=="")
			{
				$user_pwd_result['status'] = 0;
				$data[] = array("type"=>"form_tip","field"=>"user_pwd","info"=>"请输入会员密码");
			}
			elseif(strlen($user_pwd)<4)
			{
				$user_pwd_result['status'] = 0;
				$data[] = array("type"=>"form_error","field"=>"user_pwd","info"=>"密码不得小于四位");
			}
			else
			{
				$user_pwd_result['status'] = 1;
				$data[] = array("type"=>"form_success","field"=>"user_pwd","info"=>"");
			}
				
			if($user_pwd==$confirm_user_pwd)
			{
				$confirm_user_pwd_result['status'] = 1;
				$data[] = array("type"=>"form_success","field"=>"confirm_user_pwd","info"=>"");
			}
			else
			{
				$confirm_user_pwd_result['status'] = 0;
				$data[] = array("type"=>"form_error","field"=>"confirm_user_pwd","info"=>"确认密码失败");
			}
				
			if($mobile_result['status']==1&&$email_result['status']==1&&$user_name_result['status']==1&&$user_pwd_result['status']==1&&$confirm_user_pwd_result['status']==1)
			{
				$return = array("status"=>1);
			}
			else
			{
				$return = array("status"=>0,"data"=>$data,"info"=>"");
			}
			return $return;
		}
	}
	private function mobile_register_check_all()
	{
		
		$user_name = strim($_REQUEST['user_name']);
	
		$mobile = strim($_REQUEST['mobile']);
		$user_pwd = strim($_REQUEST['user_pwd']);
		$confirm_user_pwd = strim($_REQUEST['confirm_user_pwd']);
		
		$data = array();
		require_once APP_ROOT_PATH."system/libs/user.php";
		
		$user_name_result = check_user("user_name",$user_name);	
		
		
		if($user_name_result['status']==0)
		{
			if($user_name_result['data']['error']==EMPTY_ERROR)
			{
				$error = "不能为空";
				$type = "form_tip";
			}
			if($user_name_result['data']['error']==FORMAT_ERROR)
			{
				$error = "格式有误";
				$type="form_error";
			}
			if($user_name_result['data']['error']==EXIST_ERROR)
			{
				$error = "已存在";
				$type="form_error";
			}
			$data[] = array("type"=>$type,"field"=>"user_name","info"=>"会员帐号".$error);	
		}
		else
		{
			$data[] = array("type"=>"form_success","field"=>"user_name","info"=>"");	
		}
		
		$mobile_result = check_user("mobile",$mobile);		
		if($mobile_result['status']==0)
		{
			if($mobile_result['data']['error']==EMPTY_ERROR)
			{
				$error = "不能为空";
				$type = "form_tip";
			}
			if($mobile_result['data']['error']==FORMAT_ERROR)
			{
				$error = "格式有误";
				$type="form_error";
			}
			if($mobile_result['data']['error']==EXIST_ERROR)
			{
				$error = "已存在";
				$type="form_error";
			}
			$data[] = array("type"=>$type,"field"=>"mobile","info"=>"手机号码".$error);	
		}
		
		else
		{
			$data[] = array("type"=>"form_success","field"=>"mobile","info"=>"");	
		}
		
		if($user_pwd=="")
		{
			$user_pwd_result['status'] = 0;
			$data[] = array("type"=>"form_tip","field"=>"user_pwd","info"=>"请输入会员密码");	
		}
		elseif(strlen($user_pwd)<4)
		{
			$user_pwd_result['status'] = 0;
			$data[] = array("type"=>"form_error","field"=>"user_pwd","info"=>"密码不得小于四位");	
		}
		else
		{
			$user_pwd_result['status'] = 1;
			$data[] = array("type"=>"form_success","field"=>"user_pwd","info"=>"");	
		}
		
		if($user_pwd==$confirm_user_pwd)
		{
			$confirm_user_pwd_result['status'] = 1;
			$data[] = array("type"=>"form_success","field"=>"confirm_user_pwd","info"=>"");	
		}
		else
		{
			$confirm_user_pwd_result['status'] = 0;
			$data[] = array("type"=>"form_error","field"=>"confirm_user_pwd","info"=>"确认密码失败");	
		}
		
		if($mobile_result['status']==1&&$user_name_result['status']==1&&$user_pwd_result['status']==1&&$confirm_user_pwd_result['status']==1)
		{
			$return = array("status"=>1);
		}
		else
		{
			$return = array("status"=>0,"data"=>$data,"info"=>"");
		}
	
		return $return;
		
	}
        
        //判断邮箱类型及跳转到user_register_email.html界面
         function mail_check()
        { 
             //links
                $g_links =get_link_by_id(14);
                
                $GLOBALS['tmpl']->assign("g_links",$g_links);
            if(app_conf("MAIL_ON")==1)
            {
                    $user_id = (int)$_REQUEST['uid'];      
                    //发邮件
                    send_user_verify_mail($user_id);
                    $user_email = $GLOBALS['db']->getOne("select email from ".DB_PREFIX."user where id =".$user_id);
                    //开始关于跳转地址的解析
                    $domain = explode("@",$user_email);
                    $domain = $domain[1];
                    $gocheck_url = '';
                    switch($domain)
                    {
                            case '163.com':
                                    $gocheck_url = 'http://mail.163.com';
                                    break;
                            case '126.com':
                                    $gocheck_url = 'http://www.126.com';
                                    break;
                            case 'sina.com':
                                    $gocheck_url = 'http://mail.sina.com';
                                    break;
                            case 'sina.com.cn':
                                    $gocheck_url = 'http://mail.sina.com.cn';
                                    break;
                            case 'sina.cn':
                                    $gocheck_url = 'http://mail.sina.cn';
                                    break;
                            case 'qq.com':
                                    $gocheck_url = 'http://mail.qq.com';
                                    break;
                            case 'foxmail.com':
                                    $gocheck_url = 'http://mail.foxmail.com';
                                    break;
                            case 'gmail.com':
                                    $gocheck_url = 'http://www.gmail.com';
                                    break;
                            case 'yahoo.com':
                                    $gocheck_url = 'http://mail.yahoo.com';
                                    break;
                            case 'yahoo.com.cn':
                                    $gocheck_url = 'http://mail.cn.yahoo.com';
                                    break;
                            case 'hotmail.com':
                                    $gocheck_url = 'http://www.hotmail.com';
                                    break;
                            default:
                                    $gocheck_url = "";
                                    break;					
                    }

                    $GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['REGISTER_MAIL_SEND_SUCCESS']);
                    
                    $GLOBALS['tmpl']->assign("user_email",$user_email);
                  
                    $GLOBALS['tmpl']->assign("gocheck_url",$gocheck_url);
                    //end 
                    $GLOBALS['tmpl']->display("user_register_email.html");
            }
            
        }

        public function do_register()
		{
			$email = strim($_REQUEST['email']);
			require_once APP_ROOT_PATH."system/libs/user.php";
			$return = $this->register_check_all();
			if($return['status']==0)
			{
				ajax_return($return);
			}		
			$user_data = $_POST;
			foreach($_POST as $k=>$v)
			{
				$user_data[$k] = strim($v);
			}	
            //开启邮箱验证
            if(app_conf("USER_VERIFY")==0||app_conf("USER_VERIFY")==2){
                 $user_data['is_effect'] = 1;
            }else{
            	$user_data['is_effect'] = 0;
            }
               
		
			$res = save_user($user_data);
		
	
			if($res['status'] == 1)
			{
				if(!check_ipop_limit(get_client_ip(),"user_do_register",5))
				showErr("提交太快",1);	
				
				$user_id = intval($res['data']);
				$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
				if($user_info['is_effect']==1)
				{
					//在此自动登录
					send_register_success(0,$user_data);
					$result = do_login_user($user_data['email'],$user_data['user_pwd']);
				//	ajax_return(array("status"=>1,"jump"=>get_gopreview()));
					ajax_return(array("status"=>1,"data"=>$result['msg'],"jump"=>get_gopreview()));
				}
				else
				{
                    if(app_conf("USER_VERIFY")==1){
                        ajax_return(array("status"=>1,"jump"=>url("user#mail_check",array('uid'=>$user_id))));
                    }else if(app_conf("USER_VERIFY")==3){
                    	ajax_return(array("status"=>0,"info"=>"请等待管理员审核"));
                    }
						
				}                     
			}
			else
			{
				$error = $res['data'];	
				if($error['field_name']=="user_name")
				{
					$data[] = array("type"=>"form_success","field"=>"email","info"=>"");	
					$field_name = "会员帐号";
				}
				if($error['field_name']=="email")
				{
					$data[] = array("type"=>"form_success","field"=>"user_name","info"=>"");
					$field_name = "电子邮箱";
				}
				if($error['field_name']=="mobile")
				{
					$data[] = array("type"=>"form_success","field"=>"mobile","info"=>"");
					$field_name = "手机号码";
				}
				if($error['field_name']=="verify_code")
				{
					$data[] = array("type"=>"form_success","field"=>"verify_code","info"=>"");
					$field_name = "验证码";
				}
			
				if($error['error']==EMPTY_ERROR)
				{
					$error_info = "不能为空";
					$type = "form_tip";
				}
				if($error['error']==FORMAT_ERROR)
				{
					$error_info = "错误";
					$type="form_error";
				}
				if($error['error']==EXIST_ERROR)
				{
					$error_info = "已存在";
					$type="form_error";
				}
				
				$data[] = array("type"=>$type,"field"=>$error['field_name'],"info"=>$field_name.$error_info);	
				ajax_return(array("status"=>0,"data"=>$data,"info"=>""));			
				
			}
	}
	
	public function api_register()
	{			
		$api_info = es_session::get("api_user_info");		
		if(!$api_info)
		{
			app_redirect_preview();
		}
		
		$GLOBALS['tmpl']->assign("api_info",$api_info);
		$GLOBALS['tmpl']->assign("page_title","帐号绑定");
		$GLOBALS['tmpl']->display("user_api_register.html");
	}
	
	public function do_api_register()
	{
		require_once APP_ROOT_PATH."system/libs/user.php";
		$api_info = es_session::get("api_user_info");		
		if(!$api_info)
		{
			app_redirect_preview();
		}
		$user_name = strim($_REQUEST['user_name']);
		$email = strim($_REQUEST['email']);
		$user_pwd = strim($_REQUEST['user_pwd']);
		
		$user_data['user_name'] = $user_name;
		$user_data['email'] = $email;
		//$user_data['user_pwd'] = rand(100000,999999);
		$user_data['user_pwd'] = $user_pwd;
		$user_data['province'] = $api_info['province'];
		$user_data['city'] = $api_info['city'];
		$user_data['is_effect'] = 1;
		$user_data['sex'] = $api_info['sex'];
		
		$res = save_user($user_data);
		
	
		if($res['status'] == 1)
		{
			if(!check_ipop_limit(get_client_ip(),"user_do_api_register",5))
			showErr("提交太快",1);	
			$user_id = intval($res['data']);
			$GLOBALS['db']->query("update ".DB_PREFIX."user set ".$api_info['field']." = '".$api_info['id']."',".$api_info['token_field']." = '".$api_info['token']."',".$api_info['secret_field']." = '".$api_info['secret']."',".$api_info['url_field']." = '".$api_info['url']."' where id = ".$user_id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_weibo where user_id = ".$user_id." and weibo_url = '".$api_info['url']."'");
			
			update_user_weibo($user_id,$api_info['url']); 
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
			if($user_info['is_effect']==1)
			{
				//在此自动登录
				send_register_success(0,$user_data);
				do_login_user($user_data['email'],$user_data['user_pwd']);
				ajax_return(array("status"=>1,"jump"=>get_gopreview()));
			}
			else
			{
				ajax_return(array("status"=>0,"info"=>"请等待管理员审核","jump"=>get_gopreview()));
			}
		}
		else
		{
			$error = $res['data'];	
			if($error['field_name']=="user_name")
			{
				$data[] = array("type"=>"form_success","field"=>"email","info"=>"");	
				$field_name = "会员帐号";
			}
			if($error['field_name']=="email")
			{
				$data[] = array("type"=>"form_success","field"=>"user_name","info"=>"");
				$field_name = "电子邮箱";
			}
		
			if($error['error']==EMPTY_ERROR)
			{
				$error_info = "不能为空";
				$type = "form_tip";
			}
			if($error['error']==FORMAT_ERROR)
			{
				$error_info = "格式有误";
				$type="form_error";
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_info = "已存在";
				$type="form_error";
			}
			ajax_return(array("status"=>0,"info"=>$field_name.$error_info,"field"=>$error['field_name'],"jump"=>get_gopreview()));			
			
		}
		
	}
	
	public function api_login()
	{			
		$api_info = es_session::get("api_user_info");		
		if(!$api_info)
		{
			app_redirect_preview();
		}
		$GLOBALS['tmpl']->assign("api_info",$api_info);
		$GLOBALS['tmpl']->assign("page_title","帐号绑定");
		$GLOBALS['tmpl']->display("user_api_login.html");
	}
	
	
	public function do_api_login()
	{		
		
		
		$api_info = es_session::get("api_user_info");		
		if(!$api_info)
		{
			app_redirect_preview();
		}
		
		if(!$_POST)
		{
			app_redirect(APP_ROOT."/");
		}
		foreach($_POST as $k=>$v)
		{
			$_POST[$k] = strim($v);
		}
		$ajax = intval($_REQUEST['ajax']);
		if(!check_ipop_limit(get_client_ip(),"user_do_api_login",5))
		showErr("提交太快",$ajax);	
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		$result = do_login_user($_POST['email'],$_POST['user_pwd']);				
		if($result['status'])
		{	
			$s_user_info = es_session::get("user_info");
			$GLOBALS['db']->query("update ".DB_PREFIX."user set ".$api_info['field']." = '".$api_info['id']."',".$api_info['token_field']." = '".$api_info['token']."',".$api_info['secret_field']." = '".$api_info['secret']."',".$api_info['url_field']." = '".$api_info['url']."' where id = ".$s_user_info['id']);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_weibo where user_id = ".intval($s_user_info['id'])." and weibo_url = '".$api_info['url']."'");
			update_user_weibo(intval($s_user_info['id']),$api_info['url']);
			if($ajax==0&&trim(app_conf("INTEGRATE_CODE"))=='')
			{
				$redirect = $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:url("index");
				app_redirect($redirect);
			}
			else
			{			
				$jump_url = get_gopreview();				
				if($ajax==1)
				{
					$return['status'] = 1;
					$return['info'] = "登录成功";
					$return['data'] = $result['msg'];
					$return['jump'] = $jump_url;					
					ajax_return($return);
				}
				else
				{
					$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);					
					showSuccess("登录成功",$ajax,$jump_url);
				}
			}
		}
		else
		{
			if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
			{
				$err = "会员不存在";
			}
			if($result['data'] == ACCOUNT_PASSWORD_ERROR)
			{
				$err = "密码错误";
			}
			showErr($err,$ajax);
		}
	}
	public function add_weibo()
	{
		$GLOBALS['tmpl']->display("inc/weibo_row.html");
	}
	//手机注册
	public function user_register()
	{
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		$return = $this->mobile_register_check_all();
		
		if($return['status']==0)
		{
			ajax_return($return);
		}		
		$user_data = $_POST;
		foreach($_POST as $k=>$v)
		{
			$user_data[$k] = strim($v);
		}	
        		
		$user_data['is_effect'] = 1;
		if(app_conf("USER_VERIFY")==2){
			
			if($user_data["mobile"] == ""){
            	$data[] = array("type"=>"form_error","field"=>"mobile","info"=>"请输入手机号码");	
            	ajax_return(array("status"=>0,"data"=>$data));			
            }
			
            if($user_data["verify_coder"] == ""){
            	$data[] = array("type"=>"form_error","field"=>"verify_coder","info"=>"请输入验证码");	
				
            	ajax_return(array("status"=>0,"data"=>$data));			
            }
            
            if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile ='".$user_data['mobile']."' and verify_code='".$user_data["verify_coder"]."' order by create_time desc") == 0)
            {
            	$data[] = array("type"=>"form_error","field"=>"verify_coder","info"=>"验证码错误");	
            	ajax_return(array("status"=>0,"data"=>$data));
            }
            
            if(app_conf("SMS_ON")==1)
	        	$user_data['is_effect'] = 1;
	        else
	        	$user_data['is_effect'] = 0;
        }
        
        
        
		$res = save_mobile_user($user_data);
		
	
		if($res['status'] == 1)
		{
			if(!check_ipop_limit(get_client_ip(),"user_do_register",5))
			showErr("提交太快",1);	
			
			$user_id = intval($res['data']);
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
			if($user_info['is_effect']==1)
			{
				send_register_success(0,$user_data);
				do_login_user($user_data['user_name'],$user_data['user_pwd']);
				ajax_return(array("status"=>1,"jump"=>get_gopreview()));
			}
			else
			{
				 ajax_return(array("status"=>0,"info"=>"请等待管理员审核"));
			}                     
		}
		else
		{
			$error = $res['data'];	
			if($error['field_name']=="user_name")
			{
				$data[] = array("type"=>"form_success","field"=>"user_name","info"=>"");	
				$field_name = "会员帐号";
			}
			if($error['field_name']=="mobile")
			{
				$data[] = array("type"=>"form_success","field"=>"mobile","info"=>"");
				$field_name = "手机号码";
			}
		
			if($error['error']==EMPTY_ERROR)
			{
				$error_info = "不能为空";
				$type = "form_tip";
			}
			if($error['error']==FORMAT_ERROR)
			{
				$error_info = "格式有误";
				$type="form_error";
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_info = "已存在";
				$type="form_error";
			}
			
			$data[] = array("type"=>$type,"field"=>$error['field_name'],"info"=>$field_name.$error_info);	
			ajax_return(array("status"=>0,"data"=>$data,"info"=>""));			
			
		}
	}
	//手机验证修改密码=====================================================================================
	public function phone_update_password()
	{
		$mobile = strim($_REQUEST['mobile']);
		$user_pwd = strim($_REQUEST['user_pwd']);
		$confirm_user_pwd=strim($_POST['confirm_user_pwd']);
		$settings_mobile_code1=strim($_POST['code']);
		
		if(!$mobile)
		{
			$data['status'] = 0;
			$data['info'] = "手机号码为空";
			ajax_return($data);
		}
	
		if($settings_mobile_code1==""){
			$data['status'] = 0;
			$data['info'] = "手机验证码为空";
			ajax_return($data);
		}
	
		if($user_pwd==""){
			$data['status'] = 0;
			$data['info'] = "密码为空";
			ajax_return($data);
		}
	
		if($user_pwd!==$confirm_user_pwd){
			$data['status'] = 0;
			$data['info'] = "两次密码不一致";
			ajax_return($data);
		}
	
		//判断验证码是否正确=============================
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile=".$mobile." AND verify_code='".$settings_mobile_code1."'")==0){
			 
			$data['status'] = 0;
			$data['info'] = "手机验证码错误";
			ajax_return($data);
		}
		
	
		if($user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile =".$mobile))
		{
			
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user SET user_pwd='".md5($user_pwd.$user_info['code'])."' where mobile=".$mobile);
			$result = 1;  //初始为1
			$data['status'] = 1;
			$data['info'] = "密码修改成功";
			ajax_return($data);//密码修改成功
		}
		else{
			$data['status'] = 0;
			$data['info'] = "没有该手机账户";
			ajax_return($data);//密码修改成功
		}
	}
	
	//检查验证码是否正确
	function check_verify_code()
	{
		$settings_mobile_code=strim($_POST['code']);
		$mobile=strim($_POST['mobile']);
		//判断验证码是否正确=============================
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile=".$mobile." AND verify_code='".$settings_mobile_code."'")==0){
			$data['status'] = 0;
			$data['info'] = "手机验证码出错";
			ajax_return($data);
		}else{
			$data['status'] = 1;
			$data['info'] = "验证码正确";
			ajax_return($data);
		}
	}
	
}
?>