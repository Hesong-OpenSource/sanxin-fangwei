<?php
include 'RSAUtils.php';
/**
 * 签名
 *
 * @author wylitu
 *        
 */
class SignUtil {
	
	public static $unSignKeyList = array (
			"merchantSign",
			"version", 
			"successCallbackUrl",
			"forPayLayerUrl"
	);
	public static function signWithoutToHex($params) {
		ksort($params);
  		$sourceSignString = SignUtil::signString ( $params, SignUtil::$unSignKeyList );
  		error_log($sourceSignString, 0);
  		$sha256SourceSignString = hash ( "sha256", $sourceSignString,true);	
  		error_log($sha256SourceSignString, 0);
        return RSAUtils::encryptByPrivateKey ($sha256SourceSignString);
	}
	
	public static function sign($params) {
		ksort($params);
		$sourceSignString = SignUtil::signString ( $params, SignUtil::$unSignKeyList );
		error_log($sourceSignString, 0);
		$sha256SourceSignString = hash ( "sha256", $sourceSignString);
		error_log($sha256SourceSignString, 0);
		return RSAUtils::encryptByPrivateKey ($sha256SourceSignString);
	}
	
	public static function signString($params, $unSignKeyList) {
		
		// 拼原String
		$sb = "";
		// 删除不需要参与签名的属性
		foreach ( $params as $k => $arc ) {
			for($i = 0; $i < count ( $unSignKeyList ); $i ++) {
				
				if ($k == $unSignKeyList [$i]) {
					unset ( $params [$k] );
				}
			}
		}
		
		foreach ( $params as $k => $arc ) {
			
			$sb = $sb . $k . "=" . ($arc == null ? "" : $arc) . "&";
		}
		// 去掉最后一个&
		$sb = substr ( $sb, 0, - 1 );
		
		return $sb;
	}
}
$params = array (
		'currency' => 'CNY',
		'version' => '1.0.0',
		'failCallbackUrl' => 'http://www.baidu.com&merchantNum=22294531',
		'merchantRemark' => '生产环境-测试商户号',
		'notifyUrl' => 'http://www.jd.com&successCallbackUrl=http://www.jd.com',
		'tradeAmount' => '1',
		'tradeDescription' => '交易描述&tradeName=交易名称',
		'tradeTime' => '2014-09-01 22:11:32',
		'tradeNum' => '222945311409580692475'
		
);


//echo SignUtil::sign ( $params );

?>