<?php
class Validator
{

    private static $passError = null;

    public static function getpassError()
    {
        return self::$passError;
    }

    public static function validateForm($fields)
    {
        foreach ($fields as $index => $value) {
            $value = trim($value);
            $fields[$index] = $value;
        }
        return $fields;
    }

    public static function validateNaturalNumber($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT, array('min_range' => 1))) {
            return true;
        } else {
            return false;
        }
    }
    public static function validateStock($value)
    {
        // Se verifica que el valor sea un número entero mayor o igual a uno.
        if (!($value >=0)) {
            return false;
        } else {
            return true;
        }
    }

    public static function validateEmail($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateString($value, $minimum, $maximum)
    {
        // Se verifica el contenido y la longitud de acuerdo con la base de datos.
        if (preg_match('/^[a-zA-Z0-9ñÑáÁéÉíÍóÓúÚ\s\,\;\.\-\/]{' . $minimum . ',' . $maximum . '}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateAlphabetic($value, $minimum, $maximum)
    {
        // Se verifica el contenido y la longitud de acuerdo con la base de datos.
        if (preg_match('/^[a-zA-ZñÑáÁéÉíÍóÓúÚ\s]{' . $minimum . ',' . $maximum . '}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateAlphanumeric($value, $minimum, $maximum)
    {
        // Se verifica el contenido y la longitud de acuerdo con la base de datos.
        if (preg_match('/^[a-zA-Z0-9ñÑáÁéÉíÍóÓúÚ\s]{' . $minimum . ',' . $maximum . '}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateMoney($value)
    {
        // Se verifica que el número tenga una parte entera y como máximo dos cifras decimales.
        if (preg_match('/^[0-9]+(?:\.[0-9]{1,2})?$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validatePassword($value, $nombres, $apellidos, $alias)
    {
        // Convertimos el nombre, apellido y alias en mayúsculas
        $nombresupper = strtoupper($nombres);
        $apellidosupper = strtoupper($apellidos);
        $aliasupper = strtoupper($alias);

        $nombreslower = strtolower($nombres);
        $apellidoslower = strtolower($apellidos);
        $aliaslower = strtolower($alias);
        // Se verifica la longitud mínima.
        if (strlen($value) >= 8) {
            // Se verifica la longitud máxima.
            if (strlen($value) <= 128) {
                // Se verifica que contenga al menos 1 número.
                if (preg_match("#[0-9]+#", $value)) {
                    // Se verifica que contenga al menos una mayúscula
                    if(preg_match("#[A-Z]+#", $value)) {
                        // Se verifica que contenga al menos una minúscula
                        if(preg_match("#[a-z]+#", $value)) {
                            // Se verifica que al menos contenga un caracter especial
                            if(preg_match("#[\W]+#", $value)) {
                                // Se verifica que la contraseña no contenga ni el nombre, ni el apellido, ni el alias
                                if (preg_match("#(($nombres)|($apellidos)|($alias)|($nombresupper)|($apellidosupper)|($aliasupper)|($nombreslower)|($apellidoslower)|($aliaslower)|(Admin)|(qwerty)|(123)|(Password))#", $value)){
                                    self::$passError = "La clave no debe contener el nombre, apellido, alias, o contraseñas casuales";
                                    return false;
                                } else {
                                    return true;
                                }
                            } else {
                                self::$passError = "La clave debe contener al menos 1 caracter especial";
                                return false;
                            }
                        } else {
                            self::$passError = "La clave debe contener al menos 1 letra minúscula";
                            return false;
                        }
                    } else {
                        self::$passError = "La clave debe contener al menos 1 letra mayúscula";
                        return false;
                    }
                } else {
                    self::$passError = "La clave debe contener al menos 1 número";
                    return false;
                }
            } else {
                self::$passError = 'Clave mayor a 72 caracteres';
                return false;
            }
        } else {
            self::$passError = 'Clave menor a 8 caracteres';
            return false;
        }
    }
}    
?>