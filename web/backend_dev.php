<?php

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'backend');
define('SF_ENVIRONMENT', 'dev');
define('SF_DEBUG',       true);

require SF_ROOT_DIR . '/apps/backend/lib/kint/Kint.class.php';
Kint::enabled(true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);
sfConfig::set('sf_web_debug', false);

sfContext::getInstance()->getController()->dispatch();
