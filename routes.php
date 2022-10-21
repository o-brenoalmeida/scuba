<?php

class Routes {
    public static $login = 'login';
    public static $register = 'register';
    public static $doNotFound = 'not_found';
}

$page = ($_GET['page']);
$controller = new Controller();

match($page){
    'register' => $controller->doRegister(),
    'not_found' => $controller->doNotFound(),
    'mail-validation' => $controller->doValidation(),
    default => $controller->doLogin()
};