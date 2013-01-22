<?php

class mfwDBIBase {

	/**
	 * make prepared statement.
	 * @param[in] $sql 実行するSQL
	 * @param[in] $con DB connection (PDO)
	 * @return PDOStatement
	 */
	public static function prepare($sql, PDO $con=null)
	{
		if(!$con){
			$con = mfwDBConnection::getPDO();
		}
		return $con->prepare($sql);
	}

	/**
	 * SQL実行して、実行後のPDOStatementを返す.
	 * @param[in] $sql  実行するSQL文
	 * @param[in] $bind バインドする配列
	 * @param[in] $con  DB connection (PDO)
	 * @return PDOStatement
	 */
	public static function query($sql, $bind=array(), PDO $con=null)
	{
		$stmt = static::prepare($sql,$con);
		$stmt->execute($bind);
		return $stmt;
	}

	/**
	 * SQL実行して、先頭一行を取得する.
	 * @param[in] $sql  実行するSQL文
	 * @param[in] $bind バインドする配列
	 * @param[in] $con  DB connection (PDO)
	 * @return 行の連想配列
	 */
	public static function getRow($sql, $bind=array(), PDO $con=null)
	{
		$query = static::query($sql,$bind,$con);
		return $query->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * SQL実行して、多次元連想配列で取得する.
	 * @param[in] $sql 実行するSQL文
	 * @param[in] $bind バインドする配列
	 * @param[in] $con DB connection
	 * @return 行の連想配列の配列
	 */
	public static function getAll($sql, $bind=array(), PDO $con=null)
	{
		$query = static::query($sql,$bind,$con);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * SQL実行して、先頭行の先頭カラムの値のみを取得.
	 * @param[in] $sql  実行するSQL
	 * @param[in] $bind バインドする配列
	 * @param[in] $con  DB connection (PDO)
	 * @return カラムの値
	 */
	public static function getOne($sql, $bind=array(), PDO $con=null)
	{
		$query = static::query($sql,$bind,$con);
		return $query->fetchColumn(0);
	}
}
