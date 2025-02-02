<?php

namespace Models;

use DateTime;
use Lib\BaseDatos;
use Lib\Validar;

/**
 * Clase para crear los objetos Pedido
 */
class Pedido {
    private BaseDatos $conexion;
    private mixed $stmt;

    /**
     * Constructor que instancia objetos de la clase Pedido
     */
    public function __construct(
        private ?int $id = null,
        private int $usuario_id = 0,
        private string $provincia = "",
        private string $localidad = "",
        private string $direccion = "",
        private float $coste = 0.0,
        private string $estado = "",
        private string $fecha = "",
        private string $hora = ""
    ) {
        $this->conexion = new BaseDatos();
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getUsuarioId(): int {
        return $this->usuario_id;
    }
    
    public function getProvincia(): string {
        return $this->provincia;
    }
    
    public function getLocalidad(): string {
        return $this->localidad;
    }
    
    public function getDireccion(): string {
        return $this->direccion;
    }
    
    public function getCoste(): float {
        return $this->coste;
    }
    
    public function getEstado(): string {
        return $this->estado;
    }
    
    public function getFecha(): string {
        return $this->fecha;
    }
    
    public function getHora(): string {
        return $this->hora;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }
    
    public function setUsuarioId(int $usuario_id): void {
        $this->usuario_id = $usuario_id;
    }
    
    public function setProvincia(string $provincia): void {
        $this->provincia = $provincia;
    }
    
    public function setLocalidad(string $localidad): void {
        $this->localidad = $localidad;
    }
    
    public function setDireccion(string $direccion): void {
        $this->direccion = $direccion;
    }
    
    public function setCoste(float $coste): void {
        $this->coste = $coste;
    }
    
    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }
    
    public function setFecha(string $fecha): void {
        $this->fecha = $fecha;
    }
    
    public function setHora(string $hora): void {
        $this->hora = $hora;
    }

    /**
     * Metodo para validar los campos de los formularios
     * @return array array si existen errores.
     */
    public function validarDatos(): array {
        $errores = [];
    
        // Validar dirección
        if (empty($this->direccion)) {
            $errores["direccion"] = "El campo 'dirección' es obligatorio";
        } 

        if (empty($this->provincia)) {
            $errores["provincia"] = "El campo 'provincia' es obligatorio";
        } 

        if (empty($this->localidad)) {
            $errores["localidad"] = "El campo 'localidad' es obligatorio";
        } 
        


        if (!Validar::validateDireccion($this->direccion)) {
            $errores["direccion"] = "El formato de la direccion no es valido";
        }

        if (!Validar::validateCiudad($this->provincia)) {
            $errores["provincia"] = "El formato de la provincia no es valido";
        }

        if (!Validar::validateCiudad($this->localidad)) {
            $errores["localidad"] = "El formato de la localidad no es valido";
        }
    
        return $errores;
    }

    
    /**
     * Metodo para sanetizar los datos recibidos por el formulario
     * @return void
     */
    public function sanitizarDatos(): void {
        $this->direccion = Validar::sanitizeString($this->direccion);
        $this->provincia = Validar::sanitizeString($this->provincia);
        $this->localidad = Validar::sanitizeString($this->localidad);
    }

    

}
