<?php

class Routes
{
    public static $login = 'login';
    public static $register = 'register';
    public static $doNotFound = 'not_found';
    public static $home = 'home';
    public static $logout = 'logout';

    public function __construct()
    {
        $this->page = $_GET['page'];
        $this->controller = new Controller();
    }

    public static function guest_routes()
    {
        $router = new Routes();
        match ($router->page) {
            'register' => $router->controller->doRegister(),
            'not_found' => $router->controller->doNotFound(),
            'mail-validation' => $router->controller->doValidation(),
            default => $router->controller->doLogin()
        };
    }

    public static function auth_routes()
    {
        $router = new Routes();

        match ($router->page) {
            'logout' => $router->controller->doLogout(),
            'not_found' => $router->controller->doNotFound(),
            default => $router->controller->doHome()
        };
    }
}