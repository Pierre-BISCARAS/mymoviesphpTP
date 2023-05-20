<?php

$action = GETPOST('action');

switch ($action) {
    case '':
    case 'delete':
        include(dirname(__FILE__) . '/delete.php');
        break;
    case 'list':
        include(dirname(__FILE__) . '/list.php');
        break;
    case 'add':
        include(dirname(__FILE__) . '/add.php');
        break;
    case 'edit':
        include(dirname(__FILE__) . '/edit.php');
        break;
    case 'tmdb':
        include(dirname(__FILE__) . '/tmdb.php');
        break;
    default:
        include(dirname(__FILE__) . '/notfound.php');
}
