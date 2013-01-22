<?php
/** @file
 * Server environment.
 */

class mfwServerEnv
{
	const CONFIG_FILE = '/config/mfw_serverenv_config.php';

	protected static $env = null;
	protected static $config = null;

	protected static $dbauth = array();

	protected static function errorLog($message)
	{
		$file = self::CONFIG_FILE;
		$env = self::$env;
		error_log("$file: serverenv_config[$env]: $message");
	}

	/**
	 * 環境を指定(設定読み込み).
	 * 必ず最初に呼ぶ.
	 * @return 成功なら$env, 失敗ならnull.
	 */
	public static function setEnv($env)
	{
		include APP_ROOT.self::CONFIG_FILE;

		self::$env = $env;
		if(isset($serverenv_config[$env])){
			self::$config = $serverenv_config[$env];
		}
		else{
			static::errorLog("undefined environment");
			self::$config = null;
		}
		return self::$config;
	}

	/**
	 * 現在の環境を取得.
	 */
	public static function getEnv()
	{
		return self::$env;
	}

	protected static function loadConfig($cat)
	{
		if(!isset(self::$config[$cat])){
			if(is_null(self::$config)){
				// config読み込まれていない.
				throw new InvalidArgumentException(__CLASS__.': invalid environment ('.self::$env.')');
			}
			static::errorLog("undefined category: $cat");
			return null; // 項目が無いだけ
		}
		return self::$config[$cat];
	}

	/**
	 * HTTP Proxy.
	 * @return array('host'=>hostname, 'port'=>port)
	 */
	public static function httpProxy()
	{
		$proxy = static::loadConfig('http_proxy');
		if(is_null($proxy)){
			return null;
		}
		if(!isset($proxy['host']) || !isset($proxy['port'])){
			static::errorLog("http_proxy: host/port is not defined");
			return null;
		}
		return $proxy;
	}

	/**
	 * DatabaseのDSN, ユーザ名, パスワード.
	 * @return array('dsn'=>DSN, 'user'=>USER, 'pass'=>PASS)
	 */
	public static function databaseSetting($dbname)
	{
		$conf = static::loadConfig('database');
		if(!isset($conf['authfile']) || !isset($conf[$dbname])){
			static::errorLog("database: authfile/dsn($dbname) is not defined");
			return null;
		}
		$authfile = $conf['authfile'];

		if(!isset(self::$dbauth[$authfile])){
			if(!is_file($authfile)){
				throw new Exception(__CLASS__.": no database authfile ($authfile)");
			}
			$content = file_get_contents($authfile);
			self::$dbauth[$authfile] = explode(':',trim($content));
		}
		$auth = self::$dbauth[$authfile];

		return array(
			'dsn' => $conf[$dbname],
			'user' => $auth[0],
			'pass' => isset($auth[1])?$auth[1]:null);
	}

	/**
	 * Memcacheのホスト、ポート.
	 * @return array('host'=>host, 'port'=>port)
	 */
	public static function memcache()
	{
		$conf = static::loadConfig('memcache');
		if(!isset($conf['host']) || !isset($conf['port'])){
			static::errorLog("memcache: host/port is not defined");
			return null;
		}
		return $conf;
	}

	/**
	 * swfmill コマンドのパス.
	 * 指定無しならnull.
	 */
	public static function swfmill()
	{
		return static::loadConfig('swfmill');
	}

}

