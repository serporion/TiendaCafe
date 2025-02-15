<?php

namespace Services;

use Repositories\CarritoRepository;
use Models\Carrito;

class CarritoService
{
    private CarritoRepository $carritoRepository;

    public function __construct()
    {
        $this->carritoRepository = new CarritoRepository();

    }

    /**
     * Guarda el carrito en la base de datos.
     *
     * @param int $usuarioId
     * @param array $carrito
     * @return bool
     */
    public function guardarCarrito(int $usuario, array $carrito): bool
    {
        try {
            return $this->carritoRepository->guardarCarrito($usuario, $carrito);
        } catch (\Exception $e) {
            error_log("Error al guardar carrito " . $e->getMessage());
            return false;
        }
    }

    public function borrarCarrito(int $usuario): bool
    {
        try {
            return $this->carritoRepository->borrarCarrito($usuario);
        } catch (\Exception $e) {
            error_log("Error al borrar carrito " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el carrito asociado a un usuario específico.
     *
     * @param int $usuarioId Identificador único del usuario.
     * @return array|null Retorna un arreglo con los datos del carrito si existe,
     * o null si ocurre un error o no se encuentra.
     */
    public function obtenerCarritoPorUsuarioId(int $usuarioId): ?array
    {
        try {

            return $this->carritoRepository->cargarCarritoDeBaseDatos($usuarioId);

        } catch (\Exception $e) {
            error_log("Error en obtener el carrito por usuario: " . $e->getMessage());
            return null;
        }
    }
}