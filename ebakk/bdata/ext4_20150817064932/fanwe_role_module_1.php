<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_role_module`;");
E_C("CREATE TABLE `fanwe_role_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=146 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_role_module` values('5','Role','权限组别','1','0');");
E_D("replace into `fanwe_role_module` values('6','Admin','管理员','1','0');");
E_D("replace into `fanwe_role_module` values('12','Conf','系统配置','1','0');");
E_D("replace into `fanwe_role_module` values('13','Database','数据库','1','0');");
E_D("replace into `fanwe_role_module` values('15','Log','系统日志','1','0');");
E_D("replace into `fanwe_role_module` values('19','File','文件管理','1','0');");
E_D("replace into `fanwe_role_module` values('58','Index','首页','1','0');");
E_D("replace into `fanwe_role_module` values('36','Nav','导航菜单','1','0');");
E_D("replace into `fanwe_role_module` values('47','MailServer','邮件服务器','1','0');");
E_D("replace into `fanwe_role_module` values('48','Sms','短信接口','1','0');");
E_D("replace into `fanwe_role_module` values('53','Adv','广告模块','1','0');");
E_D("replace into `fanwe_role_module` values('56','DealMsgList','业务群发队列','1','0');");
E_D("replace into `fanwe_role_module` values('92','Cache','缓存处理','1','0');");
E_D("replace into `fanwe_role_module` values('113','User','会员管理','1','0');");
E_D("replace into `fanwe_role_module` values('114','MsgTemplate','消息模板管理','1','0');");
E_D("replace into `fanwe_role_module` values('115','Integrate','会员整合','1','0');");
E_D("replace into `fanwe_role_module` values('116','ApiLogin','同步登录','1','0');");
E_D("replace into `fanwe_role_module` values('117','DealCate','项目分类','1','0');");
E_D("replace into `fanwe_role_module` values('118','Deal','项目管理','1','0');");
E_D("replace into `fanwe_role_module` values('119','Payment','支付接口','1','0');");
E_D("replace into `fanwe_role_module` values('120','IndexImage','轮播广告图','1','0');");
E_D("replace into `fanwe_role_module` values('121','Help','站点帮助','1','0');");
E_D("replace into `fanwe_role_module` values('122','Faq','常见问题','1','0');");
E_D("replace into `fanwe_role_module` values('123','DealOrder','项目支持','1','0');");
E_D("replace into `fanwe_role_module` values('124','DealComment','项目点评','1','0');");
E_D("replace into `fanwe_role_module` values('125','PaymentNotice','付款记录','1','0');");
E_D("replace into `fanwe_role_module` values('126','UserRefund','用户提现','1','0');");
E_D("replace into `fanwe_role_module` values('127','PromoteMsg','推广模块','1','0');");
E_D("replace into `fanwe_role_module` values('128','PromoteMsgList','推广队列','1','0');");
E_D("replace into `fanwe_role_module` values('129','Link','友情链接','1','0');");
E_D("replace into `fanwe_role_module` values('130','LinkGroup','友情链接分组','1','0');");
E_D("replace into `fanwe_role_module` values('131','UserLevel','会员等级','1','0');");
E_D("replace into `fanwe_role_module` values('132','DealLevel','项目等级','0','0');");
E_D("replace into `fanwe_role_module` values('133','Article','文章','1','0');");
E_D("replace into `fanwe_role_module` values('134','ArticleCate','文章分类','1','0');");
E_D("replace into `fanwe_role_module` values('135','RegionConf','地区','1','0');");
E_D("replace into `fanwe_role_module` values('136','SqlCheck','系统监测','1','0');");
E_D("replace into `fanwe_role_module` values('93','MAdv','手机端广告','1','0');");
E_D("replace into `fanwe_role_module` values('137','UserInvestor','投资人申请管理','1','0');");
E_D("replace into `fanwe_role_module` values('138','Bank','提现银行设置','1','0');");
E_D("replace into `fanwe_role_module` values('139','Vote','问卷调查','1','0');");
E_D("replace into `fanwe_role_module` values('141','Collocation','资金托管','1','0');");
E_D("replace into `fanwe_role_module` values('140','UserCarry','提现手续费','1','0');");
E_D("replace into `fanwe_role_module` values('142','Referrals','会员邀请','1','0');");
E_D("replace into `fanwe_role_module` values('143','Statistics','统计','1','0');");
E_D("replace into `fanwe_role_module` values('144','Message','留言列表','1','0');");
E_D("replace into `fanwe_role_module` values('145','MessageCate','留言分类列表','1','0');");

require("../../inc/footer.php");
?>