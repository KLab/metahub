<?php
require_once dirname(__FILE__).'/actions.php';

class repoActions extends pullsActions {

	protected $repo;

	public function initialize()
	{
		if(($err=parent::initialize())){
			return $err;
		}

		$repo_id = (int)mfwRequest::get('repo_id');
		$repo = RepositoryDb::retrieveByPK($repo_id);
		if(!$repo){
			return $this->buildErrorPage(
				'no repository found.',
				'/pulls/index',
				'return'
				);
		}

		$this->repo = $repo;
		return null;
	}

	public function build($params=array())
	{
		$params['repo'] = $this->repo;
		return parent::build($params);
	}

	public function executeRepo()
	{
		$page = (int)mfwRequest::get('page',1);
		$count = PullRequestDb::totalCountForRepository($this->repo);
		$page_max = floor(($count-1) /self::ITEMS_PER_PAGE) +1;

		$page = max(1,min($page,$page_max));
		$offset = ($page-1) * self::ITEMS_PER_PAGE;

		$pulls = PullRequestDb::selectByRepository($this->repo,$offset,self::ITEMS_PER_PAGE);

		$alerts = array();
		foreach($pulls as $p){
			$alerts[] = $this->makeAlertBlock($p,$this->repo);
		}

		$params = array(
			'alerts' => $alerts,
			'page' => $page,
			'page_max' => $page_max,
			);
		return $this->build($params);
	}

	public function executeRepoEdit()
	{
		$params = array(
			);
		return $this->build($params);
	}

	public function executeRepoSave()
	{
		$name = mfwRequest::get('name');
		$project = mfwRequest::get('project');
		if(!$name || !$project){
			return $this->buildErrorPage(
				"invalid argument (name=$name, project=$project)",
				"/pulls/repoEdit?repo_id={$this->repo->getId()}",
				'return');
		}

		$this->repo->update($name,$project);
		return $this->redirect("/pulls/repoEdit?repo_id={$this->repo->getId()}");
	}

}

