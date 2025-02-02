<?php

namespace Services;

use Models\Categoria;
use Repositories\CategoriaRepository;

/**
 * Clase que recibe peticiones de CategoriaController y se pone en contacto con CategoriaRepository
 */
class CategoriaService {

    private CategoriaRepository $categoriaRepository;

    public function __construct() {
        $this->categoriaRepository = new CategoriaRepository();
    }

    /**
     * Metodo que llama al repository para guardar una categoria
     * @var array $userData con los datos de la categoria a guardar
     * @return bool|string
     */
    public function guardarCategoria(array $userData): bool|string {
        try {
            $categoria = new Categoria(
                null,
                $userData['nombre']
            );

            return $this->categoriaRepository->guardarCategoria($categoria);
        } 
        catch (\Exception $e) {
            error_log("Error al guardar la categoria: " . $e->getMessage());
            return false;
        }
    }
    

    /**
     * Metodo que llama al repository para listar todas las categorias
     * @return array
     */
    public function listarCategorias(): array {
        return $this->categoriaRepository->listarCategorias();
    }

    /**
     * Metodo que llama al repository para actualizar una categoria
     * @var array $userData con los datos a actualizar
     * @var int $id de la categoria a actualizar
     * @return bool|string
     */
    public function actualizarCategoria(array $userData, int $id): bool|string {
        try {
            $categoria = new Categoria(
                null,
                $userData['nombre']
            );

            return $this->categoriaRepository->actualizarCategoria($categoria, $id);
        } 
        catch (\Exception $e) {
            error_log("Error al actualizar la categoria: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Metodo que llama al repository para borrar una categoria.
     * @var int $id con la categoria a borrar
     * @return bool
     */
    public function borrarCategoria(int $id): bool {
        return $this->categoriaRepository->borrarCategoria($id);
    }


}