<?php
require_once(dirname(__FILE__) . '/class/myAuthClass.php');
require_once(dirname(__FILE__) . '/class/myDbClass.php');
require_once(dirname(__FILE__) . '/lib/mymoviesphp.lib.php');

$authorized = myAuthClass::is_auth();

if ($authorized == true) {
    include 'main.inc.php';
} else {
    include 'login.php';
}