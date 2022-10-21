<?php
namespace Model;

class User 
{
    public string $name;
    public string $email;
    public string $password;
    public bool $mailValidation = false;

}