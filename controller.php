<?php

use Model\User;
use Service\Crud;

class Controller
{

    public function __construct()
    {
        $this->view = new View();
        $this->crud = new Crud();
    }

    public function doRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            http_response_code(200);
            return $this->view->render(Routes::$register);
        } else {
            $dados = $_POST;

            $user = new User();
            $user->nome = $dados['person']['name'];
            $user->email = $dados['person']['email'];
            $user->senha = $dados['person']['password'];

            $this->crud->create($user);

            header("Location: {$this->doLogin()}"); 
            exit;
        }
    }

    public function doLogin()
    {
        http_response_code(200);
        return $this->view->render(Routes::$login);
    }

    public function doNotFound()
    {
        http_response_code(404);
        return $this->view->render(Routes::$doNotFound);
    }
}
