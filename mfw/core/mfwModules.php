<?php

abstract class mfwModules {

	abstract protected static function rootdir();

	protected static function getActionClassNames($module,$action)
	{
		$ret = array();

		$classname = "{$action}Action";
		$ret["{$classname}.php"] = $classname;

		if(preg_match('/^(.[^A-Z]*)([A-Z][^A-Z]*)?/',$action,$match)){
			if(isset($match[2])){
				$classname = "{$match[1]}{$match[2]}Actions";
				$ret["{$classname}.php"] = $classname;
			}
			if(isset($match[1])){
				$classname = "{$match[1]}Actions";
				$ret["{$classname}.php"] = $classname;
			}
		}

		$classname = "{$module}Actions";
		$ret['actions.php'] = $classname;

		return $ret;
	}

	protected static function getActionClass($module,$action)
	{
		$classnames = static::getActionClassNames($module,$action);

		$actionsdir = static::rootdir()."/{$module}/actions";
		$class = null;
		foreach($classnames as $filename => $classname){
			$path = "{$actionsdir}/{$filename}";
			if(file_exists($path)){
				require_once $path;
				$class = new $classname($module,$action);
				break;
			}
		}
		return $class;
	}

	protected static function executeAction($module,$action)
	{
		$class = static::getActionClass($module,$action);
		if($class===null){
			throw new Exception("action class not found: {$module}/{$action}");
		}

		if(($err=$class->initialize())){
			error_log("initializing action failed: {$module}/{$action}");
			return $err;
		}

		$funcname= 'execute'.ucfirst($action);
		if(!method_exists($class,$funcname)){
			$funcname = 'executeDefaultAction';
		}
		return $class->$funcname();
	}

}
