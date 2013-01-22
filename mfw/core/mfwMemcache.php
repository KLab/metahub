<?php
/*!@file
 * Memcache wrapper.
 */

/**
 * Memcache wrapper class.
 * アプリ固有のprefixをキーに透過的に付加する.
 * @see mfwApplication::cachePrefix()
 */
class mfwMemcache {

	const URL_EXPIRE = 86400; ///< link_idの有効時間

	protected static $mc = null;

	/**
	 * memcacheサーバに接続.
	 * @return Memcache object.
	 */
	protected static function connect()
	{
		if(is_null(self::$mc)){
			$conf = mfwServerEnv::memcache();
			if(!$conf){
				throw new Exception('memcache server undefined');
			}

			$mc = new Memcache();
			if($mc->connect($conf['host'],$conf['port'])){
				self::$mc = $mc;
			}
			else{
				error_log("cannot connect memcached ({$conf['host']}:{$conf['port']})");
			}
		}
		return self::$mc;
	}

	/**
	 * 明示的に接続を破棄.
	 */
	public static function disconnect()
	{
		if(self::$mc){
			self::$mc->close();
			self::$mc = null;
		}
	}

	/**
	 * prefixを付加したキーを生成.
	 */
	protected static function makeKey($key)
	{
		$prefix = mfwApplication::cachePrefix();
		return "{$prefix}{$key}";
	}

	/**
	 * Memcacheへ保存.
	 * @param string $key 保存するキー
	 * @param mixed $value 値
	 * @param integer  $expire 有効期限 (seconds)
	 * @return 成功したらtrue
	 */
	public static function set($key, $value, $expire=600)
	{
		$mc = static::connect();
		return $mc->set(static::makeKey($key),$value,0,$expire);
	}

	/**
	 * Memcacheから取得
	 * @param string $key 取得するキー
	 * @return 保存されていた値
	 */
	public static function get($key)
	{
		$mc = static::connect();
		return $mc->get(static::makeKey($key));
	}

	/**
	 * Memcacheから削除.
	 * @param string $key 削除するキー
	 * @return 成功したらtrue
	 */
	public static function delete($key)
	{
		$mc = static::connect();
		return $mc->delete(static::makeKey($key),0);
	}

	/**
	 * 全削除.
	 * @return 成功したらtrue
	 */
	public static function flush()
	{
		$mc = static::connect();
		return $mc->flush();
	}

	/*!
	 * @name link_id
	 * @{
	 */

	/**
	 * URLを格納してハッシュ(link_id)を返す.
	 * @param string $url 格納するURL.
	 * @return ハッシュ.
	 */
	public static function storeURL($url)
	{
		$hash = sha1($url);
		static::set("URL_{$hash}",$url,self::URL_EXPIRE);
		return $hash;
	}

	/**
	 * ハッシュ(link_id)からURLを取り出す
	 * @param string $hash ハッシュ(link_id)
	 * @return URL.
	 */
	public static function fetchURL($hash)
	{
		if($hash){
			return static::get("URL_{$hash}");
		}
		return false;
	}

	/*!@}*/
}

