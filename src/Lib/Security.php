<?php

namespace Lib;


use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Services\AuthService;

class Security {
    
    final public static function encryptPassw(string $passw): string {
        return password_hash($passw, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    final public static function validatePassw(string $passw, string $passwhash): bool {
        return password_verify($passw, $passwhash);
    }

    final public static function secretKey(): string {
        return $_ENV['SECRET_KEY'];
    }


    final public static function createToken(string $key, array $data, bool $withExpiration = true): string {
        $time = strtotime("now");
        $token = array(
            "iat" => $time, // tiempo en el que creamos el JWT, cuando se inicia el token
            "data" => $data
        );

        if ($withExpiration) {
            $token["exp"] = $time + 86400; // el token expira en 24 horas
        }

        return JWT::encode($token, $key, 'HS256');
    }


    final public static function validaToken($token) :bool {

        $authService = new AuthService();

        try {
            $info = JWT::decode($token, new Key(self::secretKey(), 'HS256'));
            $exp = isset($info->exp) ? $info->exp : null;  // Añadimos null a la fecha de expiracion si es que no tiene.
            $email = $info->data->email;


            if ($exp === null) {

                $usuConf = $authService->obtenerCorreo($email);
                $confirmado = $usuConf['confirmado'];

                if ($confirmado === 1){
                    return true;
                }else {
                    $_SESSION['token_error'] = "El correo electrónico asociado al token no está confirmado.";
                    return false;
                }

            }

            if (time() > $exp) {
                $_SESSION['token_error'] = "El token ha expirado.";
                return false;
            }

            if (!$authService->comprobarCorreo($email)) {
                $_SESSION['token_error'] = "El correo electrónico asociado al token no existe.";
                return false;
            }

            return true;

        } catch (Exception $e) {
            $_SESSION['token_error'] = "Error al validar el token: " . $e->getMessage();
            return false;
        }
    }


}