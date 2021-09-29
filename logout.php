<?php error_reporting (E_ALL ^ E_NOTICE); ?>
<?php
require_once 'php/core/init.php';
$user = new User();
$user->logout();
Redirect::to('index.php');
