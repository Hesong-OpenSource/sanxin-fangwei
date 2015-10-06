<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_conf`;");
E_C("CREATE TABLE `fanwe_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `input_type` tinyint(1) NOT NULL COMMENT '配置输入的类型 0:文本输入 1:下拉框输入 2:图片上传 3:编辑器',
  `value_scope` text NOT NULL COMMENT '取值范围',
  `is_effect` tinyint(1) NOT NULL,
  `is_conf` tinyint(1) NOT NULL COMMENT '是否可配置 0: 可配置  1:不可配置',
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=312 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_conf` values('1','DEFAULT_ADMIN','admin','1','0','','1','0','0');");
E_D("replace into `fanwe_conf` values('2','URL_MODEL','1','1','1','0,1','1','1','3');");
E_D("replace into `fanwe_conf` values('3','AUTH_KEY','fanwe','1','0','','1','1','4');");
E_D("replace into `fanwe_conf` values('4','TIME_ZONE','8','1','1','0,8','1','1','1');");
E_D("replace into `fanwe_conf` values('5','ADMIN_LOG','1','1','1','0,1','0','1','0');");
E_D("replace into `fanwe_conf` values('6','DB_VERSION','1.6','0','0','','1','0','0');");
E_D("replace into `fanwe_conf` values('7','DB_VOL_MAXSIZE','8000000','1','0','','1','1','11');");
E_D("replace into `fanwe_conf` values('8','WATER_MARK','','2','2','','1','1','48');");
E_D("replace into `fanwe_conf` values('10','BIG_WIDTH','500','2','0','','0','0','49');");
E_D("replace into `fanwe_conf` values('11','BIG_HEIGHT','500','2','0','','0','0','50');");
E_D("replace into `fanwe_conf` values('12','SMALL_WIDTH','200','2','0','','0','0','51');");
E_D("replace into `fanwe_conf` values('13','SMALL_HEIGHT','200','2','0','','0','0','52');");
E_D("replace into `fanwe_conf` values('14','WATER_ALPHA','50','2','0','','1','1','53');");
E_D("replace into `fanwe_conf` values('15','WATER_POSITION','4','2','1','1,2,3,4,5','1','1','54');");
E_D("replace into `fanwe_conf` values('16','MAX_IMAGE_SIZE','3000000','2','0','','1','1','55');");
E_D("replace into `fanwe_conf` values('17','ALLOW_IMAGE_EXT','jpg,gif,png','2','0','','1','1','56');");
E_D("replace into `fanwe_conf` values('18','BG_COLOR','#ffffff','2','0','','0','0','57');");
E_D("replace into `fanwe_conf` values('19','IS_WATER_MARK','0','2','1','0,1','1','1','58');");
E_D("replace into `fanwe_conf` values('20','TEMPLATE','fanwe_1','1','0','','1','1','17');");
E_D("replace into `fanwe_conf` values('21','SITE_LOGO','./public/attachment/201508/17/12/55d162085b8dd.png','1','2','','1','1','19');");
E_D("replace into `fanwe_conf` values('173','SEO_TITLE','方维众筹 - 预购一个梦想','1','0','','1','1','20');");
E_D("replace into `fanwe_conf` values('23','MAIL_ON','1','3','1','0,1','1','1','72');");
E_D("replace into `fanwe_conf` values('24','SMS_ON','1','5','1','0,1','1','1','78');");
E_D("replace into `fanwe_conf` values('26','PUBLIC_DOMAIN_ROOT','','2','0','','0','1','59');");
E_D("replace into `fanwe_conf` values('27','APP_MSG_SENDER_OPEN','1','1','1','0,1','1','1','9');");
E_D("replace into `fanwe_conf` values('28','ADMIN_MSG_SENDER_OPEN','1','1','1','0,1','1','1','10');");
E_D("replace into `fanwe_conf` values('29','GZIP_ON','1','1','1','0,1','1','1','2');");
E_D("replace into `fanwe_conf` values('42','SITE_NAME','众筹网','1','0','','1','1','1');");
E_D("replace into `fanwe_conf` values('30','CACHE_ON','1','1','1','0,1','1','1','7');");
E_D("replace into `fanwe_conf` values('31','EXPIRED_TIME','0','1','0','','1','1','5');");
E_D("replace into `fanwe_conf` values('32','TMPL_DOMAIN_ROOT','','2','0','0','0','0','62');");
E_D("replace into `fanwe_conf` values('33','CACHE_TYPE','File','1','1','File,Xcache,Memcached','1','1','7');");
E_D("replace into `fanwe_conf` values('34','MEMCACHE_HOST','127.0.0.1:11211','1','0','','1','1','8');");
E_D("replace into `fanwe_conf` values('35','IMAGE_USERNAME','admin','2','0','','0','1','60');");
E_D("replace into `fanwe_conf` values('36','IMAGE_PASSWORD','admin','2','4','','0','1','61');");
E_D("replace into `fanwe_conf` values('37','DEAL_MSG_LOCK','0','0','0','','0','0','0');");
E_D("replace into `fanwe_conf` values('38','SEND_SPAN','2','1','0','','1','1','85');");
E_D("replace into `fanwe_conf` values('39','TMPL_CACHE_ON','1','1','1','0,1','1','1','6');");
E_D("replace into `fanwe_conf` values('40','DOMAIN_ROOT','','1','0','','1','0','10');");
E_D("replace into `fanwe_conf` values('41','COOKIE_PATH','/','1','0','','0','1','10');");
E_D("replace into `fanwe_conf` values('43','INTEGRATE_CFG','','0','0','','1','0','0');");
E_D("replace into `fanwe_conf` values('44','INTEGRATE_CODE','','0','0','','1','0','0');");
E_D("replace into `fanwe_conf` values('172','PAY_RADIO','0','3','0','','1','1','10');");
E_D("replace into `fanwe_conf` values('176','SITE_LICENSE','众筹网 版权所有','1','0','','1','1','22');");
E_D("replace into `fanwe_conf` values('174','SEO_KEYWORD','方维众筹 - 预购一个梦想','1','0','','1','1','21');");
E_D("replace into `fanwe_conf` values('175','SEO_DESCRIPTION','方维众筹 - 预购一个梦想','1','0','','1','1','22');");
E_D("replace into `fanwe_conf` values('177','PROMOTE_MSG_LOCK','1','0','0','','0','0','0');");
E_D("replace into `fanwe_conf` values('178','PROMOTE_MSG_PAGE','0','0','0','0','0','0','0');");
E_D("replace into `fanwe_conf` values('179','STATE_CDOE','','1','0','','1','1','23');");
E_D("replace into `fanwe_conf` values('180','USER_VERIFY','2','4','1','0,1,2,3,4','1','1','63');");
E_D("replace into `fanwe_conf` values('181','INVITE_REFERRALS','10','4','0','','1','1','67');");
E_D("replace into `fanwe_conf` values('182','INVITE_REFERRALS_TYPE','1','4','1','0,1','0','1','68');");
E_D("replace into `fanwe_conf` values('183','USER_MESSAGE_AUTO_EFFECT','1','4','1','0,1','1','1','64');");
E_D("replace into `fanwe_conf` values('184','BUY_INVITE_REFERRALS','20','4','0','','1','1','67');");
E_D("replace into `fanwe_conf` values('185','REFERRAL_IP_LIMI','1','4','1','0,1','1','1','71');");
E_D("replace into `fanwe_conf` values('190','MAIL_SEND_PAYMENT','1','5','1','0,1','1','1','75');");
E_D("replace into `fanwe_conf` values('191','REPLY_ADDRESS','','5','0','','1','1','77');");
E_D("replace into `fanwe_conf` values('192','MAIL_SEND_DELIVERY','1','5','1','0,1','1','1','76');");
E_D("replace into `fanwe_conf` values('193','MAIL_ON','1','5','1','0,1','1','1','72');");
E_D("replace into `fanwe_conf` values('262','NETWORK_FOR_RECORD','闽ICP备10206706号-7','1','0','','1','1','201');");
E_D("replace into `fanwe_conf` values('263','QR_CODE','./public/attachment/201508/17/12/55d16217f3b38.jpg','3','2','','1','1','202');");
E_D("replace into `fanwe_conf` values('264','REPAY_MAKE','7','1','0','','1','1','264');");
E_D("replace into `fanwe_conf` values('265','SQL_CHECK','1','1','1','0,1','1','1','265');");
E_D("replace into `fanwe_conf` values('266','MORTGAGE_MONEY','0.01','6','0','','1','1','1');");
E_D("replace into `fanwe_conf` values('267','ENQUIER_NUM','6','6','0','','1','1','2');");
E_D("replace into `fanwe_conf` values('268','INVEST_PAY_SEND_STATUS','1','6','1','0,1,2','1','1','3');");
E_D("replace into `fanwe_conf` values('269','INVEST_STATUS_SEND_STATUS','1','6','1','0,1,2','1','1','4');");
E_D("replace into `fanwe_conf` values('270','INVEST_PAID_SEND_STATUS','1','6','1','0,1,2','1','1','5');");
E_D("replace into `fanwe_conf` values('271','INVEST_STATUS','0','6','1','0,1,2','1','1','0');");
E_D("replace into `fanwe_conf` values('272','AVERAGE_USER_STATUS','0','6','1','0,1','1','1','6');");
E_D("replace into `fanwe_conf` values('186','REFERRAL_LIMIT','999','4','0','','1','1','69');");
E_D("replace into `fanwe_conf` values('275','SCORE_TRADE_NUMBER','100','4','0','','1','1','72');");
E_D("replace into `fanwe_conf` values('276','BUY_PRESEND_SCORE_MULTIPLE','0.5','4','0','','1','1','72');");
E_D("replace into `fanwe_conf` values('277','BUY_PRESEND_POINT_MULTIPLE','0.5','4','0','','1','1','72');");
E_D("replace into `fanwe_conf` values('290','WX_MSG_LOCK','0','0','0','','0','0','0');");
E_D("replace into `fanwe_conf` values('288','VIRSUAL_NUM','2000','4','0','','1','1','288');");
E_D("replace into `fanwe_conf` values('282','WORK_TIME','09:00-18:30','3','0','','1','1','69');");
E_D("replace into `fanwe_conf` values('289','MORTGAGE_MONEY_UNFREEZE','12','6','0','','1','1','500');");
E_D("replace into `fanwe_conf` values('287','BUSINESS_TAX','1','4','1','0,1','1','1','287');");
E_D("replace into `fanwe_conf` values('284','IDENTIFY_NAGATIVE','1','4','1','0,1','1','1','284');");
E_D("replace into `fanwe_conf` values('281','PROJECT_HIDE','0','3','1','0,1','1','1','69');");
E_D("replace into `fanwe_conf` values('286','BUSINESS_CODE','1','4','1','0,1','1','1','286');");
E_D("replace into `fanwe_conf` values('280','KF_QQ','','3','0','','1','1','280');");
E_D("replace into `fanwe_conf` values('285','BUSINESS_LICENCE','1','4','1','0,1','1','1','285');");
E_D("replace into `fanwe_conf` values('283','IDENTIFY_POSITIVE','1','4','1','0,1','1','1','283');");
E_D("replace into `fanwe_conf` values('279','KF_PHONE','400-000-0000','3','0','','1','1','279');");

require("../../inc/footer.php");
?>