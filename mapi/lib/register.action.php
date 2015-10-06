<?php
/**
	 * 生成会员数据
	 * @param $user_data  提交[post或get]的会员数据
	 * @param $mode  处理的方式，注册或保存
	 * 返回：data中返回出错的字段信息，包括field_name, 可能存在的field_show_name 以及 error 错误常量
	 * 不会更新保存的字段为：score,money,verify,pid

function add_user($user_data)
{		
	
		//开始数据验证
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if(trim($user_data['user_name'])=='')
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".trim($user_data['user_name'])."' and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		
		
		//验证结束开始插入数据
		$user['user_name'] = $user_data['user_name'];
		$user['create_time'] = get_gmtime();
		$user['update_time'] = get_gmtime();
		//自动获取会员分组
		$user['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
		$user['is_effect'] = 1; //手机注册自动生效
		
		$user['email'] = $user_data['email'];

		$user['user_pwd'] = md5($user_data['user_pwd']);
		
		//载入会员整合
		$integrate_code = trim(app_conf("INTEGRATE_CODE"));
		if($integrate_code!='')
		{
			$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
			if(file_exists($integrate_file))
			{
				require_once $integrate_file;
				$integrate_class = $integrate_code."_integrate";
				$integrate_obj = new $integrate_class;
			}	
		}
		//同步整合
		if($integrate_obj)
		{
			$res = $integrate_obj->add_user($user_data['user_name'],$user_data['user_pwd'],$user_data['email']);
			$user['integrate_id'] = intval($res['data']);		
			if(intval($res['status'])==0) //整合注册失败
			{
				//return $res;  //不处理
			}
		}
		if($res['status']>0)
		{
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"INSERT","");
			$user_id = $GLOBALS['db']->insert_id();	
			if($user_id > 0)
			{
					$register_money = doubleval(app_conf("USER_REGISTER_MONEY"));
					$register_score = intval(app_conf("USER_REGISTER_SCORE"));
					if($register_money>0||$register_score>0)
					{
						$user_get['score'] = $register_score;
						$user_get['money'] = $register_money;
						modify_account($user_get,intval($user_id),"在".to_date(get_gmtime())."注册成功");
					}
			}
			$res['data'] = $user_id;
		}

			if(strim($GLOBALS['request']['sina_id'])!='')
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where sina_id = '".strim($GLOBALS['request']['sina_id'])."'")==0)
				{
					$access_token =  trim($GLOBALS['request']['access_token']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set sina_id = '".strim($GLOBALS['request']['sina_id'])."',sina_token = '".$access_token."' where id = ".$user_id);				
				}
				
				
			}
			if(strim($GLOBALS['request']['tencent_id'])!='')
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where tencent_id = '".strim($GLOBALS['request']['tencent_id'])."'")==0)
				{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set tencent_id = '".strim($GLOBALS['request']['tencent_id'])."' where id = ".$user_id);
			
				$openid = trim($GLOBALS['request']['openid']);
				$openkey = trim($GLOBALS['request']['openkey']);
		 		$access_token =  trim($GLOBALS['request']['access_token']);
				$GLOBALS['db']->query("update ".DB_PREFIX."user set t_access_token ='".$access_token."',t_openkey = '".$openkey."',t_openid = '".$openid."', login_ip = '".get_client_ip()."',login_time= ".get_gmtime()." where id =".$user_id);				
				
				}
				
			}
		
		return $res;
}
*/

class register{
	public function index()
	{
		//require_once APP_ROOT_PATH."system/libs/user.php";
		
		
		$user_name = strim($GLOBALS['request']['user_name']);//用户名
		$user_pwd = strim($GLOBALS['request']['user_pwd']);//密码
		$user_pwd_confirm = strim($GLOBALS['request']['user_pwd_confirm']);		
		$mobile = strim($GLOBALS['request']['mobile']);
		$mobile_code = strim($GLOBALS['request']['mobile_code']);
			
		$user_data = array(
			'mobile'            => $mobile,
			'user_name'        => $user_name,
			'user_pwd'         => $user_pwd,		
			'user_pwd_confirm'   => $user_pwd_confirm,
		);
				
		
		$check_status = $this->check_user($user_data);
		
		if ($check_status['status'] == 1){
			//短信验证码
			if($mobile_code != $GLOBALS['db']->getOne("select verify_code from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and create_time>=".(TIME_UTC-180)." ORDER BY id DESC"))
			{
				$root['response_code'] = 0;
				$root['show_err'] = "短信验证码错误或已失效";
				//output($root);			
			}else{
				require_once APP_ROOT_PATH."system/libs/user.php";
				
				$user_id = $this->add_user($user_data);
				if($user_id > 0)
				{
					$result = user_login($user_name,$user_pwd);					
					if($result['status'])
					{
						$user_data = $GLOBALS['user_info'];//$result['user'];
						$root['response_code'] = 1;
						$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
						$root['show_err'] = "用户登陆成功";		
						$root['id'] = $user_data['id'];
						$root['user_name'] = $user_data['user_name'];
						//$root['user_email'] = $user_data['email'];
						$root['user_money'] = $user_data['money'];
						$root['user_money_format'] = format_price($user_data['money']);//用户金额					
					}else{
						$root['response_code'] = 0;
						$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
						$root['show_err'] = "会员注册失败.";
					}
				}else
				{
					$root['response_code'] = 0;
					$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
					$root['show_err'] = "会员注册失败.";
					//output($root);
				}
			}			
			//output($root);
		}else{
			$root['response_code'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['show_err'] = $check_status['error_msg'];
			//output($root);
		}
		
		output($root);
	}
	
	function check_user($user_data){
		//开始数据验证
		$res = array('status'=>1,'info'=>'','data'=>'','error_msg'=>''); //用于返回的数据
				
		if($user_data['user_pwd'] != $user_data['user_pwd_confirm'])
		{			
			$res['status'] = 0;
			$res['error_msg'] = '密码确认失败';
			return  $res;
		}
		
		if(trim($user_data['user_pwd'])=='')
		{
			$res['status'] = 0;
			$res['error_msg'] = '请输入密码';
			return  $res;
		}		
		
		if($res['status'] == 1 && trim($user_data['user_name'])=='')
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;			
		}
		
		if($res['status'] == 1 && $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".trim($user_data['user_name'])."' and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;			
		}
		
		if($res['status'] == 1 && trim($user_data['mobile'])=='')
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;			
		}
			
		if($res['status'] == 1 && !check_mobile(trim($user_data['mobile'])))
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	FORMAT_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;			
		}
		
		
		if($res['status'] == 1 && $user_data['mobile']!=''&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".trim($user_data['mobile'])."' and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;			
		}
		
		if ($res['status'] == 0){
			$error = $res['data'];
			$error_msg = "";
			if(!$error['field_show_name'])
			{
				$error['field_show_name'] = sprintf('%s用户名不能为空',$error['field_name']);
			}
			if($error['error']==EMPTY_ERROR)
			{
				$error_msg = sprintf('%s不能为空',$error['field_show_name']);
			}
			if($error['error']==FORMAT_ERROR)
			{
				$error_msg = sprintf('%s格式错误，请重新输入',$error['field_show_name']);
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_msg = sprintf('%s已存在，请重新输入',$error['field_show_name']);
			}
			//showErr($error_msg);
		
			$res['error_msg'] = $error_msg;			
		}
		
		return $res;
	}
	
	
	
	
	/**
	 * 生成会员数据
	 * @param $user_data  提交[post或get]的会员数据
	 * @param $mode  处理的方式，注册或保存
	 * 返回：data中返回出错的字段信息，包括field_name, 可能存在的field_show_name 以及 error 错误常量
	 * 不会更新保存的字段为：score,money,verify,pid
	 */
	function add_user($user_data)
	{
		
		//$res = array('status'=>1,'id'=>0); //用于返回的数据
		
		//验证结束开始插入数据
		$user_id = 0;
		
		$user['user_name'] = $user_data['user_name'];
		$user['create_time'] = TIME_UTC;
		$user['update_time'] = TIME_UTC;
		//$user['pid'] = $user_data['pid'];	
	
		//获取默认会员组, 即升级积分最小的会员组
		$user['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
		
		$user['is_effect'] = app_conf("USER_VERIFY");
		$user['mobile'] = $user_data['mobile'];
		$user['mobilepassed'] = 1;//是否已经绑定手机；1：是；0：否; 手机注册的，直接就绑定手机了;
		$user['code'] = ''; //默认不使用code, 该值用于其他系统导入时的初次认证
		$user['user_pwd'] = md5($user_data['user_pwd'].$user['code']);
		
		/*
		//载入会员整合，手机端没填：email，暂时不做会员整合;
		$integrate_code = trim(app_conf("INTEGRATE_CODE"));
		if($integrate_code!='')
		{
			$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
			if(file_exists($integrate_file))
			{
				require_once $integrate_file;
				$integrate_class = $integrate_code."_integrate";
				$integrate_obj = new $integrate_class;
			}
		}
		
		//同步整合
		if($integrate_obj)
		{
			$res = $integrate_obj->add_user($user_data['user_name'],$user_data['user_pwd'],$user_data['email']);
			$user['integrate_id'] = intval($res['data']);
	
			if(intval($res['status'])==0) //整合注册失败
			{
				return $res;
			}
		}
		
		 $s_api_user_info = es_session::get("api_user_info");
		$user[$s_api_user_info['field']] = $s_api_user_info['id'];
		es_session::delete("api_user_info");
		*/
		if($GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,'INSERT'))
		{
			$user_id = $GLOBALS['db']->insert_id();
			$register_money = doubleval(app_conf("USER_REGISTER_MONEY"));
			$register_score = intval(app_conf("USER_REGISTER_SCORE"));
			$register_point = intval(app_conf("USER_REGISTER_POINT"));
			$register_lock_money = intval(app_conf("USER_LOCK_MONEY"));
			if($register_money>0||$register_score>0 || $register_point > 0 || $register_lock_money>0)
			{
				$user_get['score'] = $register_score;
				$user_get['money'] = $register_money;
				$user_get['point'] = $register_point;
				$user_get['reg_lock_money'] = $register_lock_money;
				modify_account($user_get,intval($user_id),"在".to_date(TIME_UTC)."注册成功");
			}
		}
	
		return $user_id;
	}
	
}
?>