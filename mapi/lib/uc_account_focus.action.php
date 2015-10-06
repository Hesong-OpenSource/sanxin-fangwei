<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹关注的项目
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_account_focus
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
			
			$page_size =  $GLOBALS['m_config']['page_size'];
			$page = intval($_REQUEST['p']);
			if($page==0)$page = 1;		
			$limit = (($page-1)*$page_size).",".$page_size	;
	
			$s = intval($_REQUEST['s']);
			if($s==3)
			$sort_field = " d.support_amount desc ";
			if($s==1)
			$sort_field = " d.support_count desc ";
			if($s==2)
			$sort_field = " d.support_amount - d.limit_price desc ";
			if($s==0)
			$sort_field = " d.end_time asc ";
			$root['s'] = $s;
			$f = intval($_REQUEST['f']);
			if($f==0)
			$cond = " 1=1 ";
			if($f==1)
			$cond = " d.begin_time < ".NOW_TIME." and (d.end_time = 0 or d.end_time > ".NOW_TIME.") ";
			if($f==2)
			$cond = " d.end_time <> 0 and d.end_time < ".NOW_TIME." and d.is_success = 1 "; //过期成功
			if($f==3)
			$cond = " d.end_time <> 0 and d.end_time < ".NOW_TIME." and d.is_success = 0 "; //过期失败
			$root['f'] = $f;
			$app_sql = " ".DB_PREFIX."deal_focus_log as dfl left join ".DB_PREFIX."deal as d on d.id = dfl.deal_id where dfl.user_id = ".intval($GLOBALS['user_info']['id']).
					   " and d.is_effect = 1 and d.is_delete = 0 and ".$cond." ";
			$deal_list = $GLOBALS['db']->getAll("select d.*,dfl.id as fid from ".$app_sql." order by ".$sort_field." limit ".$limit);
			$deal_count = $GLOBALS['db']->getOne("select count(*) from ".$app_sql);
			$now_time = get_gmtime();
			foreach($deal_list as $k=>$v)
			{
				$deal_list[$k]['remain_days'] = floor(($v['end_time'] - NOW_TIME)/(24*3600));
				$deal_list[$k]['percent'] = round($v['support_amount']/$v['limit_price']*100);
				$deal_list[$k]['content']=$v['description'];
				$deal_list[$k]['image'] =get_abs_img_root(get_spec_image($v['image'],640,240,1));
				if($v['end_time']>0&&$v['end_time']>$now_time){
				$deal_list[$k]['remain_days'] = floor(($v['end_time'] - $now_time)/(24*3600));
				}
				elseif($v['end_time']>0&&$v['end_time']<=$now_time){
				$deal_list[$k]['remain_days'] =0;
				}
				$deal_list[$k]['end_time']=to_date($v['end_time'],'Y-m-d');
				$deal_list[$k]['begin_time']=to_date($v['begin_time'],'Y-m-d');
				$deal_list[$k]['create_time']=to_date($v['create_time'],'Y-m-d');
				if($v['begin_time'] > $now_time){
					$deal_list[$k]['left_days'] = intval(($now_time - $v['create_time']) / 24 / 3600);
				}
				else{
				$deal_list[$k]['left_days'] =0;
				}
				$deal_list[$k]['num_days'] = floor(($v['end_time'] - $v['begin_time'])/(24*3600));
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
			$root['page'] = array("page"=>$page,"page_total"=> ceil($deal_count/$page_size),"page_size"=>intval($page_size),'total'=>intval($deal_count));
			$root['deal_list'] = $deal_list;
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>