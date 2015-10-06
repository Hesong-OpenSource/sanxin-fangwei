<?php

class send_reset_pwd_code{
	
	public function index()
	{
		$mobile = addslashes(htmlspecialchars(trim($GLOBALS['request']['mobile'])));
	
		$root = array();
	
		if(app_conf("SMS_ON")==0)
		{
			$root['response_code'] = 0;
			$root['show_err'] = '短信未开启';
			output($root);
		}
	
	
		if($mobile == '')
		{
			$root['response_code'] = 0;
			$root['show_err'] = '请输入你的手机号';
			output($root);
		}
	
		if(!check_mobile($mobile))
		{
			$root['response_code'] = 0;
			$root['show_err'] = '请填写正确的手机号码';
			output($root);
		}
	

		if(!check_ipop_limit(get_client_ip(),"mobile_verify",60,0))
		{
			$root['response_code'] = 0;
			$root['show_err'] = '短信发送太快,请稍后再试';
			output($root);
		}

		
		$sql = "select id,bind_verify from ".DB_PREFIX."user where mobile = '".$mobile."' and is_delete = 0";
		$user_info = $GLOBALS['db']->getRow($sql);
		$user_id = intval($user_info['id']);
		$code = intval($user_info['bind_verify']);
	
		if($user_id == 0)
		{
			//$field_show_name = $GLOBALS['lang']['USER_TITLE_mobile'];
			$root['response_code'] = 0;
			$root['show_err'] = '手机号码不存在或被禁用';
			output($root);
		}
	

		//开始生成手机验证
		if ($code == 0){
			//已经生成过了，则使用旧的验证码；反之生成一个新的
			$code = rand(1111,9999);
			$GLOBALS['db']->query("update ".DB_PREFIX."user set bind_verify = '".$code."',verify_create_time = '".TIME_UTC."' where id = ".$user_id);
		}
	
		//使用立即发送方式
		$result = send_verify_sms($mobile,$code,null,true);//
		$root['response_code'] = $result['status'];
		
		if ($root['response_code'] == 1){
			$root['show_err'] = '验证短信已经发送，请注意查收';
		}else{
			$root['show_err'] = $result['msg'];
			if ($root['show_err'] == null || $root['show_err'] == ''){
				$root['show_err'] = "验证码发送失败";
			}
		}
	
		output($root);
	}
}
?>