<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class VoteAction extends CommonAction{
	public function index()
	{
		$this->assign("default_map",$condition);
		parent::index();
	}

	public function add()
	{
		$this->assign("new_sort", M("Vote")->max("sort")+1);
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("VOTE_NAME_EMPTY_TIP"));
		}	
		
		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("VOTE_NAME_EMPTY_TIP"));
		}	
		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);		
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
	
				if ($list!==false) {
					M("VoteAsk")->where(array ('vote_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("VoteResult")->where(array ('vote_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	
	
	
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M("Vote")->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("Vote")->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	public function vote_ask()
	{
		$id = intval($_REQUEST['id']);
		$vote = M("Vote")->getById($id);
		$vote_ask = M("VoteAsk")->where("vote_id=".$id)->order("sort asc")->findAll();
		$Count = M('vote_list')->where('vote_id = '.$id)->count();
		$this->assign("vote",$vote);
		$this->assign("vote_ask",$vote_ask);
		$this->assign("Count",$Count);
		$this->display();
	}
	public function add_ask_row()
	{
		$idx = intval($_REQUEST['idx']);
		$this->assign("i",$idx);
		$this->display();
	}
	
	public function do_vote_ask()
	{
		
		M("VoteAsk")->where("vote_id=".intval($_REQUEST['vote_id']))->delete();
		$vote = M("Vote")->getById(intval($_REQUEST['vote_id']));
		foreach($_REQUEST['name'] as $k=>$v)
		{
			$vote_ask = array();
			$vote_ask['name'] = $v;
			$vote_ask['sort'] = intval($_REQUEST['sort'][$k]);
			$vote_ask['val_scope'] = trim($_REQUEST['val_scope'][$k]);
			$vote_ask['vote_id'] = intval($_REQUEST['vote_id']);
			$type = 0;
			foreach($_REQUEST['type'][$k] as $kk=>$vv)
			{
				$type += intval($vv);
			}
			$vote_ask['type'] = $type;
			if($type==1 || $type==2){
				if(!$vote_ask['val_scope']){
					$this->error("单选或者多选内容不能空！");
				}
			}
			$is_fill = 0;
			foreach($_REQUEST['is_fill'][$k] as $kk=>$vv)
			{
				$is_fill += intval($vv);
			}
			$vote_ask['is_fill'] = $is_fill;
			M("VoteAsk")->add($vote_ask);			
		}
		save_log($vote['name'],1);
		$this->success(l("EDIT_VOTE_ASK_SUCCESS"));
	}
	
	public function vote_result()
	{
		$id = intval($_REQUEST['id']);
		$vote = M("Vote")->getById($id);
		$vote_ask = M("VoteAsk")->where("vote_id=".$id)->order("sort asc")->findAll();
		foreach($vote_ask as $k=>$v)
		{
			$vote_ask[$k]['result'] = M("VoteResult")->where("vote_id = ".$vote['id']." and vote_ask_id = ".$v['id'])->findAll();
		}
		$this->assign("vote",$vote);
		$this->assign("vote_ask",$vote_ask);
		$this->display();
	}
	//导出电子表
	public function export_csv($page = 1)
	{
		$id = intval($_REQUEST['id']);
		$pagesize = 10;
		set_time_limit(0);
		$limit = (($page - 1)*intval($pagesize)).",".(intval($pagesize));
		
		$where = " 1=1 ";
		$where.= " and v.id = ".$id;
		//定义条件
		$sql ="select v.name as name,vl.value,vl.id from ".DB_PREFIX."vote as v LEFT JOIN ".DB_PREFIX."vote_list as vl on vl.vote_id = $id where ".$where." limit ".$limit;
		$vote_list=$GLOBALS['db']->getAll($sql);
		$list =array();
		foreach($vote_list as $k=>$v){
			if($v['name']){
				$vote_list[$k]['value'] =unserialize($v['value']);
 				foreach($vote_list[$k]['value'] as $kk => $vv)
				{
	 				if(is_array($vv)){
						$list[$k][]=implode("|",$vv);
					}else{
						$list[$k][]=$vv;
					}	
				}
				 
				if(!empty($list[$k])){
					array_unshift($list[$k],$vote_list[$k]['id'],$vote_list[$k]['name']);
				}
			}
			
		}
		$arr=$GLOBALS['db']->getAll("select name from ".DB_PREFIX."vote_ask where vote_id = $id ORDER BY id");
	   	$array = array();
	 	foreach($arr as $k=>$v){
			 $array[]=$v['name'];
			
		}
 	   	$array_name=implode(",",$array);
  		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
	    	if($page == 1)
	    	{
	    		//问题列表
 		    	$content = iconv("utf-8","gbk","序号,调查问卷名字,$array_name");	    		    	
		    	$content = $content . "\n";
	    	}
  	    	 
			foreach($list as $k=>$v)
			{
  				if($v){
 					$content .=iconv("utf-8","gbk", implode(",",$v)) . "\n";
 				}
 			}	
			header("Content-Disposition: attachment; filename=vote_list.csv");
	    	echo $content ;
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}	
		
	}
}
?>