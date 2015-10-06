<?php
// +----------------------------------------------------------------------
// | Fanwe 方维用户银行信息
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
// require APP_ROOT_PATH.'app/Lib/uc.php';
require APP_ROOT_PATH . 'app/Lib/shop_lip.php';
class uc_save_bank {
	public function index() {
		$root = array ();
		
		$email = strim ( $GLOBALS ['request'] ['email'] ); // 用户名或邮箱
		$pwd = strim ( $GLOBALS ['request'] ['pwd'] ); // 密码
		                                               
		// 检查用户,用户密码
		$user = user_check ( $email, $pwd );
		$user_id = intval ( $user ['id'] );
		if ($user_id > 0) {
			$root ['user_login_status'] = 1;
			if ($user ['ex_qq'] != "" || $user ['ex_account_bank'] != "" || $user ['ex_real_name'] != "" || $user ['ex_account_info'] != "" || $user ['ex_contact'] != "") {
				$root ['info'] = "银行帐户信息已经设置";
				$root ['ex_qq'] = $user ['ex_qq'];
				$root ['ex_account_bank'] = $user ['ex_account_bank'];
				$root ['ex_real_name'] = $user ['ex_real_name'];
				$root ['ex_account_info'] = $user ['ex_account_info'];
				$root ['ex_contact'] = $user ['ex_contact'];
				$root ['is_setting'] = 1;
				output ( $root );
			}
			
			if (! check_ipop_limit ( get_client_ip (), "setting_save_bank", 5 ))
				$root ['info'] = "提交太频繁";
				
				// print_r($GLOBALS['user_info']['ex_qq']);die();
			$ex_real_name = strim ( $_REQUEST ['ex_real_name'] );
			$ex_account_info = strim ( $_REQUEST ['ex_account_info'] );
			$ex_account_bank = strim ( $_REQUEST ['ex_account_bank'] );
			$ex_contact = strim ( $_REQUEST ['ex_contact'] );
			$ex_qq = strim ( $_REQUEST ['ex_qq'] );
			
			if ($ex_real_name == "") {
				$root ['info'] = "请填写姓名";
			} else {
				if ($ex_account_bank == "") {
					$root ['info'] = "请填写开户银行";
				} else {
					if ($ex_account_info == "") {
						$root ['info'] = "请填写银行帐号";
					} else {
						if ($ex_contact == "") {
							$root ['info'] = "请填写联系电话";
						}
						if (! check_mobile ( $ex_contact )) {
							$root ['info'] = "请填写正确的手机号码";
						} else {
							if ($ex_qq == "") {
								$root ['info'] = "请填写联系qq";
							} else {
								$GLOBALS ['db']->query ( "update " . DB_PREFIX . "user set ex_qq = '" . $ex_qq . "',ex_account_bank = '" . $ex_account_bank . "',ex_real_name = '" . $ex_real_name . "',ex_account_info = '" . $ex_account_info . "',ex_contact = '" . $ex_contact . "',is_bank = '" . '1' . "' where id = " . intval ( $GLOBALS ['user_info'] ['id'] ) );
								$root ['response_code'] = 1;
								$root ['info'] = "资料保存成功";
							}
						}
					}
				}
			}
			/*
			 * if($ex_account_bank=="") { $root['info'] = "请填写开户银行"; }
			 * if($ex_account_info=="") { $root['info'] = "请填写银行帐号"; }
			 * if($ex_contact=="") { $root['info'] = "请填写联系电话"; }
			 * if(!check_mobile($ex_contact)) { $root['info'] = "请填写正确的手机号码"; }
			 * if($ex_qq=="") { $root['info'] = "请填写联系qq"; }
			 * $GLOBALS['db']->query("update ".DB_PREFIX."user set ex_qq =
			 * '".$ex_qq."',ex_account_bank =
			 * '".$ex_account_bank."',ex_real_name =
			 * '".$ex_real_name."',ex_account_info =
			 * '".$ex_account_info."',ex_contact = '".$ex_contact."',is_bank =
			 * '".'1'."' where id = ".intval($GLOBALS['user_info']['id']));
			 * //	$root['info'] = "资料保存成功";
			 */
			// showSuccess("资料保存成功",$ajax,url("settings"));
		} else {
			$root ['response_code'] = 0;
			$root ['show_err'] = "未登录";
			$root ['user_login_status'] = 0;
		}
		output ( $root );
	}
}
?>
