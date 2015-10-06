<?php
class pay_order{
	public function index(){
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);		
		
			
		$root = array();
		if($user_id>0)
		{
			
			$order_id = intval($GLOBALS['request']['order_id']);			
			$credit = doubleval($_REQUEST['credit']);
			$payment_id = intval($_REQUEST['payment']);
			
			$memo = strim($_REQUEST['memo']);	
			
			$root['user_login_status'] = 1;
			$root['show_pay_btn'] = 0;//0:不显示，支付按钮; 1:显示支付按钮
			
			$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where user_id = {$user_id} and id = ".$order_id);
			
			if (empty($order)){
			//	$root['order_status'] = 1;
				$root['pay_info'] = '订单不存在.';
				$root['show_pay_btn'] = 0;
				output($root);		
			}
			
			if ($order['order_status'] == 2){
				$root['order_status'] = 1;
				$root['pay_code'] = '';
				$root['order_id'] = $order_id;
				$root['order_sn'] = $order['order_sn'];
				$root['response_code'] = 1;				
				$root['pay_info'] = '订单已支付成功.';
				$root['show_pay_btn'] = 0;
				output($root);
			}
			
			
			if ($payment_id == 0){
				$payment_id = intval($order['payment_id']);
			}
			$deal_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id= ".$order['deal_id']);
 			if($credit>0)
			{
				$sql = $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set credit_pay = credit_pay + ".$credit." where id = ".$order_id);
				require_once APP_ROOT_PATH."system/libs/user.php";
				modify_account(array("money"=>"-".$credit),intval($user_id),"支持".$deal_info['name']."项目支付");				
			}
			
			$result = pay_order($order_id);
			if($order['credit_pay']=$order['total_price']){
				$root['response_code'] = 1;	
				$root['info'] = "余额全部支付";			
			}
			if(($order['credit_pay']<$order['total_price'])&&($order['credit_pay']>0)){
				$root['response_code'] = 2;	
				$root['info'] = "余额部分支付";			
			}
			if($order['credit_pay']=0){
				$root['response_code'] = 3;	
				$root['info'] = "余额未支付";			
			}
			if($result['status']==0)
			{
				$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id=".$payment_id);
				 
				$pay_code = strtolower($payment_info['class_name']);
				/*online_pay 支付方式：1：在线支付；0：线下支付;2:手机wap;3:手机sdk */
				$online_pay = intval($payment_info['online_pay']);
				$root['online_pay']=$online_pay;
				if ($online_pay != 2){
					$root['response_code'] = 0;
					$root['pay_info'] = '手机版本不支付,无法在手机上支付.'.$pay_code;
					$root['show_pay_btn'] = 0;	
					output($root);
				}
				if($online_pay = 2){
					$money = $result['money'];
					
					$payment_notice['create_time'] = NOW_TIME;
					$payment_notice['user_id'] = intval($GLOBALS['user_info']['id']);
					$payment_notice['payment_id'] = $payment_id;
					$payment_notice['money'] = $money;
				//	$payment_notice['bank_id'] = strim($_REQUEST['bank_id']);
					$payment_notice['order_id'] = $order_id;
					$payment_notice['memo'] = $memo;
					$payment_notice['deal_id'] = $deal_info['id'];
					$payment_notice['deal_item_id'] = $order['deal_item_id'];
					$payment_notice['deal_name'] = $deal_info['name'];
					
					do{
						$payment_notice['notice_sn'] = to_date(NOW_TIME,"Ymd").rand(100,999);
						$GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$payment_notice,"INSERT","","SILENT");
						$notice_id = $GLOBALS['db']->insert_id();
					}while($notice_id==0);
				}	
					
								//创建了支付单号，通过支付接口创建支付数据
				require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
				$payment_class = $payment_info['class_name']."_payment";
				$payment_object = new $payment_class();
				$pay = $payment_object->get_payment_code($notice_id);
				
				$root['is_wap'] = intval($pay['is_wap']);			

				$root['pay_money_format'] = $pay['total_fee_format'];
				$root['pay_money'] = $pay['total_fee'];
				$root['pay_info'] = $pay['body'];
				$root['pay_wap'] = $pay['notify_url'];
						
				if ($root['pay_money'] > 0){
					$root['show_pay_btn'] = 1;
				}
				
			}
			
			output($root);
		}
		else {
			$root['response_code'] = 0;
			$root['user_login_status'] = 0;
			$root['show_err'] = "未登录";
			output($root);
		}		
	}
}
?>