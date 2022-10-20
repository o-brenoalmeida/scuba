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
            return $this->view->render(Routes::$register);
        } else {
            $dados = $_POST;

            $user = new User();
            $user->name = $dados['person']['name'];
            $user->email = $dados['person']['email'];
            $user->password = $dados['person']['password'];
            $user->passwordConfirmation = $dados['person']['password-confirm'];

            $errors = $this->crud->create($user);

            if (empty($errors)) {
                header("Location: /?page=login&from=register");
                exit;
            }else{
                $messages = ['validation_errors' => $errors];
                $this->view->render(Routes::$register, $messages);
            }
        }
    }

    public function doLogin()
    {
        $messages = [];
        switch ($_GET['from']) {
            case 'register':
                $messages['success'] = "Você ainda precisa confirmar o email!";
                break;
        }
        return $this->view->render(Routes::$login, $messages);
    }

    public function doNotFound()
    {
        return $this->view->render(Routes::$doNotFound);
    }
}
