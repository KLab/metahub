<?php

class mfwTemplate
{
	public static $curobj; ///< 現在処理中のTemplate (Template用関数で使用)

	protected $templatefile;
	protected $layoutfile;
	protected $blockdir;
	protected $params;

	/**
	 * @param[in] name テンプレート名
	 * @param[in] basedir テンプレート、ブロックのベースディレクトリ
	 */
	public function __construct($name,$basedir='/data')
	{
		$this->templatedir = APP_ROOT."{$basedir}/templates";
		$this->blockdir = APP_ROOT."{$basedir}/blocks";

		$file = "{$this->templatedir}/{$name}.php";
		if(!file_exists($file)){
			throw new Exception("template file is not exists: {$file}");
		}
		$this->templatefile = $file;

		// default layout file (optional)
		$this->layout = "{$this->templatedir}/_layout.php";
	}

	/**
	 * レイアウトファイルの差し替え.
	 */
	public function setLayout($layout)
	{
		$file = "{$this->templatedir}/{$layout}.php";
		if(!file_exists($file)){
			throw new Exception("layout file is not exists: {$file}");
		}
		$this->layout = $file;
	}

	/**
	 * ページ構築.
	 */
	public function build($params=array())
	{
		$template = file_get_contents($this->templatefile);

		self::$curobj = $this;

		$this->params = $params;
		foreach($params as $k=>$v){
			$$k = $v;
		}

		ob_start();
		eval('?>'.$template);
		$contents = ob_get_clean();

		if($this->layout && file_exists($this->layout)){
			$layout = file_get_contents($this->layout);
			ob_start();
			eval('?>'.$layout);
			$contents = ob_get_clean();
		}

		return $contents;
	}

	public function blockFileName($name)
	{
		return "{$this->blockdir}/{$name}.php";
	}

	public function getParams()
	{
		return $this->params;
	}

}

/**
 * Template用関数: URL生成
 */
function url($query)
{
	return mfwRequest::makeUrl($query);
}

/**
 * Template用関数: ブロック読み込み
 */
function block($name,$additional_params=array())
{
	$t = mfwTemplate::$curobj;

	$file = $t->blockFileName($name);

	if(!file_exists($file)){
		return "block '{$name}' is not found.";
	}

	$params = array_merge($additional_params,$t->getParams());
	foreach($params as $k=>$v){
		$$k = $v;
	}
	ob_start();
	include $file;
	return ob_get_clean();
}
