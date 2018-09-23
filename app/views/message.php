<?php

defined('_INDEX_EXEC') or die('Restricted access');

if (!isset($message) || empty($message)) $message = 'Invalid URL';

require_once APPROOT . '/views/header.php';
echo '<p>' . $message . '</p>';
require_once APPROOT . '/views/footer.php';