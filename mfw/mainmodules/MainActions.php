<?php

class MainActions extends mfwActions
{
	const TEMPLATEDIR = '/data/templates';
	const BLOCKDIR = '/data/blocks';

	protected $github_project_owner;

	public function initialize()
	{
		if(($err=parent::initialize())){
			return $err;
		}
		$this->github_project_owner = trim(file_get_contents(APP_ROOT.'/../apiauth/github_project_owner'));
		return null;
	}

	protected function build($params=array())
	{
		$params['github_project_owner'] = $this->github_project_owner;
		return parent::build($params);
	}

	protected function buildErrorPage($message,$link=null,$linkmsg=null)
	{
		$params = array(
			'message' => $message,
			'link_url' => mfwRequest::makeUrl($link),
			'link_msg' => $linkmsg?:$link,
			);
		$this->setTemplateName('_errorpage');
		return $this->build($params);
	}

}
