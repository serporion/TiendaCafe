<?php

namespace Models;

use Lib\BaseDatos;
use Lib\Validar;
use DateTime;


class Auth
{

    private BaseDatos $conexion;
    private mixed $stmt;

    /**
     * Constructor que instancia objetos de la clase Auth.
     */
    public function __construct(
        private ?int $id = null,
        private string $nombre = "",
        private string $apellidos = "",
        private string $correo = "",
        private string $contrasena = "",
        private string $rol = "",

        //Tras la inclusión del clase Security

        private bool $confirmado = false,
        private ?DateTime $fecha_expiracion = null,
        private string $token = ""


    ) {
        $this->conexion = new BaseDatos();
    }


    // Metodos Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getApellidos(): string {
        return $this->apellidos;
    }

    public function getCorreo(): string {
        return $this->correo;
    }

    public function getContrasena(): string {
        return $this->contrasena;
    }

    public function getRol(): string {
        return $this->rol;
    }

    public function isConfirmado(): bool
    {
        return $this->confirmado;
    }

    public function getFechaExpiracion(): ?DateTime
    {
        return $this->fecha_expiracion;
    }

    public function getToken(): string
    {
        return $this->token;
    }



    // Metodos Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setApellidos(string $apellidos): void {
        $this->apellidos = $apellidos;
    }

    public function setCorreo(string $correo): void {
        $this->correo = $correo;
    }

    public function setContrasena(string $contrasena): void {
        $this->contrasena = $contrasena;
    }

    public function setRol(string $rol): void {
        $this->rol = $rol;
    }

    public function setConfirmado(bool $confirmado): void
    {
        $this->confirmado = $confirmado;
    }

    public function setFechaExpiracion(?DateTime $fecha_expiracion): void
    {
        $this->fecha_expiracion = $fecha_expiracion;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * Metodo para validar los campos del formulario de registro
     * @return array array si existen errores.
     */
    public function validarDatosRegistro(): array {
        $errores = [];

        if (empty($this->nombre)) {
            $errores['nombre'] = "El nombre es obligatorio";
        } elseif (!Validar::validateNombre($this->nombre)) {
            $errores['nombre'] = "El nombre no puede contener caracteres especiales";
        }

        if (!empty($this->apellidos) && !Validar::validateApellidos($this->apellidos)) {
            $errores['apellidos'] = "Los apellidos no pueden contener caracteres especiales";
        }

        if (empty($this->correo)) {
            $errores['email'] = "El correo electrónico es obligatorio";
        } elseif (!Validar::validateEmail($this->correo)) {
            $errores['email'] = "El correo electrónico no es válido";
        }

        if (empty($this->contrasena)) {
            $errores['contrasena'] = "La contraseña es obligatoria";
        } elseif (!Validar::validatePassword($this->contrasena) || strlen($this->contrasena) < 6) {
            $errores['contrasena'] = "La contraseña debe de ser de al menos 8 caracteres una letra mayúscula, una letra minúscula y un caracteres especiales.";
        }

        return $errores;
    }

    /**
     * Metodo que valida y devuelve un array con los errores surgidos.
     * @return array con los errores encontrados tras la validación.
     */

    public function validarDatosReenvioContrasena(): array {
        $errores = [];

        if (empty($this->nombre)) {
            $errores['nombre'] = "El nombre es obligatorio";
        } elseif (!Validar::validateNombre($this->nombre)) {
            $errores['nombre'] = "El nombre no puede contener caracteres especiales";
        }


        if (empty($this->correo)) {
            $errores['email'] = "El correo electrónico es obligatorio";
        } elseif (!Validar::validateEmail($this->correo)) {
            $errores['email'] = "El correo electrónico no es válido";
        }

        return $errores;
    }
    

    /**
     * Metodo para sanetizar los datos recibidos por el formulario
     * @return void
     */
    public function sanitizarDatos(): void {
        $this->id = Validar::sanitizeInt($this->id);
        $this->nombre = Validar::sanitizeString($this->nombre);
        $this->apellidos = Validar::sanitizeString($this->apellidos);
        $this->correo = Validar::sanitizeEmail($this->correo);
        $this->contrasena = Validar::sanitizeString($this->contrasena);
        $this->rol = Validar::sanitizeString($this->rol);
    }

    /**
     * Metodo para validar los campos del formulario de inicio de sesion
     * @return array array si existen errores.
     */
    public function validarDatosLogin(): array {
        $errores = [];

        if (empty($this->correo)) {
            $errores['correo'] = "El campo correo es obligatorio.";
        }

        if (empty($this->contrasena)) {
            $errores['contrasena'] = "El campo contraseña es obligatorio.";
        }

        return $errores;
    }

    /**
     * Metodo para combertir un array en un objeto User
     * @var array $data a convertir en objeto Auth
     * @return Auth array convertido a objeto
     */
    public static function fromArray(array $data): Auth{
        return new Auth(
            $data['id'] ?? null,
            $data['nombre'] ?? "",
            $data['apellidos'] ?? "",
            $data['email'] ?? "",
            $data['contrasena'] ?? "",
            $data['rol'] ?? 'user',
                $data['confirmado'] ?? false,
                isset($data['fecha_expiracion']) ? new DateTime($data['fecha_expiracion']) : null,
                $data['token'] ?? ""
        );
    }
}