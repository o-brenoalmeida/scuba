<?php

class Routes {
    public static $login = 'login';
    public static $register = 'register';
    public static $doNotFound = 'not_found';
}

$page = ($_GET['page']);
$controller = new Controller();

switch ($page) {
    case 'register':
        $controller->doRegister();
        break;
    case 'not_found':
        $controller->doNotFound();
        break;
    default:
        $controller->doLogin();
        break;
}
