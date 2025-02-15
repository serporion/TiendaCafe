<?php

namespace Controllers;

use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lib\Mail;
use Lib\Pages;
use Lib\Utilidades;
use Lib\Security;
use Lib\Validar;
use Models\Auth;
use PHPMailer\PHPMailer\Exception;
use Services\AuthService;


/**
 * Clase AuthController.
 * Controlador encargado de gestionar las operaciones relacionadas con la autenticación
 * de usuarios como registro, inicio de sesión y cierre de sesión. Además, se
 * encarga de procesar datos de usuario y comunicarse con el servicio de autenticación
 * y otras clases necesarias para ejecutar dichas funcionalidades.
 */
class AuthController
{
    private Auth $auth;
    private Pages $pages;
    private Utilidades $utiles;
    private AuthService $authService;
    private CarritoController $carritoController;


    function __construct()
    {
        $this->auth = new Auth();
        $this->pages = new Pages();
        $this->utiles = new Utilidades();
        $this->authService = new AuthService();
        $this->carritoController = new CarritoController();
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
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($this->utiles->comprueboSesion() && !$this->utiles->comprueboAdministrador()) {
                header("Location: " . BASE_URL . "");
            } else {
                unset($_SESSION['registrado']);
                $this->pages->render('Auth/registrar');
            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['data'])) {
                $data = $_POST['data'];
                $usuario = $this->auth = Auth::fromArray($data);
                $usuario->sanitizarDatos();

                $errores = $usuario->validarDatosRegistro();

                if (!isset($errores['contrasena']) && $data['contrasena'] !== $data['confirmar_contrasena']) {
                    $errores['confirmar_contrasena'] = "Las contraseñas no son iguales";
                }

                if ($this->authService->comprobarCorreo($data['email'])) {
                    $errores['email'] = "El correo ya existe";
                }

                if (empty($errores)) {
                    //Despues de la clase Security
                    $contrasena_segura = Security::encryptPassw($usuario->getContrasena());
                    $usuario->setContrasena($contrasena_segura);

                    $tokenData = [
                        'email' => $usuario->getCorreo(),
                        'nombre' => $usuario->getNombre()
                    ];
                    $token = Security::createToken(Security::secretKey(), $tokenData, true);
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

                    $resultado = $this->authService->insertarUsuario($userData);

                    if ($resultado === true) {
                        $email = new Mail();
                        $email->initialize($usuario->getCorreo(), $usuario->getNombre(), $token);

                        if ($email->enviarConfirmation()) {
                            $_SESSION['registrado'] = true;
                        } else {
                            $errores['email'] = "No se pudo enviar el correo de confirmación. Por favor, inténtelo de nuevo más tarde.";
                        }
                    } else {
                        $errores['db'] = "Error al registrar al usuario: " . $resultado;
                    }

                    if (!empty($errores)) {
                        $this->pages->render('Auth/registrar', [
                            "errores" => $errores,
                            "user" => $this->auth
                        ]);
                    } else {
                        $this->pages->render('Auth/registrar'); // Renderiza sin errores si todo salió bien
                    }
                } else {
                    $_SESSION['falloDatos'] = 'fallo';
                    $this->pages->render('Auth/registrar');
                }
            } else {
                $_SESSION['falloDatos'] = 'fallo';
                $this->pages->render('Auth/registrar');
            }
        }
    }



    /**
     * Método para iniciar sesión. maneja el inicio de sesión de un usuario. Si se realiza una solicitud
     * GET, verifica si la sesión ya está activa; de lo contrario, redirige a la página
     * para iniciar sesión. Si se realiza una solicitud POST, valida los datos enviados
     * por el usuario y, en caso de éxito, inicia la sesión y redirige a la página principal.
     * Permite regenerar la constraseñe cuando los datos son incorrectos.
     *
     * @return void
     */
    public function iniciarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($this->utiles->comprueboSesion()) {
                header("Location: " . BASE_URL . "");
                exit;
            } else {
                $this->pages->render('Auth/iniciaSesion');
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = []; //necesito inicializar el array de errores.

            $correo = $_POST['correo'];
            $contrasenaInicioSesion = $_POST['contrasena'];

            $usuario = new Auth(null, "", "", $correo, $contrasenaInicioSesion, "");
            $usuario->sanitizarDatos();
            $errores = $usuario->validarDatosLogin();

            if (empty($errores)) {
                $resultado = $this->authService->iniciarSesion($usuario->getCorreo(), $usuario->getContrasena());

                if ($resultado) {
                    if ($resultado['confirmado']) {
                        $_SESSION['usuario'] = $resultado;


                        $carritoController = new CarritoController();
                        $carritoController->recuperarCarrito($resultado['id']);

                        header("Location: " . BASE_URL);
                        exit;
                    } else {
                        $errores['login'] = "El usuario no está confirmado. Por favor, revise su correo electrónico para confirmar su cuenta.";
                    }
                } else {
                    $_SESSION['correoRestablecer'] = $correo;
                    $errores['login'] = '<p>El usuario no está creado o los datos son incorrectos.</p><p><a href="enviarCorreoRecuperacion">Recuperar contraseña</a></p>';
                }
            }

            $this->pages->render('Auth/iniciaSesion', ["errores" => $errores]);
        }
    }


    /**
     * Metodo que cierra la sesion borrando las variables
     * de sesión. Graba en la base de datos el carrito sin
     * finalizar.
     * @return void
     */
    public function logout()
    {

        if (!$this->utiles->comprueboSesion()) {
            header("Location: " . BASE_URL . "");
        } else {

            if (isset($_SESSION['usuario'], $_SESSION['carrito'])) {
                $usuarioId = $_SESSION['usuario']['id'];
                $carrito = $_SESSION['carrito'];


                $this->carritoController->guardarCarrito($usuarioId, $carrito);
            }

            session_destroy();
            header("Location: " . BASE_URL);
            exit;
        }

    }


    /**
     * Método que llama a Service para trate la solicitud para mostrar
     * todos los usuarios en la base de datos.
     * @return void
     */
    public function extraer_todos()
    {

        $todos_los_usuarios = $this->authService->findAll();
        $this->pages->render('Auth/listarUsuarios', ['todos_los_usuarios' => $todos_los_usuarios]);
    }

    /**
     * Metodo que recibe de la vista el id de un usuario de la oplicacion.
     * @param string $id que se recibe.
     * @return void
     */
    public function editarUsuario(string $id): void
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        //$id = $_GET['id'] ?? null;

        if ($id === null) {
            echo "ID no válido.";
            return;
        } else {
            $_SESSION['id'] = $id;
        }

        $mi_usuario = $this->authService->leerUsuario($id);

        if (!$mi_usuario) {
            echo "Usuario no encontrado.";
            return;
        }

        //Envío los datos del usuario al formulario.
        $this->pages->render('Auth/modificarUsuario', ['mi_usuario' => $mi_usuario]);

    }


    /**
     * Metodo que para extraer los datos de un usuario.
     * @return void
     */
    public function verTusDatos(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!$this->utiles->comprueboSesion()) {
            header("Location: " . BASE_URL . "");
        } else {

            $usuActual = $_SESSION['usuario'];

            $this->pages->render("Auth/datosUsuario", ["usuario" => $usuActual]);
        }
    }

    /**
     * Método para probar un token.
     * @return void
     */
    public function testToken()
    {
        echo json_encode(Security::createToken(Security::secretKey(), ['id' => 19, 'mail' => 'micorreo@correo.es'], true));
        exit;
        //token de prueba
        //eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzgzNjE3NzMsImV4cCI6MTczODM2NTM3MywiZGF0YSI6eyJpZCI6MTksIm1haWwiOiJtaWNvcnJlb0Bjb3JyZW8uZXMifX0.zLxRGYQIQGJetkf8uWzxALeYsB3utxgCvpsyXjK37BU
    }

    /**
     * Método que llama a la clase utilidades para confirmar una cuenta de correo electrónico comparando el token
     * que existe ne la base de datos.
     * @param string $token a comparar y cambiar el estado de confirmado o no.
     * @return void
     */
    public function confirmarCuenta(string $token) : void
    {
        Utilidades::confirmarCuenta($token);
    }

    /**
     * Método que guarda con los datos de la vista, mandando un usuario al servicio y al repositorio.
     * @return void
     */
    public function guardarUsuarios(): void
    {
        if (isset($_SESSION['id'])) {
            $id = $_SESSION['id'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($this->utiles->comprueboSesion() && !$this->utiles->comprueboAdministrador()) {
                header("Location: " . BASE_URL . "");
            } else {
                unset($_SESSION['registrado']);
                $this->pages->render('Auth/modificarUsuario');
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if ($_POST['data']) {

                // Aquí aseguramos que el campo de la contraseña esté correctamente marcado como 'vacio' si está vacío
                if (isset($_POST['data']['contrasena']) && $_POST['data']['contrasena'] === '') {
                    $_POST['data']['contrasena'] = 'vacio';
                } elseif (!isset($_POST['data']['contrasena'])) {
                    $_POST['data']['contrasena'] = 'vacio';
                }

                $data = $_POST['data'];
                $usuario = Auth::fromArray($data);

                $usuario->sanitizarDatos();

                if (!empty($errores)) {
                    $_SESSION['errores'] = $errores;
                    $_SESSION['valores'] = $_POST; // Almacena todos los valores del POST. Sirve de auxiliar.
                    header("Location: " . BASE_URL . "Auth/modificarUsuario");
                    exit;
                } else {
                    // Ahora incluimos la contraseña (puede ser 'vacio') en los datos del usuario para pasarla al repositorio.
                    $userData = [
                        'id' => $id ?? null,
                        'nombre' => $usuario->getNombre(),
                        'apellidos' => $usuario->getApellidos(),
                        'email' => $usuario->getCorreo(),
                        'rol' => $usuario->getRol(),
                        'confirmado' => $usuario->isConfirmado(),
                        'contrasena' => $usuario->getContrasena() // Incluir la contraseña, si no el objeto lo considera null.
                    ];

                    $resultado = $this->authService->grabarUsuarioModificado($userData);

                    if ($resultado) {
                        $_SESSION['correctoModificaUsuario'] = 'Se ha modificado el usuario.';
                        header("Location: " . BASE_URL);
                        exit;
                    } else {
                        $_SESSION['correctoModificaUsuario'] = 'Error en la modificación del usuario';
                        header("Location: " . BASE_URL);
                        exit;
                    }
                }
            } else {
                $_SESSION['falloDatos'] = 'fallo';
            }
        }
    }


    /**
     * Método que redirige a la vista que nos permite rellenar los campos
     * que nos ayudará a recuperar la contraseña.
     *
     * @return void
     */
    public function enviarCorreoRecuperacion(): void
    {
        $this->pages->render('Auth/enviarCorreoRecuperacion');
    }


    /**
     * Método que maneja el proceso de envío de un correo electrónico para
     * restablecer la contraseña del usuario. * * Valida la entrada del usuario.
     * Verifica si el correo electrónico proporcionado existe * en la base de datos.
     * Si el correo electrónico existe y hay un token de restablecimiento de contraseña
     * disponible. Envía un token de restablecimiento de contraseña al correo electrónico
     * del usuario. Gestiona errores y redireccionamientos * adecuadamente en función de
     * diferentes condiciones, como datos faltantes o inválidos, * o la ausencia de un
     * token de reinicio.
     *
     * @return void
     * @throws Exception
     */
    public function envioCorreoRestablecerContrasena(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

             if ($_POST['data']) {

                $data = $_POST['data'];
                $usuario = $this->auth = Auth::fromArray($data);

                $usuario->sanitizarDatos();

                $errores = $usuario->validarDatosReenvioContrasena();

                if ($this->authService->comprobarCorreo($usuario->getCorreo())) {

                    $usu = $this->authService->obtenerCorreo($usuario->getCorreo());

                    $usu = $this->auth = Auth::fromArray($usu);

                    $token = $usu->getToken();

                    if (!$token) {
                        $_SESSION['restablecerContraseña'] = "No se ha podido recuperar el token para restablecer su contraseña";
                        header("Location: " . BASE_URL);
                        exit();

                    } else {
                        $mail = new Mail();
                        $mail->enviarRestablecerContrasena($usu->getCorreo(), $token, $usu->getNombre());
                        $_SESSION['restablecerContraseña'] = "Se ha enviado un correo electrónico con el token para restablecer su contraseña";
                        header("Location: " . BASE_URL);
                        exit();
                    }
                } else {
                    $_SESSION['restablecerContraseña'] = "El correo electrónico no existe en la base de datos";
                    header("Location: " . BASE_URL);
                    exit();
                }
            }else {
                $_SESSION['falloDatos'] = 'fallo';
            }
        }
    }


    /**
     * Restablece la contraseña del usuario mediante un token de validación.
     *
     * @param string $token Token para restablecer la contraseña.
     * @return void
     */
    public function restablecerContrasena($token): void
    {
         if (Security::validaToken($token)) {

                $info = JWT::decode($token, new Key(Security::secretKey(), 'HS256'));
                $email = $info->data->email;

                $tokenData = [
                    'email' => $email,
                    'nombre' => $info->data->nombre
                ];
                $newToken = Security::createToken(Security::secretKey(), $tokenData, false);

                $this->pages->render('Auth/nuevaContrasena', ['newToken' => $newToken]);

         } else {

                $_SESSION['token_error'] = $_SESSION['token_error'] ?? "Error desconocido al validar el token.";
                header("Location: " . BASE_URL . "Auth/regenerarContrasena");
                exit();
         }
    }

    /**
     * Gestiona la regeneración de la contraseña del usuario mediante un formulario.
     *
     * @return void
     */
    public function regenerarContrasena(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $this->pages->render('Auth/nuevaContrasena');

        }elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['data'])) {
                $data = $_POST['data'];
                $errores = [];


                if (empty($data['email'])) {
                    $errores['email'] = "El correo electrónico es obligatorio.";
                } elseif (!Validar::validateEmail($data['email'])) {
                    $errores['email'] = "El correo electrónico no es válido.";
                }

                if (empty($data['contrasena'])) {
                    $errores['contrasena'] = "La contraseña es obligatoria.";
                } elseif (!Validar::validatePassword($data['contrasena']) || strlen($data['contrasena']) < 8) {
                    $errores['contrasena'] = "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un carácter especial.";
                }

                if (empty($data['contrasenaRepetida'])) {
                    $errores['contrasenaRepetida'] = "Debe repetir la contraseña.";
                } elseif ($data['contrasena'] !== $data['contrasenaRepetida']) {
                    $errores['contrasenaRepetida'] = "Las contraseñas no coinciden.";
                }

                if (!$this->authService->comprobarCorreo($data['email'])) {
                    $errores['email'] = "El correo no está registrado.";
                }else {
                    $idUsuMod = $this->authService->obtenerCorreo($data['email']);
                    $idUsuMod = $this->auth = Auth::fromArray($idUsuMod);
                    $idUsuMod = $idUsuMod->getId();
                }

                if (!empty($errores)) {
                    $_SESSION['errores'] = $errores;
                    header("Location: " . BASE_URL . "Auth/regenerarContrasena");
                    exit();
                }

                $usuario = Auth::fromArray($data);
                $usuario->sanitizarDatos();

                $contrasena_segura = Security::encryptPassw($usuario->getContrasena());
                $usuario->setContrasena($contrasena_segura);

                $resultado = $this->authService->actualizarTokenYContrasena($idUsuMod, $contrasena_segura, $data['token']);

                if ($resultado) {

                    $_SESSION['mensaje_exito'] = "La contraseña se ha actualizado correctamente.";
                    header("Location: " . BASE_URL . "Auth/regenerarContrasena");
                    exit();

                } else {

                    $_SESSION['errores'] = ["general" => "Hubo un problema al actualizar los datos. Por favor, inténtelo de nuevo."];
                    header("Location: " . BASE_URL . "Auth/regenerarContrasena");
                    exit();
                }

            } else {
                $_SESSION['sesion_token'] = "El formulario no fue enviado correctamente.";
                header("Location: " . BASE_URL . "Auth/regenerarContrasena");
                exit();
            }

        }
    }

}
