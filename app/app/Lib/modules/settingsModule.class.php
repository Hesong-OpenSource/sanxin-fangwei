<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/shop_lip.php';
class settingsModule extends BaseModule
{
	public function index()
	{	
        //links
        $g_links =get_link_by_id();
        $GLOBALS['tmpl']->assign("g_links",$g_links);
		$GLOBALS['tmpl']->assign("page_title","最新动态");
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		$region_pid = 0;
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['name'] == $GLOBALS['user_info']['province'])
			{
				$region_lv2[$k]['selected'] = 1;
				$region_pid = $region_lv2[$k]['id'];
				break;
			}
		}
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		
		
		if($region_pid>0)
		{
			$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$region_pid." order by py asc");  //三级地址
			foreach($region_lv3 as $k=>$v)
			{
				if($v['name'] == $GLOBALS['user_info']['city'])
				{
					$region_lv3[$k]['selected'] = 1;
					break;
				}
			}
			$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		}
		
		$weibo_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_weibo where user_id = ".intval($GLOBALS['user_info']['id']));
		$GLOBALS['tmpl']->assign("weibo_list",$weibo_list);
		
		$GLOBALS['tmpl']->display("settings_index.html");
	}
	
	public function save_index()
	{		
		$ajax = intval($_REQUEST['ajax']);		
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_index",5))
		showErr("提交太频繁",$ajax,"");	
		
		require_once APP_ROOT_PATH."system/libs/user.php";


		$user_data = array();
		$user_data['province'] = strim($_REQUEST['province']);
		$user_data['city'] = strim($_REQUEST['city']);
		$user_data['sex'] = intval($_REQUEST['sex']);
		$user_data['intro'] = strim($_REQUEST['intro']);
		$user_data['intro'] = strim($_REQUEST['intro']);
		
		if(strim($_REQUEST['mobile'])){
			$user_data['mobile'] = strim($_REQUEST['mobile']);
			$num=$GLOBALS['db']->getOne("select count(*) from  ".DB_PREFIX."user where mobile='".$user_data['mobile']."' and id!=".$GLOBALS['user_info']['id']);
			if($num>0){
				showErr("手机已经绑定其他账号,请输入新的手机号",$ajax,"");
			}
			$has_code=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile='".$user_data['mobile']."' and verify_code='".strim($_REQUEST['verify_coder'])."' ");
			if(!$has_code){
				showErr("验证码错误",$ajax,"");
			}
		}
	 	
	 	$user_data['cate_name'] =addslashes(serialize($_POST['cates']));
		$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"UPDATE","id=".intval($GLOBALS['user_info']['id']));
		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."user_weibo where user_id = ".intval($GLOBALS['user_info']['id']));
		foreach($_REQUEST['weibo_url'] as $k=>$v)
		{
			if($v!="")
			{
				$weibo_data = array();
				$weibo_data['user_id'] = intval($GLOBALS['user_info']['id']);
				$weibo_data['weibo_url'] = strim($v);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_weibo",$weibo_data);
			}
		}
		
		showSuccess("资料保存成功",$ajax,url('settings#index'));
		//$res = save_user($user_data);
	}
	
	public function password()
	{
                    //links
                $g_links =get_link_by_id();
                
                $GLOBALS['tmpl']->assign("g_links",$g_links);
		$GLOBALS['tmpl']->assign("page_title","最新动态");
		if(intval($_REQUEST['code'])!=0)
		{
			$uid = intval($_REQUEST['id']);
			$code = intval($_REQUEST['code']); 
			$GLOBALS['user_info'] = $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$uid." and password_verify = '".$code."' and is_effect = 1");
			if($user_info)
			{
				es_session::set("user_info",$user_info);
				$GLOBALS['tmpl']->assign("user_info",$user_info);
				
			}
			else
			{
				app_redirect(url("index"));
			}
		}
		else if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		if(app_conf("USER_VERIFY")==2){
			$GLOBALS['tmpl']->display("settings_mobile_password.html");
		}else{
			$GLOBALS['tmpl']->display("settings_password.html");
		}
		
	}
	public function mobile_password()
	{
                    //links
                $g_links =get_link_by_id();
                
        $GLOBALS['tmpl']->assign("g_links",$g_links);
		$GLOBALS['tmpl']->assign("page_title","最新动态");
		if(intval($_REQUEST['code'])!=0)
		{
			$uid = intval($_REQUEST['id']);
			$code = intval($_REQUEST['code']); 
			$GLOBALS['user_info'] = $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$uid." and password_verify = '".$code."' and is_effect = 1");
			if($user_info)
			{
				es_session::set("user_info",$user_info);
				$GLOBALS['tmpl']->assign("user_info",$user_info);
				
			}
			else
			{
				app_redirect(url("index"));
			}
		}
		else if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));		
		$GLOBALS['tmpl']->display("settings_mobile_password.html");
	}
	
	public function save_password()
	{		
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_password",5))
		showErr("提交太频繁",$ajax,"");	
		//$user_old_pwd=strim($_REQUEST['user_old_pwd']);
		$user_pwd = strim($_REQUEST['user_pwd']);
		$confirm_user_pwd = strim($_REQUEST['confirm_user_pwd']);
		$user_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
		unset($user_info['user_pwd']);
		if(strlen($user_pwd)<0){
			showErr("请输入新密码",$ajax,"");
		}
//		if( md5($user_old_pwd.$user_info['code'])!= $user_info['user_pwd']){
//			showErr("旧密码输入错误",$ajax,"");
//		}
		if(strlen($user_pwd)<4)
		{
			showErr("密码不能低于四位",$ajax,"");
		}
		if($user_pwd!=$confirm_user_pwd)
		{
			showErr("密码确认失败",$ajax,"");
		}
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		//$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
		$user_info['user_pwd'] = $user_pwd;
		save_user($user_info,"UPDATE");
		$GLOBALS['db']->query("update ".DB_PREFIX."user set password_verify = '' where id = ".intval($GLOBALS['user_info']['id']));
		showSuccess("保存成功",$ajax,url("settings#password"));
		//$res = save_user($user_data);
	}
	public function save_mobile_password()
	{		
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_mobile_password",5))
		showErr("提交太频繁",$ajax,"");	
		
		
		$user_pwd = strim($_REQUEST['user_pwd']);
		$confirm_user_pwd = strim($_REQUEST['confirm_user_pwd']);
		$user_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
		$mobile=strim($user_info['mobile']);
		$user_info['verify_coder']=strim($_REQUEST['verify_coder']);
		if($mobile){
			
			$has_code=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile='".$mobile."' and verify_code='".strim($_REQUEST['verify_coder'])."' ");
			if(!$has_code){
				showErr("验证码错误",$ajax,"");
			}
		}else{
			showErr("请绑定手机号",$ajax,"");
		}
		
		if(strlen($user_pwd)<4)
		{
			showErr("密码不能低于四位",$ajax,"");
		}
		if($user_pwd!=$confirm_user_pwd)
		{
			showErr("密码确认失败",$ajax,"");
		}
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		//$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
		$user_info['user_pwd'] = $user_pwd;
		save_user($user_info,"UPDATE");
		$GLOBALS['db']->query("update ".DB_PREFIX."user set password_verify = '' where id = ".intval($GLOBALS['user_info']['id']));
		showSuccess("保存成功",$ajax,url("settings#password"));
		//$res = save_user($user_data);
	}

	public function bind()
	{
        //links
        $g_links =get_link_by_id();   
        $GLOBALS['tmpl']->assign("g_links",$g_links);
		$GLOBALS['tmpl']->assign("page_title","最新动态");
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		
		
		$api_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."api_login where class_name <> 'Weixin' ");
		foreach($api_list as $k=>$v)
		{
			if($GLOBALS['user_info'][strtolower($v['class_name'])."_id"]!='')
			{
				$api_list[$k]['is_bind'] = true;
				$api_list[$k]['weibo_url'] = $GLOBALS['user_info'][strtolower($v['class_name'])."_url"];
			}
			else
			{
				$api_list[$k]['is_bind'] = false;
				require_once APP_ROOT_PATH."system/api_login/".$v['class_name']."_api.php";
				$class_name = $v['class_name']."_api";
				$o = new $class_name($v);
				$api_list[$k]['url'] = $o->get_bind_api_url();
			}
			
		}
		
		
		$GLOBALS['tmpl']->assign("api_list",$api_list);
		$GLOBALS['tmpl']->display("settings_bind.html");
	}
	
	public function unbind()
	{
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		
		$class_name = strim($_REQUEST['c']);
		$api_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."api_login class_name='".$class_name."'");
		if($api_info['is_weibo'] ==1)
		{
			$class_name_update = strtolower($class_name);
			update_user_weibo($GLOBALS['user_info']['id'],$GLOBALS['user_info'][$class_name.'_url'],2); //删除微博		
			$GLOBALS['db']->query("update ".DB_PREFIX."user set ".$class_name_update."_id = '',".$class_name_update."_url = '' where id = ".intval($GLOBALS['user_info']['id']),"SILENT");
		}
		else
		{
			require_once APP_ROOT_PATH."system/api_login/".$class_name."_api.php";
			$class_name = $class_name."_api";
			$o = new $class_name($api_info);
			$o->unset_api();
		}

		app_redirect(url("settings#bind"));
	}
	
	public function consignee()
	{
        //links
        $g_links =get_link_by_id();    
        $GLOBALS['tmpl']->assign("g_links",$g_links);
		$GLOBALS['tmpl']->assign("page_title","最新动态");
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));

		$consignee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_consignee where user_id = ".intval($GLOBALS['user_info']['id']));
		$GLOBALS['tmpl']->assign("user_id",intval($GLOBALS['user_info']['id']));
		$GLOBALS['tmpl']->assign("consignee_list",$consignee_list);
		$GLOBALS['tmpl']->display("settings_consignee.html");
	}
	public function set_default_consignee(){
		$data=array('status'=>0,'info'=>'');
		$id=intval($_POST['id']);
		$user_id=intval($_POST['user_id']);
		if(!$id){
			$data['info']="信息错误";
		}else{
			if($GLOBALS['db']->getOne("select count(*) from  ".DB_PREFIX."user_consignee where id=$id")>0){
				$consignee_all['is_default']=0;
				$consignee['is_default']=1;
				$GLOBALS['db']->autoExecute(DB_PREFIX.'user_consignee',$consignee_all,"UPDATE","user_id=".$user_id);//全部设置为0
				$GLOBALS['db']->autoExecute(DB_PREFIX.'user_consignee',$consignee,"UPDATE","id=".$id);//设置对应的为默认
				if($GLOBALS['db']->affected_rows()){
					$data['status']=1;
				}else{
					$data['status']=2;//表示更新数据失败，让用户重新提交
					$data['info']="设置失败,请重新设置";
				}
			}else{
				$data['info']="没有该地址";
			}
		}
		ajax_return($data);
	}
	
	public function add_consignee()
	{

		if(!$GLOBALS['user_info'])
		{
			$data['html'] = $GLOBALS['tmpl']->display("inc/user_login_box.html","",true);			
			$data['status'] = 0;
		}
		else
		{
			$GLOBALS['tmpl']->caching = true;
			$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
			if (!$GLOBALS['tmpl']->is_cached('inc/add_consignee.html', $cache_id))
			{		
				$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
				$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
			}			
			$data['html'] = $GLOBALS['tmpl']->display("inc/add_consignee.html",$cache_id,true);			
			$data['status'] = 1;
		}
		ajax_return($data);
	}
	
	public function save_consignee()
	{		
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_consignee where user_id = ".intval($GLOBALS['user_info']['id']))>10)
		{
			showErr("每个会员只能预设10个配送地址",$ajax,"");
		}
		
		$id = intval($_REQUEST['id']);
		$consignee = strim($_REQUEST['consignee']);
		$province = strim($_REQUEST['province']);
		$city = strim($_REQUEST['city']);
		$address = strim($_REQUEST['address']);
		$zip = strim($_REQUEST['zip']);
		$mobile = strim($_REQUEST['mobile']);
		if($consignee=="")
		{
			showErr("请填写收货人姓名",$ajax,"");	
		}
		if($province=="")
		{
			showErr("请选择省份",$ajax,"");	
		}
		if($city=="")
		{
			showErr("请选择城市",$ajax,"");	
		}
		if($address=="")
		{
			showErr("请填写详细地址",$ajax,"");	
		}
		if(!check_postcode($zip))
		{
			showErr("请填写正确的邮编",$ajax,"");	
		}
		if($mobile=="")
		{
			showErr("请填写收货人手机号码",$ajax,"");	
		}
		if(!check_mobile($mobile))
		{
			showErr("请填写正确的手机号码",$ajax,"");	
		}
		
		$data = array();
		$data['consignee'] = $consignee;
		$data['province'] = $province;
		$data['city'] = $city;
		$data['address'] = $address;
		$data['zip'] = $zip;
		$data['mobile'] = $mobile;
		$data['user_id'] = intval($GLOBALS['user_info']['id']);
		
		
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_consignee",5))
		showErr("提交太频繁",$ajax,"");	
		
	
		if($id>0)
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_consignee",$data,"UPDATE","id=".$id);
		else
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_consignee",$data);
		
		showSuccess("保存成功",$ajax,get_gopreview());
		//$res = save_user($user_data);
	}
	
	public function edit_consignee()
	{

		if(!$GLOBALS['user_info'])
		{
			$data['html'] = $GLOBALS['tmpl']->display("inc/user_login_box.html","",true);			
			$data['status'] = 0;
		}
		else
		{
			$id = intval($_REQUEST['id']);
			$consignee_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_consignee where id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id']));
			
			$region_pid = 0;
			$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
			foreach($region_lv2 as $k=>$v)
			{
				if($v['name'] == $consignee_info['province'])
				{
					$region_lv2[$k]['selected'] = 1;
					$region_pid = $region_lv2[$k]['id'];
					break;
				}
			}
			$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
			
			
			if($region_pid>0)
			{
				$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$region_pid." order by py asc");  //三级地址
				foreach($region_lv3 as $k=>$v)
				{
					if($v['name'] == $consignee_info['city'])
					{
						$region_lv3[$k]['selected'] = 1;
						break;
					}
				}
				$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
			}
						
			$GLOBALS['tmpl']->assign("consignee_info",$consignee_info);
			$data['html'] = $GLOBALS['tmpl']->display("inc/add_consignee.html","",true);			
			$data['status'] = 1;
		}
		ajax_return($data);
	}
	
	public function del_consignee()
	{
		if(!$GLOBALS['user_info'])
		{
			$data['html'] = $GLOBALS['tmpl']->display("inc/user_login_box.html","",true);			
			$data['status'] = 1;
			ajax_return($data);
		}
		else
		{
			$id = intval($_REQUEST['id']);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_consignee where id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id']));
			
			showSuccess("",1,get_gopreview());
		}
	}
	
	
	public function bank()
	{
                    //links
        $g_links =get_link_by_id();
        $GLOBALS['tmpl']->assign("g_links",$g_links);
		$GLOBALS['tmpl']->assign("page_title","最新动态");
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
	/*	if($GLOBALS['user_info']['ex_real_name']!=""||$GLOBALS['user_info']['ex_account_info']!=""||$GLOBALS['user_info']['ex_contact']!="")
		{
			app_redirect_preview();
		}
	*/	
		$GLOBALS['tmpl']->display("settings_bank.html");
	}
	
	
	public function save_bank()
	{	
		$ajax = intval($_REQUEST['ajax']);		
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		if($GLOBALS['user_info']['ex_qq']!=""&&$GLOBALS['user_info']['ex_account_bank']!=""&&$GLOBALS['user_info']['ex_real_name']!=""&&$GLOBALS['user_info']['ex_account_info']!=""&&$GLOBALS['user_info']['ex_contact']!="")
		{
			showErr("银行帐户信息已经设置过",$ajax,"");	
		}
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_bank",5))
		showErr("提交太频繁",$ajax,"");	
		
		$ex_real_name = strim($_REQUEST['ex_real_name']);
		$ex_account_info = strim($_REQUEST['ex_account_info']);
		$ex_account_bank = strim($_REQUEST['ex_account_bank']);
		$ex_contact = strim($_REQUEST['ex_contact']);
		$ex_qq = strim($_REQUEST['ex_qq']);
		
		if($ex_real_name=="")
		{
			showErr("请填写姓名",$ajax,"");	
		}
		if($ex_account_bank=="")
		{
			showErr("请填写开户银行",$ajax,"");	
		}
		if($ex_account_info=="")
		{
			showErr("请填写银行帐号",$ajax,"");	
		}
		if($ex_contact=="")
		{
			showErr("请填写联系电话",$ajax,"");	
		}
		if(!check_mobile($ex_contact))
		{
			showErr("请填写正确的手机号码",$ajax,"");	
		}
		if($ex_qq=="")
		{
			showErr("请填写联系qq",$ajax,"");	
		}	
		$GLOBALS['db']->query("update ".DB_PREFIX."user set ex_qq = '".$ex_qq."',ex_account_bank = '".$ex_account_bank."',ex_real_name = '".$ex_real_name."',ex_account_info = '".$ex_account_info."',ex_contact = '".$ex_contact."',is_bank = '".'1'."' where id = ".intval($GLOBALS['user_info']['id']));
		
		
		showSuccess("资料保存成功",$ajax,url("settings#bank"));
		//$res = save_user($user_data);
	}
	//设置手机号
	public function mobile_change(){
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
 		if(!$GLOBALS['user_info']['mobile']){
			showErr("您未设置手机,请先设置手机");
 		}
 		$GLOBALS['tmpl']->assign("user",$GLOBALS['user_info']);
 		$step=intval($_REQUEST['step']);
 		if($step==0){
 			es_session::set("mobile_status",0);
 		}elseif($step==1){
  			if(es_session::get("mobile_status")!=1){
 				showErr("请进行第一步先验证手机",0,url("settings#mobile_change"));
  			}
 		}elseif($step==2){
 			if(es_session::get("mobile_status")==1){
 				showErr("请进行第二步先验证手机",0,url("settings#mobile_change",array("step"=>1)));
 			}elseif(es_session::get("mobile_status")==0){
 				showErr("请进行第一步先验证手机",0,url("settings#mobile_change"));
  			}
 			es_session::set("mobile_status",0);
 		}
 		
 		$GLOBALS['tmpl']->assign("step",$step);
		$GLOBALS['tmpl']->display("settings_mobile_change.html");
	}
	public function mobile_change_step(){
		$return=array("status"=>1,'info'=>'','jump'=>'');
		$mobile=strim($_REQUEST["mobile"]);
		$verify=strim($_REQUEST["verify"]);
		$step=intval($_REQUEST['step']);
		$num=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."'  and verify_code='".$verify."'  ORDER BY id DESC");
		if($num<=0){
 			$return['status']=0;
			$return['info']='验证码错误';
		}else{
			if($step==0){
				es_session::set("mobile_status",1);
				$return['jump']=url("settings#mobile_change",array("step"=>1));
			}elseif($step=1){
				$re=$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile='".$mobile."' where id=".$GLOBALS['user_info']['id']);
 				es_session::set("mobile_status",2);
				$return['jump']=url("settings#mobile_change",array("step"=>2));
			}
			
		}
		ajax_return($return);
	}
	//设置邮箱
	public function mail_change(){
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		if(!$GLOBALS['user_info']['email']){
			showErr("您未设置邮箱,请先设置手机");
 		}
 		
 		$GLOBALS['tmpl']->assign("user",$GLOBALS['user_info']);
 		$step=intval($_REQUEST['step']);
 		if($step==1){
 			if(app_conf("MAIL_ON")==1)
            {
            		$user_id = intval($GLOBALS['user_info']['id']);      
                    //发邮件
                    send_user_verify_mail_change($user_id);
                    $user_email = $GLOBALS['db']->getOne("select email from ".DB_PREFIX."user where id =".$user_id);
                    //开始关于跳转地址的解析
                    $domain = explode("@",$user_email);
                    $domain = $domain[1];
                    $gocheck_url = '';
                    switch($domain)
                    {
                            case '163.com':
                                    $gocheck_url = 'http://mail.163.com';
                                    break;
                            case '126.com':
                                    $gocheck_url = 'http://www.126.com';
                                    break;
                            case 'sina.com':
                                    $gocheck_url = 'http://mail.sina.com';
                                    break;
                            case 'sina.com.cn':
                                    $gocheck_url = 'http://mail.sina.com.cn';
                                    break;
                            case 'sina.cn':
                                    $gocheck_url = 'http://mail.sina.cn';
                                    break;
                            case 'qq.com':
                                    $gocheck_url = 'http://mail.qq.com';
                                    break;
                            case 'foxmail.com':
                                    $gocheck_url = 'http://mail.foxmail.com';
                                    break;
                            case 'gmail.com':
                                    $gocheck_url = 'http://www.gmail.com';
                                    break;
                            case 'yahoo.com':
                                    $gocheck_url = 'http://mail.yahoo.com';
                                    break;
                            case 'yahoo.com.cn':
                                    $gocheck_url = 'http://mail.cn.yahoo.com';
                                    break;
                            case 'hotmail.com':
                                    $gocheck_url = 'http://www.hotmail.com';
                                    break;
                            default:
                                    $gocheck_url = "";
                                    break;					
                    }

                    $GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['REGISTER_MAIL_SEND_SUCCESS']);
                    
                    $GLOBALS['tmpl']->assign("user_email",$user_email);
                  
                    $GLOBALS['tmpl']->assign("gocheck_url",$gocheck_url);
            	
            }else{
            	showErr("邮箱功能未开启，请联系管理员");
            }
 		}
 		$GLOBALS['tmpl']->assign('step',$step);
		$GLOBALS['tmpl']->display("settings_mail_change.html");
	}
	public function mail_change_1(){
		$step=intval($_REQUEST['step']);
		$return=array("status"=>1,'info'=>'','jump'=>'');
		$email=strim($_REQUEST['email']);
		if(empty($email)){
			$return['status']=0;
		 	$return['info']='邮箱不能为空';
		}
		
		if($step==1){
			
			if($email!=$GLOBALS['user_info']['email']){
	 			$return['status']=0;
				$return['info']='邮箱错误';
			}else{
				 if(!check_ipop_limit(get_client_ip(),"mail_change_".$step,60,0))
				{
					$return['status'] = 0;
					$return['info'] = "发送速度太快了,请1分钟后再提交";
					ajax_return($return);
				}
					$return['jump']=url("settings#mail_change",array("step"=>1));
	 		}
		}elseif($step==2){
			if($email==$GLOBALS['user_info']['email']){
				$return['status']=0;
				$return['info']='新邮箱与旧邮箱一致';
			}
			$count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email='".$email."' ");
			if($count>0){
				$return['status']=0;
				$return['info']='邮箱已存在，请重新输入';
			}else{
				 send_user_verify_mail_setting($GLOBALS['user_info']['id'],$email);
				 $return['info']='验证邮件已发送，请确认';
				 if(!check_ipop_limit(get_client_ip(),"mail_change_".$step,60,0))
				{
					$return['status'] = 0;
					$return['info'] = "发送速度太快了,请1分钟后再提交";
					ajax_return($return);
				}
			}
		}
	 		
 		ajax_return($return);
	}
	public function mail_change_verify(){
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		
		$id = intval($_REQUEST['id']);
		
		$verify_code=strim($_REQUEST['code']);
		if($GLOBALS['user_info']['id']!=$id){
			showErr("账号信息不一致，请重新发送验证申请",0,url("settings#mail_change"));
		}
		$user_info  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id);
		if(!$user_info)
		{
			showErr("没有该会员");
		}
		$send_time=$user_info['verify_time'];
		if((get_gmtime()-$send_time)>48*3600){
			showErr("该申请已过期，请重新发送验证申请",0,url("settings#mail_change"));
		}
		
		$step=intval($_REQUEST['step']);
		if($step==1){
			if($verify_code!=$user_info['verify']){
			showErr("验证错误，请重新发送验证申请",0,url("settings#mail_change"));
		}
			$step=2;
		}elseif($step==2){
			if($verify_code!=$user_info['verify_setting']){
				showErr("验证错误，请重新发送验证申请",0,url("settings#mail_change"));
			}
			$email=strim(base64_decode($_REQUEST['e']));
 			if(!check_email($email)){
				showErr("邮箱格式错误，请重新发送验证申请",0,url("settings#mail_change"));
			}else{
				$count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email='".$email."' ");
				if($count>0){
					showErr("邮箱已存在，请重新发送验证申请",0,url("settings#mail_change"));
				}
				
			}
			$GLOBALS['db']->query("update  ".DB_PREFIX."user set email='".$email."' where id=".$id);
			if($GLOBALS['db']->affected_rows()){
				showSuccess("邮箱修改成功",0,url("settings"));	
			}else{
				showErr("邮箱修改失败,请重新点击邮箱中的链接");
			}
			
		}
		$GLOBALS['tmpl']->assign("step",$step);
		$GLOBALS['tmpl']->display("settings_mail_change.html");
	}
	public function invest_info()
	{	
		 //
		 settings_invest_info('web',$GLOBALS['user_info']);
 	}
 	
 	public function security(){
 		$g_links =get_link_by_id();
        $GLOBALS['tmpl']->assign("g_links",$g_links);
 		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		$method=strim($_REQUEST['method']);
		$method_array=array("setting-username-box","setting-pwd-box","setting-email-box","setting-mobile-box","setting-pass-box","setting-id-box",);
		if(!in_array($method,$method_array)){
			$method='';
		}
		$GLOBALS['tmpl']->assign("method",$method);
		$GLOBALS['tmpl']->display("settings_security.html");
	}
	public function save_pass(){
 		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		//if(!check_ipop_limit(get_client_ip(),"setting_save_password",5))
		//showErr("提交太频繁",$ajax,"");	
		$user_old_pwd=strim($_REQUEST['user_old_pwd']);
		$user_pwd = strim($_REQUEST['user_pwd']);
		$confirm_user_pwd = strim($_REQUEST['confirm_user_pwd']);
		$change_pwd=intval($_REQUEST['change_pwd']);
		$user_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
 		if(strlen($user_pwd)<=0){
			showErr("请输入新密码",$ajax,"");
		}
		if( md5($user_old_pwd)!= $user_info['user_pwd']&&$change_pwd==1){
			showErr("旧密码输入错误",$ajax,"");
		}
		
		if(strlen($user_pwd)<4)
		{
			showErr("新密码不能低于四位",$ajax,"");
		}
		if($user_pwd!=$confirm_user_pwd)
		{
			showErr("新密码确认失败",$ajax,"");
		}
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		
 		$user_info['user_pwd'] = $user_pwd;
  		$user_info['money'] = 100;
		$res=save_user($user_info,"UPDATE");
 		$GLOBALS['db']->query("update ".DB_PREFIX."user set password_verify = '' where id = ".intval($GLOBALS['user_info']['id']));
		showSuccess("保存成功",$ajax,url("settings#security"));
		
	}
	public function save_username(){
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("请先登录",$ajax,url("user#login"));
		}
		$user_name=strim($_REQUEST['user_name']);
		if(empty($user_name)){
			showErr("请填写昵称",$ajax);
		}
		$re=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name='$user_name' and id!=".$GLOBALS['user_info']['id']);
		if($re>0){
			showErr("昵称已经存在，请重新填写",$ajax);
		}else{
			$user_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
			$user_info['user_name'] = $user_name;
			$res=save_user($user_info,"UPDATE");
			showSuccess("昵称设置成功",$ajax,url("settings#security"));
		}
	}
	public function mobile_binding(){
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		$return=array("status"=>1,'info'=>'','jump'=>'');
		$mobile=strim($_REQUEST["mobile"]);
		$verify=strim($_REQUEST["verify_coder"]);
		$bind_mobile=intval($_REQUEST["bind_mobile"]);
 		if(strlen($verify)< 0 || strlen($verify)== 0){
			showErr("请输入手机验证号码",$ajax,"");
		}
		if(!$bind_mobile){
 			if($mobile==$GLOBALS['user_info']['mobile']){
 				showErr("新号码和旧号码一样，请重新输入",$ajax,"");
 			}
		} 
		check_registor_mobile($mobile);
		if(!$bind_mobile){
			$condition="mobile = '".$GLOBALS['user_info']['mobile']."'  and verify_code='".$verify."' ";
		}else{
			$condition="mobile = '".$mobile."'  and verify_code='".$verify."' ";
		}
		$num=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where $condition  ORDER BY id DESC");
		if($num<=0){
			showErr("验证码错误",$ajax,"");
		}else{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile='".$mobile."' where id=".$GLOBALS['user_info']['id']);
				showSuccess("保存成功",$ajax,url("settings#security"));	
		}
		
		
	}
	public function email_binding(){
		$ajax = intval($_REQUEST['ajax']);
		$email=strim($_REQUEST["email"]);
		$verify=strim($_REQUEST["verify_coder"]);
		$step=intval($_REQUEST["step"]);
		if(strlen($verify)< 0 || strlen($verify)== 0){
			showErr("请输入邮件验证号码",$ajax,"");
		}
		if($step==2){
 			if($email==$GLOBALS['user_info']['email']){
 				showErr("新邮箱和旧邮箱一样，请重新输入",$ajax,"");
 			}
		} 
		check_registor_email($email);
		if($step==2){
			$condition="email = '".$GLOBALS['user_info']['email']."'  and verify_code='".$verify."' ";
		}else{
			$condition="email = '".$email."'  and verify_code='".$verify."' ";
		}
		$num=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where $condition  ORDER BY id DESC");
		if($num<=0){
			showErr("验证码错误",$ajax,"");
		}else{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set email='".$email."' where id=".$GLOBALS['user_info']['id']);
				showSuccess("保存成功",$ajax,url("settings#security"));	
		}
		
	}
	public function paypassword_binding(){
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		$return=array("status"=>1,'info'=>'','jump'=>'');
 		$paypassword=strim($_REQUEST["paypassword"]);
		$confirm_paypassword=strim($_REQUEST["confirm_pypassword"]);
		$verify=strim($_REQUEST['verify']);
		if($paypassword==''||$confirm_paypassword==''){
			showErr("请输入密码",$ajax,"");
		}
		if($paypassword!=$confirm_paypassword){
			showErr("密码不一致",$ajax,"");
		}
 		$condition="mobile = '".$GLOBALS['user_info']['mobile']."'  and verify_code='".$verify."' ";
		 
		$num=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where $condition  ORDER BY id DESC");
		if($num<=0){
			showErr("验证码错误",$ajax,"");
		}else{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set paypassword='".md5($paypassword)."' where id=".$GLOBALS['user_info']['id']);
				showSuccess("保存成功",$ajax,url("settings#security"));	
		}
		
		
	}
	public function binding_investor(){
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		$return=array("status"=>1,'info'=>'','jump'=>'');
		$is_investor=intval($_REQUEST['is_investor']);
		$identify_name=strim($_REQUEST['identify_name']);
		$identify_number=strim($_REQUEST['identify_number']);
		$identify_positive_image=strim($_REQUEST['identify_positive_image']);
		$identify_nagative_image=strim($_REQUEST['identify_nagative_image']);
		//=============================
		
		
		$verify=strim($_REQUEST['verify']);
		if($identify_name==''){
			showErr("身份证姓名不能为空!",$ajax,"");
		}
		if($identify_number==''){
			showErr("身份证号码不能为空!",$ajax,"");
		}
		if(!isCreditNo($identify_number)){
			showErr("请输入正确的身份证号码!",$ajax,"");
		}
		if($identify_positive_image==''&&app_conf('IDENTIFY_POSITIVE')){
			showErr("请上传身份证正面照片！",$ajax,"");
		}
		if($identify_nagative_image==''&&app_conf('IDENTIFY_NAGATIVE')){
			showErr("请上传身份证背面照片！",$ajax,"");
		}
		//判断该实名是否存在
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where (identify_name = '$identify_name' or identify_number = '$identify_number') and id<>".$GLOBALS['user_info']['id']) > 0 ){
			showErr("该实名已被其他用户认证，非本人请联系客服",$ajax,"");
		}
		if($is_investor==2){
			$identify_business_name=strim($_REQUEST['identify_business_name']);
			$identify_business_licence=strim($_REQUEST['identify_business_licence']);
			$identify_business_code=strim($_REQUEST['identify_business_code']);
			$identify_business_tax=strim($_REQUEST['identify_business_tax']);
			if($identify_business_name==''){
				showErr("企业名称不能为空!",$ajax,"");
			}
			if($identify_business_licence==''&&app_conf('BUSINESS_LICENCE')){
				showErr("营业执照不能为空!",$ajax,"");
			}
			if($identify_business_code==''&&app_conf('BUSINESS_CODE')){
				showErr("组织机构代码证!",$ajax,"");
			}
			if($identify_business_tax==''&&app_conf('BUSINESS_TAX')){
				showErr("税务登记证!",$ajax,"");
			}
		
		}
		
		$condition="mobile = '".$GLOBALS['user_info']['mobile']."'  and verify_code='".$verify."' ";
 		$num=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where $condition  ORDER BY id DESC");
		if($num<=0){
			showErr("验证码错误",$ajax,"");
		}else{
				$user_info=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."user where id=".$GLOBALS['user_info']['id']);
 				unset($user_info['user_pwd']);
 				if($user_info){
 					require_once APP_ROOT_PATH."system/libs/user.php";
 					$user_info['is_investor']=$is_investor;
 					if($is_investor==1){
 						$user_info['identify_business_name']='';
 						$user_info['identify_business_licence']='';
 						$user_info['identify_business_code']='';
 						$user_info['identify_business_tax']='';
 					}else{
 						$user_info['identify_business_name']=$identify_business_name;
 						$user_info['identify_business_licence']=$identify_business_licence;
 						$user_info['identify_business_code']=$identify_business_code;
 						$user_info['identify_business_tax']=$identify_business_tax;
 					}
 					$user_info['identify_name']=$identify_name;
 					$user_info['identify_number']=$identify_number;
 					$user_info['identify_positive_image']=$identify_positive_image;
 					$user_info['identify_nagative_image']=$identify_nagative_image;
 					$user_info['investor_status']=0;
 					$user_info['investor_send_info']='';
 					
 					$res=save_user($user_info,"UPDATE");
 					showSuccess("保存成功",$ajax,url("settings#security"));	
 				}else{
 					showErr("会员信息不存在",$ajax);
 				}
 				
		}
	}
}
?>