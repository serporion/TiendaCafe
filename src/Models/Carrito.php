<?php

namespace Models;

use Lib\BaseDatos;
use PDO;

/**
 * Clase para manejar el modelo de Carrito.
 */
class Carrito {
    private BaseDatos $conexion;
    private mixed $stmt;

    /**
     * Constructor que instancia un objeto del modelo Carrito.
     */
    public function __construct(
        private ?int $id = null,
        private int $usuario_id = 0,
        private array $carrito = []
    ) {
        $this->conexion = new BaseDatos(); // Instancia de la conexión de base de datos.
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getUsuarioId(): int {
        return $this->usuario_id;
    }

    public function getCarrito(): array {
        return $this->carrito;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setUsuarioId(int $usuario_id): void {
        $this->usuario_id = $usuario_id;
    }

    public function setCarrito(array $carrito): void {
        $this->carrito = $carrito;
    }

}