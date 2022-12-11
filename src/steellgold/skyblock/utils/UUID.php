<?php

namespace steellgold\skyblock\utils;

use Exception;

/**
 * https://gist.github.com/Steellgold/6e35fa696e197f650276b43d03237235
 */
class UUID {
	/**
	 * @throws Exception
	 */
	public function generate($format = "%s%s-%s-%s-%s-%s%s%s", $length = 4, $random_bytes_length = 16, $data = null): string {
		$data = $data ?? random_bytes($random_bytes_length);
		assert(strlen($data) == $random_bytes_length);

		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
		return vsprintf($format, str_split(bin2hex($data), $length));
	}
}