<?php

/*
 * cryptoHelpers.php: implements AES - Advanced Encryption Standard
 * from the SlowAES project, http://code.google.com/p/slowaes/
 * 
 * Copyright (c) 2008 	Josh Davis ( http://www.josh-davis.org ),
 *						Mark Percival ( http://mpercival.com ),
 *						Johan Sundstrom ( http://ecmanaut.blogspot.com ),
 *			 			John Resig ( http://ejohn.org )
 * 
 * Ported from JavaScript to PHP by ALeX Kazik
 * 
 * Licensed under the Apache License, Version 2.0
 * http://www.apache.org/licenses/
 */

class cryptoHelpers {
	/*
		for utf8 en-/decoding see:
		http://www.php.net/manual/en/function.utf8-encode.php
		http://www.php.net/manual/en/function.iconv.php
		http://www.php.net/manual/en/function.mb-convert-encoding.php
	*/
	
	//convert a number array to a hex string
	public static function toHex($args){
		if(func_num_args() != 1 || !is_array($args)){
			$args = func_get_args();
		}
		$ret = '';
		for($i = 0; $i < count($args) ;$i++)
			$ret .= sprintf('%02x', $args[$i]);
		return $ret;
	}
	
	//convert a hex string to a number array
	public static function toNumbers($s){
		$ret = array();
		for($i=0; $i<strlen($s); $i+=2){
			$ret[] = hexdec(substr($s, $i, 2));
		}
		return $ret;
	}
	
	// get a random number in the range [min,max]
	public static function getRandom($min,$max){
		if($min === null)
			$min = 0;
		if($max === null)
			$max = 1;
		return mt_rand($min, $max);
	}
	
	public static function generateSharedKey($len){
		if($len === null)
			$len = 16;
		$key = array();
		for($i = 0; $i < $len; $i++)
			$key[] = self::getRandom(0,255);
		return $key;
	}
	
	public static function generatePrivateKey($s,$size){
		if(function_exists('mhash') && defined('MHASH_SHA256')){
			return convertStringToByteArray(substr(mhash(MHASH_SHA256, $s), 0, $size));
		}else{
			throw new Exception('cryptoHelpers::generatePrivateKey currently requires mhash');
		}
	}

	public static function convertStringToByteArray($s){
		$byteArray = array();
		for($i = 0; $i < strlen($s); $i++){
			$byteArray[] = ord($s[$i]);
		}
		return $byteArray;
	}

	public static function convertByteArrayToString($byteArray){
		$s = '';
		for($i = 0; $i < count($byteArray); $i++){
			$s .= chr($byteArray[$i]);
		}
		return $s;
	}
	
	public static function base64_encode_line($b){
		return base64_encode(self::convertByteArrayToString($b));
	}
	
	public static function base64_encode($b){
			$b64 = self::base64_encode_line($b);
			return chunk_split($b, 64, "\n");
	}
	
	public static function base64_decode($b){
		return self::convertStringToByteArray(base64_decode($b));
	}
};

//var_dump(cryptoHelpers::base64_decode('BBB'));
