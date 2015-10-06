<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_weixin_conf`;");
E_C("CREATE TABLE `fanwe_weixin_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '配置输入的类型 0:文本输入 1:下拉框输入 2:图片上传 3:编辑器',
  `value_scope` text NOT NULL COMMENT '取值范围',
  `is_require` tinyint(1) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `is_conf` tinyint(1) NOT NULL COMMENT '是否可配置 0: 可配置  1:不可配置',
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='//微信配置选项'");
E_D("replace into `fanwe_weixin_conf` values('1','第三方平台appid','platform_appid','appid','0','0','','0','1','1','1');");
E_D("replace into `fanwe_weixin_conf` values('2','第三方平台token','platform_token','token','0','0','','0','1','1','2');");
E_D("replace into `fanwe_weixin_conf` values('3','第三方平台symmetric_key','platform_encodingAesKey','symmetric_key','0','0','','0','1','1','3');");
E_D("replace into `fanwe_weixin_conf` values('4','是否开启第三方平台','platform_status','0','0','4','0,1','0','1','1','4');");
E_D("replace into `fanwe_weixin_conf` values('5','第三方平台AppSecret','platform_appsecret','0','0','0','','0','1','1','1');");
E_D("replace into `fanwe_weixin_conf` values('6','component_verify_ticket','platform_component_verify_ticket','0','0','0','','0','1','0','6');");
E_D("replace into `fanwe_weixin_conf` values('7','第三方平台access_token','platform_component_access_token','0','0','0','','0','1','0','7');");
E_D("replace into `fanwe_weixin_conf` values('8','第三方平台预授权码','platform_pre_auth_code','0','0','0','','0','1','0','8');");
E_D("replace into `fanwe_weixin_conf` values('9','第三方平台access_token有效期','platform_component_access_token_expire','0','0','0','','0','1','0','9');");
E_D("replace into `fanwe_weixin_conf` values('10','第三方平台预授权码有效期','platform_pre_auth_code_expire','0','0','0','','0','1','0','10');");

require("../../inc/footer.php");
?>