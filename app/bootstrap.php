<?php

defined('_INDEX_EXEC') or die('Restricted access');

// starting session
session_start();

// load constants
require_once 'config/config.php';

// autoload core libraries
spl_autoload_register(function($classname){
	require_once 'libraries/' . $classname . '.php';
});

// load helper classes
require_once 'helpers/Misc.php';
require_once 'helpers/Crypt.php';
require_once 'helpers/Forms.php';
require_once 'helpers/Messages.php';
require_once 'helpers/Session.php';
require_once 'helpers/Validations.php';