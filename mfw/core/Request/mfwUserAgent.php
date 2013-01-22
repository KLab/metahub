<?php

class mfwUserAgent
{
	protected $str;
	protected $type;
	protected $subtype;

	public function __construct($ua=null)
	{
		if($ua===null){
			$ua = $_SERVER['HTTP_USER_AGENT'];
		}
		$this->str = $ua;
		$this->type = null;
		$this->subtype = array();

		if(strpos($ua,'Android')!==false){
			$this->type = 'Android';
			$this->setAndroidSubtypes($ua);
		}
		elseif(strpos($ua,'(iPhone;')!==false){
			$this->type = 'iPhone';
			$this->setIOSSubtypes($ua);
		}
		elseif(strpos($ua,'(iPod;')!==false){
			$this->type = 'iPod';
			$this->setIOSSubtypes($ua);
		}
		elseif(strpos($ua,'(iPad;')!==false){
			$this->type = 'iPad';
			$this->setIOSSubtypes($ua);
		}
		elseif(strpos($ua,'DoCoMo')===0){
			$this->type = 'DoCoMo';
			$this->setDoCoMoSubtypes($ua);
		}
		elseif(strpos($ua,'KDDI')===0 || strpos($ua,'UP.Browser')===0){
			$this->type = 'au';
			$this->setAuSubtypes($ua);
		}
		elseif(strpos($ua,'SoftBank')===0){
			$this->type = 'SoftBank';
			$this->setSoftbankSubtypes($ua);
		}
		elseif(strpos($ua,'J-PHONE')===0){
			$this->type = 'SoftBank';
			$this->setSoftbankSubtypes($ua);
		}
		elseif(strpos($ua,'Vodafone')===0){
			$this->type = 'SoftBank';
			$this->setSoftbankSubtypes($ua);
		}
		elseif(strpos($ua,'MOT')===0){
			$this->type = 'SoftBank';
			$this->setSoftbankMotorolaSubtypes($ua);
		}
	}

	protected function setAndroidSubtypes($ua)
	{
		$this->subtype = array(
			'version' => null,
			'major_version' => null,
			'minor_version' => null,
			'device' => null,
			'browser' => null,
			);
		if(preg_match('/Android ([^;]*);/',$ua,$m)){
			$this->subtype['version'] = $m[1];
			if(preg_match('/^([0-9\.]*)/',$m[1],$m)){
				$var = explode('.',$m[1]);
				$this->subtype['major_version'] = $var[0];
				$this->subtype['minor_version'] = isset($var[1])?$var[1]:0;
			}
		}
		if(preg_match('|; ([^;]*) Build/|',$ua,$m)){
			$this->subtype['device'] = $m[1];
		}
		if(strpos($ua,'Chrome')!==false){
			$this->subtype['browser'] = 'Chrome';
		}
		elseif(strpos($ua,'Firefox')!==false){
			$this->subtype['browser'] = 'FireFox';
		}
		elseif(strpos($ua,'Opera')!==false){
			$this->subtype['browser'] = 'Opera';
		}
	}
	protected function setIOSSubtypes($ua)
	{
		$this->subtype = array(
			'version' => null,
			'major_version' => null,
			'minor_version' => null,
			);
		if(preg_match('/ OS ([0-9_]*) like Mac OS X/',$ua,$m)){
			$var = explode('_',$m[1]);
			$this->subtype['version'] = $m[1];
			$this->subtype['major_version'] = $var[0];
			$this->subtype['minor_version'] = isset($var[1])?$var[1]:0;
		}
	}
	protected function setDocomoSubtypes($ua)
	{
		$this->subtype = array(
			'version' => null,
			'device' => null,
			'uid' => null,
			);
		if(preg_match('|^DoCoMo/([0-9\.]*)[/ ]([^/\(]*)[/\(]|',$ua,$m)){
			$this->subtype['version'] = $m[1];
			$this->subtype['device'] = $m[2];
		}
		if(isset($_SERVER['HTTP_X_DCMGUID'])){
			$this->subtype['uid'] = $_SERVER['HTTP_X_DCMGUID'];
		}
	}
	protected function setAuSubtypes($ua)
	{
		$this->subtype = array(
			'device' => null,
			'hdml_only' => false,
			'uid' => null,
			);
		if(preg_match('/^KDDI-([^ ]*) /',$ua,$m)){
			$this->subtype['device'] = $m[1];
		}
		elseif(preg_match('|^UP\.Browser/[0-9\.]*-([^ ]*) |',$ua,$m)){
			$this->subtype['device'] = $m[1];
			$this->subtype['hdml_only'] = true;
		}
		if(isset($_SERVER['HTTP_X_UP_SUBNO'])){
			$this->subtype['uid'] = $_SERVER['HTTP_X_UP_SUBNO'];
		}
	}
	protected function setSoftbankSubtypes($ua)
	{
		$this->subtype = array(
			'device' => null,
			'version' => null,
			'uid' => null,
			);
		$regexp = '/^(?:J-PHONE|Vodafone|SoftBank)\/([^\/]*)\/([^\/]*)/';
		if(preg_match($regexp,$ua,$m)){
			$this->subtype['version'] = $m[1];
			$this->subtype['device'] = $m[2];
		}
		if(isset($_SERVER['HTTP_X_JPHONE_UID'])){
			$this->subtype['uid'] = substr($_SERVER['HTTP_X_JPHONE_UID'],1);
		}
	}
	protected function setSoftbankMotorolaSubtypes($ua)
	{
		$this->subtype = array(
			'device' => null,
			'version' => null,
			'uid' => null,
			);
		if(strpos($ua,'MOT-V980')===0){
			$this->subtype['device'] = 'V702MO';
		}
		elseif(strpos($ua,'MOT-C980')===0){
			$this->subtype['device'] = 'V702sMO';
		}
		if(isset($_SERVER['HTTP_X_JPHONE_UID'])){
			$this->subtype['uid'] = substr($_SERVER['HTTP_X_JPHONE_UID'],1);
		}
	}

	public function getType()
	{
		return $this->type;
	}
	public function getSubType()
	{
		return $this->subtype;
	}
	public function getString()
	{
		return $this->str;
	}

	public function isAndroid()
	{
		return $this->type === 'Android';
	}
	public function isIOS()
	{
		return in_array($this->type,array('iPhone','iPod','iPad'));
	}
	public function isSmartPhone()
	{
		return $this->isAndroid() || $this->isIOS();
	}
	public function isDocomo()
	{
		return $this->type === 'DoCoMo';
	}
	public function isAu()
	{
		return $this->type === 'au';
	}
	public function isSoftBank()
	{
		return $this->type === 'SoftBank';
	}
	public function isPC()
	{
		return is_null($this->type);
	}
}

