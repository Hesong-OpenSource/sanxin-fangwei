<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/shop_lip.php';
require APP_ROOT_PATH.'app/Lib/page.php';
class indexModule extends BaseModule
{
	public function index()
	{
    	//get_mortgate();
        $GLOBALS['tmpl']->caching = true;
        
        $cache_id = md5(MODULE_NAME . ACTION_NAME);
//		$image_list = load_dynamic_cache("INDEX_IMAGE_LIST");
//		if($image_list===false)
//		{
//			$image_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."index_image order by sort asc");
//			set_dynamic_cache("INDEX_IMAGE_LIST",$image_list);
//		}
		$image_list = load_auto_cache("index_image");
 		$GLOBALS['tmpl']->assign("image_list",$image_list[0]); 		
		$cate_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate where is_delete=0 order by sort asc");
		
		$cate_result= array();
		foreach($cate_list as $k=>$v)
		{
			$cate_result[$v['id']] = $v;
		}
	 
		$GLOBALS['tmpl']->assign("cate_list",$cate_result);
		send_deal_success_1();
		send_deal_fail_1();
		
		//===============首页项目列表START===================
		$page_size = 8;
	 
		$limit =  "0,8";
		
		$GLOBALS['tmpl']->assign("current_page",1);
 		$deal_result = get_deal_list($limit,'type=0');
 		foreach ($deal_result['list'] as $k=>$v){
 			$cate_id=$v['cate_id'];
			$deal_result['list'][$k]['cate_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = $cate_id");
 			$deal_result['list'][$k]['limit_prices']=$v['limit_price']/10000;
 		}
		$GLOBALS['tmpl']->assign("deal_list",$deal_result['list']);
 		$deal_invest_result = get_deal_list($limit,'type=1');
		$GLOBALS['tmpl']->assign("deal_list_invest",$deal_invest_result['list']);
		
		$new_condition='';
		$hot_conditon='';
		if(app_conf("INVEST_STATUS")==1){
			$new_condition='type=0';
			$hot_conditon='type=0';
		}elseif(app_conf("INVEST_STATUS")==2){
			$new_condition='type=1';
			$hot_conditon='type=1';
		}else{
			$new_condition='type=0';
			$hot_conditon='type=1';
		}
		$hot_conditon.=' and is_hot=1 ';
		//最新的项目
		$deal_new_result = get_deal_list('0,8',$new_condition,'id desc');
		$GLOBALS['tmpl']->assign("deal_new_list",$deal_new_result['list']);
		//热门的项目
		$deal_hot_result = get_deal_list('0,4',$hot_conditon,'support_count desc');
		$GLOBALS['tmpl']->assign("deal_hot_list",$deal_hot_result['list']);

		//成功的项目
		$deal_success_result = get_deal_list($limit,'is_success=1');
		$GLOBALS['tmpl']->assign("deal_success_list",$deal_success_result['list']);
		//推荐的项目
		$deal_recommend_result = get_deal_list($limit,'is_recommend=1');
		$GLOBALS['tmpl']->assign("deal_recommend_list",$deal_recommend_result['list']);
		//专题项目
		$deal_special_result = get_deal_list($limit,'is_special=1');
		$GLOBALS['tmpl']->assign("deal_special_list",$deal_special_result['list']);

		//成功项目总数
		$success_sum = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where is_delete=0 and is_effect = 1 and is_success = 1");
		$GLOBALS['tmpl']->assign("success_sum",$success_sum);
		//注册会员总数
		$register_sum = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where  is_effect = 1 ");
		$register_sum=intval($register_sum)+intval(app_conf("VIRSUAL_NUM"));
		$GLOBALS['tmpl']->assign("register_sum",$register_sum);
		//===============首页项目列表END===================
		
        
                
    	/*虚拟的累计项目总个数，支持总人数，项目支持总金额*/
         if(app_conf("INVEST_STATUS")==0)
         {
         	$condition = " and 1=1 ";
         }
         elseif (app_conf("INVEST_STATUS")==1)
         {
         	$condition = " and type=0 ";
         }
         elseif (app_conf("INVEST_STATUS")==2)
         {
         	$condition = " and type=1 ";
         }
 	 	$virtual_effect = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where is_effect = 1 and is_delete=0 $condition");
 	 	$virtual_person =  $GLOBALS['db']->getOne("select sum((support_count+virtual_num)) from ".DB_PREFIX."deal where is_effect = 1 and is_delete=0 $condition");
 	  
	 	$virtual_money_product =  $GLOBALS['db']->getOne("select sum(support_amount+virtual_price) from ".DB_PREFIX."deal where is_effect = 1 and is_delete=0 and type=0 $condition");
		$virtual_money_invest =  $GLOBALS['db']->getOne("select sum(invote_money) from ".DB_PREFIX."deal where is_effect = 1 and is_delete=0 and type=1 $condition");
		$virtual_money=$virtual_money_product+$virtual_money_invest;
	 	
	 
		$GLOBALS['tmpl']->assign("virtual_effect",intval($virtual_effect));//项目总个数
		$GLOBALS['tmpl']->assign("virtual_person",intval($virtual_person));//累计支持人
		$GLOBALS['tmpl']->assign("virtual_money",$virtual_money);//筹资总金额
	    /*虚拟的累计项目总个数，支持总人数，项目支持总金额 结束*/
		
		//首页TAB选项卡
		if(app_conf("INVEST_STATUS")==0)
		{
			$condition = " d.is_delete = 0 and d.is_effect = 1 ";
		}
		elseif (app_conf("INVEST_STATUS")==1)
		{
			$condition = " d.is_delete = 0 and d.is_effect = 1 and d.type=0 ";
		}
		elseif (app_conf("INVEST_STATUS")==2)
		{
			$condition = " d.is_delete = 0 and d.is_effect = 1 and d.type=1 ";
		}
	
		$condition.="  AND d.is_recommend='1'";
		
		//最后发起的项目，如果被设置为推荐，被选项卡显示
 		 
		$deal_cate_array=$GLOBALS['db']->getAll("select d.* from (select * from ".DB_PREFIX."deal order by sort asc)  as d left join ".DB_PREFIX."deal_cate as dc on dc.id=d.cate_id    where $condition group by d.cate_id order by dc.sort asc ");
		$deal_cate=array();
		$now_time=NOW_TIME;
		foreach ($deal_cate_array as $k=>$v){
			if($v['id']>0){
				$v['cate_name']=$cate_result[$v['cate_id']]['name'];
				$v['percent'] = round($v['support_amount']/$v['limit_price']*100,2);
				$v['num_days'] = ceil(($v['end_time'] - $v['begin_time'])/(24*3600));
				
				$v['remain_days'] = ceil(($v['end_time'] - $now_time)/(24*3600));
				if($v['begin_time'] > $now_time){
					$v['left_days'] = ceil(($v['begin_time']-$now_time) / 24 / 3600);
				}
				$v['num_days'] = ceil(($v['end_time'] - $v['begin_time'])/(24*3600));
				$deal_ids[] =  $v['id'];
				
				$deal_cate[$v['id']]=$v;
				
				
			}
			 
		}
	 
		//将获取到的虚拟人数和虚拟价格拿到项目列表里面进行统计
		foreach($deal_cate as $k=>$v)
		{
			if($v['type']==1){
				$deal_cate[$k]['virtual_person']=$deal_cate[$k]['invote_num'];
				$deal_cate[$k]['support_count'] =$deal_cate[$k]['invote_num'];
				$deal_cate[$k]['support_amount'] =$deal_cate[$k]['invote_money'];
				$deal_cate[$k]['percent'] = round(($deal_cate[$k]['support_amount'])/$v['limit_price']*100,2);
			}else{
				$deal_cate[$k]['virtual_person']=$deal_cate[$k]['virtual_num'];
				$deal_cate[$k]['support_count'] =$deal_cate[$k]['virtual_num']+$deal_cate[$k]['support_count'];
				$deal_cate[$k]['support_amount'] =$deal_cate[$k]['virtual_price']+$deal_cate[$k]['support_amount'];
				$deal_cate[$k]['percent'] = round(($deal_cate[$k]['support_amount'])/$v['limit_price']*100,2);
 			}
  		}
  		
		$GLOBALS['tmpl']->assign("deal_cate",$deal_cate);
       
    	/*投资人列表*/
		$invester_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user where is_effect=1    order by login_time desc limit 0,22");
		foreach($invester_list as $k=>$v)
		{
			$invester_list[$k]['image'] =get_user_avatar($v["id"],"middle");//用户头像
			$invester_list[$k]['cate_name'] =unserialize($v["cate_name"]);//所在行业领域
			//$invester_list[$k]['icon']=get_user_lever_icon($v["user_level"]);
		}
		$GLOBALS['tmpl']->assign("invester_list",$invester_list);	
 		$GLOBALS['tmpl']->display("index.html");
	}		
}
?>