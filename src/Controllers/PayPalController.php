<?php

namespace Controllers;


use Lib\Mail;
use Lib\BaseDatos;
use Services\PayPalService;
use Services\PedidoService;

class PayPalController
{
    private $pedidoService;
    private Mail $mail;
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->pedidoService = new PedidoService();
        $this->mail = new Mail();
        $this->conexion = new BaseDatos();
    }

    /**
     * Método que inicia el pago a través de PayPal.
     *
     * @param string $pedidoId El ID del pedido que se está procesando.
     * @return void.
     */
    public function iniciarPago(string $pedidoId) : void
    {
        $pedido = $this->pedidoService->mostrarPedidoPorId($pedidoId);

        if (!$pedido || $pedido['pagado'] == 1) {
            $_SESSION['errorPedido'] = "El pedido no existe o ya está pagado.";
            header("Location: " . BASE_URL);
            exit;
        }

        try {
            $total = $pedido['coste']; // Usar el coste total del pedido
            $paypalService = new PayPalService();
            $approvalLink = $paypalService->crearPago($total, 'EUR', "Pago del pedido #$pedidoId", $pedidoId);

            // Redirige a PayPal para la aprobación del pago
            header("Location: " . $approvalLink);
            exit;
        } catch (\Exception $e) {
            $_SESSION['errorPedido'] = "Error al procesar el pago: " . $e->getMessage();
            header("Location: " . BASE_URL);
            exit;
        }
    }

    /**
     * Método que procesa el pago exitoso de un pedido a través de PayPal. Verifica
     * la autenticidad del pedido, captura el pago, y actualiza el estado del pedido
     * e incluye la transaccion para tener referencia con PayPal.
     *
     * @param string $pedidoId El ID del pedido que se está procesando.
     * @param string $token El token de autorización proporcionado por PayPal.
     * @param string $payerId El ID del pagador proporcionado por PayPal.
     *
     * @throws \Exception Si ocurre un error durante el procesamiento del pago.
     *
     * @return void Este método no retorna un valor, pero redirige al usuario a diferentes páginas según el resultado.
     */


    public function pagoExitoso(string $pedidoId, string $token, string $payerId)
    {
        $userId = $_SESSION['usuario']['id'];

        $pedido = $this->pedidoService->mostrarPedidoPorId($pedidoId);

        if (!$pedido || $pedido['usuario_id'] != $userId) {
            $_SESSION['errorPedido'] = "No tienes permiso para acceder a este pedido.";
            header("Location: " . BASE_URL);
            exit;
        }

        if ($token && $payerId) {
            try {
                $paypalService = new PayPalService();
                $jsonPago = $paypalService->capturarPago($token);

                if ($jsonPago->status !== 'COMPLETED') {
                    throw new \Exception("El estado del pago no es 'COMPLETED'.");
                }

                $transactionId = null;
                foreach ($jsonPago->purchase_units as $unit) {
                    if (!empty($unit->payments->captures)) {
                        foreach ($unit->payments->captures as $capture) {
                            if ($capture->status === 'COMPLETED') {
                                $transactionId = $capture->id;
                                break;
                            }
                        }
                    }
                }

                if (!$transactionId) {
                    throw new \Exception("No se encontró una transacción válida.");
                }

                // Iniciar una transacción en la base de datos
                $this->conexion->beginTransaction();

                $order = $this->pedidoService->selectOrder($pedidoId);

                if (!$order) {
                    throw new \Exception("No se encontró el pedido en la base de datos.");
                }

                if (!$this->mail->sendMail($order)) {
                    throw new \Exception("Error al enviar el correo.");
                }

                $this->pedidoService->marcarPedidoPagado($pedidoId, $transactionId);

                // Confirmar la transacción
                $this->conexion->commit();

                $_SESSION['successPedido'] = "Pago realizado con éxito. Revise su correo";
                header("Location: " . BASE_URL);
                exit;

            } catch (\Exception $e) {
                // Revertir cambios si algo falla
                $this->conexion->rollback();
                $_SESSION['errorPedido'] = "Error procesando el pago: " . $e->getMessage();
                header("Location: " . BASE_URL);
                exit;
            }
        } else {
            $_SESSION['errorPedido'] = "El token o el PayerID no están presentes.";
            header("Location: " . BASE_URL);
            exit;
        }
    }


    /*
    public function pagoExitoso(string $pedidoId, string $token, string $payerId)
    {
        //var_dump($pedidoId, $token, $payerId);
        //die(); // Para detener la ejecución y ver el resultado

        $userId = $_SESSION['usuario']['id'];

        $pedido = $this->pedidoService->mostrarPedidoPorId($pedidoId);

        if (!$pedido || $pedido['usuario_id'] != $userId) {
            $_SESSION['errorPedido'] = "No tienes permiso para acceder a este pedido.";
            header("Location: " . BASE_URL);
            exit;
        }

        if ($token && $payerId) {
            try {
                $paypalService = new PayPalService();

                // Captura el pago utilizando el token de PayPal
                $jsonPago = $paypalService->capturarPago($token);

                // El estado se recoge ahí.
                $status = $jsonPago->status;

                if ($status === 'COMPLETED') {

                    $transactionId = null;

                    foreach ($jsonPago->purchase_units as $unit) {
                        if (!empty($unit->payments->captures)) {
                            foreach ($unit->payments->captures as $capture) {
                                if ($capture->status === 'COMPLETED') {
                                    $transactionId = $capture->id;
                                    break;
                                }
                            }
                        }
                    }

                    if ($transactionId) {  // Solo actualiza si hay una transacción válida

                        $order = $this->pedidoService->selectOrder($pedidoId);

                        $emailEnviado = $this->mail->sendMail($order);

                        if (!$emailEnviado) {
                            throw new Exception("Error al enviar el correo.");
                        }

                        $this->pedidoService->marcarPedidoPagado($pedidoId, $transactionId);
                        $_SESSION['successPedido'] = "Pago realizado con éxito.";
                        header("Location: " . BASE_URL);
                        exit;
                    }

                } else {
                    throw new \Exception("El estado del pago no es 'COMPLETED'.");
                }
            } catch (\Exception $e) {
                $_SESSION['errorPedido'] = "Error procesando el pago: " . $e->getMessage();
                header("Location: " . BASE_URL);
                exit;
            }
        } else {
            $_SESSION['errorPedido'] = "El token o el PayerID no están presentes.";
            header("Location: " . BASE_URL);
            exit;
        }
    }
/*

    /**
     * Método que gestiona la cancelación de un pago.
     *
     * @param string $pedidoId El ID del pedido cuyo pago fue cancelado.
     * @return void.
     *
     * Notas: No funciona bien por parte de Paypal, parece ser por no estar
     * bien implementado en sanbox pruebas.
     */
    public function pagoCancelado(string $pedidoId)
    {
        $_SESSION['errorPedido'] = "El pago fue cancelado.";
        header("Location: " . BASE_URL);
        exit;
    }
}