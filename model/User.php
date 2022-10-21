<?php
namespace Model;

class User 
{
    public string $name;
    public string $email;
    public string $password;
    public string $passwordConfirmation;
    public bool $mailValidation = false;

}