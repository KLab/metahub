<?php

$serverenv_config = array(

	'metahub_vm' => array(
		'database' => array(
			'authfile' => APP_ROOT.'/../dbauth/admin',
			'default_master' => 'mysql:dbname=metahub;host=localhost',
			),
		'http_proxy' => array(
			'host' => '127.0.0.1',
			'port' => 10080,
			),
		'memcache' => array(
			'host' => 'localhost',
			'port' => 11211,
			),
		),
	);

