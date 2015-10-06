<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_help`;");
E_C("CREATE TABLE `fanwe_help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `is_fix` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_help` values('1','服务条款','<div class=\"layout960\">\r\n	<p>\r\n		<strong><span style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;line-height:21px;background-color:#E56600;\">关于方维众筹&nbsp;</span><br />\r\n<br />\r\n		<p style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;background-color:#FFFFFF;\">\r\n			众筹，译自国外crowdfunding一词，即大众筹资或群众筹资。是指用团购+预购的形式，向网友募集项目资金的模式。众筹利用互联网和SNS传播的特性，让许多有梦想的人可以向公众展示自己的创意，发起项目争取别人的支持与帮助，进而获得所需要的援助，支持者则会获得实物、服务等不同形式的回报。\r\n		</p>\r\n		<p style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;background-color:#FFFFFF;\">\r\n			<br />\r\n		</p>\r\n<span style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;line-height:21px;background-color:#FFFFFF;\">众筹是一个协助亲们发起创意、梦想的平台，不论你是学生、白领、艺术家、明星，如果你有一个想完成的计划（例如：电影、音乐、动漫、设计、公益等），你可以在此发起项目向大家展示你的计划，并邀请喜欢你的计划的人以资金支持你。如果你愿意帮助别人，支持别人的梦想，你可以在众筹浏览到各行各业的人发起的项目计划，也可以成为发起人的梦想合伙人，当你们一起见证项目成功后，你还会获得发起人感谢你支持的回报。</span><br />\r\n</strong>\r\n	</p>\r\n	<p>\r\n		<br />\r\n	</p>\r\n</div>','term','','1','1');");
E_D("replace into `fanwe_help` values('2','服务介绍','<p>\r\n	<span style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;line-height:21px;background-color:#E56600;\">关于方维众筹&nbsp;</span><br />\r\n<br />\r\n	<p style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;background-color:#FFFFFF;\">\r\n		众筹，译自国外crowdfunding一词，即大众筹资或群众筹资。是指用团购+预购的形式，向网友募集项目资金的模式。众筹利用互联网和SNS传播的特性，让许多有梦想的人可以向公众展示自己的创意，发起项目争取别人的支持与帮助，进而获得所需要的援助，支持者则会获得实物、服务等不同形式的回报。\r\n	</p>\r\n	<p style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;background-color:#FFFFFF;\">\r\n		<br />\r\n	</p>\r\n<span style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;line-height:21px;background-color:#FFFFFF;\">众筹是一个协助亲们发起创意、梦想的平台，不论你是学生、白领、艺术家、明星，如果你有一个想完成的计划（例如：电影、音乐、动漫、设计、公益等），你可以在此发起项目向大家展示你的计划，并邀请喜欢你的计划的人以资金支持你。如果你愿意帮助别人，支持别人的梦想，你可以在众筹浏览到各行各业的人发起的项目计划，也可以成为发起人的梦想合伙人，当你们一起见证项目成功后，你还会获得发起人感谢你支持的回报。</span>\r\n</p>\r\n<p>\r\n	<br />\r\n</p>','intro','','1','1');");
E_D("replace into `fanwe_help` values('3','隐私策略','<p>\r\n	<span style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;line-height:21px;background-color:#E56600;\">关于方维众筹&nbsp;</span><br />\r\n<br />\r\n	<p style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;background-color:#FFFFFF;\">\r\n		众筹，译自国外crowdfunding一词，即大众筹资或群众筹资。是指用团购+预购的形式，向网友募集项目资金的模式。众筹利用互联网和SNS传播的特性，让许多有梦想的人可以向公众展示自己的创意，发起项目争取别人的支持与帮助，进而获得所需要的援助，支持者则会获得实物、服务等不同形式的回报。\r\n	</p>\r\n	<p style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;background-color:#FFFFFF;\">\r\n		<br />\r\n	</p>\r\n<span style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;line-height:21px;background-color:#FFFFFF;\">众筹是一个协助亲们发起创意、梦想的平台，不论你是学生、白领、艺术家、明星，如果你有一个想完成的计划（例如：电影、音乐、动漫、设计、公益等），你可以在此发起项目向大家展示你的计划，并邀请喜欢你的计划的人以资金支持你。如果你愿意帮助别人，支持别人的梦想，你可以在众筹浏览到各行各业的人发起的项目计划，也可以成为发起人的梦想合伙人，当你们一起见证项目成功后，你还会获得发起人感谢你支持的回报。</span>\r\n</p>\r\n<p>\r\n	<br />\r\n</p>','privacy','','1','1');");
E_D("replace into `fanwe_help` values('4','关于我们','<p class=\"p0\" style=\"text-align:left;\">\r\n	<span style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;line-height:21px;background-color:#E56600;\">关于方维众筹&nbsp;</span><br />\r\n<br />\r\n	<p style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;background-color:#FFFFFF;\">\r\n		众筹，译自国外crowdfunding一词，即大众筹资或群众筹资。是指用团购+预购的形式，向网友募集项目资金的模式。众筹利用互联网和SNS传播的特性，让许多有梦想的人可以向公众展示自己的创意，发起项目争取别人的支持与帮助，进而获得所需要的援助，支持者则会获得实物、服务等不同形式的回报。\r\n	</p>\r\n	<p style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;background-color:#FFFFFF;\">\r\n		<br />\r\n	</p>\r\n<span style=\"color:#404040;font-family:''Microsoft Yahei'', 微软雅黑, Arial, ''Hiragino Sans GB'', 宋体;font-size:14px;line-height:21px;background-color:#FFFFFF;\">众筹是一个协助亲们发起创意、梦想的平台，不论你是学生、白领、艺术家、明星，如果你有一个想完成的计划（例如：电影、音乐、动漫、设计、公益等），你可以在此发起项目向大家展示你的计划，并邀请喜欢你的计划的人以资金支持你。如果你愿意帮助别人，支持别人的梦想，你可以在众筹浏览到各行各业的人发起的项目计划，也可以成为发起人的梦想合伙人，当你们一起见证项目成功后，你还会获得发起人感谢你支持的回报。</span>\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>','about','','1','1');");
E_D("replace into `fanwe_help` values('5','官方微博','<p>\r\n	<br />\r\n</p>','','http://weibo.com/','0','1');");

require("../../inc/footer.php");
?>