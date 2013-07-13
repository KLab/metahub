<?php
require_once(__DIR__.'/actions.php');
require_once(APP_ROOT.'/model/Filter.php');

class filtersAction extends apiActions
{
	public function executeFilters()
	{
		$method = mfwRequest::method();
		switch($method){
		case 'GET':
			return $this->getFilters();
		default:
			return $this->jsonErrorResponse(
				"$method is not allowed method.",
				self::CODE_405_METHODNOTALLOWED);
		}
	}

	protected function getFilters()
	{
		$filters = FilterDb::selectEnable();

		if(mfwRequest::has('target')){
			$target = mfwRequest::get('target');
			$filters = $filters->filter(
				function ($f) use($target){
					return preg_match("/^{$target}(?::|\$)/",$f['target']);
				});
		}

		$filters_arr = array();
		foreach($filters as $f){
			$filters_arr[] = $f->toArray();
		}
		return $this->jsonResponse($filters_arr);
	}
}
