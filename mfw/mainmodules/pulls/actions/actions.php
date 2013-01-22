<?php
require_once(APP_ROOT.'/model/Repository.php');
require_once(APP_ROOT.'/model/PullRequest.php');
require_once(APP_ROOT.'/model/Filter.php');

class pullsActions extends MainActions {

	const ITEMS_PER_PAGE = 5;

	protected $repolist;

	public function initialize()
	{
		$err = parent::initialize();
		if($err){
			return $err;
		}
		$this->repolist = RepositoryDb::selectAll();
		$this->repolist->sortByName();
	}
	protected function build($params)
	{
		$params['repos'] = $this->repolist;
		return parent::build($params);
	}

	public function executeIndex()
	{
		$page = (int)mfwRequest::get('page',1);
		$count = PullRequestDb::totalCount();
		$page_max = floor(($count-1) /self::ITEMS_PER_PAGE) +1;

		$page = max(1,min($page,$page_max));
		$offset = ($page-1) * self::ITEMS_PER_PAGE;

		$pulls = PullRequestDb::selectForPager($offset,self::ITEMS_PER_PAGE);

		$alerts = array();
		foreach($pulls as $p){
			$alerts[] = $this->makeAlertBlock($p);
		}

		$params = array(
			'alerts' => $alerts,
			'page' => $page,
			'page_max' => $page_max,
			);
		return $this->build($params);
	}

	protected function makeAlertBlock($pull)
	{
		static $filterlist = null;
		if(is_null($filterlist)){
			$filterlist = FilterDb::selectAll();
		}
		$repo = $this->repolist[$pull->getRepoId()];

		$alerts = $pull->getAllAlerts();

		$file_alerts = array();
		foreach($alerts as $a){
			$file_alerts[$a->getFileName()][] = $filterlist[$a->getFilterId()];
		}
		$nonfile_alerts = array();
		if(isset($file_alerts[''])){
			$nonfile_alerts = $file_alerts[''];
			unset($file_alerts['']);
		}

		$files = array();
		foreach($file_alerts as $file=>$alerts){
			$files[] = array(
				'filename' => $file,
				'alerts' => $alerts,
				);
		}

		$github_url = "https://github.com/{$this->github_project_owner}/{$repo->getName()}/pull/{$pull->getNumber()}";

		return array(
			'pull' => $pull,
			'repo' => $repo,
			'github_url' => $github_url,
			'alerts' => $nonfile_alerts,
			'files' => $files,
			);
	}

	public function executeNew()
	{
		$params = array(
			);
		return $this->build($params);
	}

	public function executeAdd()
	{
		$name = mfwRequest::get('name');
		$project = mfwRequest::get('project');
		if(!$name || !$project){
			return $this->buildErrorPage(
				"invalid argument (name=$name, project=$project)",
				"/pulls/add",
				'return');
		}

		$repo = new Repository(array(
			'name' => $name,
			'project' => $project,
			));
		$repo->save();

		return $this->redirect("/pulls/index");
	}

}

