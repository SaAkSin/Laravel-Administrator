<?php
/**
 * Created by SaAkSin.
 * We are ARTGRAMMER.
 * Date: 2018-07-17 오후 12:42
 */

namespace SaAkSin\Administrator;


class Util
{
	public static function count($data): int
	{
		if(is_array($data) || $data instanceof \Countable) {
			return count($data);
		}
		return 0;
	}
}