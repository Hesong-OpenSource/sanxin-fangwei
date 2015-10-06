<?php

class send_register_code{
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
			$root['show_err'] ='请填写正确的手机号码';
			output($root);
		}
	
	
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$mobile."'")>0)
		{
			$field_show_name ='手机号码';
			$root['response_code'] = 0;
			$root['show_err'] = sprintf('%s已存在，请重新输入',$field_show_name);
			output($root);
		}
	
	
		if(!check_ipop_limit(get_client_ip(),"mobile_verify",60,0))
		{
			$root['response_code'] = 0;
			$root['show_err'] = '短信发送太快,请稍后再试';
			output($root);
		}
	
		//删除超过5分钟的验证码
		$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."mobile_verify_code WHERE create_time <=".get_gmtime()-300);
	
		$verify_code = $GLOBALS['db']->getOne("select verify_code from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and create_time>=".(TIME_UTC-180)." ORDER BY id DESC");
		if(intval($verify_code) == 0)
		{
			//如果数据库中存在验证码，则取数据库中的（上次的 ）；确保连接发送时，前后2条的验证码是一至的.==为了防止延时
			//开始生成手机验证
			$verify_code = rand(1111,9999);
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",array("verify_code"=>$verify_code,"mobile"=>$mobile,"create_time"=>get_gmtime(),"client_ip"=>get_client_ip()),"INSERT");
		}
	
		//使用立即发送方式
		$result = send_verify_sms($mobile,$verify_code,null,true);//
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