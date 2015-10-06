<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_article_cate`;");
E_C("CREATE TABLE `fanwe_article_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '分类名称',
  `brief` varchar(255) NOT NULL COMMENT '分类简介(备用字段)',
  `pid` int(11) NOT NULL COMMENT '父ID，程序分类可分二级',
  `is_effect` tinyint(4) NOT NULL COMMENT '有效性标识',
  `is_delete` tinyint(4) NOT NULL COMMENT '删除标识',
  `type_id` tinyint(1) NOT NULL COMMENT '型 0:普通文章（可通前台分类列表查找到） 1.帮助文章（用于前台页面底部的站点帮助） 2.公告文章（用于前台页面公告模块的调用） 3.系统文章（自定义的一些文章，需要前台自定义一些入口链接到该文章） 所属该分类的所有文章类型与分类一致',
  `sort` int(11) NOT NULL,
  `seo_title` varchar(255) NOT NULL COMMENT 'SEO标题',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `type_id` (`type_id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_article_cate` values('21','站点申明','','0','1','0','1','10','zdsm');");
E_D("replace into `fanwe_article_cate` values('22','关于我们','','0','1','0','1','0','gywm');");
E_D("replace into `fanwe_article_cate` values('29','行业新闻','','0','1','0','0','20','hyxw');");
E_D("replace into `fanwe_article_cate` values('24','新手帮助','','0','1','0','1','1','xsbz');");
E_D("replace into `fanwe_article_cate` values('25','活动报名','','0','0','0','0','5','hdbm');");
E_D("replace into `fanwe_article_cate` values('26','站内新闻','','0','1','0','0','21','znxw');");
E_D("replace into `fanwe_article_cate` values('27','合作方式','','0','0','0','1','7','hzfs');");
E_D("replace into `fanwe_article_cate` values('28','法律条款','','0','1','0','1','8','fltk');");

require("../../inc/footer.php");
?>