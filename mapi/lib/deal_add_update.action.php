<?php
class deal_add_update{
	public function index()
	{		
		if(!$GLOBALS['user_info'])
		{
			//$data['html'] = $GLOBALS['tmpl']->display("inc/user_login_box.html","",true);			
			$data['status'] = 2;
		}
		else
		{
			$id = intval($_REQUEST['id']);
			$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$id." and is_delete = 0 and is_effect = 1 and user_id = ".intval($GLOBALS['user_info']['id']));
			if(!$deal_info)
			{
				showErr("不能更新该项目的动态",1);
			}
			else
			{
				$GLOBALS['tmpl']->assign("deal_info",$deal_info);
				//$data['html'] = $GLOBALS['tmpl']->fetch("inc/add_update.html");			
				$data['status'] = 1;
			}
		}
		ajax_return($data);
	}
}

?>
