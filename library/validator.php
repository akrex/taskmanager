<?php

class Validator{
    
    public static function IsCorrectEmail($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false; 
        }
        return true;
    }
    
    public static function IsCorrectUserName($name){
       if (!preg_match("/^[a-zA-ZА-Яа-я0-9]*$/",$name)) {
            return false; 
          }
       return true;
    }
    
    
    public static function IsCorrectPhone($phone){
       if (preg_match("/^[0-9()+-]*$/",$phone)) {
            return false; 
          }
       return true;
    }
    
    public static function IsCorrectLenght($text,$min,$max){
        if(utf8_strlen($text)<$min || utf8_strlen($text) > $max){
            return false;
        }
        return true;
    }
}
