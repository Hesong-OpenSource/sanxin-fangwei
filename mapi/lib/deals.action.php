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
class deals  {
	public function index() {
		$root = array ();
		$page = intval ( $GLOBALS ['request'] ['page'] ); // 分页
		$page = $page == 0 ? 1 : $page;
		$root ['response_code'] = 1;
		
		$param = array (); // 参数集合
		                   
		// 数据来源参数
		$r = strim ( $_REQUEST ['r'] ); // 推荐类型
		$param ['r'] = $r ? $r : '';

		$id = intval ( $_REQUEST ['id'] ); // 分类id3
		$param ['id'] = $id;

		// ios标志
		if($id == 0)
		{
			$isAll = 1;
		}else{
			$isAll = 0;
		}
		$loc = strim ( $_REQUEST ['loc'] ); // 地区
		$param ['loc'] = $loc;
		$GLOBALS ['tmpl']->assign ( "p_loc", $loc );
		
		$state = intval ( $_REQUEST ['state'] ); // 状态1
		$param ['state'] = $state;
		
		$tag = strim ( $_REQUEST ['tag'] ); // 标签
		$param ['tag'] = $tag;
		$GLOBALS ['tmpl']->assign ( "p_tag", $tag );
		
		$kw = strim ( $_REQUEST ['key'] ); // 关键词
		
		$param ['kw'] = $kw;
		
		$cate_list = load_dynamic_cache ( "INDEX_CATE_LIST" );
		
		if (! $cate_list) {
			$cate_list = $GLOBALS ['db']->getAll ( "select * from " . DB_PREFIX . "deal_cate order by sort asc" );
			set_dynamic_cache ( "INDEX_CATE_LIST", $cate_list );
		}
		$cate_result = array ();
		$cate_result [0] ['id'] = '0';
		$cate_result [0] ['name'] = "全部";
		foreach ( $cate_list as $k => $v ) {
				$temp_param = $param;
				$cate_result [$k+1] ['id'] = $v ['id'];
				$cate_result [$k+1] ['name'] = $v ['name'];
				$temp_param ['id'] = $v ['id'];
		}
		$root ['cate_list'] = $cate_result;
		$pid = 0;
		// 获取父类id
		if ($cate_list) {
			foreach ( $cate_list as $k => $v ) {
				if ($v ['id'] == $id) {
					if ($v ['pid'] > 0) {
						$pid = $v ['pid'];
					} else {
						$pid = $id;
					}
				}
			}
		}
		
		/* 子分类 start */
		$cate_ids = array ();
		$is_has_child = false;
		$temp_cate_ids = array ();
		if ($cate_list) {
			$child_cate_result = array ();
			foreach ( $cate_list as $k => $v ) {
				if ($v ['pid'] == $pid) {
					if ($v ['pid'] > 0) {
						$temp_param = $param;
						$child_cate_result [$v ['id']] ['id'] = $v ['id'];
						$child_cate_result [$v ['id']] ['name'] = $v ['name'];
						$temp_param ['id'] = $v ['id'];
						$child_cate_result [$v ['id']] ['url'] = url_mapi ( "deals", $temp_param );
						
						if ($v ['id'] == $id) {
							$is_has_child = true;
						}
					}
				}
				if ($v ['pid'] == $pid || $pid == 0) {
					$temp_cate_ids [] = $v ['id'];
				}
			}
		}
		
		// 假如选择了子类 那么使用子类ID 否则使用 父类和其子类
		if ($is_has_child) {
			$cate_ids [] = $id;
		} else {
			$cate_ids [] = $pid;
			$cate_ids = array_merge ( $cate_ids, $temp_cate_ids );
		}
		$root ['child_cate_list'] = $child_cate_result;
		$root ['pid'] = $pid;
		/* 子分类 end */
		$city_list = load_dynamic_cache ( "INDEX_CITY_LIST" );
		if (! $city_list) {
			$city_list = $GLOBALS ['db']->getAll ( "select province from " . DB_PREFIX . "deal group by province order by sort asc" );
			set_dynamic_cache ( "INDEX_CITY_LIST", $city_list );
		}
		foreach ( $city_list as $k => $v ) {
			$temp_param = $param;
			$temp_param ['loc'] = $v ['province'];
			$city_list [$k] ['url'] = url_mapi ( "deals", $temp_param );
		}
		$root ['city_list'] = $city_list;
		$state_list = array (
				0 => array (
						"name" => "全部" 
				),
				1 => array (
						"name" => "筹资成功" 
				),
				2 => array (
						"name" => "筹资失败" 
				),
				3 => array (
						"name" => "筹资中" 
				) 
		);
		foreach ( $state_list as $k => $v ) {
			$temp_param = $param;
			$temp_param ['state'] = $k;
			$state_list [$k] ['url'] = url_mapi ( "deals", $temp_param );
		}
		$root ['state_list'] = $state_list;
		$page_size = $GLOBALS ['m_config'] ['page_size'];
		$page = intval ( $_REQUEST ['p'] );
		if ($page == 0)
			$page = 1;
		$limit = (($page - 1) * $page_size) . "," . $page_size;
		$condition = " is_delete = 0 and is_effect = 1 ";
		if ($r != "") {
			if ($r == "new") {
				$condition .= " and " . NOW_TIME . " - begin_time < " . (24 * 3600) . " and " . NOW_TIME . " - begin_time > 0 "; // 上线不超过一天
				$GLOBALS ['tmpl']->assign ( "page_title", "最新上线" );
			}
			if ($r == "rec") {
				$condition .= " and " . NOW_TIME . " <= end_time AND " . NOW_TIME . " >= begin_time and is_recommend = 1 ";
				$GLOBALS ['tmpl']->assign ( "page_title", "推荐项目" );
			}
			if ($r == "yure") {
				$condition .= " and " . NOW_TIME . " - begin_time < " . (24 * 3600) . " and " . NOW_TIME . " - begin_time <  0 "; // 上线不超过一天
				$GLOBALS ['tmpl']->assign ( "page_title", "正在预热" );
			}
			if ($r == "nend") {
				$condition .= " and end_time - " . NOW_TIME . " < " . (24 * 3600) . " and end_time - " . NOW_TIME . " > 0 "; // 当天就要结束
				$GLOBALS ['tmpl']->assign ( "page_title", "即将结束" );
			}
			if ($r == "classic") {
				$condition .= " and is_classic = 1 ";
				$GLOBALS ['tmpl']->assign ( "page_title", "经典项目" );
			}
			if ($r == "limit_price") {
				$condition .= " and max(limit_price) ";
				$GLOBALS ['tmpl']->assign ( "page_title", "最高目标金额" );
			}
		}
		switch ($state) {
			// 筹资中
			case 0 :
				$GLOBALS ['tmpl']->assign ( "page_title", "全部" );
				break;
			// 筹资成功
			case 1 :
				$condition .= " and end_time < " . NOW_TIME . "  and support_amount >= limit_price";
				$GLOBALS ['tmpl']->assign ( "page_title", "筹资成功" );
				break;
			// 筹资失败
			case 2 :
				$condition .= " and end_time < " . NOW_TIME . "  and  support_amount < limit_price ";
				$GLOBALS ['tmpl']->assign ( "page_title", "筹资失败" );
				break;
			// 筹资中
			case 3 :
				$condition .= " and end_time > " . NOW_TIME . "  and begin_time < " . NOW_TIME . " ";
				$GLOBALS ['tmpl']->assign ( "page_title", "筹资中" );
				break;
		}
		if (count ( $cate_ids ) > 0) {
			$condition .= " and cate_id in (" . implode ( ",", $cate_ids ) . ")";
			$GLOBALS ['tmpl']->assign ( "page_title", $cate_result [$id] ['name'] );
		}
		if ($loc != "") {
			$condition .= " and (province = '" . $loc . "' or city = '" . $loc . "') ";
			$GLOBALS ['tmpl']->assign ( "page_title", $loc );
		}
		if ($tag != "") {
			$unicode_tag = str_to_unicode_string ( $tag );
			$condition .= " and match(tags_match) against('" . $unicode_tag . "'  IN BOOLEAN MODE) ";
			$GLOBALS ['tmpl']->assign ( "page_title", $tag );
		}
		
		if ($kw != "") {
			$kws_div = div_str ( $kw );
			foreach ( $kws_div as $k => $item ) {
				$kws [$k] = str_to_unicode_string ( $item );
			}
			$ukeyword = implode ( " ", $kws );
			$condition .= " and (match(name_match) against('" . $ukeyword . "'  IN BOOLEAN MODE) or match(tags_match) against('" . $ukeyword . "'  IN BOOLEAN MODE)  or name like '%" . $kw . "%') ";
			
			$GLOBALS ['tmpl']->assign ( "page_title", $kw );
		}
		
		// 权限浏览控制
		if ($GLOBALS ['user_info'] ['user_level'] != 0) {
			$condition .= " AND (user_level ='' or user_level=0 or user_level <=" . $GLOBALS ['user_info'] ['user_level'] . ") ";
		} else {
			$condition .= " AND (user_level =0 or user_level =1 or user_level ='') ";
		}
		
		$deal_count = $GLOBALS ['db']->getOne ( "select count(*) from " . DB_PREFIX . "deal where " . $condition );
		
		/* （所需项目）准备虚拟数据 start */
		$deal_list = array ();
		if ($deal_count > 0) {
			$now_time = get_gmtime ();
			// $deal_list = $GLOBALS['db']->getAll("select * from
			// ".DB_PREFIX."deal where ".$condition." order by sort asc ");
			$deal_list = $GLOBALS ['db']->getAll ( "select * from " . DB_PREFIX . "deal where " . $condition . " order by sort asc limit " . $limit );
			$deal_ids = array ();
			
			foreach ( $deal_list as $k => $v ) {
				// $deal_list['percent'] =
				// round($deal_list['support_amount']/$deal_list['limit_price']*100);
			}
			// 获取当前项目列表下的所有子项目
			$temp_virtual_person_list = $GLOBALS ['db']->getAll ( "select deal_id,virtual_person,price from " . DB_PREFIX . "deal_item where deal_id in(" . implode ( ",", $deal_ids ) . ") " );
			$virtual_person_list = array ();
			// 重新组装一个以项目ID为KEY的 统计所有的虚拟人数和虚拟价格
			foreach ( $temp_virtual_person_list as $k => $v ) {
				$virtual_person_list [$v ['deal_id']] ['total_virtual_person'] += $v ['virtual_person'];
				$virtual_person_list [$v ['deal_id']] ['total_virtual_price'] += $v ['price'] * $v ['virtual_person'];
			}
			unset ( $temp_virtual_person_list );
			
			// 将获取到的虚拟人数和虚拟价格拿到项目列表里面进行统计
			foreach ( $deal_list as $k => $v ) {
				$deal_list [$k] ['virtual_person'] = $virtual_person_list [$v ['id']] ['total_virtual_person'];
				$deal_list [$k] ['percent'] = round ( ($v ['support_amount'] + $virtual_person_list [$v ['id']] ['total_virtual_price']) / $v ['limit_price'] * 100 );
				$deal_list [$k] ['support_count'] += $deal_list [$k] ['virtual_person'];
				$deal_list [$k] ['support_amount'] += $virtual_person_list [$v ['id']] ['total_virtual_price'];
				/*
				 *
				 * $deal_list[$k]['description']=get_abs_img_root(get_spec_image($v['description'],640,240,1));
				 * $deal_list[$k]['content']=get_abs_img_root(get_spec_image($v['description'],640,240,1));
				 * $deal_list[$k]['content_1']=get_abs_img_root(get_spec_image($v['description_1'],640,240,1));
				 * $deal_list[$k]['deal_extra_cache']=null;
				 */
				$deal_list [$k] ['image'] = get_abs_img_root ( get_spec_image ( $v ['image'], 640, 240, 1 ) );
				if ($v ['end_time'] > 0 && $v ['end_time'] > $now_time) {
					$deal_list [$k] ['remain_days'] = floor ( ($v ['end_time'] - $now_time) / (24 * 3600) );
				} elseif ($v ['end_time'] > 0 && $v ['end_time'] <= $now_time) {
					$deal_list [$k] ['remain_days'] = 0;
				}
				if ($v ['begin_time'] > $now_time) {
					$deal_list [$k] ['left_days'] = intval ( ($now_time - $v ['create_time']) / 24 / 3600 );
				} else {
					$deal_list [$k] ['left_days'] = 0;
				}
				$deal_list [$k] ['num_days'] = floor ( ($v ['end_time'] - $v ['begin_time']) / (24 * 3600) );
				// $deal_list[$k]['status']值0表示即将开始；1表示已成功；2表示筹资失败；3表示筹资中；4表示长期项目
				if ($v ['begin_time'] > $now_time) {
					$deal_list [$k] ['status'] = '0';
				} elseif ($v ['end_time'] < $now_time && $v ['end_time'] > 0) {
					if ($deal_list [$k] ['percent'] >= 100) {
						$deal_list [$k] ['status'] = '1';
					} else {
						if ($deal_list [$k] ['percent'] >= 0) {
							$deal_list [$k] ['status'] = '2';
						}
					}
				} else {
					if ($v ['end_time'] > 0) {
						if ($deal_list [$k] ['percent'] >= 100) {
							$deal_list [$k] ['status'] = '1';
						} else
							$deal_list [$k] ['status'] = '3';
					} else
						$deal_list [$k] ['status'] = '4';
				}
				$deal_list [$k] ['end_time'] = to_date ( $v ['end_time'], 'Y-m-d' );
				$deal_list [$k] ['begin_time'] = to_date ( $v ['begin_time'], 'Y-m-d' );
				$deal_list [$k] ['create_time'] = to_date ( $v ['create_time'], 'Y-m-d' );
				
				$deal_ids [] = $v ['id'];
				// 查询出对应项目id的user_level
				$deal_list [$k] ['deal_level'] = $GLOBALS ['db']->getOne ( "select level from " . DB_PREFIX . "deal_level where id=" . intval ( $deal_list [$k] ['user_level'] ) );
			}
		}
		/* （所需项目）准备虚拟数据 end */
		$root['isAll'] = $isAll;
		$root ['deal_list'] = $deal_list;
		$root ['deal_count'] = $deal_count;
		$root ['page'] = array (
				"page" => $page,
				"page_total" => ceil ( $deal_count / $page_size ),
				"page_size" => intval ( $page_size ),
				'total' => intval ( $deal_count ) 
		);
		output ( $root );
	}
}
?>
