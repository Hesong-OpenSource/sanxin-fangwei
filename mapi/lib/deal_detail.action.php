<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH . 'app/Lib/shop_lip.php';
require APP_ROOT_PATH . 'app/Lib/page.php';
class deal_detail {
	public function index() {
		$root = array ();
		$id = intval ( $_REQUEST ['id'] );
		$email = strim ( $GLOBALS ['request'] ['email'] ); // 用户名或邮箱
		$pwd = strim ( $GLOBALS ['request'] ['pwd'] ); // 密码 // 检查用户,用户密码
	
		$user = user_check ( $email, $pwd );
		$user_level = intval ( $user ['user_level'] );
		$user_id=intval($user['id']);
		
		if($user_id>0){
			$is_focus = $GLOBALS['db']->getOne("select  count(*) from ".DB_PREFIX."deal_focus_log where deal_id = ".$id." and user_id = ".$user_id);	
			$root['is_focus']=$is_focus;
		}
		
		
		// 权限控制
		$condition = " is_delete = 0 and id = $id";
		if ($user_level != 0) {
			$condition .= " AND (user_level <=" . $user_level . ") ";
		} else {
			$condition .= " AND (user_level =0 or user_level =1 or user_level ='') ";
		}
		
		$deal_list = $GLOBALS ['db']->getRow ( "select * from " . DB_PREFIX . "deal where " . $condition );
		
		$now_time = get_gmtime ();
		
		
		// $deal_list['status']值0表示即将开始；1表示已成功；2表示筹资失败；3表示筹资中；4表示长期项目
		$deal_list ['percent'] = round ( $deal_list ['support_amount'] / $deal_list ['limit_price'] * 100 );
		if ($deal_list ['begin_time'] > $now_time) {
			$deal_list ['status'] = '0';
		} elseif ($deal_list ['end_time'] < $now_time && $deal_list ['end_time'] > 0) {
			if ($deal_list ['percent'] >= 100) {
				$deal_list ['status'] = '1';
			} else {
				if ($deal_list ['percent'] >= 0) {
					$deal_list ['status'] = '2';
				}
			}
		} else {
			if ($deal_list ['end_time'] > 0) {
				if ($deal_list ['percent'] >= 100) {
					$deal_list ['status'] = '1';
				} else
					$deal_list ['status'] = '3';
			} else
				$deal_list ['status'] = '4';
		}
		if ($deal_list ['end_time'] > 0 && $deal_list ['end_time'] > $now_time) {
			$deal_list ['remain_days'] = floor ( ($deal_list ['end_time'] - $now_time) / (24 * 3600) );
		} elseif ($deal_list ['end_time'] > 0 && $deal_list ['end_time'] <= $now_time) {
			$deal_list ['remain_days'] = 0;
		}
		
		if ($deal_list ['begin_time'] > $now_time) {
			$deal_list ['left_days'] = intval ( ($deal_list ['begin_time'] - $now_time) / 24 / 3600 );
		} else {
			$deal_list ['left_days'] = 0;
		}
		$pattern = "/<img([^>]*)\/>/i";
		$replacement = "<img width=300 $1 />";
		$deal_list ['description'] = preg_replace($pattern, $replacement, get_abs_img_root($deal_list ['description']));
		$deal_list ['image'] = get_abs_img_root ( get_spec_image ( $deal_list ['image'], 640, 240, 1 ) );
		$deal_list ['num_days'] = floor ( ($deal_list ['end_time'] - $deal_list ['begin_time']) / (24 * 3600) );
		$deal_list ['end_time'] = to_date ( $deal_list ['end_time'], 'Y-m-d' );
		$deal_list ['begin_time'] = to_date ( $deal_list ['begin_time'], 'Y-m-d' );
		$deal_list ['create_time'] = to_date ( $deal_list ['create_time'], 'Y-m-d' );
		$deal_list ['content'] = $deal_list ['description'];
		$deal_list ['deal_extra_cache'] = null;
		
		$root ['deal_list'] = $deal_list;
		output ( $root );
	}
}
?>
