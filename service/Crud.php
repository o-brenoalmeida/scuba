<?php

namespace Service;

use Exception;
use Model\User;
use Service\Validation;

class Crud
{
    protected $file;

    public function __construct()
    {
        $this->file = DATA_LOCATION;
    }

    public function create(User $user)
    {
        $errors = $this->validate($user);
        
        if (empty($errors)) {
            $listUsers = $this->getUsersList();

            array_push($listUsers, $user);

            file_put_contents($this->file, json_encode($listUsers));
        }

        return $errors;
    }

    function emailConfirm($email){
        foreach($this->getUsersList() as $user){
            if($user->email === $email){
                return $user;
            }
        }
        return false;
    }

    protected function validate(User $user)
    {
        $errors = [];
        if (!Validation::uniqueInArray($user, $this->getUsersList(), 'email')) {
            $errors['email'] = "Email já cadastrado";
        }

        if (!Validation::lengthPassword($user->senha)) {
            $errors['password'] = "Senha não preenche os requisitos mínimos";
        }

        if (!Validation::confirmationPassword($user->senha, $user->confirmacaoSenha)) {
            $errors['password-confirm'] = "Senhas não conferem";
        }

        return $errors;
    }

    protected function getUsersList()
    {
        return json_decode(file_get_contents($this->file), true);
    }
}
