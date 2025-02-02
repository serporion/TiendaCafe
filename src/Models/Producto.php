<?php

namespace Models;

use DateTime;
use Lib\BaseDatos;
use Lib\Validar;

/**
 * Clase para crear los objetos Producto
 */
class producto {
    private BaseDatos $conexion;
    private mixed $stmt;

    /**
     * Constructor que instancia objetos de la clase Producto.
     */
    public function __construct(
        private ?int $id = null,
        private int $categoria_id = 0,
        private string $nombre = "",
        private string $descripcion = "",
        private float $precio = 0.0,
        private int $stock = 0,
        private string $oferta = "",
        private string $fecha = "",
        private string $imagen = "",
    ) {
        $this->conexion = new BaseDatos();
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getCategoriaId(): int {
        return $this->categoria_id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getDescripcion(): string {
        return $this->descripcion;
    }

    public function getPrecio(): float {
        return $this->precio;
    }

    public function getStock(): int {
        return $this->stock;
    }

    public function getOferta(): string {
        return $this->oferta;
    }

    public function getFecha(): string {
        return $this->fecha;
    }

    public function getImagen(): string {
        return $this->imagen;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setCategoriaId(int $categoria_id): void {
        $this->categoria_id = $categoria_id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setDescripcion(string $descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function setPrecio(float $precio): void {
        $this->precio = $precio;
    }

    public function setStock(int $stock): void {
        $this->stock = $stock;
    }

    public function setOferta(string $oferta): void {
        $this->oferta = $oferta;
    }

    public function setFecha(string $fecha): void {
        $this->fecha = $fecha;
    }

    public function setImagen(string $imagen): void {
        $this->imagen = $imagen;
    }

    /**
     * Metodo para validar los campos de los formularios
     * @return array array si existen errores.
     */
    public function validarDatosProductos(): array {
        $errores = [];

        if (empty($this->nombre)) {
            $errores["nombre"] = "El campo 'nombre' es obligatorio";
        }

        if (empty($this->precio)) {
            $errores["precio"] = "El campo 'precio' es obligatorio";
        }

        if (empty($this->stock)) {
            $errores["stock"] = "El campo 'stock' es obligatorio";
        }

        if (empty($this->fecha)) {
            $errores["fecha"] = "El campo 'fecha' es obligatorio";
        }

        // Validar nombre
        if (!empty($this->nombre) && !Validar::validateString($this->nombre)) {
            $errores['nombre'] = "El nombre no puede contener caracteres especiales";
        }  

        // Validar descripcion
        if(!empty($this->descripcion) && strlen($this->descripcion) > 65535){
            $errores['descripcion'] = "La longitud de la descripción supera la longitud máxima";
        } 

        // Validar precio
        if(!empty($this->precio) && !Validar::validateDouble($this->precio)){
            $errores['precio'] = "La precio debe ser un número decimal";
        }

        // Validar stock
        if(!empty($this->stock) && !Validar::validateInt($this->stock)){
            $errores['stock'] = "El stock debe ser un número entero";
        } 

        // Validar oferta
        if (!empty($this->oferta) && !Validar::validateString($this->oferta) && strlen($this->oferta) > 2) {
            $errores['oferta'] = "La oferta no puede contener caracteres especiales y no puede ser más largo de 2 caracteres";
        } 

        // Validar fecha
        if (!empty($this->fecha) && !Validar::validateDate($this->fecha)) {
            $errores['fecha'] = "La fecha no es válida";
        }

        // Validar imagen
        if (!empty($this->imagen) && !Validar::validateString($this->imagen)) {
            $errores['imagen'] = "El nombre de la imagen no puede contener caracteres especiales";
        } 

        return $errores;
    }
    /**
     * Metodo para validar los campos del formulario de actualizacion
     * @return array array si existen errores.
     */
    public function validateUpdate(): array {
        $errores = [];

        if (empty($this->nombre)) {
            $errores["nombre"] = "El campo 'nombre' es obligatorio";
        }

        if (empty($this->precio)) {
            $errores["precio"] = "El campo 'precio' es obligatorio";
        }

        if (empty($this->stock)) {
            $errores["stock"] = "El campo 'stock' es obligatorio";
        }

        // Validar nombre
        if (!empty($this->nombre) && !Validar::validateString($this->nombre)) {
            $errores['nombre'] = "El nombre no puede contener caracteres especiales";
        }  

        // Validar descripcion
        if(!empty($this->descripcion) && strlen($this->descripcion) > 65535){
            $errores['descripcion'] = "La longitud de la descripción supera la longitud máxima";
        } 

        // Validar precio
        if(!empty($this->precio) && !Validar::validateDouble($this->precio)){
            $errores['precio'] = "La precio debe ser un número decimal";
        }

        // Validar stock
        if(!empty($this->stock) && !Validar::validateInt($this->stock)){
            $errores['stock'] = "El stock debe ser un número entero";
        } 

        // Validar oferta
        if (!empty($this->oferta) && !Validar::validateString($this->oferta) && strlen($this->oferta) > 2) {
            $errores['oferta'] = "La oferta no puede contener caracteres especiales y no puede ser más largo de 2 caracteres";
        }

        // Validar imagen
        if (!empty($this->imagen) && !Validar::validateString($this->imagen)) {
            $errores['imagen'] = "El nombre de la imagen no puede contener caracteres especiales";
        } 

        return $errores;
    }

    /**
     * Metodo para sanetizar los datos recibidos por el formulario
     * @return void
     */
    public function sanitizarDatos(): void {
        $this->id = Validar::sanitizeInt($this->id);
        $this->categoria_id = Validar::sanitizeInt($this->categoria_id);
        $this->nombre = Validar::sanitizeString($this->nombre);
        $this->descripcion = Validar::sanitizeString($this->descripcion);
        $this->precio = Validar::sanitizeDouble($this->precio);
        $this->stock = Validar::sanitizeInt($this->stock);
        $this->oferta = Validar::sanitizeString($this->oferta);
        $this->fecha = Validar::sanitizeString($this->fecha);
        $this->imagen = Validar::sanitizeString($this->imagen);
    }

    

}
