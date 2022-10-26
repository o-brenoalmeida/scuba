<?php

class Routes
{
    public static $login = 'login';
    public static $register = 'register';
    public static $doNotFound = 'not_found';
    public static $home = 'home';
    public static $logout = 'logout';
    public static $delete = 'delete-account';
    public static $forgetPassword = 'forget-password';
    public static $changePassword = 'change-password';

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
            'forget-password' => $router->controller->doForgetPassword(),
            'change-password' => $router->controller->doChangePassword(),
            default => $router->controller->doLogin()
        };
    }

    public static function auth_routes()
    {
        $router = new Routes();

        match ($router->page) {
            'logout' => $router->controller->doLogout(),
            'delete-account' => $router->controller->doDelete(),
            'not_found' => $router->controller->doNotFound(),
            default => $router->controller->doHome()
        };
    }
    
}