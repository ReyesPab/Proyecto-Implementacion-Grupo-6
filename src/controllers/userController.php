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
            // Verificar unicidad del número de identidad
            if (userModel::numeroIdentidadExiste($this->data['numero_identidad'])) {
                echo json_encode(responseHTTP::status400('El número de identidad ya está registrado'));
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
            
            // ✅ NUEVA VALIDACIÓN: Verificar si el correo ya existe
            if (userModel::correoElectronicoExiste($this->data['correo_electronico'])) {
                echo json_encode(responseHTTP::status400('El correo electrónico ya está registrado en el sistema'));
                return;
            }
        }
        
        // Validar contraseña con los NUEVOS requisitos (5-10 caracteres robusta)
        $password = $this->data['contraseña'];
        $erroresPassword = Security::validarPasswordRobusta($password);
        if (!empty($erroresPassword)) {
            echo json_encode(responseHTTP::status400(implode(', ', $erroresPassword)));
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
            echo json_encode([
                'status' => 201,
                'message' => $result['message'],
                'data' => ['id_usuario' => $result['id_usuario']]
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    }
    
    // Obtener bitácora del sistema - CORREGIDO
// Obtener bitácora del sistema - VERSIÓN CORREGIDA
public function obtenerBitacora() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $model = new userModel();
        $resultado = $model->obtenerBitacora();
        
        // DEBUG: Log para verificar estructura
        error_log("Resultado de obtenerBitacora: " . print_r($resultado, true));
        
            // Normalizar resultado: el modelo puede devolver directamente un array
            // de filas (fetchAll) o un array asociativo con la clave 'bitacora'.
            $bitacora = [];
            if (is_array($resultado)) {
                if (isset($resultado['bitacora']) && is_array($resultado['bitacora'])) {
                    $bitacora = $resultado['bitacora'];
                } else {
                    // Asumir que $resultado es ya el listado de filas
                    $bitacora = array_values($resultado);
                }
            }

            echo json_encode([
                'status' => 200,
                'data' => [ 'bitacora' => $bitacora ],
                'message' => count($bitacora) ? 'Bitácora obtenida correctamente' : 'No hay registros en la bitácora'
            ]);
    } catch (\Exception $e) {
        error_log("Error en obtenerBitacora: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener la bitácora'));
    }
}
    
    // Listar usuarios
    public function listarUsuarios() {
        try {
            $usuarios = userModel::listarUsuarios();
            
            if (empty($usuarios)) {
                echo json_encode([
                    'status' => 200,
                    'data' => ['usuarios' => []],
                    'message' => 'No hay usuarios registrados'
                ]);
                return;
            }
            
            echo json_encode([
                'status' => 200,
                'data' => ['usuarios' => $usuarios],
                'message' => 'Usuarios obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("userController::listarUsuarios -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener usuarios'));
        }
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
    
    // Validar campos individualmente
    $errores = [];
    
    // Validar nombre de usuario si se proporciona
    if (!empty($this->data['nombre_usuario'])) {
        $erroresNombre = Security::validarNombreUsuario($this->data['nombre_usuario']);
        if (!empty($erroresNombre)) {
            $errores[] = implode(', ', $erroresNombre);
        }
    }
    
    // Validar número de identidad si se proporciona
    if (!empty($this->data['numero_identidad'])) {
        $erroresIdentidad = Security::validarNumeroIdentidad($this->data['numero_identidad']);
        if (!empty($erroresIdentidad)) {
            $errores[] = implode(', ', $erroresIdentidad);
        } else {
            // Verificar unicidad del número de identidad (excluir el propio usuario)
            $excludeId = $this->data['id_usuario'];
            if (userModel::numeroIdentidadExiste($this->data['numero_identidad'], $excludeId)) {
                $errores[] = 'El número de identidad ya está registrado por otro usuario';
            }
        }
    }
    
    // Validar correo electrónico si se proporciona
    if (!empty($this->data['correo_electronico'])) {
        $errorEmail = Security::validarEmail($this->data['correo_electronico']);
        if ($errorEmail) {
            $errores[] = $errorEmail;
        } else {
            // Verificar unicidad del correo (excluir el propio usuario)
            $excludeId = $this->data['id_usuario'];
            if (userModel::correoExiste($this->data['correo_electronico'], $excludeId)) {
                $errores[] = 'El correo electrónico ya está registrado por otro usuario';
            }
        }
    }
    
    // Validar rol si se proporciona
    if (!empty($this->data['id_rol'])) {
        $errorRol = Security::validarRol($this->data['id_rol']);
        if ($errorRol) {
            $errores[] = $errorRol;
        }
    }
    
    // Validar estado de usuario si se proporciona
    if (!empty($this->data['estado_usuario'])) {
        $errorEstado = Security::validarEstadoUsuario($this->data['estado_usuario']);
        if ($errorEstado) {
            $errores[] = $errorEstado;
        }
    }
    
    // Validar contraseña si se proporciona
    if (!empty($this->data['nueva_contraseña'])) {
        $password = $this->data['nueva_contraseña'];
        if (strlen($password) < 5 || strlen($password) > 10) {
            $errores[] = 'La contraseña debe tener entre 5 y 10 caracteres';
        }
        
        if (preg_match('/\s/', $password)) {
            $errores[] = 'La contraseña no puede contener espacios';
        }
    }
    
    // Si hay errores, retornarlos
    if (!empty($errores)) {
        echo json_encode(responseHTTP::status400(implode('; ', $errores)));
        return;
    }
    
    // Actualizar usuario
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
   // En userController.php, actualiza este método:
public function generarPassword() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Generar contraseña robusta que cumpla con los requisitos (5-10 caracteres)
    $password = Security::generarPasswordRobusta();
    
    echo json_encode([
        'status' => 200,
        'message' => 'Contraseña generada exitosamente',
        'data' => ['password' => $password]
    ]);
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

    // Verificar disponibilidad de número de identidad (AJAX)
    public function verificarIdentidad() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        if (empty($this->data['numero_identidad'])) {
            echo json_encode(responseHTTP::status400('Número de identidad requerido'));
            return;
        }

        $excludeId = $this->data['id_usuario'] ?? null;
        $existe = userModel::numeroIdentidadExiste($this->data['numero_identidad'], $excludeId);

        if ($existe) {
            echo json_encode(responseHTTP::status400('El número de identidad ya existe'));
        } else {
            echo json_encode(responseHTTP::status200('Número de identidad disponible'));
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

    public function toggle2FA() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(responseHTTP::status401('No autenticado'));
        return;
    }
    
    $idUsuario = $_SESSION['user_id'];
    $habilitar2FA = $this->data['habilitar_2fa'] ?? 0;
    
    try {
        $con = \App\db\connectionDB::getConnection();
        $sql = "UPDATE TBL_MS_USUARIOS SET HABILITAR_2FA = :habilitar_2fa WHERE ID_USUARIO = :id_usuario";
        $query = $con->prepare($sql);
        $query->execute([
            'habilitar_2fa' => $habilitar2FA,
            'id_usuario' => $idUsuario
        ]);
        
        // Registrar en bitácora
        authModel::registrarBitacora(
            $idUsuario, 
            'CONFIG_2FA', 
            ($habilitar2FA ? 'Activó' : 'Desactivó') . ' la autenticación en dos pasos'
        );
        
        $mensaje = $habilitar2FA ? 
            'Autenticación en dos pasos activada correctamente' : 
            'Autenticación en dos pasos desactivada correctamente';
            
        echo json_encode(responseHTTP::status200($mensaje));
        
    } catch (\PDOException $e) {
        error_log("Error toggle2FA: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al cambiar configuración'));
    }
}

public function cambiarPassword() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos
    if (empty($this->data['id_usuario']) || empty($this->data['nueva_password'])) {
        echo json_encode(responseHTTP::status400('Datos incompletos'));
        return;
    }
    
    $idUsuario = $this->data['id_usuario'];
    $nuevaPassword = $this->data['nueva_password'];
    $passwordActual = $this->data['password_actual'] ?? null;
    
    // Validar contraseña
    $errores = Security::validarPassword($nuevaPassword);
    if (!empty($errores)) {
        echo json_encode(responseHTTP::status400(implode(', ', $errores)));
        return;
    }
    
    // Cambiar contraseña usando procedimiento almacenado
    $result = authModel::cambiarPassword($idUsuario, $nuevaPassword, $passwordActual);
    
    if ($result['success']) {
        // NUEVO: Si el usuario era "Nuevo", actualizar su estado a "ACTIVO"
        $userData = authModel::obtenerDatosUsuarioCompletosPorId($idUsuario);
        if ($userData && $userData['ESTADO_USUARIO'] == 'Nuevo' && $userData['PRIMER_INGRESO'] == 0) {
            authModel::actualizarEstadoUsuario($idUsuario);
            
            // Registrar en bitácora
            authModel::registrarBitacora(
                $idUsuario, 
                'PRIMER_INGRESO_COMPLETADO', 
                'Usuario completó primer ingreso y cambió contraseña'
            );
        }
        
        echo json_encode(responseHTTP::status200($result['message']));
    } else {
        echo json_encode(responseHTTP::status400($result['message']));
    }
}

// Agrega este método en userController.php
public function verificarCorreo() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }

    if (empty($this->data['correo'])) {
        echo json_encode(responseHTTP::status400('Correo electrónico requerido'));
        return;
    }

    $excludeId = $this->data['excluir_usuario'] ?? null;
    
    // Verificar si el correo existe (excluyendo el usuario actual si se proporciona)
    try {
        $con = connectionDB::getConnection();
        $sql = "SELECT COUNT(*) as EXISTE FROM TBL_MS_USUARIOS WHERE CORREO_ELECTRONICO = :correo";
        
        if (!empty($excludeId)) {
            $sql .= " AND ID_USUARIO != :exclude_id";
        }
        
        $query = $con->prepare($sql);
        $params = ['correo' => $this->data['correo']];
        if (!empty($excludeId)) $params['exclude_id'] = $excludeId;
        
        $query->execute($params);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        $existe = ($result && $result['EXISTE'] > 0);
        
        if ($existe) {
            echo json_encode(responseHTTP::status400('El correo electrónico ya está registrado'));
        } else {
            echo json_encode(responseHTTP::status200('Correo electrónico disponible'));
        }
        
    } catch (\PDOException $e) {
        error_log("Error en verificarCorreo: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al verificar correo'));
    }
}

// Agrega este método en userController.php
public function obtenerEstado2FA() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar que el usuario esté autenticado
    session_start();
    if (empty($_SESSION['id_usuario'])) {
        echo json_encode(responseHTTP::status401('Usuario no autenticado'));
        return;
    }
    
    if (empty($this->data['id_usuario'])) {
        echo json_encode(responseHTTP::status400('ID de usuario requerido'));
        return;
    }
    
    $idUsuario = $this->data['id_usuario'];
    
    // Verificar que el usuario solo consulte su propia información
    if ($idUsuario != $_SESSION['id_usuario']) {
        echo json_encode(responseHTTP::status403('No tiene permisos para acceder a esta información'));
        return;
    }
    
    try {
        $con = \App\db\connectionDB::getConnection();
        
        $sql = "SELECT HABILITAR_2FA, CORREO_ELECTRONICO FROM TBL_MS_USUARIOS WHERE ID_USUARIO = :id_usuario";
        $query = $con->prepare($sql);
        $query->execute(['id_usuario' => $idUsuario]);
        $resultado = $query->fetch();
        
        if ($resultado) {
            echo json_encode(responseHTTP::status200([
                'habilitar_2fa' => (int)$resultado['HABILITAR_2FA'],
                'correo_electronico' => $resultado['CORREO_ELECTRONICO']
            ]));
        } else {
            echo json_encode(responseHTTP::status404('Usuario no encontrado'));
        }
        
    } catch (\PDOException $e) {
        error_log("Error en obtenerEstado2FA: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener configuración: ' . $e->getMessage()));
    }
}

// En userController.php, agrega este nuevo método:
public function obtenerUsuarioEdicion() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_usuario'])) {
        echo json_encode(responseHTTP::status400('ID de usuario requerido'));
        return;
    }
    
    $idUsuario = $this->data['id_usuario'];
    error_log("DEBUG - Buscando usuario para edición con ID: " . $idUsuario);
    
    $usuario = userModel::obtenerUsuarioParaEdicion($idUsuario);
    
    if ($usuario) {
        error_log("DEBUG - Usuario para edición encontrado: " . print_r($usuario, true));
        echo json_encode(responseHTTP::status200('Usuario obtenido para edición', ['usuario' => $usuario]));
    } else {
        error_log("DEBUG - Usuario NO encontrado para edición ID: " . $idUsuario);
        echo json_encode(responseHTTP::status404('Usuario no encontrado'));
    }
}

// En userController.php, agrega este método:

// En userController.php, modifica el método:

public function resetearContrasenaAdmin() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_usuario']) || empty($this->data['nueva_contrasena'])) {
        echo json_encode(responseHTTP::status400('ID de usuario y nueva contraseña requeridos'));
        return;
    }
    
    // Validar contraseña con las NUEVAS reglas (5-10 caracteres con todos los requisitos)
    $errores = Security::validarPasswordRobusta($this->data['nueva_contrasena']);
    if (!empty($errores)) {
        echo json_encode(responseHTTP::status400(implode(', ', $errores)));
        return;
    }
    
    // Resetear contraseña usando el procedimiento almacenado actualizado
    $result = userModel::resetearContrasenaAdmin(
        $this->data['id_usuario'], 
        $this->data['nueva_contrasena'], // Se encripta en el SP
        $this->data['modificado_por'] ?? 'ADMIN'
    );
    
    if ($result['success']) {
        echo json_encode(responseHTTP::status200($result['message']));
    } else {
        echo json_encode(responseHTTP::status400($result['message']));
    }
}

// En userController.php, agrega este método:

public function exportarUsuariosPDF() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $usuarios = userModel::exportarUsuariosPDF();
        
        if (empty($usuarios)) {
            echo json_encode(responseHTTP::status404('No hay usuarios para exportar'));
            return;
        }
        
        // Devolver los datos para que el frontend los procese
        echo json_encode([
            'status' => 200,
            'message' => 'Datos de usuarios obtenidos para exportación',
            'data' => ['usuarios' => $usuarios]
        ]);
        
    } catch (\Exception $e) {
        error_log("Error en exportarUsuariosPDF: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al exportar usuarios'));
    }
}

// En userController.php, agrega este método:

public function registro() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos
    $camposRequeridos = ['usuario', 'nombre_usuario', 'contrasena', 'confirmar_contrasena'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    // Validar que las contraseñas coincidan
    if ($this->data['contrasena'] !== $this->data['confirmar_contrasena']) {
        echo json_encode(responseHTTP::status400("Las contraseñas no coinciden"));
        return;
    }
    
    // Validar número de identidad si se proporciona
    if (!empty($this->data['numero_identidad'])) {
        $erroresIdentidad = Security::validarNumeroIdentidad($this->data['numero_identidad']);
        if (!empty($erroresIdentidad)) {
            echo json_encode(responseHTTP::status400(implode(', ', $erroresIdentidad)));
            return;
        }
        // Verificar unicidad del número de identidad
        if (userModel::numeroIdentidadExiste($this->data['numero_identidad'])) {
            echo json_encode(responseHTTP::status400('El número de identidad ya está registrado'));
            return;
        }
    }
    
    // Validar usuario
    $erroresUsuario = Security::validarUsuario($this->data['usuario']);
    if (!empty($erroresUsuario)) {
        echo json_encode(responseHTTP::status400(implode(', ', $erroresUsuario)));
        return;
    }
    
    // Verificar si usuario ya existe
    if (userModel::usuarioExiste($this->data['usuario'])) {
        echo json_encode(responseHTTP::status400('El nombre de usuario ya está registrado'));
        return;
    }
    
    // Validar nombre de usuario
    $erroresNombre = Security::validarNombreUsuario($this->data['nombre_usuario']);
    if (!empty($erroresNombre)) {
        echo json_encode(responseHTTP::status400(implode(', ', $erroresNombre)));
        return;
    }
    
    // Validar correo electrónico si se proporciona
    if (!empty($this->data['correo_electronico'])) {
        $errorEmail = Security::validarEmail($this->data['correo_electronico']);
        if ($errorEmail) {
            echo json_encode(responseHTTP::status400($errorEmail));
            return;
        }
        
        // Verificar si el correo ya existe
        if (userModel::correoElectronicoExiste($this->data['correo_electronico'])) {
            echo json_encode(responseHTTP::status400('El correo electrónico ya está registrado en el sistema'));
            return;
        }
    }
    
    // Validar contraseña robusta
    $password = $this->data['contrasena'];
    $erroresPassword = Security::validarPasswordRobusta($password);
    if (!empty($erroresPassword)) {
        echo json_encode(responseHTTP::status400(implode(', ', $erroresPassword)));
        return;
    }
    
    // Registrar usuario
    $result = userModel::registrarUsuario($this->data);
    
    if ($result['success']) {
        echo json_encode([
            'status' => 201,
            'message' => $result['message']
        ]);
    } else {
        echo json_encode([
            'status' => 400,
            'message' => $result['message']
        ]);
    }
}

// Obtener información básica del usuario
    public function getBasicInfo() {
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }
        
        $userInfo = $this->model->getUserBasicInfo($_SESSION['id_usuario']);
        if ($userInfo) {
            echo json_encode(['success' => true, 'data' => $userInfo]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    }
    
    // Obtener información completa del usuario
    public function getFullInfo() {
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }
        
        $userInfo = $this->model->getUserFullInfo($_SESSION['id_usuario']);
        if ($userInfo) {
            echo json_encode(['success' => true, 'data' => $userInfo]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    }
    
    // Actualizar información del usuario
    public function updateUser() {
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }
        
        $data = [
            'nombre_usuario' => $_POST['nombre_usuario'] ?? '',
            'correo_electronico' => $_POST['correo_electronico'] ?? ''
        ];
        
        if (empty($data['nombre_usuario']) || empty($data['correo_electronico'])) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            return;
        }
        
        if (!filter_var($data['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Correo electrónico no válido']);
            return;
        }
        
        $result = $this->model->updateUser($_SESSION['id_usuario'], $data);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario']);
        }
    }
}