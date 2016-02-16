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

/*function encrypt($data, $encriptarForzado = 0) {
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
}*/

// Encrypt Function
function encrypt($encrypt, $encriptarForzado = 0){
	if ( (_ENCRIPTAR == 1)||($encriptarForzado == 1) ) {
		$encrypt = json_encode($encrypt);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
		$key = pack('H*', _EKEY);
		$mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
		$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
		$encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
		return $encoded;
	} else {
		return $encrypt;
	}
}

// Decrypt Function
function decrypt($decrypt, $encriptarForzado = 0){
	if ( (_ENCRIPTAR == 1)||($encriptarForzado == 1) ) {
		$decrypt = explode('|', $decrypt.'|');
		$decoded = base64_decode($decrypt[0]);
		$iv = base64_decode($decrypt[1]);
		if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
		$key = pack('H*', _EKEY);
		$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
		$mac = substr($decrypted, -64);
		$decrypted = substr($decrypted, 0, -64);
		$calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
		if($calcmac!==$mac){ return false; }
		$decrypted = json_decode($decrypted, true);
		return $decrypted;
	} else {
		return $decrypt;
	}
}


?>