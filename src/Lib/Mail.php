<?php 

namespace Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Clase utilizada para mandar emails usando la libreria de PHPMailer.
 * Uso un constructor vacío y un método donde le paso parámetros necesarios.
 */
class Mail {

    private $correo;
    private $nombre;
    private $token;

    // Constructor vacío
    public function __construct() {
    }

    // Constructor con parámetros

    /** Metodo que uso para inicializa
     * @param $correo
     * @param $nombre
     * @param $token
     * @return void
     */
    public function initialize($correo, $nombre, $token) {
        $this->correo = $correo;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    /**
     * Metodo que manda el envío de confirmación de un pedido.
     * @var array $order Recibe los datos del pedido a mandar por correo
     * @return bool
     */
    public function sendMail(array $order){

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        //var_dump($_ENV['SMTP_HOST'], $_ENV['SMTP_USERNAME'], $_ENV['SMTP_PASSWORD'], $_ENV['SMTPSECURE'], $_ENV['SMTP_PORT']);
        //die();

        try{

            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            //Debo desactivar las opciones de seguridad.
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('mitiendadelcafe@info.com', 'Tienda del CAFE');
            $mail->addAddress($_SESSION['usuario']['email'], $_SESSION['usuario']['nombre']);

            $mail->Subject = 'Datos del pedido';

            $contenido = $this->generateMail($order);
            $mail->isHTML(true);
            $mail->Body = $contenido;

            $mail->send();
            $_SESSION['mailOk'] = true;
            return true;
        }
        catch(Exception $e){
            error_log("Error al enviar el correo: " . $e->getMessage());
            $_SESSION['mailOk'] = false;
            return false;
        }

    }

    /**
     * Metodo que se encarga de enviar un correo de confirmacion para
     * convertirse en usuario y poder logarse en la aplicación.
     * @return bool
     * @throws Exception
     */
    public function enviarConfirmation():bool {

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        try{

            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            //Debo desactivar las opciones de seguridad.
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );


            $mail->setFrom('mitiendadelcafe@info.com', 'Tienda del CAFE');
            $mail->addAddress($this->correo, $this->nombre);

            $mail->Subject = 'Confirma tu Cuenta';

            $mail->isHTML(TRUE);

            /*
            $contenido = '<html>';
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has Creado tu cuenta en TiendaDelCafe.com, solo debes confirmarla presionando el siguiente enlace</p>";
            $contenido .= "<p>Presiona aquí: <a href='http://localhost/ejercicios/DWES/Tienda/Auth/confirmarCuenta/" . $this->token . "'>Confirmar Cuenta</a></p>";
            $contenido .= '</html>';
            $mail->Body = $contenido;
            */

            $contenido = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; }
                        .container { max-width: 600px; margin: 20px auto; padding: 20px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                        h1 { color: #2c3e50; text-align: center; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
                        p { margin-bottom: 20px; }
                        .btn { display: inline-block; background-color: #3498db; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
                        .btn:hover { background-color: #2980b9; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Bienvenido a TiendaDelCafe.com</h1>
                        <p><strong>Hola ' . htmlspecialchars($this->nombre) . ',</strong></p>
                        <p>Has creado tu cuenta en TiendaDelCafe.com. Para completar tu registro, por favor confirma tu cuenta haciendo clic en el siguiente botón:</p>
                        <p style="text-align: center;">
                            <a href="http://localhost/ejercicios/DWES/Tienda/Auth/confirmarCuenta/' . $this->token . '" class="btn">Confirmar mi cuenta</a>
                        </p>
                        <p>Si el botón no funciona, puedes copiar y pegar el siguiente enlace en tu navegador:</p>
                        <p>' . htmlspecialchars('http://localhost/ejercicios/DWES/Tienda/Auth/confirmarCuenta/' . $this->token) . '</p>
                        <p>Gracias por unirte a nuestra comunidad de amantes del café.</p>
                    </div>
                </body>
                </html>';

            $mail->Body = $contenido;


            $mail->send();
            return true;
        }
        catch(Exception $e) {
            error_log("Error al enviar el correo: " . $e->getMessage());
            return false;
        }

    }


    /**
     * Metodo que genera el contenido del correo a mandar
     * @return string
     *@var array Recibe una array con los datos del pedido
     */
    function generateMail(array $order){

        $contenido = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 10px; }
            h1 { color: #2c3e50; text-align: center; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #3498db; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 20px; }
            .estado { background-color: #2ecc71; color: white; padding: 5px 10px; border-radius: 5px; display: inline-block; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Pedido realizado por ' . htmlspecialchars($_SESSION['usuario']['nombre']) . '</h1>
            <h2>Pedido Número: ' . htmlspecialchars($order[0]['id']) . '</h2>
            <table>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                </tr>';

        foreach ($_SESSION['carrito'] as $product) {
            $contenido .= '
                <tr>
                    <td>' . htmlspecialchars($product['nombre']) . '</td>
                    <td>' . htmlspecialchars($product['cantidad']) . '</td>
                    <td>' . htmlspecialchars($product['precio']) . ' euros </td>
                </tr>';
        }

        $total = $_SESSION['totalCost'];
        $contenido .= '
            </table>
            <div class="total">Total: ' . htmlspecialchars($total) . ' euros </div>
            <p><span class="estado">Estado: ' . htmlspecialchars($order[0]['estado']) . '</span></p>
        </div>
    </body>
    </html>';

        return $contenido;

    }


}