<?php

namespace Services;

use Models\Auth;
use Repositories\AuthRepository;
use Lib\Security;


/**
 * Clase que recibe peticiones de AuthController y se pone en contacto con AuthRepository.
 */
class AuthService {

    private AuthRepository $authRepository;

    /**
     * Constructor que inicializa las variables
     */
    public function __construct() {
        $this->authRepository = new AuthRepository();
    }

    /**
     * Metodo que llama al repository para guardar un usuario
     * @var array $AuthData con los datos del usuario a guardar
     * @return bool|string
     */
    public function guardarUsuarios(array $AuthData): bool|string {

        $usuario = Auth::fromArray($AuthData);

        try {

            return $this->authRepository->guardarUsuarios($usuario);
        }
        catch (\Exception $e) {
            error_log("Error al guardar el usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Método que llama al repository y obtiene el correo del usuario
     * @var string $correo con el correo a obtener
     * @return ?array
     */
    public function obtenerCorreo(string $correo): ?array {
        return $this->authRepository->obtenerCorreo($correo);
    }

    /**
     * Método que llama al repository y comprueba el correo
     * @var string $correoUsu con el correo a comprobar que se
     * le pasa al repositorio.
     * @return ?bool
     */
    public function comprobarCorreo(string $correoUsu): ?bool {
        return $this->authRepository->comprobarCorreo($correoUsu);
    }

    /**
     * Método que realiza la confirmación a true, si el correo existe y
     * el token ha sido validado como correcto.
     * @var string $correo que se le pasa al método del repositorio.
     * @return bool
     */
    public function confirmarUsuario(string $correo): bool {
        return $this->authRepository->confirmarUsuario($correo);
    }

    /**
     * Método para comprobar el usuario que se esta introduciendo esta en la base de datos.
     * @var string $correo con el correo del usuario a comprobar
     * @var string $contrasena con la contraseña del usuario a comprobar
     * @return ?array
     */
    public function iniciarSesion(string $correo, string $contrasena): ?array {
        $usuario = $this->obtenerCorreo($correo);


        //Antes de la clase Security
        /*
        if ($usuario && password_verify($contrasena, $usuario['password'])) {
            return $usuario;
        }
        */

        //Despues de la clase Security
        if ($usuario && Security::validatePassw($contrasena, $usuario['password'])) {
            return $usuario;
        }
        
        return null;
    }

    /**
     * Metodo que recoge los datos de email y datos nuevos para
     * pasarselos al Repository.
     * @param string $email
     * @param array $userData
     * @return bool
     */
    public function actualizarUsuario(string $email, array $userData): bool {
        try {
            $usuarioArray = $this->authRepository->obtenerCorreo($email);
            if (!$usuarioArray) {
                return false;
            }

            $usuario = Auth::fromArray($usuarioArray);
            $usuario->setConfirmado($userData['confirmado']);
            $usuario->setToken($userData['token']);
            $usuario->setFechaExpiracion(null);

            return $this->authRepository->actualizarUsuario($usuario);

        } catch (\Exception $e) {
            error_log("Error al actualizar el usuario: " . $e->getMessage());
            return false;
        }
    }


}