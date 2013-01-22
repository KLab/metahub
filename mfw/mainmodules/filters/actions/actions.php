<?php
require_once(APP_ROOT.'/model/Filter.php');

class filtersActions extends MainActions {

	public function executeIndex()
	{
		$filters = FilterDB::selectAll();

		$params = array(
			'filters' => $filters,
			);
		return $this->build($params);
	}

	public function executeNew()
	{
		return $this->build();
	}

	public function executeEdit()
	{
		$fid = (int)mfwRequest::get('id');
		$filter = FilterDB::retrieveByPK($fid);

		$params = array(
			'filter' => $filter,
			);
		return $this->build($params);
	}

	public function executeSave()
	{
		$arr = array();

		$fid = (int)mfwRequest::get('id');
		if($fid){
			$arr['id'] = $fid;
		}

		$arr['enable'] = (bool)mfwRequest::get('enable',true);
		$arr['name'] = mfwRequest::get('name');
		$arr['target'] = mfwRequest::get('target');
		$arr['pattern'] = mfwRequest::get('pattern');

		$filter = new Filter($arr);
		$filter->save();

		return $this->redirect('/filters/index');
	}

}

