<?php
/**
 * HTTP工具类
 *
 * @author wywangzhenlong
 *        
 */
class HttpUtils {

	public function http_post_data($url, $data_string ) {

    	$cacert = '';	//CA根证书  (目前暂不提供)
    	$CA = false ; 	//HTTPS时是否进行严格认证 
		$TIMEOUT = 30;	//超时时间(秒)
		$SSL = substr($url, 0, 8) == "https://" ? true : false; 

		$ch = curl_init ();
    	if ($SSL && $CA) {  
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 	// 	只信任CA颁布的证书  
        	curl_setopt($ch, CURLOPT_CAINFO, $cacert); 			// 	CA根证书（用来验证的网站证书是否是CA颁布）  
        	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 		//	检查证书中是否设置域名，并且是否与提供的主机名匹配  
    	} else if ($SSL && !$CA) {  
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 	// 	信任任何证书  
        	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); 		// 	检查证书中是否设置域名  
    	}  

    	curl_setopt ( $ch, CURLOPT_TIMEOUT, $TIMEOUT);  
    	curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $TIMEOUT-2);  
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'Content-Type:application/json;charset=utf-8',
				'Content-Length:' . strlen( $data_string )
		) );

		ob_start();
		curl_exec($ch);
		$return_content = ob_get_contents();
		ob_end_clean();

		$return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		return array (
				$return_code,
				$return_content 
		);
	}

}
?>