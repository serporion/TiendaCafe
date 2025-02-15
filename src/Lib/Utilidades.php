<?php

namespace Lib;

use ErrorException;
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
                    //$newToken = Security::createToken(Security::secretKey(), $tokenData);
                    $newToken = Security::createToken(Security::secretKey(), $tokenData, false); //Agrego parámetro para manejar la fecha expiracion.

                    $userData = [
                        'confirmado' => true,
                        'token' => $newToken
                    ];

                    if ($authService->actualizarUsuario($email, $userData)) {
                        $_SESSION['correoConfirmado'] = "El usuario ha confirmado su correo correctamente";
                        header("Location: " . BASE_URL);
                        exit();
                    } else {
                        $_SESSION['correoConfirmado'] = "No se ha podido confirmar su correo correctamente. 
                    El correo no existe en la base de datos";
                        header("Location: " . BASE_URL);
                        exit();
                    }
                } else {
                    $_SESSION['correoConfirmado'] = "No se ha podido confirmar su correo. El usuario no pudo ser confirmado";
                    header("Location: " . BASE_URL);
                    exit();
                }
            } else {
                $_SESSION['correoConfirmado'] = "No se ha podido confirmar su correo correctamente. 
            El correo no existe en la base de datos";
                header("Location: " . BASE_URL);
                exit();
            }

        } else {
            $_SESSION['correoConfirmado'] = "Error al confirmar el correo. El token es inválido";
            header("Location: " . BASE_URL);
            exit();
        }

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

    /**
     * Manejador de errores para convertir warnings en excepciones.
     *
     * @param int $errno Número del error.
     * @param string $errstr Mensaje del error.
     * @param string $errfile Nombre del archivo donde ocurrió el error.
     * @param int $errline Número de línea donde ocurrió el error.
     * @throws ErrorException Lanza una excepción con la información del error.
     * @return void Esta función no devuelve nada, siempre lanza una excepción.
     */
    function manejadorWarning($errno, $errstr, $errfile, $errline) :void {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

}