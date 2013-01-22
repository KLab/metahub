<?php

class mfwActions {

	protected $module = null;
	protected $action = null;
	protected $templatename = null;
	protected $templatecls = 'mfwTemplate';

	public function __construct($module,$action)
	{
		$this->module = $module;
		$this->action = $action;
	}

	/**
	 * アクション初期化.
	 * execute*()の前に呼ばれる。Actions共通の初期化処理を書く.
	 * @return error responce. エラー無しならnull.
	 *         ex: return $this->redirect(...)
	 */
	public function initialize()
	{
		return null;
	}

	public function getModule()
	{
		return $this->module;
	}

	public function getAction()
	{
		return $this->action;
	}

	protected function setTemplateName($templatename)
	{
		$this->templatename = $templatename;
	}
	protected function setTemplateClass($templatecls)
	{
		$this->templatecls = $templatecls;
	}

	protected function build($params=array())
	{
		if(empty($this->templatename)){
			$this->setTemplateName("{$this->getModule()}/{$this->getAction()}");
		}

		$template = new $this->templatecls($this->templatename);
		$content = $template->build($params);

		return array(array(),$content);
	}

	protected function redirect($query,$params=array())
	{
		$query = mfwHttp::composeUrl($query,$params);

		if(strpos($query,'http')!==0){
			$query = mfwRequest::makeUrl($query);
		}

		$headers = array(
			"Location: $query",
			);
		return array($headers,null);
	}

	public function executeDefaultAction()
	{
		$headers = array(
			'HTTP/1.1 404 Not Found',
			);
		$content = '404 Not Found';
		return array($headers,$content);
	}
}

