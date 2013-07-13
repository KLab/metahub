<?php

class apiActions extends MainActions {

	const CODE_200_OK = '200 OK';
	const CODE_405_METHODNOTALLOWED = '405 Method Not Allowed';
	const CODE_500_INTERNALSERVERERROR = '500 Internal Server Error';

	protected function jsonResponse($params,$code=self::CODE_200_OK)
	{
		return array(
			array("HTTP/1.1 $code"),
			json_encode($params));
	}

	protected function jsonErrorResponse($message,$code=self::CODE_500_INTERNALSERVERERROR)
	{
		$response = array(
			'message' => $message,
			);
		return array(
			array("HTTP/1.1 $code"),
			json_encode($response));
	}

}

