<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹我的项目
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_account_project
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
			$now_time = get_gmtime();
			$deal_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal where user_id = ".$user_id." and is_delete = 0 order by create_time desc limit ".$limit);
			foreach ($deal_list as $k=>$v){
				$deal_list[$k]['image'] =get_abs_img_root(get_spec_image($v['image'],640,240,1));
				$deal_list[$k]['content']=$v['description'];
				//$deal_list[$k]['zhuangtai']值0表示准备中；1表示等待审核；2表示未通过；3表示未开始；4表示已成功;5表示未成功；6表示进行中
				
				if($v['is_effect'] == 0){
					if($v['is_edit'] == 1){
						$deal_list[$k]['zhuangtai']=0;
					}
					else
						$deal_list[$k]['zhuangtai']=1;
				}
				elseif($v['is_effect'] == 2){
					$deal_list[$k]['zhuangtai']=2;	
				}
				else
				{
					if($v['is_success'] == 1){
						if($v['begin_time'] > $now_time ){
							$deal_list[$k]['zhuangtai']=3;	
						}
						if($v['end_time'] < $now_time && $v['end_time'] != 0  ){
							$deal_list[$k]['zhuangtai']=4;	
						}
						if($v['begin_time'] < $now_time && ($v['end_time']>$now_time || $v['end_time'] =0)  ){
							$deal_list[$k]['zhuangtai']=4;	
						}					
					}
					else{
						if($v['begin_time'] > $now_time ){
							$deal_list[$k]['zhuangtai']=3;	
						}
						if($v['end_time'] < $now_time && $v['end_time'] != 0  ){
							$deal_list[$k]['zhuangtai']=5;	
						}
						if($v['begin_time'] < $now_time && ($v['end_time']>$now_time || $v['end_time'] =0)  ){
							$deal_list[$k]['zhuangtai']=6;	
						}
					}
				}				
			}
			$deal_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where user_id = ".intval($GLOBALS['user_info']['id'])." and is_delete = 0");
			
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