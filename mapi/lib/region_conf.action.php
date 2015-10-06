<?php
class region_conf{
	public function index()
	{

			//`region_level` tinyint(4) NOT NULL COMMENT '1:国 2:省 3:市(县) 4:区(镇)',
			
			$min_region_level = $GLOBALS['db']->getOne("select min(region_level) from ".DB_PREFIX."region_conf");
			//$max_region_level = $GLOBALS['db']->getOne("select max(region_level) from ".DB_PREFIX."region_conf");
			
			$sql = "select id,pid,name,region_level from ".DB_PREFIX."region_conf order by pid";
			$list = $GLOBALS['db']->getAll($sql);
			
			$root = array();
			$root['response_code'] = 1;
			
			$region_list = $GLOBALS['cache']->get("MOBILE_REGION_LIST");
			if($region_list===false)
			{
				$sql = "select id,pid,name,region_level from ".DB_PREFIX."region_conf where region_level = $min_region_level order by pid";
				$region_list = $GLOBALS['db']->getAll($sql);
				foreach($region_list as $k=>$v)
				{
					$this->getNext($region_list[$k],$v['id']);				
				}
				
				$GLOBALS['cache']->set("MOBILE_REGION_LIST",$region_list,300);
			}
			
			$root['region_list'] = $region_list;

		output($root);
	}
	
	function getNext(&$region, $pid){
		$sql = "select id,pid,name,region_level from ".DB_PREFIX."region_conf where pid = ".$pid;
		$list = $GLOBALS['db']->getAll($sql);
		if ($list === false){
			$region['child'] = array();
		}else{
			$region['child'] = $list;			
			foreach($region['child'] as $k=>$v)
			{
				$this->getNext($region['child'][$k],$v['id']);
			}
		}			
	}
	
}





?>