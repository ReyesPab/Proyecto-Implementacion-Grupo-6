<?php
namespace App\config;

class responseHTTP {
    public static $mensaje = [
        'status' => '',
        'message' => '',
        'data' => []
    ];

    // Respuesta 200: OK
    final public static function status200(string $message = 'OK', array $data = []) {
        self::$mensaje['status'] = '200';
        self::$mensaje['message'] = $message;
        self::$mensaje['data'] = $data;
        http_response_code(200);
        return self::$mensaje;
    }

    // Respuesta 201: Created
    final public static function status201(string $message = 'Created', array $data = []) {
        self::$mensaje['status'] = '201';
        self::$mensaje['message'] = $message;
        self::$mensaje['data'] = $data;
        http_response_code(201);
        return self::$mensaje;
    }

    // Respuesta 400: Bad Request
    final public static function status400(string $message = 'Bad Request') {
        self::$mensaje['status'] = '400';
        self::$mensaje['message'] = $message;
        self::$mensaje['data'] = [];
        http_response_code(400);
        return self::$mensaje;
    }

    // Respuesta 401: Unauthorized
    final public static function status401(string $message = 'Unauthorized') {
        self::$mensaje['status'] = '401';
        self::$mensaje['message'] = $message;
        self::$mensaje['data'] = [];
        http_response_code(401);
        return self::$mensaje;
    }

    // Respuesta 404: Not Found
    final public static function status404(string $message = 'Not Found') {
        self::$mensaje['status'] = '404';
        self::$mensaje['message'] = $message;
        self::$mensaje['data'] = [];
        http_response_code(404);
        return self::$mensaje;
    }

    // === AGREGAR ESTE MÉTODO QUE FALTA ===
    // Respuesta 405: Method Not Allowed
    final public static function status405(string $message = 'Method Not Allowed') {
        self::$mensaje['status'] = '405';
        self::$mensaje['message'] = $message;
        self::$mensaje['data'] = [];
        http_response_code(405);
        return self::$mensaje;
    }

    // Respuesta 500: Internal Server Error
    final public static function status500(string $message = 'Internal Server Error') {
        self::$mensaje['status'] = '500';
        self::$mensaje['message'] = $message;
        self::$mensaje['data'] = [];
        http_response_code(500);
        return self::$mensaje;
    }

    // Respuesta personalizada
    final public static function customResponse(int $statusCode, string $message, array $data = []) {
        self::$mensaje['status'] = (string)$statusCode;
        self::$mensaje['message'] = $message;
        self::$mensaje['data'] = $data;
        http_response_code($statusCode);
        return self::$mensaje;
    }
}