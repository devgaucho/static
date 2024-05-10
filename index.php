<?php
/**
 * Site: guest
 * Date: 08/05/24
 * Time: 18:55
 */
require 'vendor/autoload.php';
use src\Utils;
$Utils=new Utils();
$Utils->env('.env');
$Utils->showErrors($_ENV['SHOW_ERRORS']);
if(!$Utils->isCli()){
	$Utils->router();
}