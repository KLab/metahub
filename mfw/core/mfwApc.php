<?php

/**
 * APC accessor for user cahce.
 */
class mfwApc {

	static $default_expire = 0; ///< 無期限

	protected static function makeKey($key)
	{
		return mfwApplication::cachePrefix().$key;
	}

	public static function defaultExpire($expire=null)
	{
		if(!is_null($expire)){
			self::$default_expire = (int)$expire;
		}
		return self::$default_expire;
	}

	public static function set($key, $value, $expire=null)
	{
		if(is_null($expire)){
			$expire = self::$default_expire;
		}
		return apc_store(static::makeKey($key),$value,$expire);
	}

	public static function get($key)
	{
		return apc_fetch(static::makeKey($key));
	}

	public static function delete($key)
	{
		return apc_delete(static::makeKey($key));
	}

	public static function deletePrefixMatch($keyprefix)
	{
		$keyprefix = mfwApplication::cachePrefix().$keyprefix;
		$keyprefix = preg_quote($keyprefix,'/');

		$it = new APCIterator('user',"/^{$keyprefix}/",APC_ITER_KEY);
		foreach($it as $i){
			apc_delete($i['key']);
		}
	}

}

