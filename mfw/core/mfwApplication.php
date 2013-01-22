<?php
/**
 * Application固有設定(識別子など).
 */

class mfwApplication
{
	const CONFIG_FILE = '/config/mfw_application_config.php';

	protected $title;
	protected $identifier;
	protected $cache_prefix;

	protected function __construct()
	{
		include APP_ROOT.self::CONFIG_FILE;

		$this->title = isset($application_config['title'])? $application_config['title']: '';
		$this->identifier = isset($application_config['identifier'])? $application_config['identifier']: '';

		$i = str_replace(' ','_',$this->identifier);
		$env = mfwServerEnv::getEnv();
		$branch = basename(APP_ROOT);
		$this->cache_prefix = "{$i}_{$env}_{$branch}_";
	}

	protected static function singleton()
	{
		static $singleton = null;
		if(is_null($singleton)){
			$singleton = new static();
		}
		return $singleton;
	}

	public static function title()
	{
		return static::singleton()->title;
	}

	public static function identifier()
	{
		return static::singleton()->identifier;
	}

	public static function cachePrefix()
	{
		return static::singleton()->cache_prefix;
	}

}
