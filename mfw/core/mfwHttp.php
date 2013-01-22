<?php

class mfwHttp {

	protected static function initialize_curl($url,$headers,$timeout)
	{
		$curl = curl_init($url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_HEADER,true);
		if(!empty($headers)){
			curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
		}
		if($timeout){
			curl_setopt($curl,CURLOPT_TIMEOUT,$timeout);
		}

		$proxy = mfwServerEnv::httpProxy();
		if($proxy){
			curl_setopt($curl,CURLOPT_PROXY, $proxy['host']);
			curl_setopt($curl,CURLOPT_PROXYPORT, $proxy['port']);
		}

		return $curl;
	}

	protected static function exec($curl,&$response)
	{
		$ret = curl_exec($curl);
		if(!$ret){
			$response['status'] = 0;
			$response['status_msg'] = '';
			$response['headers'] = array();
			return null;
		}

		list($headers,$body) = explode("\r\n\r\n",$ret,2);
		if(preg_match('|^HTTP/[0-9\.]+ 200 Connection Established|',$headers)){
			// HTTPSの時はCONNECTのヘッダを除外
			list($headers,$body) = explode("\r\n\r\n",$body,2);
		}
		$headers = explode("\r\n",$headers);
		$status = explode(' ', array_shift($headers), 3);

		$response['status'] = (int)$status[1];
		$response['status_msg'] = $status[2];
		$response['headers'] = $headers;

		return $body;
	}

	public static function get($url,$headers=array(),&$response=null,$timeout=10)
	{
		$curl = static::initialize_curl($url,$headers,$timeout);
		return static::exec($curl,$response);
	}

	public static function post($url,$body='',$headers=array(),&$response=null,$timeout=10)
	{
		if(is_array($body)){
			$body = static::composeParams($body);
			$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		}
		$curl = static::initialize_curl($url,$headers,$timeout);
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$body);
		return static::exec($curl,$response);
	}

	public static function put($url,$body='',$headers=null,&$response=null,$timeout=10)
	{
		if(is_array($body)){
			$body = static::composeParams($body);
		}
		$curl = static::initialize_curl($url,$headers,$timeout);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($curl,CURLOPT_POSTFIELDS,$body);
		return static::exec($curl,$response);
	}

	public static function delete($url,$headers=null,&$response=null,$timeout=10)
	{
		$curl = static::initialize_curl($url,$headers,$timeout);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		return static::exec($curl,$response);
	}

	public static function composeParams(Array $params)
	{
		$p = array();
		foreach($params as $k=>$v){
			if($k!==''){
				$p[] = urlencode($k).'='.urlencode($v);
			}
		}
		return implode('&',$p);
	}

	public static function composeURL($basefeed,$params)
	{
		list($url,$baseparams,$anchor) = static::extractURL($basefeed);
		$params = static::composeParams(array_merge($baseparams,$params));
		if($params){
			$url .= "?{$params}";
		}
		if(!is_null($anchor)){
			$url .= "#{$anchor}";
		}
		return $url;
	}

	public static function extractURL($url)
	{
		$u = explode('#',$url);
		$anchor = (isset($u[1]))? $u[1]: null;

		$u = explode('?',$u[0]);
		$base = $u[0];

		$params = array();
		if(isset($u[1]) && $u[1]!=''){
			foreach(explode('&',$u[1]) as $p){
				$pp = explode('=',$p);
				$k = urldecode($pp[0]);
				$v = urldecode($pp[1]);
				$params[$k] = $v;
			}
		}
		return array($base,$params,$anchor);
	}
}

