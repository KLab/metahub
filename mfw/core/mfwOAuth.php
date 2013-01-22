<?php
require_once APP_ROOT.'/core/vendor/oauth/OAuth.php';

class mfwOAuth {

	protected $consumer = null;
	protected $token = null;
	protected $sign_method = null;
	protected $from_request = null;

	public function __construct($key=null,$secret=null)
	{
		if($key && $secret){
			$this->consumer = new OAuthConsumer($key,$secret);
		}
	}

	public function setToken($key,$secret)
	{
		$this->token = new OAuthtoken($key,$secret);
	}
	public function getToken()
	{
		if(!$this->token){
			return null;
		}
		return array($this->token->key,$this->token->secret);
	}

	public function setConsumer($key,$secret)
	{
		$this->consumer = new OAuthConsumer($key,$secret);
	}
	public function getConsumer($key,$secret)
	{
		if(!$this->consumer){
			return null;
		}
		return array($this->consumer->key,$this->consumer->secret);
	}

	public function initFromRequest($realurl=null)
	{
		$request = OAuthRequest::from_request(null,$realurl);
		$token = null;
		if($request->get_parameter('oauth_token')
		   && $request->get_parameter('oauth_token_secret')
			){
			$token = new OAuthToken(
				$request->get_parameter('oauth_token'),
				$request->get_parameter('oauth_token_secret')
				);
		}
		switch($request->get_parameter('auth_signature_method')){
		default:
		case 'HMAC-SHA1':
			$sign_method = new OAuthSignatureMethod_HMAC_SHA1();
			break;
		//case 'RSA-SHA1':
		//	break;
		case 'PLAINTEXT':
			$sign_method = new OAuthSignatureMethod_PLAINTEXT();
			break;
		}

		$this->from_request = $request;
		$this->token = $token;
		$this->sign_method = $sign_method;
	}

	public function checkFromRequest()
	{
		if(!$this->from_request){
			return false;
		}
		return $this->sign_method->check_signature(
			$this->from_request,
			$this->consumer,
			$this->token,
			$this->from_request->get_parameter('oauth_signature'));
	}

	public function fromParameter($key)
	{
		return $this->from_request->get_parameter($key);
	}

	public function signHeader($method,$url,$query_params=array(),$oauth_params=array(),$realm=null)
	{
		$request = $this->newRequest($method,$url,$query_params,$oauth_params);
		return $request->to_header($realm);
	}

	public function signUrl($method,$url,$query_params=array(),$oauth_params=array())
	{
		$request = $this->newRequest($method,$url,$query_params,$oauth_params);
		return $request->to_url();
	}

	protected function newRequest($method,$url,$query_params,$oauth_params)
	{
		if(!$this->sign_method){
			$this->sign_method = new OAuthSignatureMethod_HMAC_SHA1();
		}

		$request = OAuthRequest::from_consumer_and_token($this->consumer,$this->token,$method,$url,$query_params);

		foreach($oauth_params as $key=>$value){
			$request->set_parameter($key,$value,false);
		}

		$request->sign_request($this->sign_method,$this->consumer,$this->token);
		return $request;
	}

}

