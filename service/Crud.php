<?php

namespace Service;

use Model\User;
use Service\Validation;

class Crud
{
    protected $file;

    public function __construct()
    {
        $this->file = DATA_LOCATION;
    }

    public function create($dados)
    {
        $user = new User();
        $user->name = $dados['person']['name'];
        $user->email = $dados['person']['email'];
        $user->password = $dados['person']['password'];
        $user->passwordConfirm = $dados['person']['password-confirm'];

        $errors = $this->validate($user);

        if (empty($errors)) {
            $listUsers = $this->getUsersList();

            $user->password = sha1($user->password);
            $user->passwordConfirm = sha1($user->passwordConfirm);

            array_push($listUsers, $user);

            file_put_contents($this->file, json_encode($listUsers));
        }

        return $errors;
    }

    protected function validate(User $user, $noCheck = true)
    {
        $errors = [];

        if ($noCheck) {
            if (!Validation::uniqueInArray($user, $this->getUsersList(), 'email')) {
                $errors['email'] = "Email já cadastrado";
            }
        }

        if (!Validation::lengthPassword($user->password)) {
            $errors['password'] = "Senha não preenche os requisitos mínimos";
        }

        if (!Validation::confirmationPassword($user->password, $user->passwordConfirm)) {
            $errors['password-confirm'] = "Senhas não conferem";
        }

        return $errors;
    }

    public function getUsersList()
    {
        return json_decode(file_get_contents($this->file), true);
    }

    public function confirmEmail($email)
    {
        $listUsers = $this->getUsersList();
        $index = $this->getIndexByValue($listUsers, 'email', $email);
        if (is_int($index)) {
            $listUsers[$index]['mailValidation'] = true;
            file_put_contents($this->file, json_encode($listUsers));
            return true;
        }

        return false;
    }

    public function emailRegister($email)
    {
        $url = APP_URL . "?page=mail-validation&token=";
        $url .= ssl_crypt($email);

        send_mail($email, "Ative a conta", $url);
    }

    public function getIndexByValue($list, $field, $value)
    {
        foreach ($list as $item => $element) {
            if ($element[$field] == $value) {
                return (int) $item;
            }
        }
        return false;
    }

    public function deleteAccount()
    {
        $listUsers = $this->getUsersList();
        $index = $this->getIndexByValue($listUsers, 'email', $_SESSION['user']['email']);
        if (is_int($index)) {
            unset($listUsers[$index]);
            file_put_contents($this->file, json_encode($listUsers));
            return true;
        }
        return false;
    }

    public function forgetPassword($email)
    {
        $listUsers = $this->getUsersList();
        $index = $this->getIndexByValue($listUsers, 'email', $email);

        if (is_int($index)) {
            $url = APP_URL . "?page=change-password&token=";
            $url .= ssl_crypt($email . '&' . date('d-m-YH:i:s'));

            if (send_mail($email, utf8_decode("Redefinição de senha"), $url)) return true;
        }

        return false;
    }

    public function validateToken($token)
    {
        if ($data = ssl_decrypt($token)) {
            list($email, $date) = explode('&', $data);
            $today = new \DateTime('now');
            $interval = $today->diff(new \DateTime($date))->format('%d');
            if ($interval > 1) {
                return false;
            }
            return true;
        }
    }

    public function updatePassword($dados)
    {
        $token = $dados['token'];

        $data = ssl_decrypt($token);
        list($email, $date) = explode('&', $data);

        $listUsers = $this->getUsersList();
        $index = $this->getIndexByValue($listUsers, 'email', $email);

        $user = new User();
        $user->arrayToObject($listUsers[$index]);

        $user->password = $dados['person']['password'];
        $user->passwordConfirm = $dados['person']['password-confirm'];

        $errors = $this->validate($user, false);

        if (empty($errors)) {

            $user->password = sha1($user->password);
            $user->passwordConfirm = sha1($user->passwordConfirm);

            unset($listUsers[$index]);
            array_push($listUsers, $user);

            file_put_contents($this->file, json_encode($listUsers));
        }


        return $errors;
    }
}
