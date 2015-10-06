<?php

// +----------------------------------------------------------------------
// | Fanwe 方维众筹支持的项目
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class pay_cart {
	public function index() {

		$root = array ();

		$email = strim($GLOBALS['request']['email']); //用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']); //密码
		//检查用户,用户密码
		$user = user_check($email, $pwd);
		$user_id = intval($user['id']);
		if ($user_id > 0) {
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			$user['money_format'] = format_price($user['money']);//可用资金	
			$root['money_format'] = $user['money_format'];
			$root['money'] = $user['money'];
			$id = intval($_REQUEST['id']);
			$deal_item = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_item where id = " . $id);
			if (!$deal_item) {
				$root['addr'] = get_domain().APP_ROOT;	
			}
			elseif ($deal_item['support_count'] >= $deal_item['limit_user'] && $deal_item['limit_user'] != 0) {
				$root['addr'] = url("deal#show",array("id"=>$deal_item['deal_id']));
				//app_redirect(url("deal#show",array("id"=>$deal_item['deal_id'])));
			}
			$deal_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal where is_delete = 0 and is_effect = 1 and id = " . $deal_item['deal_id']);
			if (!$deal_info) {
				$root['addr'] = get_domain().APP_ROOT;	
			}
			elseif ($deal_info['begin_time'] > NOW_TIME || ($deal_info['end_time'] < NOW_TIME && $deal_info['end_time'] != 0)) {
				$root['addr'] = url("deal#show",array("id"=>$deal_item['deal_id']));
				//app_redirect(url("deal#show", array (	"id" => $deal_item['deal_id'])));
			}

			$deal_item['price_format'] = number_price_format($deal_item['price']);
			$deal_item['delivery_fee_format'] = number_price_format($deal_item['delivery_fee']);
			$deal_item['total_price'] = $deal_item['price']+$deal_item['delivery_fee'];
			$deal_item['total_price_format'] = number_price_format($deal_item['total_price']);
			$deal_info['percent'] = round($deal_info['support_amount'] / $deal_info['limit_price'] * 100);
			$deal_info['remain_days'] = floor(($deal_info['end_time'] - NOW_TIME) / (24 * 3600));
			$root['deal_item'] = $deal_item;
			$root['deal_info'] = $deal_info;

			if ($deal_info['seo_title'] != "")
				$root['seo_title'] = $deal_info['seo_title'];
			if ($deal_info['seo_keyword'] != "")
				$root['seo_keyword'] = $deal_info['seo_keyword'];
			if ($deal_info['seo_description'] != "")
				$root['seo_description'] = $deal_info['seo_description'];
			$root['page_title'] = $deal_info['name'];
			if ($deal_item['is_delivery']) {
				$consignee_list = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "user_consignee where user_id = " . intval($GLOBALS['user_info']['id']));
				if ($consignee_list)
					$root['consignee_list'] = $consignee_list;
				else {
					$region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); //二级地址
					$root['region_lv2'] = $region_lv2;
				}
			}
			$consignee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_consignee where user_id = ".intval($GLOBALS['user_info']['id']));
			$root['consignee_list'] = $consignee_list;

			$memo = es_session::get("cart_memo_".$id);
			$consignee_id = intval($_REQUEST['did']);
			$root['memo'] = $memo;
			$root['consignee_id'] = $consignee_id;
		
			$payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment where is_effect = 1 and online_pay = 2 order by sort asc ");
			$payment_html = "";
			foreach($payment_list as $k=>$v)
			{
				$class_name = $v['class_name']."_payment";
				require_once APP_ROOT_PATH."system/payment/".$class_name.".php";
				$o = new $class_name;
				$payment_html .= "<div>".$o->get_display_code()."<div class='blank'></div></div>";
			}
			$root['payment_html'] = $payment_html;
			$root['payment_list'] = $payment_list;
		} else {
			$root['response_code'] = 0;
			$root['show_err'] = "未登录";
			$root['user_login_status'] = 0;
		}
		output($root);
	}
}
?>