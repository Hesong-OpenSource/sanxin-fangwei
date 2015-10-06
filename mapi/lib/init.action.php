<?php
class init{
	public function index()
	{		
		$root = array();
		$root['response_code'] = 1;

		$root['kf_phone'] = $GLOBALS['m_config']['kf_phone'];//客服电话
		$root['kf_email'] = $GLOBALS['m_config']['kf_email'];//客服邮箱
		
		//$pattern = "/<img([^>]*)\/>/i";
		//$replacement = "<img width=300 $1 />";
		//$goods['goods_desc'] = preg_replace($pattern, $replacement, get_abs_img_root($goods['goods_desc']));
		//关于我们(填文章ID)
		$root['about_info'] = intval($GLOBALS['m_config']['about_info']);
		$root['version'] = VERSION; //接口版本号int
		$root['page_size'] = PAGE_SIZE;//默认分页大小
		$root['program_title'] = $GLOBALS['m_config']['program_title'];
		$root['site_domain'] = str_replace("/mapi", "", SITE_DOMAIN.APP_ROOT);//站点域名;
		$root['site_domain'] = str_replace("http://", "", $root['site_domain']);//站点域名;
		$root['site_domain'] = str_replace("https://", "", $root['site_domain']);//站点域名;
		//$root['newslist'] = $GLOBALS['m_config']['newslist'];
		
		/*虚拟的累计项目总个数，支持总人数，项目支持总金额*/ 
	 	$virtual_effect = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where is_effect = 1 and is_delete=0");
	 	$virtual_person =  $GLOBALS['db']->getOne("select sum((support_count+virtual_person)) from ".DB_PREFIX."deal_item");
	 	$virtual_money =  $GLOBALS['db']->getOne("select sum((support_count+virtual_person)*price) from ".DB_PREFIX."deal_item");

	 	$root['virtual_effect'] = $virtual_effect;//项目总个数
		$root['virtual_person'] = $virtual_person;//累计支持人
		$root['virtual_money'] =number_format($virtual_money,2);//筹资总金额
	
	    /*虚拟的累计项目总个数，支持总人数，项目支持总金额 结束*/
	    /*首页广告*/
	    $adv_num=intval($GLOBALS['m_config']['adv_num'])?$GLOBALS['m_config']['adv_num']:5;
		$index_list = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_adv where status = 1 order by sort asc limit 0,$adv_num");
		$adv_list = array();
		foreach($index_list as $k=>$v)
		{
			if($v['page'] == 'top'){
				if ($v['img'] != '')
						$v['img'] = get_abs_img_root(get_spec_image($v['img'],640,240,1));	
				$adv_list[] = $v;	
			}
			
		}
		$root['adv_list'] = $adv_list;
		
		
		/*项目显示以及权限控制*/
		//===============首页项目列表START===================
		$page_size =  $GLOBALS['m_config']['page_size'];
		$page = intval($_REQUEST['p']);
//		if($page==0)$page = 1;		
//		$limit = (($page-1)*$page_size).",".$page_size	;
		$limit="";
		$index_pro_num=$GLOBALS['m_config']['index_pro_num'];
		if($index_pro_num>0){
			$limit=" limit 0,$index_pro_num";
		}
		
		$GLOBALS['tmpl']->assign("current_page",$page);
		
		//权限控制
		$condition = " is_delete = 0 and is_effect = 1 ";
		if($GLOBALS['user_info']['user_level']!=0){
			$condition .=" AND (user_level <=".$GLOBALS['user_info']['user_level'].") AND is_recommend='1'";
		}
		else{
			$condition.=" AND (user_level =0 or user_level =1 or user_level ='') AND is_recommend='1'";
		}
		
		$now_time = get_gmtime();
		$deal_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal where ".$condition." order by sort asc  ".$limit);
		$deal_count =  $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where ".$condition);
		foreach ($deal_list as $k=>$v){
				$deal_list[$k]['image'] =get_abs_img_root(get_spec_image($v['image'],640,240,1));
				$deal_list[$k]['end_time']=to_date($v['end_time'],'Y-m-d');
				$deal_list[$k]['begin_time']=to_date($v['begin_time'],'Y-m-d');
				$deal_list[$k]['create_time']=to_date($v['create_time'],'Y-m-d');
				$deal_list[$k]['content']=$v['description'];
				$deal_list[$k]['deal_extra_cache']=null;
				if($v['end_time']>0&&$v['end_time']>$now_time){
				$deal_list[$k]['remain_days'] = floor(($v['end_time'] - $now_time)/(24*3600));
				}
				elseif($v['end_time']>0&&$v['end_time']<=$now_time){
				$deal_list[$k]['remain_days'] =0;
				}
				$deal_list[$k]['percent'] = round($v['support_amount']/$v['limit_price']*100);
				$deal_list[$k]['num_days'] = floor(($v['end_time'] - $v['begin_time'])/(24*3600));
				if($v['begin_time'] > $now_time){
				$deal_list[$k]['left_days'] = intval(($v['begin_time'] - $now_time) / 24 / 3600);
				}
				else{
				$deal_list[$k]['left_days'] =0;
				}
				//$deal_list[$k]['status']值0表示即将开始；1表示已成功；2表示筹资失败；3表示筹资中；4表示长期项目
				if($v['begin_time'] > $now_time){
					$deal_list[$k]['status']= '0';                                 
				}
				elseif($v['end_time'] < $now_time && $v['end_time']>0){
					if($deal_list[$k]['percent'] >=100){
						$deal_list[$k]['status']= '1';  
					}
					else{
						if($deal_list[$k]['percent'] >=0){
							$deal_list[$k]['status']= '2'; 
						}	
					}
				} 
				else{
						if ($v['end_time'] > 0) {
							if($deal_list[$k]['percent'] >=100){
								$deal_list[$k]['status']= '1';  
							}
							else
							$deal_list[$k]['status']= '3'; 
						}
						else
						$deal_list[$k]['status']= '4'; 
				}
		}
		$root['deal_count'] = $deal_count;
		$root['deal_list'] = $deal_list;
		//$root['deal_cate_list'] = getDealCateArray();//分类
		$root['page'] = array("page"=>$page,"page_total"=> ceil($deal_count/$page_size),"page_size"=>intval($page_size),'total'=>intval($deal_count));
		output($root);
	}
}

function getDealCateArray(){
	//$land_list = FanweService::instance()->cache->loadCache("land_list");
		
		$sql = "select id, pid, name, icon from ".DB_PREFIX."deal_cate where pid = 0 and is_effect = 1 and is_delete = 0 order by sort desc ";
		//echo $sql; exit;
		$list = $GLOBALS['db']->getAll($sql);

	return $list;
}
?>