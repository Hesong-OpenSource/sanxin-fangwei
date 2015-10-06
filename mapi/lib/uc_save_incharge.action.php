<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_save_incharge
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		$payment_id = intval($GLOBALS['request']['payment_id']);
		$money = floatval($GLOBALS['request']['money']);
		$bank_id = addslashes(htmlspecialchars(trim($GLOBALS['request']['bank_id'])));
		$memo = addslashes(htmlspecialchars(trim($GLOBALS['request']['memo'])));
		
		if($payment_id == 0){
			$root['response_code'] = 0;
			$root['show_err'] = '支付方式不能为空';
			output($root);
		}
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){

			$root['response_code'] = 1;
			$root['user_login_status'] = 1;
			
			//require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$status = getInchargeDone($payment_id,$money,$bank_id,$memo);
			
			if($status['status'] == 0){				
				$root['response_code'] = 0;
				$root['show_err'] = $status['show_err'];
			}
			else{
				$root['pay_status'] = $status['pay_status'];
				$root['order_id'] = $status['order_id'];
				$root['payment_notice_id'] = $status['payment_notice_id'];	

				$payment_info = $status['payment_info'];
				$payment_notice_id = $status['payment_notice_id'];	
				
				//创建了支付单号，通过支付接口创建支付数据
				if ($payment_info['class_name']=='Otherpay'){
					$root['pay_code'] == 'otherpay';
					$root['pay_type'] = 2;
					$root['show_err'] ="信息已经提交,请等待管理员审核";
				}else{
					require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
					$payment_class = $payment_info['class_name']."_payment";
					$payment_object = new $payment_class();
					$pay = $payment_object->get_payment_code($payment_notice_id);
					
					$root['pay_code'] = $pay['pay_code'];
					if ($pay['pay_code'] == 'walipay'){		
						$root['pay_type'] = 1;
						$root['pay_wap'] = $pay['notify_url'];								
					}
				}
			}
									
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>
