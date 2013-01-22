<?php

/**
 * IDカラムを持つテーブルのレコードのオブジェクト.
 *
 * 派生クラスでは次の定数を定義する。
 * - DB_CLASS: オブジェクトに紐づくDBアクセサクラス名
 */
abstract class mfwObject {

	protected $row;

	/**
	 * コンストラクタ.
	 * @param[in] $rows 初期化に使う連想配列の配列.
	 */
	public function __construct(Array $row=array())
	{
		$this->fromArray($row);
	}

	/**
	 * 連想配列として取得.
	 */
	public function toArray()
	{
		return $this->row;
	}

	/**
	 * 連想配列で初期化.
	 */
	public function fromArray(Array $row)
	{
		$this->row = $row;
		return $this;
	}

	/**
	 * keyカラムの値.
	 * 定義されていない時はNULL.
	 */
	protected function value($key){
		return isset($this->row[$key])? $this->row[$key]: null;
	}
	/**
	 * IDカラムの値.
	 */
	public function getId(){
		return $this->value('id');
	}

	/**
	 * DBへ保存.
	 * idが定義されているときはupdate, 無いときはinsert.
	 * idを指定したinsertをする場合は insert() を使う.
	 */
	public function save($con=null)
	{
		if($this->getId()!==null){
			$db = static::DB_CLASS;
			return $db::update($this,$con);
		}
		return $this->insert($con);
	}

	/**
	 * DBへのinsert.
	 * idを設定する.
	 */
	public function insert($con=null)
	{
		$db = static::DB_CLASS;
		$id = $db::insert($this,$con);
		$this->row['id'] = $id;
		return true;
	}

	/**
	 * DBから削除.
	 */
	public function delete($con=null)
	{
		if($this->getId()===null){
			return false;
		}
		$db = static::DB_CLASS;
		return $db::delete($this,$con);
	}

}

