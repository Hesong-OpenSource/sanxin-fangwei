<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_center
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

			$province_str = $GLOBALS['db']->getOne("select province from ".DB_PREFIX."user where id = ".$user_id);
			$city_str = $GLOBALS['db']->getOne("select city from ".DB_PREFIX."user where id = ".$user_id);
			if($province_str.$city_str=='')
				$user_location = '未知';
			else
				$user_location = $province_str." ".$city_str;
			$user['money_format'] = format_price($user['money']);//可用资金						
			$user['create_time_format'] = to_date($user['create_time'],'Y-m-d'); //注册时间
			$root['response_code'] = 1;
			$root['user_name'] = $user['user_name'];
			$root['money_format'] = $user['money_format'];
			$root['money'] = $user['money'];
			$root['create_time_format'] = $user['create_time_format'];
			$root['sex'] = $user['sex'];
			$root['province'] = $user['province'];
			$root['city'] = $user['city'];
			$root['intro'] = $user['intro'];
			$root['email'] = $user['email'];
			$root['image'] = get_user_avatar_root($user['id'],"middle");;
			
			$weibo_list = $GLOBALS['db']->getOne("select weibo_url from ".DB_PREFIX."user_weibo where user_id = ".intval($GLOBALS['user_info']['id']));
			$root['weibo_list'] = $weibo_list;
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>
