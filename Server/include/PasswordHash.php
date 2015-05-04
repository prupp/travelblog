<?php

class PasswordHash {

    public static function createHash($password){
        return crypt($password);
    }

    public static function checkPassword($password, $hash) {
        return crypt($password, $hash) == $hash;
    }

}