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

                $this->crud->emailRegister($dados['person']['email']);

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
                $messages['validation_errors']['email'] = "Usuário ou/e senha incorretos";
            }
        } else {
            switch ($_GET['from']) {
                case 'register':
                    $messages['success'] = "Você ainda precisa confirmar o email!";
                    break;
            };
        }
        return $this->view->render(Routes::$login, $messages);
    }

    public function doDelete()
    {
        if ($this->crud->deleteAccount()) {
            return $this->view->render(Routes::$logout);
        }

        return $this->view->render(Routes::$login);
    }

    public function doLogout()
    {
        if (Auth::logout()) {
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

            if (!$this->crud->confirmEmail(ssl_decrypt($token))) {
                $messages['success'] = "Você ainda precisa confirmar o email!";
            }

            return $this->view->render(Routes::$login, $messages);
        }
    }

    public function doForgetPassword()
    {
        $messages = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->crud->forgetPassword($_POST['person']['email'])) {
                header("Location: /?page=change-password&from=forget-password");
                exit;
            }
            $messages['validation_errors']['email'] = 'E-mail informado não foi encontrado em nossa base.';
        }


        return $this->view->render(Routes::$forgetPassword, $messages);
    }

    public function doChangePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            $token = $_GET['token'];

            if ($this->crud->validateToken($token)) {
                return $this->view->render(Routes::$changePassword, ['data' => ['token' => $token]]);
            }

            return $this->view->render(Routes::$login, ['success' => 'Link expirado']);
        } else {
            $token = $_POST['token'];

            if ($this->crud->validateToken($token)) {
                $errors = $this->crud->updatePassword($_POST);

                if (empty($errors)) {

                    header("Location: /?page=login&from=change-password");
                    exit;
                } else {
                    $messages = ['validation_errors' => $errors];
                    return $this->view->render(Routes::$changePassword, array_merge(['data' => ['token' => $token]], $messages));
                }
            }
            die('a');
            return $this->view->render(Routes::$changePassword, ['data' => ['token' => $token]]);
        }
    }
}
