<?php
// +----------------------------------------------------------------------
// | Fanwe 方维用户提现表单提交
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_account_submitrefund
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			$money = doubleval($_REQUEST['money']);
			$memo = strim($_REQUEST['memo']);
			
			if($money<=0)
			{
				$root['info'] = "提现金额出错";
				output($root);
			}
			
			$ready_refund_money =doubleval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."user_refund where user_id = ".intval($GLOBALS['user_info']['id'])." and is_pay = 0"));
			if($ready_refund_money + $money > $GLOBALS['user_info']['money'])
			{
				$root['info'] = "提现超出限制";
				output($root);
			}
			
			$refund_data['money'] = $money;
			$refund_data['user_id'] = $GLOBALS['user_info']['id'];
			$refund_data['create_time'] = NOW_TIME;
			$refund_data['memo'] = $memo;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_refund",$refund_data);
			$root['info'] = "申请成功";
		//	showSuccess("",$ajax,get_gopreview());
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>