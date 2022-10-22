<?php

use Service\Crud;

class Controller
{

    public function __construct()
    {
        $this->view = new View();
        $this->crud = new Crud();
    }

    public function doHome()
    {
        $this->view->render(Routes::$home, ['data' => Auth::authUser()]);
    }

    public function doRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            return $this->view->render(Routes::$register);
        } else {
            $dados = $_POST;

            $errors = $this->crud->create($dados);
            if (empty($errors)) {
                $url = APP_URL . "?page=mail-validation&token=";
                $url .= ssl_crypt($dados['person']['email']);

                send_mail($dados['person']['email'], "Ative a conta", $url);
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dados = $_POST;
            if (Auth::authentication($dados['person']['email'], $dados['person']['password'])) {
                header("Location: /?page=home");
                exit;
            } else {
                $messages['success'] = "Usuário ou/e senha incorretos";
            }
        } else {
            switch ($_GET['from']) {
                case 'register':
                    $messages['success'] = "Você ainda precisa confirmar o email!";
                    break;
            }
        }
        return $this->view->render(Routes::$login, $messages);
    }

    public function doDelete()
    {
        if($this->crud->deleteAccount()){
            return $this->view->render(Routes::$logout);   
        }
        
        return $this->view->render(Routes::$login);   
    }

    public function doLogout()
    {
        if(Auth::logout()){
            return $this->view->render(Routes::$login);
        }
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
