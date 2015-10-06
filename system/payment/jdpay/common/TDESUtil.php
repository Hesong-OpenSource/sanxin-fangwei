<?php
include 'BytesUtils.php';
class TDESUtil {
	
	/**
	 * 将元数据进行补位后进行3DES加密
	 * <p/>
	 * 补位后 byte[] = 描述有效数据长度(int)的byte[]+原始数据byte[]+补位byte[]
	 *
	 * @param
	 *        	sourceData 元数据字符串
	 * @return 返回3DES加密后的16进制表示的字符串
	 */
	public static function encrypt2HexStr($keys, $sourceData) {
		$source = array ();
		
		// 元数据
		$source = ByteUtils::getBytes ( $sourceData );
		
		// 1.原数据byte长度
		$merchantData = count($source);
		// echo "原数据据:" . $sourceData . "\n";
		// echo "原数据byte长度:" . $merchantData . "\n";
		// echo "原数据HEX表示:" . ByteUtils::bytesToHex ( $source ) . "\n";
		// 2.计算补位
		$x = ($merchantData + 4) % 8;
		$y = ($x == 0) ? 0 : (8 - $x);
		// echo ("需要补位 :" . $y . "\n");
		// 3.将有效数据长度byte[]添加到原始byte数组的头部
		$sizeByte = ByteUtils::integerToBytes ( $merchantData );
		$resultByte = array ();
		
		for($i = 0; $i < 4; $i ++) {
			$resultByte [$i] = $sizeByte [$i];
		}
		//var_dump($sizeByte);
		// 4.填充补位数据
		for($j = 0; $j < $merchantData; $j ++) {
			$resultByte [4 + $j] = $source [$j];
		}
		//var_dump($resultByte);
		for($k = 0; $k < $y; $k ++) {
			$resultByte [$merchantData + 4 + $k] = 0x00;
		}
		//var_dump($resultByte);
		//echo ("补位后的byte数组长度:" . count ( $resultByte ) . "\n");
		//echo ("补位后数据HEX表示:" . ByteUtils::bytesToHex ( $resultByte ) . "\n");
		//echo ("秘钥HEX表示:" . ByteUtils::strToHex ( $keys ) . "\n");
		//echo ("秘钥长度:" . count ( ByteUtils::getBytes ( $keys ) ) . "\n");
		//echo ByteUtils::toStr ( $resultByte );
		$desdata = TDESUtil::encrypt ( ByteUtils::toStr ( $resultByte ), $keys );
		//echo ("加密后的长度:" . strlen ( $desdata ) . "\n");
		return ByteUtils::strToHex ( $desdata );
	}
	
	/**
	 * 3DES 解密 进行了补位的16进制表示的字符串数据
	 *
	 * @return
	 *
	 */
	public static function decrypt4HexStr($keys, $data) {
		$hexSourceData = array ();
		
		$hexSourceData = ByteUtils::hexStrToBytes ($data);
		//var_dump($hexSourceData);

		// 解密
		$unDesResult = TDESUtil::decrypt (ByteUtils::toStr($hexSourceData),$keys);
        //echo $unDesResult;
		$unDesResultByte = ByteUtils::getBytes($unDesResult);
		//var_dump($unDesResultByte);
		$dataSizeByte = array ();
		for($i = 0; $i < 4; $i ++) {
			$dataSizeByte [$i] = $unDesResultByte [$i];
		}
		// 有效数据长度
		$dsb = ByteUtils::byteArrayToInt( $dataSizeByte, 0 );
		$tempData = array ();
 		for($j = 0; $j < $dsb; $j++) {
 			$tempData [$j] = $unDesResultByte [4 + $j];
 		}
 		
		return ByteUtils::hexTobin (ByteUtils::bytesToHex ( $tempData ));

	}
	private static function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen ( $text ) % $blocksize); // in php, strlen returns the bytes of $text
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
	private static function pkcs5Unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text ))
			return false;
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return false;
		return substr ( $text, 0, - 1 * $pad );
	}
	
	// 加密算法
	public static function encrypt($input, $key) {
		$size = mcrypt_get_block_size ( 'des', 'ecb' );
		$td = mcrypt_module_open ( MCRYPT_3DES, '', 'ecb', '' );
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		// 使用MCRYPT_3DES算法,cbc模式
		@mcrypt_generic_init ( $td, $key, $iv );
		// 初始处理
		$data = mcrypt_generic ( $td, $input );
		// 加密
		mcrypt_generic_deinit ( $td );
		// 结束
		mcrypt_module_close ( $td );
		
		return $data;
	}
	public static function decrypt($encrypted, $key) {
		//$encrypted = base64_decode($encrypted);
		$td = mcrypt_module_open ( MCRYPT_3DES, '', 'ecb', '' ); // 使用MCRYPT_DES算法,cbc模式
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		$ks = mcrypt_enc_get_key_size ( $td );
		@mcrypt_generic_init ( $td, $key, $iv ); // 初始处理
		$decrypted = mdecrypt_generic ( $td, $encrypted ); // 解密
		mcrypt_generic_deinit ( $td ); // 结束
		mcrypt_module_close ( $td );
		//$y = TDESUtil::pkcs5Unpad ( $decrypted );
		return $decrypted;
	}
	
	// 填充密码，填充至8的倍数
	public static function padding($str) {
		$len = 8 - strlen ( $str ) % 8;
		for($i = 0; $i < $len; $i ++) {
			$str .= chr ( 0 );
		}
		return $str;
	}
	
	// 删除填充符
	public static function removePadding($str) {
		$len = strlen ( $str );
		$newstr = "";
		$str = str_split ( $str );
		for($i = 0; $i < $len; $i ++) {
			if ($str [$i] != chr ( 0 )) {
				$newstr .= $str [$i];
			}
		}
		return $newstr;
	}
	
	// 删除回车和换行
	public static function removeBR($str) {
		$len = strlen ( $str );
		$newstr = "";
		$str = str_split ( $str );
		for($i = 0; $i < $len; $i ++) {
			if ($str [$i] != '\n' and $str [$i] != '\r') {
				$newstr .= $str [$i];
			}
		}
		
		return $newstr;
	}
}

//$encryp = TDESUtil::encrypt2HexStr ( base64_decode ( "ta4E/aspLA3lgFGKmNDNRYU92RkZ4w2t" ), "{\"tradeNum\": \"2014091598514957\"}");
//echo $encryp;
//echo TDESUtil::decrypt4HexStr(base64_decode ("ta4E/aspLA3lgFGKmNDNRYU92RkZ4w2t" ), $encryp );
?>