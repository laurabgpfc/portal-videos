<?php

if (!function_exists('hash_equals')) {
	function hash_equals($str1, $str2) {
		if (strlen($str1) != strlen($str2)) {
			return false;
		} else {
			$res = $str1 ^ $str2;
			$ret = 0;
			for ($i = strlen($res) - 1; $i >= 0; $i--) {
				$ret |= ord($res[$i]);
			}
			return !$ret;
		}
	}
}

function encrypt($data, $encriptarForzado = 0) {
	if ( (_ENCRIPTAR == 1)||($encriptarForzado == 1) ) {
		$iv = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, _EKEY, json_encode($data), MCRYPT_MODE_CBC, $iv);
		
		// Note: We cover the IV in our HMAC
		$hmac = hash_hmac('sha256', $iv.$ciphertext, _AKEY, true);
		
		return base64_encode($hmac.$iv.$ciphertext);
	} else {
		return $data;
	}
}

function decrypt($data, $encriptarForzado = 0) {
	if ( (_ENCRIPTAR == 1)||($encriptarForzado == 1) ) {
		$decoded = base64_decode($data);
		$hmac = mb_substr($decoded, 0, 32, '8bit');
		$iv = mb_substr($decoded, 32, 16, '8bit');
		$ciphertext = mb_substr($decoded, 48, null, '8bit');
		
		$calculated = hash_hmac('sha256', $iv.$ciphertext, _AKEY, true);

		if (hash_equals($hmac, $calculated)) {
			$decrypted = rtrim( mcrypt_decrypt(MCRYPT_RIJNDAEL_128, _EKEY, $ciphertext, MCRYPT_MODE_CBC, $iv), "\0" );		
			
			return json_decode($decrypted, true);
		}
	} else {
		return $data;
	}
}

?>