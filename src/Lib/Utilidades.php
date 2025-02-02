<?php

namespace Lib;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Services\AuthService;

/**
 * Clase para diferentes usos.
 */
class Utilidades {


    /**
     * Metodo que comprueba si la cuenta está confirmada.
     * @return bool
     */
    public static function confirmarCuenta($token) {
        $authService = new AuthService();
        if (Security::validaToken($token)) {
            $info = JWT::decode($token, new Key(Security::secretKey(), 'HS256'));
            $email = $info->data->email;

            if ($authService->comprobarCorreo($email)) {

                if ($authService->confirmarUsuario($email)) {

                    $tokenData = [
                        'email' => $email,
                        'nombre' => $info->data->nombre
                    ];
                    $newToken = Security::createToken(Security::secretKey(), $tokenData);

                    // Actualizar usuario con nuevo token
                    $userData = [
                        'confirmado' => true,
                        'token' => $newToken
                    ];
                    $authService->actualizarUsuario($email, $userData);

                    $_SESSION['correoConfirmado'] = "El usuario ha confirmado su correo correctamente";
                    header("Location: " . BASE_URL);
                    exit();
                }
            }

        }
        $_SESSION['correoConfirmado'] = "Error al confirmar el correo";
        header("Location: " . BASE_URL);
        exit();
    }


    /**
     * Metodo que comprueba si la sesion esta iniciada.
     * @return bool
     */
    public static function comprueboSesion():bool {
        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
    }

    /**
     * Metodo que comprueba si el usuario logueado es administrador o no.
     * @return bool
     */
    public static function comprueboAdministrador():bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario']) && $_SESSION["usuario"]["rol"] === 'admin';
    }



}