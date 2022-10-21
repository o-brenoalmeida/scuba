<?php

use Service\Crud;

class Auth
{
    public static function authentication($email, $password)
    {
        $crud = new Crud();
        $listUsers = $crud->getUsersList();

        $index = $crud->getIndexByValue($listUsers, 'email', $email);

        if (is_int($index)) {
            if ($listUsers[$index]['email'] == $email && $listUsers[$index]['password'] == sha1($password)) {
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['password'] = $password;

                return true;
            }
        }

        return false;
    }

    public static function authUser()
    {
        return $_SESSION['user'];
    }

    public static function logout()
    {
        return session_destroy();
    }
}
