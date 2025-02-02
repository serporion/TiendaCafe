<?php

namespace Lib;
/**
 * Clase con los distintos metodos para validar los campos de los formulario
 */
class Validar {

    /**
     * Metodo para sanetizar los string
     * @var string con el texto a sanetizar
     * @return string texto sanetizado
     */
    public static function sanitizeString(string $input): string {
        return strip_tags(trim($input));
    }

     /**
     * Metodo para sanetizar los correos
     * @var string con el correo a sanetizar
     * @return string correo sanetizado
     */
    public static function sanitizeEmail(string $email): string {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

     /**
     * Metodo para sanetizar los telefonos
     * @var string con el telefono a sanetizar
     * @return string telefono sanetizado
     */
    public static function sanitizePhone(string $phone): string {
        return preg_replace('/[^0-9+\-\(\) ]/', '', $phone);
    }

     /**
     * Metodo para sanetizar los int
     * @var int con el texto a sanetizar
     * @return int entero sanetizado
     */
    public static function sanitizeInt($input): int {
        return (int) preg_replace('/[^0-9-]/', '', $input);
    }

    /**
     * Metodo para sanetizar los float
     * @var float con el flaot a sanetizar
     * @return int float sanetizado
     */
    public static function sanitizeDouble($input): float {
        $cleaned = preg_replace('/[^0-9\.-]/', '', str_replace(',', '.', $input));
        return (float) $cleaned;
    }

    public static function validatePassword(string $password): bool {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        return preg_match($pattern, $password);
    }

    /**
     * Metodos para validar los distintos tipos de string
     */
    public static function validateString(string $input): bool {
        return !empty($input) && is_string($input);
    }

    public static function validateDireccion(string $input): bool {
        return preg_match('/^[\p{L}\d\s.,-]+$/u', trim($input)) === 1;
    }

    public static function validateCiudad(string $input): bool {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', trim($input)) === 1;
    }

    public static function validateNombre(string $input): bool {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $input) === 1;
    }
    

    public static function validateApellidos(string $input): bool {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $input);
    }

    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validateInt($input): bool {
        return filter_var($input, FILTER_VALIDATE_INT) !== false;
    }

    public static function validateDouble($input): bool {
        return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Metodo para validar las fecha
     * @var string fecha a validar
     * @return bool si hay error en el formato devuelve false
     */
    public static function validateDate(string $date): bool {
        $dateArray = explode('-', $date);
        return count($dateArray) === 3 && checkdate((int) $dateArray[1], (int) $dateArray[2], (int) $dateArray[0]);
    }
    

}

?>
