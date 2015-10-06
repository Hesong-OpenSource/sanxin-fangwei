<?php
// +----------------------------------------------------------------------
// | Fanwe 编辑用户配送地址
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
class edit_consignee
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
			
			if(!$GLOBALS['user_info'])
			{
				//$data['html'] = $GLOBALS['tmpl']->display("inc/user_login_box.html","",true);			
				$data['status'] = 0;
			}
			else
			{
				$id = intval($_REQUEST['id']);
				$consignee_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_consignee where id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id']));
				$root['consignee_info'] = $consignee_info;	
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
				$root['region_lv2'] = $region_lv2;
				
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
					$root['region_lv3'] = $region_lv3;
				}
				$consignee = strim($_REQUEST['consignee']);
				$province = strim($_REQUEST['province']);
				$city = strim($_REQUEST['city']);
				$address = strim($_REQUEST['address']);
				$zip = strim($_REQUEST['zip']);
				$mobile = strim($_REQUEST['mobile']);
				$data = array();
				$data['consignee'] = $consignee;
				$data['province'] = $province;
				$data['city'] = $city;
				$data['address'] = $address;
				$data['zip'] = $zip;
				$data['mobile'] = $mobile;
				$data['user_id'] = intval($GLOBALS['user_info']['id']);
				
			
			
				if(!check_ipop_limit(get_client_ip(),"setting_save_consignee",5))
				$root['info']='提交太频繁';
				if($id>0)
				{
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_consignee",$data,"UPDATE","id=".$id);
					$root['response_code'] = 1;
					$root['info']='修改成功';
				}
				
				else
				{
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_consignee",$data);
					$root['response_code'] = 1;
					$root['info']='保存成功';
				}
		//		$root['consignee_info'] = $consignee_info;			
		//		$data['html'] = $GLOBALS['tmpl']->display("inc/add_consignee.html","",true);			
				$data['status'] = 1;
			}
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>