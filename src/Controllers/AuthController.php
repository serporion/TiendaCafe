<?php

namespace Controllers;

use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lib\Mail;
use Lib\Pages;
use Lib\Utilidades;
use Lib\BaseDatos;
use Lib\Security;
use Models\Auth;
use Services\AuthService;
use PDO;
use PDOException;

class AuthController
{
    private Auth $auth;
    private Pages $pages;
    private Utilidades $utiles;
    private AuthService $authService;


    function __construct()
    {
        $this->auth = new Auth();
        $this->pages = new Pages();
        $this->utiles = new Utilidades();
        $this->authService = new AuthService();
    }

	public function registrarUsuario(): void
    {
        $this->pages->render('Auth/registrar');
    }

    /**
     * Metodo que Valida los datos ingresados, asegurando que las contraseñas coincidan 
     * y que el correo no esté registrado previamente. Cifra la contraseña y genera 
     * un token JWT con fecha de expiración, y guarda la información del usuario 
     * en la base de datos. Envía un correo de confirmación al usuario registrado. 
     * En caso de errores de validación o problemas en el envío del correo, muestra 
     * los mensajes de error en la vista de registro.
     * 
     * @return void
     */
    public function insertarUsuario(): void
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if($this->utiles->comprueboSesion() && !$this->utiles->comprueboAdministrador()){
                header("Location: " . BASE_URL ."");
            }
            else{

                unset($_SESSION['registrado']);
                $this->pages->render('Auth/registrar');
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if($_POST['data']){

                $data = $_POST['data'];
                $usuario = $this->auth = Auth::fromArray($data);
                
                $usuario->sanitizarDatos();

                $errores = $usuario->validarDatosRegistro();
               
                if (!isset($errores['contrasena']) && $data['contrasena'] !== $data['confirmar_contrasena']) {
                    $errores['confirmar_contrasena'] = "Las contraseñas no son iguales";
                }
                

                if($this->authService->comprobarCorreo($data['email'])){
                    $errores['email'] = "El correo ya existe";
                }

                if (empty($errores)) {
                    
                    //Antes de la clase Security
                    /*
                    $contrasena_segura = password_hash($usuario->getContrasena(), PASSWORD_BCRYPT, ['cost' => 10]);
                    $usuario->setContrasena($contrasena_segura);

                    $userData = [
                        'nombre' => $usuario->getNombre(),
                        'apellidos' => $usuario->getApellidos(),
                        'correo' => $usuario->getCorreo(),
                        'contrasena' => $contrasena_segura,
                        'rol' => $usuario->getRol()
                    ];
                    */

                    //Despues de la clase Security
                    $contrasena_segura = Security::encryptPassw($usuario->getContrasena());
                    $usuario->setContrasena($contrasena_segura);


                    $tokenData = [
                        'email' => $usuario->getCorreo(),
                        'nombre' => $usuario->getNombre()
                    ];
                    $token = Security::createToken(Security::secretKey(), $tokenData);
                    $tokenDecoded = JWT::decode($token, new Key(Security::secretKey(), 'HS256'));
                    $fechaExpiracion = new DateTime('@' . $tokenDecoded->exp);
                    $usuario->setFechaExpiracion($fechaExpiracion);
                    $usuario->setToken($token);


                    $userData = [
                        'nombre' => $usuario->getNombre(),
                        'apellidos' => $usuario->getApellidos(),
                        'email' => $usuario->getCorreo(),
                        'contrasena' => $contrasena_segura,
                        'rol' => $usuario->getRol(),
                        'confirmado' => $usuario->isConfirmado(),
                        'fecha_expiracion' => $usuario->getFechaExpiracion()->format('Y-m-d H:i:s'),
                        'token' => $usuario->getToken(),
                    ];


                    $resultado = $this->authService->guardarUsuarios($userData);


                    if ($resultado === true) {

                        $email = new Mail();
                        $email->initialize($usuario->getCorreo(), $usuario->getNombre(), $token);

                        if ($email->enviarConfirmation()) {
                            $_SESSION['registrado'] = true;
                            $this->pages->render('Auth/registrar');
                            unset($_SESSION['registrado']);
                            exit;
                        } else {
                            $errores['email'] = "No se pudo enviar el correo de confirmación. Por favor, inténtelo de nuevo más tarde.";
                            $this->pages->render('Auth/registrar', ["errores" => $errores]);
                        }
                    }

                    else {
                        $errores['db'] = "Error al registrar al usuario: " . $resultado;
                        $this->pages->render('Auth/registrar', [
                            "errores" => $errores,
                            "user" => $this->auth
                        ]);
                    }
                }
                else {
                    $this->pages->render('Auth/registrar', [
                        "errores" => $errores,
                        "user" => $this->auth
                    ]);
                }
            }
            else{
                $_SESSION['falloDatos'] = 'fallo';
            }
        }
    }

    
    /**
     * Metodo para iniciar sesion. Comprueba si el usuario existe, 
     * si no es asi, genera un token
     * 
     * @return void
     */
    public function iniciarSesion():void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if($this->utiles->comprueboSesion()){
                header("Location: " . BASE_URL ."");
            }
            else{
                $this->pages->render('Auth/iniciaSesion');
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $errores = [];//necesito inicializar el array de errores.

            $correo = $_POST['correo'];
            $contrasenaInicioSesion = $_POST['contrasena'];

            $usuario = new Auth( null, "", "", $correo, $contrasenaInicioSesion, "");
            $usuario->sanitizarDatos();             
            $errores = $usuario->validarDatosLogin();

            if (empty($errores)) {
                $resultado = $this->authService->iniciarSesion($usuario->getCorreo(), $usuario->getContrasena());

                if ($resultado) {
                    if ($resultado['confirmado']) {
                        $_SESSION['usuario'] = $resultado;
                        header("Location: " . BASE_URL);
                        exit;
                    } else {
                        $errores['login'] = "El usuario no está confirmado. Por favor, revise su correo electrónico para confirmar su cuenta.";
                    }
                } else {
                    $errores['login'] = "El usuario no está creado o los datos son incorrectos.";
                }
            }

            $this->pages->render('Auth/iniciaSesion', ["errores" => $errores]);


            /*
                if ($resultado) {
                    $_SESSION['usuario'] = $resultado;
                    header("Location: " . BASE_URL);
                    exit;
                }
                else {
                    $errores['login'] = "Datos incorrectos al iniciar sesión. Si no dispone de cuenta, regístrese.";
                }
            }

            $this->pages->render('Auth/iniciaSesion', ["errores" => $errores]);

            */

        }
    }


    /**
     * Metodo que cierra la cesion y borra todas las variables
     * de sesión
     * @return void
     */
    public function logout() {

        if(!$this->utiles->comprueboSesion()){
            header("Location: " . BASE_URL ."");
        }
        else{

            session_start();
            session_unset();
            unset($_SESSION['carrito']);
            session_destroy();
            header("Location: " . BASE_URL);
            exit;
        }

    }

    /**
     * Metodo que para extraer los datos de un usuario.
     * @return void
     */
    public function verTusDatos(): void
    {
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        if(!$this->utiles->comprueboSesion()){
            header("Location: " . BASE_URL ."");
        }
        else{

            $usuActual = $_SESSION['usuario'];

            $this->pages->render("Auth/datosUsuario", ["usuario" => $usuActual]);
        }
    }

    /**
     * Metodo para probar un token.
     * @return void
     */
    public function testToken() {
        echo json_encode(Security::createToken(Security::secretKey(), ['id' => 19, 'mail' => 'micorreo@correo.es']));
        exit;
        //token de prueba
        //eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzgzNjE3NzMsImV4cCI6MTczODM2NTM3MywiZGF0YSI6eyJpZCI6MTksIm1haWwiOiJtaWNvcnJlb0Bjb3JyZW8uZXMifX0.zLxRGYQIQGJetkf8uWzxALeYsB3utxgCvpsyXjK37BU
    }

    public function confirmarCuenta(string $token)
    {
        Utilidades::confirmarCuenta($token);
    }


    
}
