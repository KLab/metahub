<?php

/**
 * Row object for 'repository' table.
 */
class Repository extends mfwObject {
	const DB_CLASS = 'RepositoryDb';

	protected $cursor=null;

	public function getName(){
		return $this->value('name');
	}
	public function getProject(){
		return $this->value('project');
	}


	public function getCursor()
	{
		if(is_null($this->cursor)){
			$sql = "select number from repository_cursor where repo_id=?";
			$this->cursor = (int)mfwDBIBase::getOne($sql,array($this->getId()));
		}
		return $this->cursor;
	}

	public function update($name,$project,$con=null)
	{
		$this->row['name'] = $name;
		$this->row['project'] = $project;
		$this->save($con);
	}
}

/**
 * Set of Repository objects.
 */
class RepositorySet extends mfwObjectSet {
	public static function hypostatize(Array $row=array())
	{
		return new Repository($row);
	}

	public function sortByName()
	{
		$this->sort(function($a,$b){return strcmp($a['name'],$b['name']);});
	}
}

/**
 * database accessor for 'repository' table.
 */
class RepositoryDb extends mfwObjectDb {
	const TABLE_NAME = 'repository';
	const SET_CLASS = 'RepositorySet';

	public static function selectSetByNames($names)
	{
		$bind = array();
		$pf = self::makeInPlaceholder($names,$bind,'name');
		$query = "WHERE name IN ($pf)";
		return self::selectSet($query,$bind);
	}

	public static function updateCursor($repo_id,$cursor,$con=null)
	{
		$cursor_table = 'repository_cursor';
		$sql = "SELECT * FROM $cursor_table WHERE repo_id=?";
		$row = mfwDBIBase::getRow($sql,array($repo_id),$con);

		if(empty($row)){
			$sql = "INSERT INTO $cursor_table (repo_id,number) VALUES (:repo_id,:number) ON DUPLICATE KEY UPDATE number=:number";
		}
		else{
			$sql = "UPDATE $cursor_table SET number=:number WHERE repo_id=:repo_id";
		}
		$bind = array(
			':repo_id' => $repo_id,
			':number' => $cursor,
			);
		return mfwDBIBase::query($sql,$bind,$con);
	}

}

