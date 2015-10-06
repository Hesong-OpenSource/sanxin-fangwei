<?php
class show_article{
	public function index()
	{		
		$root = array();
				
		$id = intval($GLOBALS['request']['id']);
		
		$sql = "select id, title, content from ".DB_PREFIX."article where is_effect = 1 and is_delete = 0 and id =".$id;
		//echo $sql; exit;
		$article = $GLOBALS['db']->getRow($sql);
		
		$root['id'] = $article['id'];
		$root['title'] = $article['title'];
		$root['content'] = get_abs_img_root($article['content']);
		
		$root['response_code'] = 1;

		output($root);
	}
}

?>