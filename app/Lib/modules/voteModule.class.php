<?php
// +----------------------------------------------------------------------
// | 问卷调查
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/page.php';
class voteModule extends BaseModule
{
	public function index()
	{
			$now =NOW_TIME;;
			$vote = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vote where is_effect = 1 and begin_time < ".$now." and (end_time = 0 or end_time > ".$now.") order by sort desc limit 1");
			
			if($vote)
			{
				$vote_ask = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."vote_ask where vote_id = ".intval($vote['id'])." order by sort asc");
				$vote_ask_num= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."vote_ask where vote_id = ".intval($vote['id']));
				if($vote_ask_num==0)
				{
					showErr("问卷内容不能为空");
				}
				foreach($vote_ask as $k=>$v)
				{
					$vote_ask[$k]['val_scope'] = preg_split("/[\r\n]/i",$v['val_scope']);
				}			
				
				$GLOBALS['tmpl']->assign("vote",$vote);
				$GLOBALS['tmpl']->assign("vote_ask",$vote_ask);
				$GLOBALS['tmpl']->assign("page_title","问卷调查");
				
			}
			else
			{
				showErr("当前没有进行中的调查");	
			}
			$GLOBALS['tmpl']->display("vote_index.html");
	}
	
	public function dovote()
	{
		$ok = false;
		$ajax = intval($_REQUEST['ajax']);	
		foreach($_REQUEST['name'] as $vote_ask_id=>$names)
		{			
				foreach($names as $kk=>$name)
				{
					if($name!='')
					{
						$ok = true;
					}
				}
		}
		if(!$ok)
		{
			showErr("请选择要调查的内容",$ajax,'');
		}
		$vote_id = intval($_REQUEST['vote_id']);
		if(check_ipop_limit(get_client_ip(),"vote",3600,$vote_id))
		{
			foreach($_REQUEST['name'] as $vote_ask_id=>$names)
			{
				
				foreach($names as $kk=>$name)
				{
					$name = htmlspecialchars(addslashes(trim($name)));
					
					$result = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vote_result where name = '".$name."' and vote_id = ".$vote_id." and vote_ask_id = ".$vote_ask_id);
					$is_add = true;
					if($result)
					{
						$GLOBALS['db']->query("update ".DB_PREFIX."vote_result set count = count + 1 where name = '".$name."' and vote_id = ".$vote_id." and vote_ask_id = ".$vote_ask_id);
						if(intval($GLOBALS['db']->affected_rows())!=0)
						{
							$is_add = false;
						}
					}
					
					if($is_add)
					{
						if($name!='')
						{
							$result = array();
							$result['name'] = $name;
							$result['vote_id'] = $vote_id;
							$result['vote_ask_id'] = $vote_ask_id;
							$result['count'] = 1;
							$GLOBALS['db']->autoExecute(DB_PREFIX."vote_result",$result);
							
							
							
						}
					}
				}
				
			}
			
			$vote_list = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vote_list where vote_id = ".$vote_id);
		
			$vote_list = array();
			$vote_list['vote_id'] = $vote_id;
			$vote_list['value'] = serialize($_REQUEST['name']);
			$GLOBALS['db']->autoExecute(DB_PREFIX."vote_list",$vote_list);
			showSuccess("调查提交成功",$ajax,url("vote#index"));
		}
		else
		{
			showErr("你已经提交过该问卷",$ajax,'');
		}
	}
}
?>