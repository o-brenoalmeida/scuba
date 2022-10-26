<?php
namespace Model;

class User 
{
    public string $name;
    public string $email;
    public string $password;
    public string $passwordConfirm;
    public bool $mailValidation = false;

    public function arrayToObject($arr)
    {
        foreach($arr as $attribute => $value){
            $this->$attribute = $value;
        }
    }

}