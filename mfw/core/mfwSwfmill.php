<?php
/*!@file
 * swfmillコマンドのwrapper
 */

/**
 * swfmillコマンドのwrapperクラス
 */
class mfwSwfmill
{
	/**
	 * SWFからXMLに変換
	 * @param mixed $swf swfのバイナリ
	 * @param bool  $cp932 内部文字コード=shift_jis (default: false)
	 * @return XML文字列
	 */
	public static function swf2xml($swf,$cp932=false)
	{
		return static::execCommand('swf2xml',$swf,$cp932);
	}

	/**
	 * XMLからSWFに変換
	 * @param mixed $xml XML文字列
	 * @param bool  $cp932 内部文字コード=shift_jis (default: false)
	 * @return SWFバイナリ
	 */
	public static function xml2swf($xml,$cp932=false)
	{
		return static::execCommand('xml2swf',$xml,$cp932);
	}

	/**
	 * コマンド実行.
	 * @param string $command 'swf2xml' or 'xml2swf'
	 * @param string $data    SWF or XML datastream
	 * @param bool   $cp932   内部文字コード=shift_jis
	 * @return XML or SWF datastream
	 */
	protected function execCommand($command,$data,$cp932)
	{
		$swfmill = mfwServerEnv::swfmill();
		if(!$swfmill){
			throw new RuntimeException('no swfmill command in serverenv_config');
		}
		$opt = ($cp932)? '-e cp932': '';
		$cmd = "$swfmill $opt $command stdin stdout";

		$desc = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
			);
		$process = proc_open($cmd,$desc,$pipes);

		fwrite($pipes[0],$data);
		fclose($pipes[0]);

		$ret = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		$err = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		proc_close($process);

		if($err){
			error_log($err);
		}

		return $ret;
	}

}
