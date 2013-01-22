#!/usr/bin/php
<?php
require realpath(dirname(__FILE__).'/../../initialize.php');
require APP_ROOT.'/core/vendor/optionparse.php';

$parser = new Optionparse(array(
		'description' => 'generate template for model classes.',
		'arguments' => 'table_name',
		));
$parser->addOption('help',array(
		'short_name' => '-h',
		'long_name' => '--help',
		'description' => 'Show this message and exit.',
		));
$opts = $parser->parse();
if($opts['help']){
	$parser->displayUsage();
	exit(0);
}

if(empty($opts['_arguments_'])){
	fputs(STDERR,"{$argv[0]}: missing table_name\n");
	exit(-1);
}

$table = $opts['_arguments_'][0];

$class = '';
foreach(explode('_',$table) as $s){
	$class .= ucwords($s);
}

echo "<?php\n";
?>

/**
 * Row object for '<?=$table?>' table.
 */
class <?=$class?> extends mfwObject {
	const DB_CLASS = '<?=$class?>Db';
}

/**
 * Set of <?=$class?> objects.
 */
class <?=$class?>Set extends mfwObjectSet {
	public static function hypostatize(Array $row=array())
	{
		return new <?=$class?>($row);
	}
}

/**
 * database accessor for '<?=$table?>' table.
 */
class <?=$class?>Db extends mfwObjectDb {
	const TABLE_NAME = '<?=$table?>';
	const SET_CLASS = '<?=$class?>Set';
}

