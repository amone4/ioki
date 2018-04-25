<?php

function alphaID($in, $to_num = false, $pad_up = false, $pass_key = null) {
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

/**
 * function uses alphaID to encrypt string of numbers to string of alphanumeric
 * @param string            $string string to be encrypted
 * @param bool or number    $pad    number of pads
 *
 * @return mixed    result of encryption
 */
function encryptAlpha($string, $pad = false) {
	return alphaID($string, false, $pad, PASS);
}

/**
 * function uses alphaID to decrypt string of alphanumeric to string of numbers
 * @param string            $string string to be decrypted
 * @param bool or number    $pad    number of pads
 *
 * @return mixed    result of decryption
 */
function decryptAlpha($string, $pad = false) {
	return alphaID($string, true, $pad, PASS);
}