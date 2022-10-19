<?php

namespace Service;

class Validation
{
    public static function uniqueInArray(object $object, array $list, string $field)
    {
        if (!in_array($object->$field, array_column($list, $field)))
            return true;

        return false;
    }


    public static function lengthPassword($password)
    {
        if (strlen($password) >= 10)
            return true;

        return false;
    }

    public static function confirmationPassword($password, $confirmation)
    {
        if ($password == $confirmation)
            return true;

        return false;
    }
}
