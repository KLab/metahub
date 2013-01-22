<?php
/*!@file
 * mfwObjectの集合.
 */

/**
 * mfwObjectの集合クラス.
 * 同じIDのオブジェクトは重複できない.
 *
 * 派生クラスでは次のメソッドを実装する。
 * - hypostatize(): 1行を行オブジェクトに実体化する
 *
 * ArrayAccess Interface
 *  - $set[id] でアクセスできる。キーは必ずID。
 *  - ID無しのオブジェクトが追加された場合、仮IDキーになる('-undefId-XX')
 *  - $set[] = object では、既存の同じIDの項目は削除され、末尾に新たに項目を追加する。
 *  - $set[id] = object とすると[id]の項目は削除され、同じ場所に新たな項目を挿入する。
 *   既に同じIDの項目がある場合、それも削除される。
 *
 * Iterator Interface
 *  - foreach($set as $id => $obj) のように扱える。
 *  - foreachのループ内で$setを書き換えると予期しない結果になりうる。
 */
abstract class mfwObjectSet implements ArrayAccess,Iterator {

	protected $rows; ///< 連想配列の配列. @see mfwObject::toArray()
	protected $obj_cache; ///< hypostatizeの結果をキャッシュ.

	protected $keys = array(); ///< for iterator interface
	protected $position = 0; ///< for iterator interface

	protected $undefined_key_num = 0;

	protected function makeOffset(Array $row){
		if(isset($row['id'])){
			return $row['id'];
		}
		return '-undefId-'.(++$this->undefined_key_num);
	}

	/**
	 * コンストラクタ.
	 * 内部配列のキーはID.
	 * @param array $rows 初期化に使う連想配列の配列.
	 *                    select * tableの結果をそのまま使う.
	 */
	public function __construct(Array $rows=array())
	{
		$this->rows = array();
		foreach($rows as $r){
			$offset = $this->makeOffset($r);
			$this->rows[$offset] = $r;
		}
	}

	/**
	 * 行オブジェクトを実体化する.
	 * @param array $row 1レコードの連想配列.
	 */
	abstract public static function hypostatize(Array $row=array());

	/*!
	 * @name ArrayAccess Interface
	 * @{
	 */
	public function offsetExists($offset)
	{
		return isset($this->rows[$offset]);
	}
	public function offsetGet($offset)
	{
		if(!isset($this->rows[$offset])){
			return null;
		}
		if(!isset($this->obj_cache[$offset])){
			$this->obj_cache[$offset] = static::hypostatize($this->rows[$offset]);
		}
		return $this->obj_cache[$offset];
	}
	public function offsetSet($offset,$value)
	{
		$row = $value->toArray();
		$newoffset = $this->makeOffset($row);

		if($offset===null || !isset($this->rows[$offset])){ // 末尾に追加
			if(isset($this->rows[$newoffset])){ // 同じIDがあったら削除
				$this->offsetUnset($newoffset);
			}
			$this->rows[$newoffset] = $row;
			$this->obj_cache[$newoffset] = $value;
		}
		else{
			// $offsetの位置を$valueで置き換え
			// 添字は$newoffsetになる (ちょっと直感的じゃない動作)
			unset($this->obj_cache[$offset]);
			$this->obj_cache[$newoffset] = $value;

			$new = array();
			foreach($this->rows as $k=>$v){
				if($k==$offset){
					$new[$newoffset] = $row;
				}
				else{
					$new[$k] = $v;
				}
			}
			$this->rows = $new;
		}
	}
	public function offsetUnset($offset)
	{
		unset($this->rows[$offset]);
		unset($this->obj_cache[$offset]);
	}
	/*! @}*/

	/*!
	 * @name Iterator Interface
	 * @{
	 */
	public function rewind()
	{
		$this->keys = array_keys($this->rows);
		$this->position = 0;
	}
	public function current()
	{
		$key = $this->keys[$this->position];
		return $this[$key];
	}
	public function key()
	{
		return $this->keys[$this->position];
	}
	public function next()
	{
		++$this->position;
	}
	public function valid()
	{
		return array_key_exists($this->position,$this->keys);
	}
	/*! @}*/


	/**
	 * 含まれる要素数.
	 */
	public function count()
	{
		return count($this->rows);
	}

	/**
	 * 全要素をオブジェクトとして取得.
	 */
	public function getAll()
	{
		$ret = array();
		foreach($this->rows as $k=>$r){
			$ret[] = $this[$k];
		}
		return $ret;
	}

	/**
	 * 全要素を連想配列の配列として取得.
	 */
	public function toArray()
	{
		return $this->rows;
	}

	/**
	 * あるカラムの値の配列を取得.
	 */
	public function getColumnArray($column)
	{
		$ret = array();
		foreach($this->rows as $r){
			$ret[] = isset($r[$column])? $r[$column]: null;
		}
		return $ret;
	}

	/**
	 * key=>valueを持つ要素のoffset.
	 * 複数ある場合は最初に見つかったものを返す.
	 *
	 * @return id. $set[id]でアクセスできる.
	 *         存在しない時はnull. id=0 と区別すること.
	 *
	 * @note
	 *  このメソッドは全件検索する。
	 *  パフォーマンスが必要な検索は、派生クラスで索引やメソッドを用意する.
	 */
	public function searchId($key,$value)
	{
		foreach($this->rows as $id => $r){
			if($r[$key] == $value){
				return $id;
			}
		}
		return null;
	}

	/**
	 * ソートする (副作用).
	 * @param callback $func 比較関数.
	 */
	public function sort($func)
	{
		uasort($this->rows,$func);
	}

	/**
	 * 抽出. (array_filter)
	 * @param callback $func フィルタ関数. 引数は$rowsの要素.
	 * return ObjectSet (新たに作られる).
	 */
	public function filter($func)
	{
		$ret = new static;
		$ret->rows = array_filter($this->rows,$func);
		return $ret;
	}

	/**
	 * 切り出す (array_slice).
	 * @param integer $offset 開始位置 (0 origin)
	 * @param integer $length 要素数
	 * @return ObjectSet (新たに作られる).
	 */
	public function slice($offset,$length)
	{
		$ret = new static;
		$ret->rows = array_slice($this->rows,$offset,$length,true);
		return $ret;
	}

}

