<?php

namespace Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

//composer require paypal/rest-api-sdk-php
//composer remove paypal/rest-api-sdk-php
//composer require paypal/paypal-checkout-sdk


class PayPalService
{
    private $client;

    public function __construct()
    {

        $clientId = getenv('PAYPAL_CLIENT_ID') ?? null; //: $_ENV['PAYPAL_CLIENT_ID'];
        $clientSecret = getenv('PAYPAL_SECRET') ?? null; //: $_ENV['PAYPAL_SECRET'];
        $mode = getenv('PAYPAL_MODE') ?? null; //: $_ENV['PAYPAL_MODE']; // sandbox o live

        // Crea el entorno según el modo: sanbox que sería en modo de pruebas, o live que sería en modo producción.
        $environment = $mode === 'sandbox'
            ? new SandboxEnvironment($clientId, $clientSecret)
            : new ProductionEnvironment($clientId, $clientSecret);

        $this->client = new PayPalHttpClient($environment);
    }

    /**
     * Método que nos ayuda a obtiene el cliente utilizado para realizar
     * solicitudes.
     *
     * @return object Instancia del cliente configurado.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Método que crea un pago en PayPal utilizando la información proporcionada y
     * devuelve el enlace de aprobación.
     *
     * @param float $monto importe total del pago en la moneda especificada.
     * @param string $moneda código de la moneda en formato ISO 4217 (por ejemplo, "USD", "EUR").
     * @param string $descripcion describe el pago que será visible en PayPal.
     * @param string|int $pedidoId pedido asociado al pago.
     *
     * @return string Enlace de aprobación de PayPal para completar el proceso de pago.
     * @throws \Exception Si ocurre un error al crear el pago o no se encuentra el enlace de aprobación.
     */
    public function crearPago($monto, $moneda, $descripcion, $pedidoId)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $pedidoId,
                    'amount' => [
                        'currency_code' => $moneda,
                        'value' => $monto,
                    ],
                    'description' => $descripcion,
                ]
            ],
            'application_context' => [
                'return_url' => getenv('BASE_URLPAYPAL') . "PayPal/pagoExitoso?pedidoId={$pedidoId}",
                'cancel_url' => getenv('BASE_URLPAYPAL') . "PayPal/pagoCancelado?pedidoId={$pedidoId}",
                //'return_url' => "http:/localhost/ejercicios/DWES/Tienda/PayPal/pagoExitoso?pedidoId={$pedidoId}",
                //'cancel_url' => "http:/localhost/ejercicios/DWES/Tienda/PayPal/pagoCancelado?pedidoId={$pedidoId}",
            ]
        ];

        try {
            $response = $this->client->execute($request);

            $jsonOrden = $response->result;

            // Localizar solo el enlace de aprobación por si nos hace falta.
            foreach ($response->result->links as $link) {
                if ($link->rel === 'approve') {
                    return $link->href;
                }
            }

            throw new \Exception('No se encontró el enlace de aprobación de PayPal.');

        } catch (\Exception $e) {
            throw new \Exception("Error creando el pago en PayPal: " . $e->getMessage());
        }
    }

    /**
     * Método que captura un pago para un ID de pedido especificado mediante la API de PayPal.
     *
     * @param string $orderId el ID del pedido para capturar el pago.
     *
     * @return object El resultado de la captura de pago devuelto por la API de PayPal.
     * @throws \Exception Si se produce un error al capturar el pago en PayPal.
     */
    public function capturarPago($orderId)
    {
        $request = new \PayPalCheckoutSdk\Orders\OrdersCaptureRequest($orderId);
        $request->prefer('return=representation');

        try {
            $response = $this->client->execute($request);

            // Retornar JSON completo del pago capturado
            return $response->result;

        } catch (\Exception $e) {
            throw new \Exception("Error capturando el pago en PayPal: " . $e->getMessage());
        }
    }
}