<?php

/*
 * aes.php: implements AES - Advanced Encryption Standard
 * from the SlowAES project, http://code.google.com/p/slowaes/
 * 
 * Copyright (c) 2008 	Josh Davis ( http://www.josh-davis.org ),
 *						Mark Percival ( http://mpercival.com ),
 *
 * Ported from C code written by Laurent Haan ( http://www.progressive-coding.com )
 * 
 * Ported from JavaScript to PHP by ALeX Kazik
 * 
 * Licensed under the Apache License, Version 2.0
 * http://www.apache.org/licenses/
 */

class AES {
	/*
	 * START AES SECTION
	 */
	
	const keySize_128 = 16;
	const keySize_192 = 24;
	const keySize_256 = 32;
		
		// Rijndael S-box
	private static $sbox = array(
		0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5, 0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76,
		0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0, 0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0,
		0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc, 0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15,
		0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a, 0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75,
		0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0, 0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84,
		0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b, 0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf,
		0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85, 0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8,
		0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5, 0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2,
		0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17, 0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73,
		0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88, 0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb,
		0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c, 0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79,
		0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9, 0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08,
		0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6, 0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a,
		0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e, 0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e,
		0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94, 0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf,
		0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68, 0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16
	);
		
		// Rijndael Inverted S-box
	private static $rsbox = array(
		  0x52, 0x09, 0x6a, 0xd5, 0x30, 0x36, 0xa5, 0x38, 0xbf, 0x40, 0xa3, 0x9e, 0x81, 0xf3, 0xd7, 0xfb
		, 0x7c, 0xe3, 0x39, 0x82, 0x9b, 0x2f, 0xff, 0x87, 0x34, 0x8e, 0x43, 0x44, 0xc4, 0xde, 0xe9, 0xcb
		, 0x54, 0x7b, 0x94, 0x32, 0xa6, 0xc2, 0x23, 0x3d, 0xee, 0x4c, 0x95, 0x0b, 0x42, 0xfa, 0xc3, 0x4e
		, 0x08, 0x2e, 0xa1, 0x66, 0x28, 0xd9, 0x24, 0xb2, 0x76, 0x5b, 0xa2, 0x49, 0x6d, 0x8b, 0xd1, 0x25
		, 0x72, 0xf8, 0xf6, 0x64, 0x86, 0x68, 0x98, 0x16, 0xd4, 0xa4, 0x5c, 0xcc, 0x5d, 0x65, 0xb6, 0x92
		, 0x6c, 0x70, 0x48, 0x50, 0xfd, 0xed, 0xb9, 0xda, 0x5e, 0x15, 0x46, 0x57, 0xa7, 0x8d, 0x9d, 0x84
		, 0x90, 0xd8, 0xab, 0x00, 0x8c, 0xbc, 0xd3, 0x0a, 0xf7, 0xe4, 0x58, 0x05, 0xb8, 0xb3, 0x45, 0x06
		, 0xd0, 0x2c, 0x1e, 0x8f, 0xca, 0x3f, 0x0f, 0x02, 0xc1, 0xaf, 0xbd, 0x03, 0x01, 0x13, 0x8a, 0x6b
		, 0x3a, 0x91, 0x11, 0x41, 0x4f, 0x67, 0xdc, 0xea, 0x97, 0xf2, 0xcf, 0xce, 0xf0, 0xb4, 0xe6, 0x73
		, 0x96, 0xac, 0x74, 0x22, 0xe7, 0xad, 0x35, 0x85, 0xe2, 0xf9, 0x37, 0xe8, 0x1c, 0x75, 0xdf, 0x6e
		, 0x47, 0xf1, 0x1a, 0x71, 0x1d, 0x29, 0xc5, 0x89, 0x6f, 0xb7, 0x62, 0x0e, 0xaa, 0x18, 0xbe, 0x1b
		, 0xfc, 0x56, 0x3e, 0x4b, 0xc6, 0xd2, 0x79, 0x20, 0x9a, 0xdb, 0xc0, 0xfe, 0x78, 0xcd, 0x5a, 0xf4
		, 0x1f, 0xdd, 0xa8, 0x33, 0x88, 0x07, 0xc7, 0x31, 0xb1, 0x12, 0x10, 0x59, 0x27, 0x80, 0xec, 0x5f
		, 0x60, 0x51, 0x7f, 0xa9, 0x19, 0xb5, 0x4a, 0x0d, 0x2d, 0xe5, 0x7a, 0x9f, 0x93, 0xc9, 0x9c, 0xef
		, 0xa0, 0xe0, 0x3b, 0x4d, 0xae, 0x2a, 0xf5, 0xb0, 0xc8, 0xeb, 0xbb, 0x3c, 0x83, 0x53, 0x99, 0x61
		, 0x17, 0x2b, 0x04, 0x7e, 0xba, 0x77, 0xd6, 0x26, 0xe1, 0x69, 0x14, 0x63, 0x55, 0x21, 0x0c, 0x7d
	);
		
		/* rotate the word eight bits to the left */
	private static function rotate($word){
		$c = $word[0];
		for ($i = 0; $i < 3; $i++)
			$word[$i] = $word[$i+1];
		$word[3] = $c;
		
		return $word;
	}
		
		// Rijndael Rcon
	private static $Rcon = array(
		0x8d, 0x01, 0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1b, 0x36, 0x6c, 0xd8,
		0xab, 0x4d, 0x9a, 0x2f, 0x5e, 0xbc, 0x63, 0xc6, 0x97, 0x35, 0x6a, 0xd4, 0xb3,
		0x7d, 0xfa, 0xef, 0xc5, 0x91, 0x39, 0x72, 0xe4, 0xd3, 0xbd, 0x61, 0xc2, 0x9f,
		0x25, 0x4a, 0x94, 0x33, 0x66, 0xcc, 0x83, 0x1d, 0x3a, 0x74, 0xe8, 0xcb, 0x8d,
		0x01, 0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1b, 0x36, 0x6c, 0xd8, 0xab,
		0x4d, 0x9a, 0x2f, 0x5e, 0xbc, 0x63, 0xc6, 0x97, 0x35, 0x6a, 0xd4, 0xb3, 0x7d,
		0xfa, 0xef, 0xc5, 0x91, 0x39, 0x72, 0xe4, 0xd3, 0xbd, 0x61, 0xc2, 0x9f, 0x25,
		0x4a, 0x94, 0x33, 0x66, 0xcc, 0x83, 0x1d, 0x3a, 0x74, 0xe8, 0xcb, 0x8d, 0x01,
		0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1b, 0x36, 0x6c, 0xd8, 0xab, 0x4d,
		0x9a, 0x2f, 0x5e, 0xbc, 0x63, 0xc6, 0x97, 0x35, 0x6a, 0xd4, 0xb3, 0x7d, 0xfa,
		0xef, 0xc5, 0x91, 0x39, 0x72, 0xe4, 0xd3, 0xbd, 0x61, 0xc2, 0x9f, 0x25, 0x4a,
		0x94, 0x33, 0x66, 0xcc, 0x83, 0x1d, 0x3a, 0x74, 0xe8, 0xcb, 0x8d, 0x01, 0x02,
		0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1b, 0x36, 0x6c, 0xd8, 0xab, 0x4d, 0x9a,
		0x2f, 0x5e, 0xbc, 0x63, 0xc6, 0x97, 0x35, 0x6a, 0xd4, 0xb3, 0x7d, 0xfa, 0xef,
		0xc5, 0x91, 0x39, 0x72, 0xe4, 0xd3, 0xbd, 0x61, 0xc2, 0x9f, 0x25, 0x4a, 0x94,
		0x33, 0x66, 0xcc, 0x83, 0x1d, 0x3a, 0x74, 0xe8, 0xcb, 0x8d, 0x01, 0x02, 0x04,
		0x08, 0x10, 0x20, 0x40, 0x80, 0x1b, 0x36, 0x6c, 0xd8, 0xab, 0x4d, 0x9a, 0x2f,
		0x5e, 0xbc, 0x63, 0xc6, 0x97, 0x35, 0x6a, 0xd4, 0xb3, 0x7d, 0xfa, 0xef, 0xc5,
		0x91, 0x39, 0x72, 0xe4, 0xd3, 0xbd, 0x61, 0xc2, 0x9f, 0x25, 0x4a, 0x94, 0x33,
		0x66, 0xcc, 0x83, 0x1d, 0x3a, 0x74, 0xe8, 0xcb
	);

		// Key Schedule Core
	private static function core($word,$iteration){
			/* rotate the 32-bit word 8 bits to the left */
			$word = self::rotate($word);
			/* apply S-Box substitution on all 4 parts of the 32-bit word */
			for ($i = 0; $i < 4; ++$i)
				$word[$i] = self::$sbox[$word[$i]];
			/* XOR the output of the rcon operation with i to the first part (leftmost) only */
			$word[0] = $word[0]^self::$Rcon[$iteration];
			return $word;
		}
		
		/* Rijndael's key expansion
		 * expands an 128,192,256 key into an 176,208,240 bytes key
		 *
		 * expandedKey is a pointer to an char array of large enough size
		 * key is a pointer to a non-expanded key
		 */
	private static function expandKey($key,$size)
		{
			$expandedKeySize = (16*(self::numberOfRounds($size)+1));
			
			/* current expanded keySize, in bytes */
			$currentSize = 0;
			$rconIteration = 1;
			$t = array();   // temporary 4-byte variable
			
			$expandedKey = array();
			for($i = 0;$i < $expandedKeySize;$i++)
				$expandedKey[$i] = 0;
		
			/* set the 16,24,32 bytes of the expanded key to the input key */
			for ($j = 0; $j < $size; $j++)
				$expandedKey[$j] = $key[$j];
			$currentSize += $size;
		
			while ($currentSize < $expandedKeySize)
			{
				/* assign the previous 4 bytes to the temporary value t */
				for ($k = 0; $k < 4; $k++)
					$t[$k] = $expandedKey[($currentSize - 4) + $k];
		
				/* every 16,24,32 bytes we apply the core schedule to t
				 * and increment rconIteration afterwards
				 */
				if($currentSize % $size == 0)
					$t = self::core($t, $rconIteration++);
		
				/* For 256-bit keys, we add an extra sbox to the calculation */
				if($size == self::keySize_256 && (($currentSize % $size) == 16))
					for($l = 0; $l < 4; $l++)
						$t[$l] = self::$sbox[$t[$l]];
		
				/* We XOR t with the four-byte block 16,24,32 bytes before the new expanded key.
				 * This becomes the next four bytes in the expanded key.
				 */
				for($m = 0; $m < 4; $m++) {
					$expandedKey[$currentSize] = $expandedKey[$currentSize - $size] ^ $t[$m];
					$currentSize++;
				}
			}
			return $expandedKey;
		}
		
		// Adds (XORs) the round key to the state
	private static function addRoundKey($state,$roundKey){
			for ($i = 0; $i < 16; $i++)
				$state[$i] = $state[$i] ^ $roundKey[$i];
			return $state;
	}
		
		// Creates a round key from the given expanded key and the
		// position within the expanded key.
	private static function createRoundKey($expandedKey,$roundKeyPointer){
			$roundKey = array();
			for ($i = 0; $i < 4; $i++)
				for ($j = 0; $j < 4; $j++)
					$roundKey[$j*4+$i] = $expandedKey[$roundKeyPointer + $i*4 + $j];
			return $roundKey;
	}
		
		/* substitute all the values from the state with the value in the SBox
		 * using the state value as index for the SBox
		 */
	private static function subBytes($state,$isInv){
			for ($i = 0; $i < 16; $i++)
				$state[$i] = $isInv?self::$rsbox[$state[$i]]:self::$sbox[$state[$i]];
			return $state;
	}
		
		/* iterate over the 4 rows and call shiftRow() with that row */
	private static function shiftRows($state,$isInv){
			for ($i = 0; $i < 4; $i++)
				$state = self::shiftRow($state,$i*4, $i,$isInv);
			return $state;
	}
		
		/* each iteration shifts the row to the left by 1 */
	private static function shiftRow($state,$statePointer,$nbr,$isInv){
			for ($i = 0; $i < $nbr; $i++)
			{
				if($isInv)
				{
					$tmp = $state[$statePointer + 3];
					for ($j = 3; $j > 0; $j--)
						$state[$statePointer + $j] = $state[$statePointer + $j-1];
					$state[$statePointer] = $tmp;
				}
				else
				{
					$tmp = $state[$statePointer];
					for ($j = 0; $j < 3; $j++)
						$state[$statePointer + $j] = $state[$statePointer + $j+1];
					$state[$statePointer + 3] = $tmp;
				}
			}
			return $state;
	}

		// galois multiplication of 8 bit characters a and b
	private static function galois_multiplication($a,$b){
			$p = 0;
			for($counter = 0; $counter < 8; $counter++)
			{
				if(($b & 1) == 1)
					$p ^= $a;
				if($p > 0x100) $p ^= 0x100;
				$hi_bit_set = ($a & 0x80); //keep p 8 bit
				$a <<= 1;
				if($a > 0x100) $a ^= 0x100; //keep a 8 bit
				if($hi_bit_set == 0x80)
					$a ^= 0x1b;
				if($a > 0x100) $a ^= 0x100; //keep a 8 bit
				$b >>= 1;
				if($b > 0x100) $b ^= 0x100; //keep b 8 bit
			}
			return $p;
	}
		
		// galois multipication of 1 column of the 4x4 matrix
	private static function mixColumn($column,$isInv){
			if($isInv)
				$mult = array(14,9,13,11);
			else
				$mult = array(2,1,1,3);
			$cpy = array();
			for($i = 0; $i < 4; $i++)
				$cpy[$i] = $column[$i];
			
			$column[0] = 	self::galois_multiplication($cpy[0],$mult[0]) ^
					self::galois_multiplication($cpy[3],$mult[1]) ^
					self::galois_multiplication($cpy[2],$mult[2]) ^
					self::galois_multiplication($cpy[1],$mult[3]);
			$column[1] = 	self::galois_multiplication($cpy[1],$mult[0]) ^
					self::galois_multiplication($cpy[0],$mult[1]) ^
					self::galois_multiplication($cpy[3],$mult[2]) ^
					self::galois_multiplication($cpy[2],$mult[3]);
			$column[2] = 	self::galois_multiplication($cpy[2],$mult[0]) ^
					self::galois_multiplication($cpy[1],$mult[1]) ^
					self::galois_multiplication($cpy[0],$mult[2]) ^
					self::galois_multiplication($cpy[3],$mult[3]);
			$column[3] = 	self::galois_multiplication($cpy[3],$mult[0]) ^
					self::galois_multiplication($cpy[2],$mult[1]) ^
					self::galois_multiplication($cpy[1],$mult[2]) ^
					self::galois_multiplication($cpy[0],$mult[3]);
			return $column;
	}
		
		// galois multipication of the 4x4 matrix
	private static function mixColumns($state,$isInv){
			$column = array();
			/* iterate over the 4 columns */
			for ($i = 0; $i < 4; $i++)
			{
				/* construct one column by iterating over the 4 rows */
				for ($j = 0; $j < 4; $j++)
					$column[$j] = $state[($j*4)+$i];
				/* apply the mixColumn on one column */
				$column = self::mixColumn($column,$isInv);
				/* put the values back into the state */
				for ($k = 0; $k < 4; $k++)
					$state[($k*4)+$i] = $column[$k];
			}
			return $state;
	}

		// applies the 4 operations of the forward round in sequence
	private static function round($state, $roundKey){
			$state = self::subBytes($state,false);
			$state = self::shiftRows($state,false);
			$state = self::mixColumns($state,false);
			$state = self::addRoundKey($state, $roundKey);
			return $state;
	}
		
		// applies the 4 operations of the inverse round in sequence
	private static function invRound($state,$roundKey){
			$state = self::shiftRows($state,true);
			$state = self::subBytes($state,true);
			$state = self::addRoundKey($state, $roundKey);
			$state = self::mixColumns($state,true);
			return $state;
	}
		
		/*
		 * Perform the initial operations, the standard round, and the final operations
		 * of the forward aes, creating a round key for each round
		 */
	private static function main($state,$expandedKey,$nbrRounds){
			$state = self::addRoundKey($state, self::createRoundKey($expandedKey,0));
			for ($i = 1; $i < $nbrRounds; $i++)
				$state = self::round($state, self::createRoundKey($expandedKey,16*$i));
			$state = self::subBytes($state,false);
			$state = self::shiftRows($state,false);
			$state = self::addRoundKey($state, self::createRoundKey($expandedKey,16*$nbrRounds));
			return $state;
	}
		
		/*
		 * Perform the initial operations, the standard round, and the final operations
		 * of the inverse aes, creating a round key for each round
		 */
	private static function invMain($state, $expandedKey, $nbrRounds){
			$state = self::addRoundKey($state, self::createRoundKey($expandedKey,16*$nbrRounds));
			for ($i = $nbrRounds-1; $i > 0; $i--)
				$state = self::invRound($state, self::createRoundKey($expandedKey,16*$i));
			$state = self::shiftRows($state,true);
			$state = self::subBytes($state,true);
			$state = self::addRoundKey($state, self::createRoundKey($expandedKey,0));
			return $state;
	}

	private static function numberOfRounds($size){
			$nbrRounds;
			switch ($size) /* set the number of rounds */
			{
				case self::keySize_128:
					$nbrRounds = 10;
					break;
				case self::keySize_192:
					$nbrRounds = 12;
					break;
				case self::keySize_256:
					$nbrRounds = 14;
					break;
				default:
					return null;
					break;
			}
			return $nbrRounds;
	}
		
		// encrypts a 128 bit input block against the given key of size specified
	private static function encryptBlock($input,$key,$size){
			$output = array();
			$block = array(); /* the 128 bit block to encode */
			$nbrRounds = self::numberOfRounds($size);
			/* Set the block values, for the block:
			 * a0,0 a0,1 a0,2 a0,3
			 * a1,0 a1,1 a1,2 a1,3
			 * a2,0 a2,1 a2,2 a2,3
			 * a3,0 a3,1 a3,2 a3,3
			 * the mapping order is a0,0 a1,0 a2,0 a3,0 a0,1 a1,1 ... a2,3 a3,3
			 */
			for ($i = 0; $i < 4; $i++) /* iterate over the columns */
				for ($j = 0; $j < 4; $j++) /* iterate over the rows */
					$block[($i+($j*4))] = $input[($i*4)+$j];
		
			/* expand the key into an 176, 208, 240 bytes key */
			$expandedKey = self::expandKey($key, $size); /* the expanded key */
			/* encrypt the block using the expandedKey */
			$block = self::main($block, $expandedKey, $nbrRounds);
			for ($k = 0; $k < 4; $k++) /* unmap the block again into the output */
				for ($l = 0; $l < 4; $l++) /* iterate over the rows */
					$output[($k*4)+$l] = $block[($k+($l*4))];
			return $output;
	}
		
		// decrypts a 128 bit input block against the given key of size specified
	private static function decryptBlock($input, $key, $size){
			$output = array();
			$block = array(); /* the 128 bit block to decode */
			$nbrRounds = self::numberOfRounds($size);
			/* Set the block values, for the block:
			 * a0,0 a0,1 a0,2 a0,3
			 * a1,0 a1,1 a1,2 a1,3
			 * a2,0 a2,1 a2,2 a2,3
			 * a3,0 a3,1 a3,2 a3,3
			 * the mapping order is a0,0 a1,0 a2,0 a3,0 a0,1 a1,1 ... a2,3 a3,3
			 */
			for ($i = 0; $i < 4; $i++) /* iterate over the columns */
				for ($j = 0; $j < 4; $j++) /* iterate over the rows */
					$block[($i+($j*4))] = $input[($i*4)+$j];
			/* expand the key into an 176, 208, 240 bytes key */
			$expandedKey = self::expandKey($key, $size);
			/* decrypt the block using the expandedKey */
			$block = self::invMain($block, $expandedKey, $nbrRounds);
			for ($k = 0; $k < 4; $k++)/* unmap the block again into the output */
				for ($l = 0; $l < 4; $l++)/* iterate over the rows */
					$output[($k*4)+$l] = $block[($k+($l*4))];
			return $output;
	}
	/*
	 * END AES SECTION
	 */
	 
	/*
	 * START MODE OF OPERATION SECTION
	 */
	//structure of supported modes of operation
	const modeOfOperation_OFB = 0;
	const modeOfOperation_CFB = 1;
	const modeOfOperation_CBC = 2;
	
	// gets a properly padded block
	private static function getPaddedBlock($bytesIn,$start,$end,$mode){
		if($end - $start > 16)
			$end = $start + 16;
		
		$xarray = array_slice($bytesIn, $start, $end-$start);
		
		$cpad = 16 - count($xarray);
		
		while(count($xarray) < 16){
			array_push($xarray, $cpad);
		}
		
		return $xarray;
	}
	
	/*
	 * Mode of Operation Encryption
	 * bytesIn - Input String as array of bytes
	 * mode - mode of type modeOfOperation
	 * key - a number array of length 'size'
	 * size - the bit length of the key
	 * iv - the 128 bit number array Initialization Vector
	 */
	public static function encrypt($bytesIn, $mode, $key, $size, $iv){
		if(count($key)%$size)
		{
			throw new Exception('Key length does not match specified size.');
		}
		if(count($iv)%16)
		{
			throw new Exception('iv length must be 128 bits.');
		}
		// the AES input/output
		$byteArray = array();
		$input = array();
		$output = array();
		$ciphertext = array();
		$cipherOut = array();
		// char firstRound
		$firstRound = true;
		if ($bytesIn !== null)
		{
			for ($j = 0;$j < ceil(count($bytesIn)/16); $j++)
			{
				$start = $j*16;
				$end = $j*16+16;
				if($j*16+16 > count($bytesIn))
					$end = count($bytesIn);
				$byteArray = self::getPaddedBlock($bytesIn,$start,$end,$mode);
				if ($mode == self::modeOfOperation_CFB)
				{
					if ($firstRound)
					{
						$output = self::encryptBlock($iv, $key, $size);
						$firstRound = false;
					}
					else
						$output = self::encryptBlock($input, $key, $size);
					for ($i = 0; $i < 16; $i++)
						$ciphertext[$i] = $byteArray[$i] ^ $output[$i];
					for($k = 0;$k < $end-$start;$k++)
						array_push($cipherOut, $ciphertext[$k]);
					$input = $ciphertext;
				}
				else if ($mode == self::modeOfOperation_OFB)
				{
					if ($firstRound)
					{
						$output = self::encryptBlock($iv, $key, $size);
						$firstRound = false;
					}
					else
						$output = self::encryptBlock($input, $key, $size);
					for ($i = 0; $i < 16; $i++)
						$ciphertext[$i] = $byteArray[$i] ^ $output[$i];
					for($k = 0;$k < $end-$start;$k++)
						array_push($cipherOut, $ciphertext[$k]);
					$input = $output;
				}
				else if ($mode == self::modeOfOperation_CBC)
				{
					for ($i = 0; $i < 16; $i++)
						$input[$i] = $byteArray[$i] ^ (($firstRound) ? $iv[$i] : $ciphertext[$i]);
					$firstRound = false;
					$ciphertext = self::encryptBlock($input, $key, $size);
					// always 16 bytes because of the padding for CBC
					for($k = 0;$k < 16;$k++)
						array_push($cipherOut, $ciphertext[$k]);
				}
			}
		}
		return array('mode' => $mode, 'originalsize' => count($bytesIn), 'cipher' => $cipherOut);
	}
	
	/*
	 * Mode of Operation Decryption
	 * cipherIn - Encrypted String as array of bytes
	 * originalsize - The unencrypted string length - required for CBC
	 * mode - mode of type modeOfOperation
	 * key - a number array of length 'size'
	 * size - the bit length of the key
	 * iv - the 128 bit number array Initialization Vector
	 */
	public static function decrypt($cipherIn,$originalsize,$mode,$key,$size,$iv)
	{
		if(count($key)%$size)
		{
			throw new Exception('Key length does not match specified size.');
			return null;
		}
		if(count($iv)%16)
		{
			throw new Exception('iv length must be 128 bits.');
		}
		// the AES input/output
		$ciphertext = array();
		$input = array();
		$output = array();
		$byteArray = array();
		$bytesOut = array();
		// char firstRound
		$firstRound = true;
		if ($cipherIn !== null)
		{
			for ($j = 0;$j < ceil(count($cipherIn)/16); $j++)
			{
				$start = $j*16;
				$end = $j*16+16;
				if($j*16+16 > count($cipherIn))
					$end = count($cipherIn);
				$ciphertext = self::getPaddedBlock($cipherIn,$start,$end,$mode);
				if ($mode == self::modeOfOperation_CFB)
				{
					if ($firstRound)
					{
						$output = self::encryptBlock($iv, $key, $size);
						$firstRound = false;
					}
					else
						$output = self::encryptBlock($input, $key, $size);
					for ($i = 0; $i < 16; $i++)
						$byteArray[$i] = $output[$i] ^ $ciphertext[$i];
					for($k = 0;$k < $end-$start;$k++)
						array_push($bytesOut, $byteArray[$k]);
					$input = $ciphertext;
				}
				else if ($mode == self::modeOfOperation_OFB)
				{
					if ($firstRound)
					{
						$output = self::encryptBlock($iv, $key, $size);
						$firstRound = false;
					}
					else
						$output = self::encryptBlock($input, $key, $size);
					for ($i = 0; $i < 16; $i++)
						$byteArray[$i] = $output[$i] ^ $ciphertext[$i];
					for($k = 0;$k < $end-$start;$k++)
						array_push($bytesOut, $byteArray[$k]);
					$input = $output;
				}
				else if($mode == self::modeOfOperation_CBC)
				{
					$output = self::decryptBlock($ciphertext, $key, $size);
					for ($i = 0; $i < 16; $i++)
						$byteArray[$i] = (($firstRound) ? $iv[$i] : $input[$i]) ^ $output[$i];
					$firstRound = false;
					if ($originalsize < $end)
						for($k = 0;$k < $originalsize-$start;$k++)
							array_push($bytesOut, $byteArray[$k]);
					else
						for($k = 0;$k < $end-$start;$k++)
							array_push($bytesOut, $byteArray[$k]);
					$input = $ciphertext;
				}
			}
		}
		return $bytesOut;
	}
	/*
	 * END MODE OF OPERATION SECTION
	 */
}

?>
