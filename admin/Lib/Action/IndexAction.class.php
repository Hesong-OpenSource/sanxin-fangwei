<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class IndexAction extends AuthAction{
	//首页
    public function index(){
		$this->display();
    }
    

    //框架头
	public function top()
	{
		//$navs = M("RoleNav")->where("is_effect=1 and is_delete=0")->order("sort asc")->findAll();
		$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";	
		if(!WEIXIN_TYPE){
			unset($navs['weixin']);
		}
		if(!LICAI_TYPE){
			unset($navs['licai']);
		}
		$navs = deal_admin_nav($navs);
		$this->assign("navs",$navs);
		$this->display();
	}
	//框架左侧
	public function left()
	{
		$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";	
		if(!WEIXIN_TYPE){
			unset($navs['weixin']);
		}
		if(!LICAI_TYPE){
			unset($navs['licai']);
		}
		$navs = deal_admin_nav($navs);
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_id = intval($adm_session['adm_id']);
		
		$nav_key = strim($_REQUEST['key']);
		
 		$nav_group = $navs[$nav_key]['groups'];
 		 
 		$this->assign("menus",$nav_group);
		$this->display();
	}
	//默认框架主区域
	public function main()
	{
		$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";		
		if(!WEIXIN_TYPE){
			unset($navs['weixin']);
		}
		if(!LICAI_TYPE){
			unset($navs['licai']);
		}
		$navs = deal_admin_nav($navs);
		$this->assign("navs",$navs);
		$info=array();
		//注册待验证
		$info['user_num']=$GLOBALS['db']->getOne("select count(*)  from ".DB_PREFIX."user where is_effect=0 ");
		$info['project_none_num']=$GLOBALS['db']->getOne("select count(*)  from ".DB_PREFIX."deal where is_effect in(0,2) and is_delete=0 ");
		$info['user_invest_num']=$GLOBALS['db']->getOne("select count(*)  from ".DB_PREFIX."user where (is_investor=1 or is_investor=2) and investor_status!=1 ");
 		//待审核提醒
		$info['user_refund_num']=$GLOBALS['db']->getOne("select count(*)  from ".DB_PREFIX."user_refund where is_pay=0 ");
 		//支付成功
 		$info['deal_order']=$GLOBALS['db']->getRow("select count(*) as num,sum(total_price) as money  from ".DB_PREFIX."deal_order  where  order_status=3 ");
 	 	$info['deal_order']['money']=floatval($info['deal_order']['money']);
 		$this->assign("info",$info);
 		$this->display();
	}	
	//底部
	public function footer()
	{
		$this->display();
	}
	
	//修改管理员密码
	public function change_password()
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$this->assign("adm_data",$adm_session);
		$this->display();
	}
	public function do_change_password()
	{
		$adm_id = intval($_REQUEST['adm_id']);
		if(!check_empty($_REQUEST['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_EMPTY_TIP"));
		}
		if(!check_empty($_REQUEST['adm_new_password']))
		{
			$this->error(L("ADM_NEW_PASSWORD_EMPTY_TIP"));
		}
		if($_REQUEST['adm_confirm_password']!=$_REQUEST['adm_new_password'])
		{
			$this->error(L("ADM_NEW_PASSWORD_NOT_MATCH_TIP"));
		}		
		if(M("Admin")->where("id=".$adm_id)->getField("adm_password")!=md5($_REQUEST['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_ERROR"));
		}
		M("Admin")->where("id=".$adm_id)->setField("adm_password",md5($_REQUEST['adm_new_password']));
		save_log(M("Admin")->where("id=".$adm_id)->getField("adm_name").L("CHANGE_SUCCESS"),1);
		$this->success(L("CHANGE_SUCCESS"));
		
		
	}
	
	public function reset_sending()
	{
		$field = trim($_REQUEST['field']);
		if($field=='DEAL_MSG_LOCK'||$field=='PROMOTE_MSG_LOCK'||$field=='APNS_MSG_LOCK')
		{
			M("Conf")->where("name='".$field."'")->setField("value",'0');
			$this->success(L("RESET_SUCCESS"),1);
		}
		else
		{
			$this->error(L("INVALID_OPERATION"),1);
		}
	}
}
?>