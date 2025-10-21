<?php
namespace App\config;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Security {

    final public static function secretKey() {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        return $_ENV['SECRET_KEY'];
    }

    final public static function createPassword(string $pass) {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    final public static function validatePassword(string $pw, string $pwh) {
        return password_verify($pw, $pwh);
    }

    final public static function createTokenJwt(string $key, array $data) {
        $payload = [
            "iat" => time(),
            "exp" => time() + 3600,
            "data" => $data
        ];
        return JWT::encode($payload, $key, 'HS256');
    }

    final public static function validateTokenJwt(string $key) {
        if (!isset(getallheaders()['Authorization'])) {
            die(json_encode(responseHTTP::status400('Token de acceso requerido')));
        }
        try {
            $jwt = explode(" ", getallheaders()['Authorization']);
            return JWT::decode($jwt[1], new Key($key, 'HS256'));
        } catch (\Exception $e) {
            die(json_encode(responseHTTP::status401('Token inválido o expirado')));
        }
    }

    final public static function getDataJwt() {
        $jwt_decoded = self::validateTokenJwt(self::secretKey());
        return $jwt_decoded->data;
    }

    // ========== MÉTODOS DE VALIDACIÓN Y SANITIZACIÓN ==========

    /**
     * Sanitizar entrada de datos
     */
    final public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar formato de contraseña robusta
     */
    final public static function validarPassword($password, $usuario = '') {
        $errores = [];
        
        // Longitud mínima y máxima
        if (strlen($password) < 5) {
            $errores[] = "La contraseña debe tener al menos 5 caracteres";
        }
        
        if (strlen($password) > 10) {
            $errores[] = "La contraseña no puede tener más de 10 caracteres";
        }
        
        // Contiene al menos una mayúscula
        if (!preg_match('/[A-Z]/', $password)) {
            $errores[] = "La contraseña debe contener al menos una letra mayúscula";
        }
        
        // Contiene al menos una minúscula
        if (!preg_match('/[a-z]/', $password)) {
            $errores[] = "La contraseña debe contener al menos una letra minúscula";
        }
        
        // Contiene al menos un número
        if (!preg_match('/[0-9]/', $password)) {
            $errores[] = "La contraseña debe contener al menos un número";
        }
        
        // Contiene al menos un carácter especial
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            $errores[] = "La contraseña debe contener al menos un carácter especial";
        }
        
        // No puede ser igual al usuario
        if (!empty($usuario) && strtoupper($password) === strtoupper($usuario)) {
            $errores[] = "La contraseña no puede ser igual al usuario";
        }
        
        // No se permiten espacios
        if (preg_match('/\s/', $password)) {
            $errores[] = "La contraseña no puede contener espacios";
        }
        
        return $errores;
    }
    
    /**
     * Validar formato de email
     */
    final public static function validarEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "El formato del correo electrónico no es válido";
        }
        
        // Validar longitud máxima (50 caracteres según BD)
        if (strlen($email) > 50) {
            return "El correo electrónico no puede tener más de 50 caracteres";
        }
        
        return null;
    }
    
    /**
     * Validar campo obligatorio con longitud máxima
     */
   final public static function validarCampo($valor, $longitudMaxima, $nombreCampo, $permitirEspacios = false) {
    $errores = [];
    
    // CORRECCIÓN: Convertir null a string vacío
    $valor = $valor ?? '';
    $valor = trim($valor);
    
    // Validar que no esté vacío
    if (empty($valor)) {
        $errores[] = "El campo $nombreCampo es obligatorio";
    }
    
    // Validar longitud máxima
    if (strlen($valor) > $longitudMaxima) {
        $errores[] = "El campo $nombreCampo no puede tener más de $longitudMaxima caracteres";
    }
    
    // Validar espacios múltiples
    if (!$permitirEspacios && preg_match('/\s{2,}/', $valor)) {
        $errores[] = "El campo $nombreCampo no puede tener espacios múltiples";
    }
    
    return $errores;
}
    
   /**
 * Validar campo de usuario
 */
final public static function validarUsuario($usuario) {
    $errores = [];
    
    // Validar obligatorio
    if (empty(trim($usuario))) {
        $errores[] = "El usuario es obligatorio";
    }
    
    // Validar longitud (15 caracteres según BD)
    if (strlen($usuario) > 15) {
        $errores[] = "El usuario no puede tener más de 15 caracteres";
    }
    
    // Validar que no contenga espacios
    if (preg_match('/\s/', $usuario)) {
        $errores[] = "El usuario no puede contener espacios";
    }
    
    return $errores;
}
       
    
    /**
     * Generar contraseña robusta automática
     */
    final public static function generarPasswordRobusta($longitud = 8) {
        $mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $minusculas = 'abcdefghijklmnopqrstuvwxyz';
        $numeros = '0123456789';
        $especiales = '!@#$%^&*()-_=+';
        
        $password = '';
        
        // Asegurar al menos un carácter de cada tipo
        $password .= $mayusculas[rand(0, strlen($mayusculas) - 1)];
        $password .= $minusculas[rand(0, strlen($minusculas) - 1)];
        $password .= $numeros[rand(0, strlen($numeros) - 1)];
        $password .= $especiales[rand(0, strlen($especiales) - 1)];
        
        // Completar el resto de la longitud
        $todosCaracteres = $mayusculas . $minusculas . $numeros . $especiales;
        for ($i = strlen($password); $i < $longitud; $i++) {
            $password .= $todosCaracteres[rand(0, strlen($todosCaracteres) - 1)];
        }
        
        // Mezclar los caracteres
        return str_shuffle($password);
    }
    
    /**
     * Enmascarar contraseña para mostrar
     */
    final public static function enmascararPassword($password) {
        return str_repeat('•', strlen($password));
    }

    /**
     * Validar respuesta de seguridad (permite espacios simples)
     */
    final public static function validarRespuestaSeguridad($respuesta) {
        $errores = [];
        
        if (empty(trim($respuesta))) {
            $errores[] = "La respuesta de seguridad es obligatoria";
        }
        
        // Longitud máxima (255 según BD)
        if (strlen($respuesta) > 255) {
            $errores[] = "La respuesta no puede tener más de 255 caracteres";
        }
        
        // Validar espacios múltiples (solo permitir un espacio entre palabras)
        if (preg_match('/\s{2,}/', $respuesta)) {
            $errores[] = "La respuesta no puede tener espacios múltiples";
        }
        
        return $errores;
    }

    /**
     * Validar nombre de usuario (100 caracteres según BD)
     */
    final public static function validarNombreUsuario($nombre) {
        $errores = [];
        
        if (empty(trim($nombre))) {
            $errores[] = "El nombre de usuario es obligatorio";
        }
        
        if (strlen($nombre) > 100) {
            $errores[] = "El nombre no puede tener más de 100 caracteres";
        }
        
        // Solo permitir letras, números y espacios simples
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
            $errores[] = "El nombre solo puede contener letras y espacios";
        }
        
        // Validar espacios múltiples
        if (preg_match('/\s{2,}/', $nombre)) {
            $errores[] = "El nombre no puede tener espacios múltiples";
        }
        
        return $errores;
    }

    /**
     * Validar número de identidad (20 caracteres según BD)
     */
    final public static function validarNumeroIdentidad($numero) {
        $errores = [];
        
        // Longitud máxima
        if (strlen($numero) > 20) {
            $errores[] = "El número de identidad no puede tener más de 20 caracteres";
        }
        
        // Solo permitir números y guiones
        if (!empty($numero) && !preg_match('/^[0-9\-]+$/', $numero)) {
            $errores[] = "El número de identidad solo puede contener números y guiones";
        }
        
        return $errores;
    }

    /**
     * Validar que las contraseñas coincidan
     */
    final public static function validarCoincidenciaPassword($password1, $password2) {
        if ($password1 !== $password2) {
            return "Las contraseñas no coinciden";
        }
        return null;
    }

    /**
     * Validar fecha de vencimiento
     */
    final public static function validarFechaVencimiento($fecha) {
        if (empty($fecha)) {
            return "La fecha de vencimiento es obligatoria";
        }
        
        // Validar formato de fecha
        $fechaObj = \DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            return "El formato de fecha debe ser YYYY-MM-DD";
        }
        
        // Validar que no sea una fecha pasada
        $hoy = new \DateTime();
        if ($fechaObj < $hoy) {
            return "La fecha de vencimiento no puede ser una fecha pasada";
        }
        
        return null;
    }

    /**
     * Validar estado de usuario
     */
    final public static function validarEstadoUsuario($estado) {
        $estadosPermitidos = ['Activo', 'Inactivo', 'Bloqueado', 'Nuevo'];
        
        if (!in_array($estado, $estadosPermitidos)) {
            return "Estado de usuario no válido. Debe ser: " . implode(', ', $estadosPermitidos);
        }
        
        return null;
    }

    /**
     * Validar rol de usuario
     */
    final public static function validarRol($rol) {
        if (!is_numeric($rol) || $rol <= 0) {
            return "El rol debe ser un número válido";
        }
        
        return null;
    }

    /**
     * Validar parámetros del sistema
     */
    final public static function validarParametro($parametro, $valor) {
        $errores = [];
        
        // Validar nombre del parámetro (50 caracteres según BD)
        if (empty(trim($parametro))) {
            $errores[] = "El nombre del parámetro es obligatorio";
        }
        
        if (strlen($parametro) > 50) {
            $errores[] = "El nombre del parámetro no puede tener más de 50 caracteres";
        }
        
        // Validar valor del parámetro (100 caracteres según BD)
        if (empty(trim($valor))) {
            $errores[] = "El valor del parámetro es obligatorio";
        }
        
        if (strlen($valor) > 100) {
            $errores[] = "El valor del parámetro no puede tener más de 100 caracteres";
        }
        
        return $errores;
    }
}