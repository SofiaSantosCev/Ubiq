<?php 

namespace App;

class Validator
{
	public static function isValidEmail($email)
	{
	  $matches = null;
	  return (1 === preg_match('/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/', $email, $matches));
	}

	public static function hasOnlyOneWord($string)
    {     
        
        return ctype_graph($string);
        
    }

    public static function isStringEmpty($string)
    {             
        return $string == "";        
    }

    //Devuelve true si el tamaño del string supera el máximo indicado
    public static function exceedsMaxLength($string, $max)
    {   
        return strlen($string) > $max;
        
    }

    //Devuelve true si el tamaño del string alcanza el mínimo indicado
     public static function reachesMinLength($string, $min)
    {    
        return strlen($string) >= $min;
        
    }
    
    public static function isEmailInUse($email)
    {  
        $users = User::where('email', $email)->get();
        foreach ($users as &$user) 
        {
            if ($user->email == $email) 
            {
                return true; 
            }
        }
        
    }
}