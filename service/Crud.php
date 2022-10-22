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
        $user->passwordConfirmation = $dados['person']['password-confirm'];

        $errors = $this->validate($user);

        if (empty($errors)) {
            $listUsers = $this->getUsersList();

            $user->password = sha1($user->password);

            array_push($listUsers, $user);

            file_put_contents($this->file, json_encode($listUsers));
        }

        return $errors;
    }

    protected function validate(User $user)
    {
        $errors = [];
        
        if (!Validation::uniqueInArray($user, $this->getUsersList(), 'email')) {
            $errors['email'] = "Email já cadastrado";
        }

        if (!Validation::lengthPassword($user->password)) {
            $errors['password'] = "Senha não preenche os requisitos mínimos";
        }

        if (!Validation::confirmationPassword($user->password, $user->passwordConfirmation)) {
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
        $index = $this->getIndexByValue($listUsers,'email', $_SESSION['user']['email']);
        if(is_int($index)){
            unset($listUsers[$index]);
            file_put_contents($this->file, json_encode($listUsers));
            return true;
        }
        return false;
    }
}
