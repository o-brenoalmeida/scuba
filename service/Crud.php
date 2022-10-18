<?php
namespace Service;

use Exception;
use Model\User;

class Crud
{
    protected $file;

    public function __construct()
    {
        $this->file = DATA_LOCATION;
    }

    public function create(User $user)
    {
        try {
            $listUsers = json_decode(file_get_contents($this->file), true);

            array_push($listUsers, $user);

            file_put_contents($this->file, json_encode($listUsers));
        } catch (\Throwable $e) {
            new Exception("Não foi possível salvar o usuário no arquivo {$this->file}", 1);
        }
        
    }
}