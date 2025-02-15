<?php 

namespace Lib;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Clase que establece la conexion con la base de datos en Xampp.
 */
class BaseDatos{
    /**
     * Variables necesarias para establecer la conexión con la base de datos.
     */
    private $conexion;
    private mixed $resultado;
    private string $servidor;
    private string $usuario;
    private string $pass;
    private string $baseDatos;

    /**
     * Constructor que inicializa las variables
     */
    function __construct()
    {
        $this->servidor = $_ENV['DB_SERVERNAME'];
        $this->usuario = $_ENV['DB_USERNAME'];
        $this->pass = $_ENV['DB_PASSWORD'];
        $this->baseDatos = $_ENV['DB_DATABASE'];
        $this->conexion = $this->conectar();
    }

    /**
     * Metodo que establece la conexion con la base de datos
     * @return PDO devuelve la variable con la conexión a la base de datos
     */
    private function conectar(): PDO{
        try{
            $opciones = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::MYSQL_ATTR_FOUND_ROWS => true,
                PDO::ATTR_ERRMODE => true,
                PDO::ERRMODE_EXCEPTION => true
            );
            $conexion = new PDO("mysql:host={$this->servidor};dbname={$this->baseDatos}",$this->usuario,$this->pass,$opciones);
            return $conexion;
        }
        catch(PDOException $e){
            echo "Problemas al conectar a la base de datos. " . $e->getMessage();
            exit;
        }
    }

    /**
     * Metodo que permite realizar consultas preparadas
     * @var string recibe un string con la consulta preparada a realizar
     * @return PDOStatement devuelve una consulta ya preparada.
     */
    public function prepare(string $consultaSQL): PDOStatement {
        return $this->conexion->prepare($consultaSQL);
    }

    /**
     * Metodo que permite guardar consultas 
     * @var string recibe un string con la consulta.
     * @return void
     */
    public function consulta(string $consultaSQL): ?PDOStatement {
        try {
            $this->resultado = $this->conexion->query($consultaSQL);
            return $this->resultado;
        } catch (PDOException $e) {
            echo "Error en la consulta SQL: " . $e->getMessage();
            $this->resultado = null;
            return null;
        }
    }



    /**
     * Metodo que extraer registros
     * @return mixed con el registro extraido.
     */
    public function extraer_registro(): mixed{
        return ($fila = $this->resultado->fetch(PDO::FETCH_ASSOC))? $fila:false;
    }

    /**
     * Metodo que permite extraer todos los registros coincidentes con la consulta
     * @return array array con todos los registros
     */
    public function extraer_todos(): array{
        return $this->resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Metodo que permite saber las filas afectadas por las consultas
     * @return int devuelve el número de filas afectadas
     */
    public function filasAfectadas(): int{
        return $this->resultado->rowCount();
    }
    
    /**
     * Metodo que permite conocer el último id insertado en la base de datos.
     * @return int id entero con el último id insertado.
     */
    public function ultimoIDInsertado(): int{
        return $this->conexion->lastInsertId();
    }

    /**
     * Metodo que permite cerrar la conexion con la base de datos
     * @return void cierra la conexion con la base de datos
     */
    public function cierraConexion():void{
        $this->conexion = null;
    }

    public function limpiarRecursos() {
        if (isset($this->stmt)) {
            $this->stmt->closeCursor();
        }
    }


    /**
     * Metodo para iniciar una transaccion
     * @return bool devuelve true si se inicia y false sino
     */
    public function beginTransaction(): bool {
        return $this->conexion->beginTransaction();
    }

    /**
     * Metodo para realizar un commit dentro de una transaccion
     * @return bool devulve true si se realizar y false sino
     */
    public function commit(): bool {
        return $this->conexion->commit();
    }

    /**
     * Metodo que deshace todo lo que haya dentro de una transaccion
     * si hay algun error
     * @return bool devuelve true si no hay errores y false si los hay 
     */
    public function rollBack(): bool {
        return $this->conexion->rollBack();
    }
}
