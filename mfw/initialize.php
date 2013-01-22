<?php

define('APP_ROOT',realpath(dirname(__FILE__)));

require_once APP_ROOT.'/core/mfwApplication.php';
require_once APP_ROOT.'/core/mfwServerEnv.php';
require_once APP_ROOT.'/core/mfwRequest.php';
require_once APP_ROOT.'/core/mfwModules.php';
require_once APP_ROOT.'/core/mfwActions.php';
require_once APP_ROOT.'/core/mfwTemplate.php';
require_once APP_ROOT.'/core/mfwSession.php';
require_once APP_ROOT.'/core/mfwMemcache.php';
require_once APP_ROOT.'/core/mfwApc.php';
require_once APP_ROOT.'/core/mfwDBConnection.php';
require_once APP_ROOT.'/core/mfwDBIBase.php';
require_once APP_ROOT.'/core/mfwObject.php';
require_once APP_ROOT.'/core/mfwObjectSet.php';
require_once APP_ROOT.'/core/mfwObjectDb.php';
require_once APP_ROOT.'/core/mfwHttp.php';
require_once APP_ROOT.'/core/mfwOAuth.php';

