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
     * Metodo que llama al repository para guardar un usuario.
     *
     * @var array $AuthData con los datos del usuario a guardar
     * @return bool|string
     */
    public function insertarUsuario(array $AuthData): bool|string {

        $usuario = Auth::fromArray($AuthData);

        try {

            return $this->authRepository->insertarUsuario($usuario);
        }
        catch (\Exception $e) {
            error_log("Error al insertar el usuario nuevo en la base de datos: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Método que llama al repositorio y obtiene un array con los datos
     * del usuario si existe el correo.
     *
     * @var string $correo con el correo a obtener
     * @return ?array, con los datos del usuario.
     */
    public function obtenerCorreo(string $correo): ?array {
        return $this->authRepository->obtenerCorreo($correo);
    }

    /**
     * Método que llama al repository y comprueba su existe en la base
     * de datos.
     *
     * @var string $correoUsu con el correo a comprobar que se
     * le pasa al repositorio para comprobar si existe.
     * @return ?bool, devuelve su existencia o no.
     */
    public function comprobarCorreo(string $correoUsu): ?bool {
        return $this->authRepository->comprobarCorreo($correoUsu);
    }

    /**
     * Método que realiza la confirmación a true, si el correo existe y
     * el token ha sido validado como correcto.
     *
     * @var string $correo que se le pasa al método del repositorio.
     * @return bool
     */
    public function confirmarUsuario(string $correo): bool {
        return $this->authRepository->confirmarUsuario($correo);
    }

    /**
     * Método para comprobar el usuario que se esta introduciendo esta en
     * la base de datos.
     *
     * @var string $correo con el correo del usuario a comprobar
     * @var string $contrasena con la contraseña del usuario a comprobar
     * @return ?array, con los datos recogidos.
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
     * Metodo que recoge los datos de email y datos en el array para
     * pasarlos al repositorio para actualilzar ciertos datos.
     *
     * @param string $email del usuario a actualizar.
     * @param array $userData, datos de un usuario en un array.
     * @return bool, devolviendo el resultado.
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
            return false;
        }
    }

    /**
     * Método que llama al repositorio para que haga una consulta que muestre
     * todos los usuarios existente.
     *
     * @return array|null de los datos localizados en al base de datos.
     */
    public function findAll(): ?array
    {
        return $this->authRepository->extractAll(); //Conversión en array de Objetos.
    }

    /**
     * Método que recibe la llamada de controller para llevar el parámetro del
     * usuario al repositorio y consultar sus datos.
     *
     * @param int $id del usuario seleccionado.
     * @return Auth / null devuelve un objeto o null.
     */
    public function leerUsuario(int $id): ?Auth{

        return $this->authRepository->extraer_Usuario($id);

    }

    /**
     * Método que recibe un array con los datos de un usuario y envía al Repositorio para
     * que proceda a su grabación.
     *
     * @param array $usuarioData
     * @return bool
     */
    public function grabarUsuarioModificado(array $usuarioData): bool {

        try {

            $usuario = Auth::fromArray($usuarioData);

            return $this->authRepository->grabarUsuarioModificado($usuario);

        } catch (\Exception $e) {

            return false;
        }
    }

    /**
     * Método que utiliza el repositorio para actualizar los valores del token y la contraseña
     * asociados a un usuario identificado por su ID. En caso de error, captura cualquier excepción
     * y devuelve `false`.
     *
     * @param int $idUsuario El ID del usuario cuyo token y contraseña se van a actualizar.
     * @param string $nuevaContrasena La nueva contraseña encriptada del usuario.
     * @param string $nuevoToken El nuevo token generado para el usuario.
     * @return bool Devuelve `true` si la actualización fue exitosa, o `false` si ocurrió algún error.
     */
    public function actualizarTokenYContrasena(int $idUsuario, string $nuevaContrasena, string $nuevoToken): bool {

        try {

            return $this->authRepository->actualizarTokenYContrasena($idUsuario, $nuevaContrasena, $nuevoToken);

        } catch (\Exception $e) {

            return false;
        }
    }



}