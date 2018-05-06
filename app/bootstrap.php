<?php
// starting the session
session_start();

// load config
require_once 'config/config.php';

// loading all helper files
require_once 'helpers/control_messages.php';
require_once 'helpers/form_validations.php';
require_once 'helpers/misc.php';
require_once 'helpers/send_otp.php';
require_once 'helpers/encrypt_decrypt.php';
require_once 'helpers/misc.php';

// autoload core libraries
spl_autoload_register(function($classname){
	require_once 'libraries/' . $classname . '.php';
});