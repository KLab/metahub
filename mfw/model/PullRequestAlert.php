<?php

/**
 * Row object for 'pull_request_alert' table.
 */
class PullRequestAlert extends mfwObject {
	const DB_CLASS = 'PullRequestAlertDb';

	public function getFileName(){
		return $this->value('filename');
	}
	public function getFilterId(){
		return $this->value('filter_id');
	}
}

/**
 * Set of PullRequestAlert objects.
 */
class PullRequestAlertSet extends mfwObjectSet {
	public static function hypostatize(Array $row=array())
	{
		return new PullRequestAlert($row);
	}
}

/**
 * database accessor for 'pull_request_alert' table.
 */
class PullRequestAlertDb extends mfwObjectDb {
	const TABLE_NAME = 'pull_request_alert';
	const SET_CLASS = 'PullRequestAlertSet';


	public static function selectByPullRequest(PullRequest $pull,$con=null)
	{
		$query = 'WHERE repo_id = :repo_id AND number = :number';
		$bind = array(
			':repo_id' => $pull->getRepoId(),
			':number' => $pull->getNumber(),
			);
		return self::selectSet($query,$bind,$con);
	}

}

