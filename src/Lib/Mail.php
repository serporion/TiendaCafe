<?php 

namespace Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use FPDF;
use Repositories\LineaPedidoRepository;

/**
 * Clase utilizada para mandar emails usando la libreria de PHPMailer.
 * Uso un constructor vacío y un método donde le paso parámetros necesarios.
 */
class Mail {

    private $correo;
    private $nombre;
    private $token;
    private LineaPedidoRepository $lineaPedido;

    // Constructor vacío
    public function __construct() {
        $this->lineaPedido = new LineaPedidoRepository();
    }

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
    public function sendMail(array $order) : bool
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        try {

            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Necesito desactivar ssl.
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
            $mail->Body = $contenido['html'];

            if (!empty($contenido['pdf'])) {
                $mail->addStringAttachment($contenido['pdf'], 'pedido.pdf', 'base64', 'application/pdf');
            }

            $mail->send();


            $_SESSION['mailOk'] = true;
            return true;
        } catch (Exception $e) {
            // Registrar el error en el log y devolver false
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
     * Metodo que genera el contenido del correo tras un pedido realizado
     * a mandar por mail. Construye un pdf con los datos del pedido y lo
     * envía igualmente.
     * @return array $contenido con el contenido del mismo.
     * @var array Recibe una array con los datos del pedido
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


        $result = $this->lineaPedido->verLineaPedido($order[0]['id']);


        foreach ($result as $product) {
            $contenido .= '
                <tr>
                    <td>' . htmlspecialchars($product['nombre']) . '</td>
                    <td>' . htmlspecialchars($product['unidades']) . '</td>
                    <td>' . htmlspecialchars($product['precio_unitario']) . ' euros </td>
                </tr>';
        }

        $total = $order[0]['coste'];
        $contenido .= '
            </table>
            <div class="total">Total: ' . htmlspecialchars($total) . ' euros </div>
            <p><span class="estado">Estado: ' . htmlspecialchars($order[0]['estado']) . '</span></p>
        </div>
    </body>
    </html>';


        //Contruir el Pdf a remitir.

        $pdf = new FPDF();
        $pdf->AddPage();

        // Título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Pedido realizado por ' . htmlspecialchars($_SESSION['usuario']['nombre']), 0, 1, 'C');

        // Subtítulo
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Pedido Número: ' . htmlspecialchars($order[0]['id']), 0, 1, 'L');

        // Productos
        //$pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 10, 'Producto', 1);
        $pdf->Cell(30, 10, 'Cantidad', 1);
        $pdf->Cell(30, 10, 'Precio', 1);
        $pdf->Ln();

        foreach ($result as $product) {
            $pdf->Cell(60, 10, htmlspecialchars($product['nombre']), 1);
            $pdf->Cell(30, 10, htmlspecialchars($product['unidades']), 1);
            $pdf->Cell(30, 10, htmlspecialchars($product['precio_unitario']) . ' euros', 1);
            $pdf->Ln();
        }

        // Total
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(120, 10, 'Total: ' . htmlspecialchars($total) . ' euros', 0, 1, 'R');

        // Estado
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Estado: ' . htmlspecialchars($order[0]['estado']), 0, 1, 'L');

        $pdfContent = $pdf->Output('S');

        return ['html' => $contenido, 'pdf' => $pdfContent];

    }

    /**
     * Metodo que se encarga de enviar un correo para restablecer la contraseña
     * @param string $correo Correo electrónico del usuario
     * @param string $nombre nombre del usuario
     * @param string $token Token de restablecimiento de contraseña
     * @return bool
     * @throws Exception
     */
    public function enviarRestablecerContrasena(string $correo, string $token, string $nombre): bool {

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
            $mail->addAddress($correo);

            $mail->Subject = 'Restablecer tu Contraseña';

            $mail->isHTML(TRUE);

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
                    <h1>Restablecer Contraseña</h1>
                    <p><strong>Hola, ' . $nombre . '</strong></p>
                    <p>Has solicitado restablecer tu contraseña en TiendaDelCafe.com. Para completar el proceso, por favor haz clic en el siguiente botón:</p>
                    <p style="text-align: center;">
                        <a href="http://localhost/ejercicios/DWES/Tienda/Auth/restablecerContrasena/' . $token . '" class="btn">Restablecer mi contraseña</a>
                    </p>
                    <p>Si el botón no funciona, puedes copiar y pegar el siguiente enlace en tu navegador:</p>
                    <p>' . htmlspecialchars('http://localhost/ejercicios/DWES/Tienda/Auth/restablecerContrasena/' . $token) . '</p>
                    <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
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



}