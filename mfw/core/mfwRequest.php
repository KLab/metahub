<?php
require_once dirname(__FILE__).'/Request/mfwUserAgent.php';

/**
 * HTTPリクエストを扱うクラス.
 */
class mfwRequest {

	protected static $pathinfo = null;
	protected static $body = null;
	protected static $headers  = null;
	protected static $user_agent = null;
	protected static $url = null;
	protected static $url_base = null;
	protected static $this_link_id = null;

	/**
	 * HTTPリクエストメソッドの取得.
	 * @return 'GET', 'POST' など
	 */
	public static function method()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * PATH_INFOを配列で取得.
	 */
	public static function getPathInfoArray()
	{
		if(self::$pathinfo===null){
			if(isset($_SERVER['PATH_INFO'])){
				self::$pathinfo = explode('/',$_SERVER['PATH_INFO']);
				array_shift(self::$pathinfo); // 先頭の空要素を除去
			}
			else{
				self::$pathinfo = array();
			}
		}
		return self::$pathinfo;
	}

	/**
	 * クエリパラメータ取得.
	 * @param[in] key パラメータのキー
	 * @param[in] default デフォルト値
	 * @param[in] method  $_GET/$_POST指定. デフォルトは$_REQUEST.
	 * @return パラメータの値. そのキーが存在しない時は$default.
	 */
	public static function get($key,$default=null,$method=null)
	{
		switch(strtoupper($method)){
		case 'GET':
			return array_key_exists($key,$_GET)? $_GET[$key]: $default;
		case 'POST':
			return array_key_exists($key,$_POST)? $_POST[$key]: $default;
		default:
			return array_key_exists($key,$_REQUEST)? $_REQUEST[$key]: $default;
		}
	}

	/**
	 * 全パラメータを配列で取得.
	 */
	public static function getAll($method=null)
	{
		switch(strtoupper($method)){
		case 'GET':
			return $_GET;
		case 'POST':
			return $_POST;
		default:
			return $_REQUEST;
		}
	}

	/**
	 * クエリパラメータの存在確認.
	 * @param[in] key パラメータのキー
	 * @param[in] method  $_GET/$_POST指定. デフォルトは$_REQUEST.
	 * @return 存在するならtrue.
	 */
	public static function has($key,$method=null)
	{
		switch(strtoupper($method)){
		case 'GET':
			return array_key_exists($key,$_GET);
		case 'POST':
			return array_key_exists($key,$_POST);
		default:
			return array_key_exists($key,$_REQUEST);
		}
	}

	/**
	 * POST data/PUT data
	 */
	public static function body()
	{
		if(is_null(self::$body)){
			self::$body = file_get_contents('php://input');
		}
		return self::$body;
	}

	/**
	 * HTTPリクエストヘッダの値取得.
	 * @param[in] key キー名.
	 * @return リクエストヘッダの値.
	 */
	public static function header($key)
	{
		if(self::$headers===null){
			self::$headers = getallheaders();
		}
		return isset(self::$headers[$key])? self::$headers[$key]: null;
	}

	/**
	 * UserAgentオブジェクト取得
	 * @return mfwUserAgentオブジェクト.
	 * @sa class mfwUserAgent.
	 */
	public static function userAgent()
	{
		if(self::$user_agent===null){
			self::$user_agent = new mfwUserAgent();
		}
		return self::$user_agent;
	}

	/**
	 * このリクエストのURL.
	 */
	public static function url()
	{
		if(self::$url===null){
			$scheme = 'http';
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'){
				$scheme = 'https';
			}
			self::$url = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		}
		return self::$url;
	}

	/**
	 * URLを生成.
	 * @param[in] $uri     request uri (ex: '/hoge/fuga?foo=bar')
	 * @param[in] $scheme  'http' or 'https'
	 * @return 完全なURL (ex: http://example.com/hoge/fuga?foo=bar)
	 */
	public static function makeUrl($uri,$scheme=null)
	{
		if(self::$url_base===null){
			$path='';
			if(preg_match('|^(.*)/[^/]+.php|',$_SERVER['SCRIPT_NAME'],$m)){
				$path = $m[1];
			}
			if(!$scheme){
				$scheme = 'http';
				if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'){
					$scheme = 'https';
				}
			}
			self::$url_base = "{$scheme}://{$_SERVER['HTTP_HOST']}{$path}";
		}
		return self::$url_base . $uri;
	}

	/**
	 * LinkId.
	 * @{
	 */

	/**
	 * 渡された link_id 取得.
	 * @return link_id
	 */
	public static function linkId()
	{
		return isset($_REQUEST['link_id'])? $_REQUEST['link_id']: null;
	}

	/**
	 * 現在のページのlink_idを生成.
	 * @return link_id
	 */
	public static function makeThisLinkId()
	{
		if(self::$this_link_id===null){
			$url = static::url();
			self::$this_link_id = static::makeLinkId($url);
		}
		return self::$this_link_id;
	}

	/**
	 * 任意のURLのlink_idを生成.
	 * @param[in] $url 戻り先URL
	 * @return link_id
	 */
	public static function makeLinkId($url)
	{
		return mfwMemcache::storeURL($url);
	}

	/**
	 * link_idから戻り先URLを取り出す.
	 * @param[in] $link_id 省略時はクエリパラメータのlink_idが対象
	 * @return URL
	 */
	public static function getReturnUrl($link_id=null)
	{
		if(!$link_id){
			$link_id = static::linkId();
		}
		static $returnurls = array();
		if(!array_key_exists($link_id,$returnurls)){
			$returnurls[$link_id] = mfwMemcache::fetchURL($link_id);
		}
		return $returnurls[$link_id];
	}

	/** @} */

}

