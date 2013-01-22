<?php

/**
 * Row object for 'filter' table.
 */
class Filter extends mfwObject {
	const DB_CLASS = 'FilterDb';

	const TARGET_FILENAME = 'file:filename';
	const TARGET_FILEPATCH = 'file:patch';
	const TARGET_USERNAME = 'pull:user:login';

	public function isEnable(){
		return (bool)$this->value('enable');
	}
	public function getName(){
		return $this->value('name');
	}
	public function getPattern(){
		return $this->value('pattern');
	}
	public function getTarget(){
		return $this->value('target');
	}

	public function test($info)
	{
		$target = explode(':',$this->getTarget());
		array_shift($target); // 先頭の 'file','pull'を捨てる

		while(!empty($target)){
			$t = array_shift($target);
			if(!isset($info[$t])){
				return false; // 該当項目なし
			}
			$info = $info[$t];
		}

		$pattern = "/{$this->getPattern()}/is";
		return preg_match($pattern,$info);
	}
}

/**
 * Set of Filter objects.
 */
class FilterSet extends mfwObjectSet {
	public static function hypostatize(Array $row=array())
	{
		return new Filter($row);
	}

	public function filterForFiles()
	{
		return $this->filter(function($f){return (strpos($f['target'],'file:')===0);});
	}
	public function filterForPulls()
	{
		return $this->filter(function($f){return (strpos($f['target'],'pull:')===0);});
	}
}

/**
 * database accessor for 'filter' table.
 */
class FilterDb extends mfwObjectDb {
	const TABLE_NAME = 'filter';
	const SET_CLASS = 'FilterSet';

	public static function selectEnable(PDO $con=null)
	{
		$query = 'WHERE enable = 1';
		return self::selectSet($query,array(),$con);
	}

}

