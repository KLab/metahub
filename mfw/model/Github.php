<?php

class Github {

	const APIHOST = 'https://api.github.com';

	protected static $owner = null;
	protected static $access_token = null;

	protected static function accessToken()
	{
		if(is_null(self::$access_token)){
			$path = APP_ROOT.'/../apiauth/github_accesstoken';
			$token = trim(file_get_contents($path));
			if($token){
				self::$access_token = $token;
			}
			else{
				self::$access_token = false;
			}
		}
		return self::$access_token;
	}

	protected static function owner()
	{
		if(is_null(self::$owner)){
			$path = APP_ROOT.'/../apiauth/github_project_owner';
			$owner = trim(file_get_contents($path));
			if($owner){
				self::$owner = $owner;
			}
			else{
				self::$owner = false;
			}
		}
		return self::$owner;
	}

	protected static function getApi($url,$params=array())
	{
		$token = self::accessToken();
		if($token){
			$params['access_token'] = $token;
		}
		$ret = mfwHttp::get(mfwHttp::composeURL($url,$params));
		return json_decode($ret,true);
	}


	public static function getOpenPullRequests($repository)
	{
		$owner = self::owner();
		$url = self::APIHOST . "/repos/$owner/$repository/pulls";
		return self::getApi($url);
	}

	public static function getClosedPullRequests($repository)
	{
		$owner = self::owner();
		$url = self::APIHOST . "/repos/$owner/$repository/pulls";
		return self::getApi($url,array('state'=>'closed'));
	}

	public static function getSinglePullRequest($repository,$number)
	{
		$owner = self::owner();
		$url = self::APIHOST . "/repos/$owner/$repository/pulls/$number";
		return self::getApi($url);
	}

	public static function getPullrequestComments($repository,$number)
	{
		$owner = self::owner();
		$url = self::APIHOST . "/repos/$owner/$repository/pulls/$number/comments";
		return self::getApi($url);
	}

	public static function getPullRequestFiles($repository,$number)
	{
		$owner = self::owner();
		$url = self::APIHOST . "/repos/$owner/$repository/pulls/$number/files";
		return self::getApi($url);
	}

}
