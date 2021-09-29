<?php error_reporting (E_ALL ^ E_NOTICE); ?>
<?php
function escape($string){
	return htmlentities($string,ENT_QUOTES,'UTF-8');
}