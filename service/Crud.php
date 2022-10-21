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

    function emailConfirm($email)
    {
        foreach ($this->getUsersList() as $user) {
            if ($user->email === $email) {
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

        if (!Validation::lengthPassword($user->password)) {
            $errors['password'] = "Senha não preenche os requisitos mínimos";
        }

        if (!Validation::confirmationPassword($user->password, $user->passwordConfirmation)) {
            $errors['password-confirm'] = "Senhas não conferem";
        }

        return $errors;
    }

    protected function getUsersList()
    {
        return json_decode(file_get_contents($this->file), true);
    }

    public function confirmEmail($email)
    {
        $listUsers = $this->getUsersList();
        $id = $this->getIdByValue($listUsers, 'email', $email);
        if(is_int($id)){
            $listUsers[$id]['mailValidation'] = true;
            file_put_contents($this->file, json_encode($listUsers));
            return true;
        }

        return false;
    }

    protected function getIdByValue($list, $field, $value)
    {
        foreach ($list as $key => $element) {
            if ($element[$field] == $value) {
                return (integer) $key;
            }
            return false;
        }
    }
}
