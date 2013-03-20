<?php
require_once(dirname(__FILE__).'/actions.php');
require_once(APP_ROOT.'/model/Github.php');

class viewActions extends pullsActions {

	public function executeView()
	{
		$pullid = (int)mfwRequest::get('pull_id');
		$pull = PullRequestDb::retrieveByPK($pullid);
		if(!$pull){
			return $this->buildErrorPage("pull request no found (id=$pullid)");
		}

		$repo = $this->repolist[$pull->getRepoId()];

		$rawpull = Github::getSinglePullRequest($repo->getName(),$pull->getNumber());
		$comments = Github::getPullRequestComments($repo->getName(),$pull->getNumber());
		$files = Github::getPullRequestFiles($repo->getName(),$pull->getNumber());

		$alerts = PullRequestAlertDb::selectByPullRequest($pull);
		$filters = FilterDb::selectAll();
		$alert_filters = array();
		foreach($alerts as $a){
			$alert_filters[$a->getFileName()][] = $filters[$a->getFilterId()];
		}

		$params = array(
			'rawpull' => $rawpull,
			'comments' => $comments,
			'files' => $files,
			'pull' => $pull,
			'repo' => $repo,
			'alert_filters' => $alert_filters,
			);
		return $this->build($params);
	}

}
