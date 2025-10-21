<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\userModel;
use PDO;

class userController {
    
    private $method;
    private $data;
    
    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
    }
    
    // Crear nuevo usuario
    public function crearUsuario() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Validar datos requeridos
        $camposRequeridos = ['usuario', 'nombre_usuario', 'contraseña', 'id_rol'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        // Validar usuario
        $erroresUsuario = Security::validarUsuario($this->data['usuario']);
        if (!empty($erroresUsuario)) {
            echo json_encode(responseHTTP::status400(implode(', ', $erroresUsuario)));
            return;
        }
        
        // Validar nombre de usuario
        $erroresNombre = Security::validarNombreUsuario($this->data['nombre_usuario']);
        if (!empty($erroresNombre)) {
            echo json_encode(responseHTTP::status400(implode(', ', $erroresNombre)));
            return;
        }
        
        // Validar número de identidad si se proporciona
        if (!empty($this->data['numero_identidad'])) {
            $erroresIdentidad = Security::validarNumeroIdentidad($this->data['numero_identidad']);
            if (!empty($erroresIdentidad)) {
                echo json_encode(responseHTTP::status400(implode(', ', $erroresIdentidad)));
                return;
            }
        }
        
        // Validar correo electrónico si se proporciona
        if (!empty($this->data['correo_electronico'])) {
            $errorEmail = Security::validarEmail($this->data['correo_electronico']);
            if ($errorEmail) {
                echo json_encode(responseHTTP::status400($errorEmail));
                return;
            }
        }
        
        // Validar contraseña (para creación del admin, puede ser menos robusta)
        $password = $this->data['contraseña'];
        if (strlen($password) < 5 || strlen($password) > 10) {
            echo json_encode(responseHTTP::status400('La contraseña debe tener entre 5 y 10 caracteres'));
            return;
        }
        
        if (preg_match('/\s/', $password)) {
            echo json_encode(responseHTTP::status400('La contraseña no puede contener espacios'));
            return;
        }
        
        // Validar rol
        $errorRol = Security::validarRol($this->data['id_rol']);
        if ($errorRol) {
            echo json_encode(responseHTTP::status400($errorRol));
            return;
        }
        
        // Validar estado de usuario si se proporciona
        if (!empty($this->data['estado_usuario'])) {
            $errorEstado = Security::validarEstadoUsuario($this->data['estado_usuario']);
            if ($errorEstado) {
                echo json_encode(responseHTTP::status400($errorEstado));
                return;
            }
        }
        
        // Crear usuario usando procedimiento almacenado
        $result = userModel::crearUsuario($this->data);
        
        if ($result['success']) {
            echo json_encode(responseHTTP::status201($result['message'], ['id_usuario' => $result['id_usuario']]));
        } else {
            echo json_encode(responseHTTP::status400($result['message']));
        }
    }
    
    // Listar usuarios
    // Listar usuarios
public function listarUsuarios() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $usuarios = userModel::listarUsuarios();
    
    // DEBUG TEMPORAL: Ver qué se está enviando al frontend
    error_log("DEBUG userController - Total usuarios: " . count($usuarios));
    if (!empty($usuarios)) {
        error_log("DEBUG userController - Primer usuario enviado: " . print_r($usuarios[0], true));
    }
    
    echo json_encode(responseHTTP::status200('Usuarios obtenidos', ['usuarios' => $usuarios]));
}
    
    // Obtener usuario específico
    public function obtenerUsuario() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_usuario'])) {
            echo json_encode(responseHTTP::status400('ID de usuario requerido'));
            return;
        }
        
        $usuario = userModel::obtenerUsuario($this->data['id_usuario']);
        
        if ($usuario) {
            echo json_encode(responseHTTP::status200('Usuario obtenido', ['usuario' => $usuario]));
        } else {
            echo json_encode(responseHTTP::status404('Usuario no encontrado'));
        }
    }
    
    // Actualizar usuario
    public function actualizarUsuario() {
        if ($this->method != 'put' && $this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_usuario'])) {
            echo json_encode(responseHTTP::status400('ID de usuario requerido'));
            return;
        }
        
        // Validar nombre de usuario si se proporciona
        if (!empty($this->data['nombre_usuario'])) {
            $erroresNombre = Security::validarNombreUsuario($this->data['nombre_usuario']);
            if (!empty($erroresNombre)) {
                echo json_encode(responseHTTP::status400(implode(', ', $erroresNombre)));
                return;
            }
        }
        
        // Validar número de identidad si se proporciona
        if (!empty($this->data['numero_identidad'])) {
            $erroresIdentidad = Security::validarNumeroIdentidad($this->data['numero_identidad']);
            if (!empty($erroresIdentidad)) {
                echo json_encode(responseHTTP::status400(implode(', ', $erroresIdentidad)));
                return;
            }
        }
        
        // Validar correo electrónico si se proporciona
        if (!empty($this->data['correo_electronico'])) {
            $errorEmail = Security::validarEmail($this->data['correo_electronico']);
            if ($errorEmail) {
                echo json_encode(responseHTTP::status400($errorEmail));
                return;
            }
        }
        
        // Validar rol si se proporciona
        if (!empty($this->data['id_rol'])) {
            $errorRol = Security::validarRol($this->data['id_rol']);
            if ($errorRol) {
                echo json_encode(responseHTTP::status400($errorRol));
                return;
            }
        }
        
        // Validar estado de usuario si se proporciona
        if (!empty($this->data['estado_usuario'])) {
            $errorEstado = Security::validarEstadoUsuario($this->data['estado_usuario']);
            if ($errorEstado) {
                echo json_encode(responseHTTP::status400($errorEstado));
                return;
            }
        }
        
        // Actualizar usuario usando procedimiento almacenado
        $result = userModel::actualizarUsuario($this->data['id_usuario'], $this->data);
        
        if ($result['success']) {
            echo json_encode(responseHTTP::status200($result['message']));
        } else {
            echo json_encode(responseHTTP::status400($result['message']));
        }
    }
    
    // Resetear contraseña
    public function resetearPassword() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_usuario']) || empty($this->data['nueva_password'])) {
            echo json_encode(responseHTTP::status400('ID de usuario y nueva contraseña requeridos'));
            return;
        }
        
        // Validar contraseña
        $errores = Security::validarPassword($this->data['nueva_password']);
        if (!empty($errores)) {
            echo json_encode(responseHTTP::status400(implode(', ', $errores)));
            return;
        }
        
        // Resetear contraseña usando procedimiento almacenado
        $result = userModel::resetearPassword(
            $this->data['id_usuario'], 
            $this->data['nueva_password'],
            $this->data['modificado_por'] ?? 'SISTEMA'
        );
        
        if ($result['success']) {
            echo json_encode(responseHTTP::status200($result['message']));
        } else {
            echo json_encode(responseHTTP::status400($result['message']));
        }
    }
    
    // Obtener roles
    public function obtenerRoles() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $roles = userModel::obtenerRoles();
        
        echo json_encode(responseHTTP::status200('Roles obtenidos', ['roles' => $roles]));
    }
    
    // Generar contraseña automática
    public function generarPassword() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $password = Security::generarPasswordRobusta(8);
        
        echo json_encode(responseHTTP::status200('Contraseña generada', ['password' => $password]));
    }
    
    // Obtener parámetros del sistema
    public function obtenerParametros() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $parametros = userModel::obtenerParametros();
        
        echo json_encode(responseHTTP::status200('Parámetros obtenidos', ['parametros' => $parametros]));
    }
    
    // Obtener bitácora
    public function obtenerBitacora() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $bitacora = userModel::obtenerBitacora();
        
        echo json_encode(responseHTTP::status200('Bitácora obtenida', ['bitacora' => $bitacora]));
    }

    // Verificar disponibilidad de usuario
    public function verificarUsuario() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['usuario'])) {
            echo json_encode(responseHTTP::status400('Usuario requerido'));
            return;
        }
        
        $existe = userModel::usuarioExiste($this->data['usuario']);
        
        if ($existe) {
            echo json_encode(responseHTTP::status400('El usuario ya existe'));
        } else {
            echo json_encode(responseHTTP::status200('Usuario disponible'));
        }
    }

    // Cambiar estado de usuario
    public function cambiarEstado() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_usuario']) || empty($this->data['estado'])) {
            echo json_encode(responseHTTP::status400('ID de usuario y estado requeridos'));
            return;
        }
        
        $estadosPermitidos = ['Activo', 'Inactivo', 'Bloqueado', 'Nuevo'];
        if (!in_array($this->data['estado'], $estadosPermitidos)) {
            echo json_encode(responseHTTP::status400('Estado no válido'));
            return;
        }
        
        $result = userModel::cambiarEstadoUsuario(
            $this->data['id_usuario'], 
            $this->data['estado'],
            $this->data['modificado_por'] ?? 'SISTEMA'
        );
        
        if ($result['success']) {
            echo json_encode(responseHTTP::status200($result['message']));
        } else {
            echo json_encode(responseHTTP::status400($result['message']));
        }
    }

    // Obtener usuario completo para edición
    public function obtenerUsuarioCompleto() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_usuario'])) {
            echo json_encode(responseHTTP::status400('ID de usuario requerido'));
            return;
        }
        
        $usuario = userModel::obtenerUsuarioCompleto($this->data['id_usuario']);
        
        if ($usuario) {
            echo json_encode(responseHTTP::status200('Usuario obtenido', ['usuario' => $usuario]));
        } else {
            echo json_encode(responseHTTP::status404('Usuario no encontrado'));
        }
    }
}