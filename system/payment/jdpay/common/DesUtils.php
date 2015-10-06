<?php
require_once(APP_ROOT_PATH.'system/payment/jdpay/common/BytesUtils.php');
/**
 * PHP版DES加解密类
 * 可与java的DES(DESede/CBC/PKCS5Padding)加密方式兼容
 */
class DesUtils {

	public function encrypt($input,$key) {
		$key = base64_decode ($key);

		$key = $this->pad2Length ($key, 8 );

		$size = mcrypt_get_block_size ( 'des', 'ecb' );
		$input = $this->pkcs5_pad ( $input, $size );
		$td = mcrypt_module_open ( 'des', '', 'ecb', '' );
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		@mcrypt_generic_init ( $td, $key, $iv );
		$data = mcrypt_generic ( $td, $input );
		mcrypt_generic_deinit ( $td );
		mcrypt_module_close ( $td );
		$data = base64_encode ( $data );
		return $data;
	}
	public function decrypt($encrypted,$key) {
		$encrypted = base64_decode ($encrypted);
		$key = base64_decode ($key);
		$key = $this->pad2Length ( $key, 8 );
		$td = mcrypt_module_open ( 'des', '', 'ecb', '' );
		// 使用MCRYPT_DES算法,cbc模式
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		$ks = mcrypt_enc_get_key_size ( $td );
		@mcrypt_generic_init ( $td, $key, $iv );
		// 初始处理
		$decrypted = mdecrypt_generic ( $td, $encrypted );
		// 解密
		mcrypt_generic_deinit ( $td );
		// 结束
		mcrypt_module_close ( $td );
		$y = $this->pkcs5_unpad ( $decrypted );
		return $y;
	}
	function pad2Length($text, $padlen) {
		$len = strlen ( $text ) % $padlen;
		$res = $text;
		$span = $padlen - $len;
		for($i = 0; $i < $span; $i ++) {
			$res .= chr ( $span );
		}
		return $res;
	}
	function pkcs5_pad($text, $blocksize) {
		$pad = $blocksize - (strlen ( $text ) % $blocksize);
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
	function pkcs5_unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text ))
			return false;
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return false;
		return substr ( $text, 0, - 1 * $pad );
	}
}


$key1 = "Z8KMT8cT4z5ruu89znxFhRP4DdDBqLUH";
$input1 = "BbCcsyU1/y229HyvxDT5FmRVucHTWPRjhhrZebEmpMP//lcum8r0QuXFr6Ox695wMtF0nS0XxcaV
9Sh1gkaMzuRGAz+s+c9hXDEg1AW5STcyQ3G7Q6Jw9Aj2LWFizT9qCQaujOQPOtOZ22EavG6s5RX/
8sp3bRJkxJ6zCbT7rL4jsIfoFONAt0HWUXQJbk4Akse+WwzcrmNPP5s1W2rDORp9gw0qUaUoCfgM
NKHcXblveVeVcYxyA0rkFOq6qHWKrlHjDgjUittRYAOBbP4L2CI5o+wLC3iWVy6eiLwvBsIyc7jl
ScajsZNH1x9OmHTV+WXP5z0ez7XoE5HbTh0frGZxDYSl0fT/2y/P1inykUJpBKbVp7sl85Urf5Sr
Ydc4W04AwIj246pi9mJPu6wL6lnUWn3uzcOlCELiZBz92nyr7jyXyr3GNGkEptWnuyXzxWWp31o3
cf0Y0Y3+0bcnnA==";
$crypt = new DesUtils();
//echo "Decode:".$crypt->encrypt("hahdddddd",$key1);
//echo "Decode:".$crypt->decrypt($input1,$key1);
?>