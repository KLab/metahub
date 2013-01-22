<?php
require_once(APP_ROOT.'/model/PullRequestAlert.php');

/**
 * Row object for 'pull_request' table.
 */
class PullRequest extends mfwObject {
	const DB_CLASS = 'PullRequestDb';

	public function getRepoId(){
		return $this->value('repo_id');
	}
	public function getNumber(){
		return $this->value('number');
	}
	public function getTitle(){
		return $this->value('title');
	}
	public function getUser(){
		return $this->value('user');
	}
	public function getCreatedAt(){
		return $this->value('pull_created_at');
	}

	public function getAllAlerts()
	{
		return PullRequestAlertDb::selectByPullRequest($this);
	}

}

/**
 * Set of PullRequest objects.
 */
class PullRequestSet extends mfwObjectSet {
	public static function hypostatize(Array $row=array())
	{
		return new PullRequest($row);
	}
}

/**
 * database accessor for 'pull_request' table.
 */
class PullRequestDb extends mfwObjectDb {
	const TABLE_NAME = 'pull_request';
	const SET_CLASS = 'PullRequestSet';

	public static function totalCount()
	{
		$sql = 'SELECT count(*) FROM pull_request';
		return mfwDBIBase::getOne($sql);
	}

	public static function selectForPager($offset,$limit,$con=null)
	{
		$offset = (int)$offset;
		$limit = (int)$limit;

		$query = "ORDER BY id DESC LIMIT $limit OFFSET $offset";
		return self::selectSet($query,array(),$con);
	}

	public static function selectByRepository(Repository $repo,$offset,$limit,$con=null)
	{
		$offset = (int)$offset;
		$limit = (int)$limit;

		$query = "WHERE repo_id = ? ORDER BY id DESC LIMIT $limit OFFSET $offset";
		$bind = array($repo->getId());
		return self::selectSet($query,$bind,$con);
	}

	public static function totalCountForRepository(Repository $repo)
	{
		$sql = 'SELECT count(*) FROM pull_request WHERE repo_id=?';
		return mfwDBIBase::getOne($sql,array($repo->getId()));
	}


}

