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

    final public static function createToken(string $key, array $data): string {
        $time = strtotime("now");
        $token = array(
            "iat" => $time, // tiempo en el que creamos el JWT, cuando se inicia el token
            "exp" => $time + 7200, // el token expira en 2 horas
            "data" => $data
        );
    
        return JWT::encode($token, $key, 'HS256');
    }

    final public static function validaToken($token) :bool {

        try {
            $info = JWT::decode($token, new Key(self::secretKey(), 'HS256'));
            $exp = $info->exp;
            $email = $info->data->email;

            // Verificar si el token ha expirado
            if (time() > $exp) {
                return false;
            }

            // Verificar si el usuario existe en la base de datos
            $authService = new AuthService();

            return $authService->comprobarCorreo($email);

        } catch (Exception $e) {
            return false;
        }
    }

}