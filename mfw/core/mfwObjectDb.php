<?php

/**
 * mfwObjectのDBアクセサ.
 *
 * 派生クラスでは次の定数を定義する。
 * - TABLE_NAME: DBのテーブル名
 * - SET_CLASS: オブジェクトに紐づくDBアクセサクラス名
 */
class mfwObjectDb {

	/**
	 * select single object.
	 * @return instance of OBJECT_CLASS.
	 */
	protected static function selectOne($query,$bind=array(),$con=null)
	{
		$table = static::TABLE_NAME;
		$sql = "SELECT * FROM `$table` $query";
		$row = mfwDBIBase::getRow($sql,$bind,$con);
		if(empty($row)){
			return null;
		}
		$class = static::SET_CLASS;
		return $class::hypostatize($row);
	}

	/**
	 * select object set.
	 * @return instance of SET_CLASS.
	 */
	protected static function selectSet($query,$bind=array(),$con=null)
	{
		$table = static::TABLE_NAME;
		$sql = "SELECT * FROM `$table` $query";
		$rows = mfwDBIBase::getAll($sql,$bind,$con);
		$class = static::SET_CLASS;
		return new $class($rows);
	}

	/**
	 * 全件取得.
	 * return mfwObjectSet
	 */
	public static function selectAll($con=null)
	{
		return static::selectSet('',array(),$con);
	}

	/**
	 * 単一IDでの取得.
	 * return mfwObject
	 */
	public static function retrieveByPK($id,$con=null)
	{
		return static::selectOne('WHERE `id`=?',array($id),$con);
	}

	/**
	 * 単一IDでの取得(select for update).
	 * return mfwObject
	 */
	public static function retrieveByPKForUpdate($id,$con=null)
	{
		return static::selectOne('WHERE `id`=? FOR UPDATE',array($id),$con);
	}

	/**
	 * 複数IDでの取得.
	 * return mfwObjectSet
	 */
	public static function retrieveByPKs(Array $ids,$con=null)
	{
		if(($c=count($ids))===0){
			$class = static::SET_CLASS;
			return new $class(array());
		}
		$q = implode(',',array_fill(0,$c,'?'));
		return static::selectSet("WHERE `id` IN ($q)",$ids,$con);
	}

	/**
	 * 複数IDでの取得(select for update).
	 * return mfwObjectSet
	 */
	public static function retrieveByPKsForUpdate(Array $ids,$con=null)
	{
		$table = static::TABLE_NAME;
		if(($c=count($ids))===0){
			$class = static::SET_CLASS;
			return new $class(array());
		}
		$q = implode(',',array_fill(0,$c,'?'));
		return static::selectSet("WHERE `id` IN ($q) FOR UPDATE",$ids,$con);
	}

	/**
	 * 単一オブジェクトのレコードをinsert.
	 */
	public static function insert(mfwObject $obj,$con=null)
	{
		$table = static::TABLE_NAME;
		$row = $obj->toArray();
		$columns = '`' . implode('`,`',array_keys($row)) . '`';
		$places = implode(',',array_fill(0,count($row),'?'));
		$sql = "INSERT INTO `$table` ($columns) VALUES ($places)";
		$bind = array_values($row);
		$con = $con ?: mfwDBConnection::getPDO();
		mfwDBIBase::query($sql,$bind,$con);
		return $con->lastInsertId();
	}

	/**
	 * オブジェクトに紐づくレコードのupdate.
	 */
	public static function update(mfwObject $obj,$con=null)
	{
		$table = static::TABLE_NAME;
		$sql = "UPDATE `$table` SET ";
		$row = $obj->toArray();
		$columns = array();
		$bind = array();
		foreach($row as $k=>$v){
			if($k != 'id'){
				$columns[] = "{$k}=:{$k}";
			}
			$bind[":{$k}"] = $v;
		}
		$sql .= implode(', ',$columns).' WHERE `id` = :id';
		return mfwDBIBase::query($sql,$bind,$con);
	}

	/**
	 * オブジェクトのレコードを削除.
	 */
	public static function delete(mfwObject $obj,$con=null)
	{
		$table = static::TABLE_NAME;
		$sql = "DELETE FROM `$table` WHERE `id` = ?";
		return mfwDBIBase::query($sql,array($obj->getId()),$con);
	}

	/**
	 * IN句のplaceholder, bind-parameterを構築.
	 * @param[in]  $list IN句の中身の配列
	 * @param[out] $bind bind-parameter受け取り口(配列の参照)
	 * @param[in]  $prefix placeholder名のprefix
	 * @return 構築されたIN句の中身の文字列.
	 *         "column IN ($return)"のように使う.
	 *         $listが空ならnull.
	 */
	protected static function makeInPlaceholder(Array $list,Array &$bind,$prefix='')
	{
		if(empty($list)){
			return null;
		}
		$i = 0;
		$ph = array();
		foreach($list as $l){
			$key = ":{$prefix}_$i";
			$ph[] = $key;
			$bind[$key] = $l;
			++$i;
		}
		return implode(',',$ph);
	}

}

