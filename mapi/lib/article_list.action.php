<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class article_list
{
	public function index(){
		
		//分页
		$page = intval($GLOBALS['request']['page']);
		if($page==0)
			$page = 1;
		
		$root = array();
		
		$cate_id =intval($GLOBALS['m_config']['article_cate_id']);
		
		//分页
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$sql = "select id,title from ".DB_PREFIX."article where is_effect = 1 and cate_id = ".$cate_id." order by sort";
		$sql.=" limit ".$limit;
		
		$sql_count = "select count(*) from ".DB_PREFIX."article where is_effect = 1 and cate_id = ".$cate_id;
		

		$count = $GLOBALS['db']->getOne($sql_count);
		$list = $GLOBALS['db']->getAll($sql);
	
	
						
		$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")));	
		
		$root['response_code'] = 1;
		$root['list'] = $list;
		
		output($root);		
	}
}
?>
