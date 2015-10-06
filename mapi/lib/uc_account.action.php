<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹支持的项目
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_account
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
			$now_time = get_gmtime();
			$page_size =  $GLOBALS['m_config']['page_size'];
			$page = intval($_REQUEST['p']);
			if($page==0)$page = 1;		
			$limit = (($page-1)*$page_size).",".$page_size	;
			
			$order_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order where user_id = ".intval($GLOBALS['user_info']['id'])." order by create_time desc limit ".$limit);
			
			$order_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order where user_id = ".intval($GLOBALS['user_info']['id']));
			$root['page'] = array("page"=>$page,"page_total"=> ceil($order_count/$page_size),"page_size"=>intval($page_size),'total'=>intval($order_count));
			foreach($order_list as $k=>$v){
				$deal_ids[] =  $v['deal_id'];	
			}
		//	print_r($order_list);exit;
			$deal_list_array=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal where  is_effect = 1 and is_delete = 0 and id in (".implode(',',$deal_ids).")");
			$deal_list=array();			
			foreach($deal_list_array as $k=>$v){
				if($v['id']){
					$deal_list[$v['id']]=$v;
				}
			}
			
	 		//unset($deal_list_array);
			foreach($order_list as $k=>$v)
			{
		//			$order_list[$k]['deal_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$v['deal_id']." and is_effect = 1 and is_delete = 0");
					$order_list[$k]['notice_sn'] = $GLOBALS['db']->getOne("select notice_sn from ".DB_PREFIX."payment_notice where order_id = ".$v['id']);
		 			$image = $GLOBALS['db']->getOne("select image from ".DB_PREFIX."deal where id = ".$v['deal_id']);
		 			$order_list[$k]['image'] = get_abs_img_root(get_spec_image($image,640,240,1));
		 			$deal_info =$deal_list[$v['deal_id']];
		 			$deal_info['image'] =get_abs_img_root(get_spec_image($deal_info['image'],640,240,1));
					$deal_info['end_time']=to_date($deal_info['end_time'],'Y-m-d');
					$deal_info['begin_time']=to_date($deal_info['begin_time'],'Y-m-d');
					$deal_info['create_time']=to_date($deal_info['create_time'],'Y-m-d');
					$deal_info['content']=$deal_info['description'];
					$deal_info['deal_extra_cache']=null;
					
					//$order_list[$k]['state']值0表示已用余额支付$order_list[$k]['credit_pay']剩余支付未完成；1表示未开始；2表示已成功；3表示回报已发放；4表示确认收到;5表示未确认收到；6表示等待发放回报；7表示未成功；8表示已退款；9表示等待退款;10表示未结束					
					if($order_list[$k]['order_status'] > 0){	
						if($deal_info['is_success'] = 1){
							 if ($deal_info['begin_time']> $now_time ){
							 	$order_list[$k]['state']=1;		//1表示未开始
							 }
							 if ($deal_info['end_time'] < $now_time and $deal_info['end_time'] != 0){
							 	$order_list[$k]['state']=2;		//2表示已成功
							 	if($order_list[$k]['repay_time'] > 0){
							 		$order_list[$k]['state']=3;  //3表示回报已发放
									if($order_list[$k]['repay_make_time'] > 0){ 
										$order_list[$k]['state']=4;  //4表示确认收到
							 		}
								 	else 
								 		$order_list[$k]['state']=5;  	//5表示未确认收到	 
							 	}	
								else
									$order_list[$k]['state']=6; 	//6表示等待发放回报
							 }
                             if($deal_info['begin_time'] < $now_time && ($deal_info['end_time'] > $now_time || $deal_info['end_time'] = 0)){
                             		$order_list[$k]['state']=2; //2表示已成功
                             	if($order_list[$k]['repay_time'] > 0){
                             		$order_list[$k]['state']=3;  //3表示回报已发放
	                             	if($order_list[$k]['repay_make_time'] > 0){
	                             		$order_list[$k]['state']=4;  //4表示确认收到
	                             	}
	                             	else
	                             	 	$order_list[$k]['state']=5;  	//5表示未确认收到
                             	}	
                             	else
                             		$order_list[$k]['state']=6; 	//6表示等待发放回报
                             	
                             }
                             else{
                             	if ($deal_info['begin_time'] > $now_time){
                             		$order_list[$k]['state']=1;		//1表示未开始
                             	}
                             	if($deal_info['end_time'] < $now_time && $deal_info['end_time'] != 0){
                             		 $order_list[$k]['state']=7;		//7表示未成功
		                             if ($order_list[$k]['is_refund'] = 1){
		                             	$order_list[$k]['state']=8;		//8表示已退款
		                             }
		                             else
		                             	$order_list[$k]['state']=9;		//9表示等待退款
		                             
                             	}
                             	if($deal_info['begin_time'] < $now_time && ($deal_info['end_time'] > $now_time || $deal_info['end_time'] = 0)) {
                             		$order_list[$k]['state']=10;		//10表示未结束
                             	}
                             }  
						}
						else{
							if ($deal_info['is_success'] = 0){
                            		$order_list[$k]['state']=7;		//7表示未成功
                            		if($order_list[$k]['repay_time'] > 0) {
                            			$order_list[$k]['state']=3;  //3表示回报已发放
	                            		if ($order_list[$k]['repay_make_time'] > 0){
	                            			$order_list[$k]['state']=4;  //4表示确认收到
	                            		} 
	                            		else 
	                            			$order_list[$k]['state']=5;  	//5表示未确认收到
	                            		
                            		}
                            		else
                            			$order_list[$k]['state']=6; 	//6表示等待发放回报		
							}
                            else{
                            	$order_list[$k]['state']=2;		//2表示已成功
                            	if($order_list[$k]['is_refund'] = 1) {
                            		$order_list[$k]['state']=8;		//8表示已退款
                            	}	
                            	else
                            		$order_list[$k]['state']=9;		//9表示等待退款
                            }   	
						}			
					}
					else
					{
						$order_list[$k]['state']=0;	
					}
					$order_list[$k]['deal_info']=$deal_info;
		 			$order_list[$k]['create_time']=to_date($v['create_time'],'Y-m-d');
					
	 			
			}
			$root['order_list'] = $order_list;
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>