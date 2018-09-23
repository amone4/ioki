<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Crypt {

	public static function encryptBlowfish($input, $key = PASS) {
		return @openssl_encrypt($input, 'bf-cbc', $key);
	}

	public static function decryptBlowfish($input, $key = PASS) {
		return @openssl_decrypt($input, 'bf-cbc', $key);
	}

	public static function encryptAlpha($string, $pad = false) {
		if ($pad === false || $pad < 1) return self::alphaID($string, false, false, PASS);
		else {
			$lower = '1';
			$upper = '9';
			for ($i = 1; $i < $pad/2; $i++) {
				$lower .= '0';
				$upper .= '9';
			}
			return self::alphaID(rand(intval($lower), intval($upper)) . $string . rand(intval($lower), intval($upper)), false, false, PASS);
		}
	}

	public static function decryptAlpha($string, $pad = false) {
		if ($pad === false || $pad < 1) return self::alphaID($string, true, false, PASS);
		else return substr(substr(number_format(self::alphaID($string, true, false, PASS), 0, '', ''), ($pad / 2)), 0, -($pad / 2));
	}

	private static function alphaID($in, $to_num = false, $pad_up = false, $pass_key = null) {
		$out   =   '';
		$index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$base  = strlen($index);

		if ($pass_key !== null) {
			for ($n = 0; $n < strlen($index); $n++) {
				$i[] = substr($index, $n, 1);
			}

			$pass_hash = hash('sha256',$pass_key);
			$pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) : $pass_hash);

			for ($n = 0; $n < strlen($index); $n++) {
				$p[] =  substr($pass_hash, $n, 1);
			}

			array_multisort($p, SORT_DESC, $i);
			$index = implode($i);
		}

		if ($to_num) {
			$len = strlen($in) - 1;

			for ($t = $len; $t >= 0; $t--) {
				$bcp = bcpow($base, $len - $t);
				@$out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
			}

			if (is_numeric($pad_up)) {
				$pad_up--;

				if ($pad_up > 0) {
					$out -= pow($base, $pad_up);
				}
			}

		} else {
			if (is_numeric($pad_up)) {
				$pad_up--;

				if ($pad_up > 0) {
					$in += pow($base, $pad_up);
				}
			}

			for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
				$bcp = bcpow($base, $t);
				$a   = floor($in / $bcp) % $base;
				$out = $out . substr($index, $a, 1);
				$in  = $in - ($a * $bcp);
			}
		}

		return $out;
	}
}