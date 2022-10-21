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

            $url = APP_URL . "?page=mail-validation&token=";
            $url .= ssl_crypt($user->email);

            $errors = $this->crud->create($user);

            if (empty($errors)) {
                send_mail($user->email, "Ative a conta", $url);
                header("Location: /?page=login&from=register");
                exit;
            } else {
                $messages = ['validation_errors' => $errors];
                $this->view->render(Routes::$register, $messages);
            }
        }
    }

    public function doLogin()
    {
        $messages = [];
        switch (@$_GET['from']) {
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

    public function doValidation()
    {
        $messages = [];
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $token = $_GET['token'];

            if (!$this->crud->confirmEmail(str_replace('"', '', ssl_decrypt($token)))) {
                $messages['success'] = "Você ainda precisa confirmar o email!";
            }
            
            return $this->view->render(Routes::$login, $messages);
        }
    }
}
