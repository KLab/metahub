<?php
/**
 * @file
 * Command line option parser for php.
 *
 * A simple command line option parser inspired from powerful tool like
 * Console_CommandLine, without complicated functions.
 *
 * This program is designed to work by single file and does not depend on
 * other libraries. There is only one thing required to use this parser
 * is include/require this file.
 *
 * @author    Daisuke MAKIUCHI <d.makiuchi@gmail.com>
 * @copyright 2012 Daisuke MAKIUCHI
 * @license   MIT License http://opensource.org/licenses/mit-license.php
 */

/**
 * command line parser class.
 *
 * Usage:
 * <code>
 * // create parser instance.
 * $parser = new Optionparse(array(
 *     'name'        => 'yourprogram',
 *     'version'     => '1.0.0',
 *     'description' => 'Description of your program',
 *     'arguments'   => 'argument list',
 *     ));
 *
 * // exsample for the option which has associated argument.
 * // the attribute 'help_name' is required.
 * // these styles are supported:
 * //   -p/path/to/dir -p=/path/to/dir -p /path/to/dir
 * //   --path=/path/to/dir --path /path/to/dir
 * $parser->addOption('path',array(
 *     'short_name'  => '-p',
 *     'long_name'   => '--path',
 *     'description' => 'path to the dir',
 *     'default'     => '/tmp',
 *     'help_name'   => '/path/to/dir',
 *     ));
 *
 * // example for the boolean option.
 * // this optoin cannot set string parameter.
 * // these are declared as true:
 * //   -v --verbose -vaaa -v=aaa --verboss=aaa
 * // these are declared as false:
 * //   -v- --verbose= -v=- --verbose=-
 * $parser->addOption('verbose',array(
 *     'short_name'  => '-v',
 *     'long_name'   => '--verbose',
 *     'description' => 'turn on verbose output',
 *     ));
 *
 * // this parser does not have default options.
 * // you need to add help/version, unlike Console_CommandLine/optparse.
 * $parser->addOption('help',array(
 *     'short_name'  => '-h',
 *     'long_name'   => '--help',
 *     'description' => 'show this help message',
 *     ));
 *
 * // parse $argv.
 * $options = $parser->parse();
 *
 * // this parser does not display help message automatically.
 * if($options['help']){
 *     $parser->displayUsage();
 *     exit(0);
 * }
 *
 * // non-option arguments are arranged into '_arguments_'.
 * foreach($options['_arguments_'] as $arg){
 *     echo $arg, "\n";
 * }
 * </code>
 */
class Optionparse {

	protected $name = '';
	protected $version = '';
	protected $description = '';
	protected $arguments = '';
	protected $opts = array();

	protected $short = array();
	protected $long = array();
	protected $withp = array();
	protected $defaults = array();

	/**
	 * Constructor.
	 * @param[in] info  attributes of the program.
	 *   - name:    program name. The default is argv[0].
	 *   - version: version string.
	 *   - description: description of the program.
	 *   - arguments:   argument names shown in usage.
	 */
	public function __construct($info=array())
	{
		global $argv;
		$this->name = isset($info['name'])? $info['name'] : $argv[0];
		if(isset($info['version'])){
			$this->version = $info['version'];
		}
		if(isset($info['description'])){
			$this->description = $info['description'];
		}
		if(isset($info['arguments'])){
			$this->arguments = $info['arguments'];
		}
	}

	/**
	 * Adds an option to the command line parser and returns it.
	 * @param[in] name  option name.
	 * @param[in] params option attribute.
	 *   - short_name: short name of the option. (ex. -n)
	 *   - long_name:  long name of the option. (ex. --name)
	 *   - description: description display in the help message.
	 *   - default: the default value.
	 *   - help_name: display in the help line.
	 *                this attribute must be set if the option
	 *                has indivisual parameter.
	 *                when this attribute was omitted,
	 *                the value will be set as boolean.
	 */
	public function addOption($name, $params=array())
	{
		if(isset($params['short_name'])){
			$this->short[$params['short_name']] = $name;
		}
		if(isset($params['long_name'])){
			$this->long[$params['long_name']] = $name;
		}
		$this->withp[$name] = isset($params['help_name']);
		$this->defaults[$name] = (isset($params['default'])) ? $params['default'] : false;
		$this->opts[] = $params;
	}

	/**
	 * Display the usage help message.
	 */
	public function displayUsage()
	{
		if($this->description){
			echo $this->description, "\n\n";
		}
		echo "Usage:\n   {$this->name}";
		if(!empty($this->opts)){
			echo " [options]";
		}
		if($this->arguments){
			echo " {$this->arguments}";
		}
		echo "\n";
		if(empty($this->opts)){
			return;
		}

		echo "\nOptions:\n";
		$w = 0; // indent width.
		foreach($this->opts as $o){
			$w = min(24,max($w,strlen($this->helpline($o))));
		}
		$pad = str_pad('',$w);
		foreach($this->opts as $o){
			$h = str_pad($this->helpline($o),$w);
			echo '  ', $h;
			if(isset($o['description'])){
				if(strlen($h)>$w){
					echo "\n  ", $pad;
				}
				echo '  ', $o['description'];
			}
			echo "\n";
		}
	}

	protected function helpline($o)
	{
		$s = isset($o['short_name'])? $o['short_name'] : '';
		$l = isset($o['long_name'])? $o['long_name'] : '';
		if(isset($o['help_name'])){
			if($s){ // -s help_name
				$s = "{$s} {$o['help_name']}";
			}
			if($l){ // --long=help_name
				$l = "{$l}={$o['help_name']}";
			}
		}
		return ($s&&$l)? "{$s}, {$l}" : "{$s}{$l}";
	}

	/**
	 * Display version.
	 */
	public function displayVersion()
	{
		echo $this->version, "\n";
	}

	/**
	 * Parse the command line arguments.
	 * @param[in] args  arguments used instead of $argv.
	 * @return associative array of the parsed arguments.
	 *         the keys are the 'name' specified addOption method.
	 *         non-option arguments are arranged into '_arguments_'.
	 */
	public function parse($args=false)
	{
		if($args===false){
			global $argv;
			$args = $argv;
			array_shift($args);
		}
		$argc = count($args);

		$ret = array();
		$arguments = array();

		for($i=0;$i<$argc;++$i){
			if($this->isShortName($args[$i])){
				list($name,$value) = $this->parseShortName($args[$i]);
			}
			elseif($this->isLongName($args[$i])){
				list($name,$value) = $this->parseLongName($args[$i]);
			}
			else{
				$arguments[] = $args[$i];
				continue;
			}

			if($this->withp[$name]){
				$ret[$name] = '';
				if($value!==null){
					$ret[$name] = $value;
				}
				elseif($i<$argc-1 && !$this->isOptionName($args[$i+1])){
					$ret[$name] = $args[++$i];
				}
			}
			else{
				$ret[$name] = ($value!=='' && $value!=='-');
			}
		}
		foreach($this->defaults as $name => $def){
			if(!isset($ret[$name])){
				$ret[$name] = $def;
			}
		}
		$ret['_arguments_'] = $arguments;
		return $ret;
	}

	protected function isShortName($arg){
		return isset($this->short[substr($arg,0,2)]);
	}
	protected function isLongName($arg){
		$list = explode('=',$arg);
		return isset($this->long[$list[0]]);
	}
	protected function isOptionName($arg){
		return ($this->isShortName($arg) || $this->isLongName($arg));
	}

	protected function parseShortName($arg){
		$name = $this->short[substr($arg,0,2)];
		$value = null;
		if(strlen($arg)>2){
			$value = (string)substr($arg,($arg[2]=='=')?3:2);
		}
		return array($name,$value);
	}
	protected function parseLongName($arg){
		$list = explode('=',$arg);
		$name = $this->long[$list[0]];
		$value = isset($list[1]) ? $list[1] : null;
		return array($name,$value);
	}

}
