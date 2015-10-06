<?php
/**
 * byte数组与字符串转化类
 */
class ByteUtils {
	
	/**
	 *
	 *
	 *
	 *
	 * 转换一个String字符串为byte数组
	 *
	 * @param $str 需要转换的字符串        	
	 *
	 * @param $bytes 目标byte数组        	
	 *
	 *
	 *
	 */
	public static function getBytes($string) {
		$bytes = array ();
		for($i = 0; $i < strlen ( $string ); $i ++) {
			$bytes [] = ord ( $string [$i] );
		}
		return $bytes;
	}
	
	/**
	 *
	 *
	 *
	 *
	 * 转换一个16进制hexString字符串为十进制byte数组
	 *
	 * @param $hexString 需要转换的十六进制字符串        	
	 * @return 一个byte数组
	 *
	 */
	public static function hexStrToBytes($hexString) {
		$bytes = array ();
		for($i = 0; $i < strlen ( $hexString ) - 1; $i += 2) {
			$bytes [$i / 2] = hexdec ( $hexString [$i] . $hexString [$i + 1] ) & 0xff;
		}
		
		return $bytes;
	}
	public static function ascToHex($asc, $AscLen) {
		$i = 0;
		$Hex = array ();
		for($i = 0; 2 * $i < $AscLen; $i ++) {
			
			/* A:0x41(0100 0001),a:0x61(0110 0001),右移4位后都是0001,加0x90等0xa */
			$Hex [$i] = (chr ( $asc [2 * $i] ) << 4);
			if (! (chr ( $asc [2 * $i] ) >= '0' && chr ( $asc [2 * $i] ) <= '9')) {
				$Hex [$i] += 0x90;
			}
			
			if (2 * $i + 1 >= $AscLen) {
				break;
			}
			
			$Hex [$i] |= (chr ( $asc [2 * $i + 1] ) & 0x0f);
			if (! (chr ( $asc [2 * $i + 1] ) >= '0' && chr ( $asc [2 * $i + 1] ) <= '9')) {
				$Hex [$i] += 0x09;
			}
		}
		return $Hex;
	}
	
	/**
	 *
	 *
	 *
	 *
	 * 将十进制字符串转换为十六进制字符串
	 *
	 * @param $string 需要转换字符串        	
	 * @return 一个十六进制字符串
	 *
	 */
	public static function strToHex($string) {
		$hex = "";
		for($i = 0; $i < strlen ( $string ); $i ++) {
			$tmp = dechex ( ord ( $string [$i] ) );
			if (strlen ( $tmp ) == 1) {
				$hex .= "0";
			}
			$hex .= $tmp;
		}
		$hex = strtolower ( $hex );
		return $hex;
	}
	public static function strToBytes($string) {
		$bytes = array ();
		for($i = 0; $i < strlen ( $string ); $i ++) {
			$bytes [] = ord ( $string [$i] );
		}
		return $bytes;
	}
	
	/**
	 *
	 *
	 *
	 *
	 * 将字节数组转化为String类型的数据
	 *
	 * @param $bytes 字节数组        	
	 *
	 * @param $str 目标字符串        	
	 *
	 * @return 一个String类型的数据
	 *
	 */
	public static function toStr($bytes) {
		$str = '';
		foreach ( $bytes as $ch ) {
			$str .= chr ( $ch );
		}
		
		return $str;
	}
	
	// 字符串转16进制
	public static function bytesToHex($bytes) {
		$str = ByteUtils::toStr ( $bytes );
		return ByteUtils::strToHex ( $str );
	}
	
	/**
	 *
	 *
	 *
	 *
	 * 转换一个int为byte数组
	 *
	 * @param $byt 目标byte数组        	
	 *
	 * @param $val 需要转换的字符串        	
	 *
	 *
	 *
	 *
	 */
	public static function integerToBytes($val) {
		$byt = array ();
		$byt [0] = ($val >> 24 & 0xff);
		$byt [1] = ($val >> 16 & 0xff);
		$byt [2] = ($val >> 8 & 0xff);
		$byt [3] = ($val & 0xff);
		return $byt;
	}
	
	/**
	 *
	 *
	 *
	 *
	 * 从字节数组中指定的位置读取一个Integer类型的数据
	 *
	 * @param $bytes 字节数组        	
	 *
	 * @param $position 指定的开始位置        	
	 *
	 * @return 一个Integer类型的数据
	 *
	 *
	 */
	public static function bytesToInteger($bytes, $position) {
		$val = 0;
		$val = $bytes [$position + 3] & 0xff;
		$val <<= 8;
		$val |= $bytes [$position + 2] & 0xff;
		$val <<= 8;
		$val |= $bytes [$position + 1] & 0xff;
		$val <<= 8;
		$val |= $bytes [$position] & 0xff;
		return $val;
	}
	
	/**
	 * 将byte数组 转换为int
	 *
	 * @param
	 *        	b
	 * @param
	 *        	offset 位游方式
	 * @return
	 *
	 *
	 */
	public static function byteArrayToInt($b, $offset) {
		$value = 0;
		for($i = 0; $i < 4; $i ++) {
			$shift = (4 - 1 - $i) * 8;
			$value = $value + ($b [$i + $offset] & 0x000000FF) << $shift; // 往高位游
		}
		return $value;
	}
	
	/**
	 *
	 *
	 *
	 *
	 * 转换一个shor字符串为byte数组
	 *
	 * @param $byt 目标byte数组        	
	 *
	 * @param $val 需要转换的字符串        	
	 *
	 *
	 *
	 *
	 */
	public static function shortToBytes($val) {
		$byt = array ();
		$byt [0] = ($val & 0xff);
		$byt [1] = ($val >> 8 & 0xff);
		return $byt;
	}
	
	/**
	 *
	 *
	 *
	 *
	 * 从字节数组中指定的位置读取一个Short类型的数据。
	 *
	 * @param $bytes 字节数组        	
	 *
	 * @param $position 指定的开始位置        	
	 *
	 * @return 一个Short类型的数据
	 *
	 *
	 */
	public static function bytesToShort($bytes, $position) {
		$val = 0;
		$val = $bytes [$position + 1] & 0xFF;
		$val = $val << 8;
		$val |= $bytes [$position] & 0xFF;
		return $val;
	}
	
	/**
	 * 
	 * @param unknown $hexstr
	 * @return Ambigous <string, unknown>
	 */
	public static function hexTobin($hexstr)
	{
		$n = strlen($hexstr);
		$sbin="";
		$i=0;
		while($i<$n)
		{
			$a =substr($hexstr,$i,2);
			$c = pack("H*",$a);
			if ($i==0){$sbin=$c;}
			else {$sbin.=$c;}
			$i+=2;
		}
		return $sbin;
	}
}

?> 