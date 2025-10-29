<?php
// src/Views/compras/registrar-proveedor.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registrar Proveedor - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 25px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .required-label::after {
            content: " *";
            color: #dc3545;
        }
        
        .valid-feedback, .invalid-feedback {
            display: block;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
    <?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
    <main id="main" class="main">
        <div class="container-fluid">
            
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">Registrar Nuevo Proveedor</h1>
                    <a href="/sistema/public/consultar-compras" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Compras
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-building me-2"></i>Información del Proveedor
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formRegistrarProveedor" novalidate>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label required-label">Nombre del Proveedor</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                                               placeholder="Ingrese el nombre completo del proveedor"
                                               minlength="5" maxlength="50"
                                               pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{5,50}">
                                        <div class="valid-feedback">Nombre válido</div>
                                        <div class="invalid-feedback">
                                            El nombre debe tener entre 5 y 50 letras (solo se permiten letras y espacios)
                                        </div>
                                        <div class="form-text">Mínimo 5 letras, máximo 50. Solo se permiten letras y espacios.</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contacto" class="form-label">Persona de Contacto</label>
                                        <input type="text" class="form-control" id="contacto" name="contacto" 
                                               placeholder="Nombre del contacto principal"
                                               maxlength="50"
                                               pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{0,50}">
                                        <div class="valid-feedback">Contacto válido</div>
                                        <div class="invalid-feedback">
                                            Solo se permiten letras y espacios (máximo 50 caracteres)
                                        </div>
                                        <div class="form-text">Máximo 50 letras. Solo se permiten letras y espacios.</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" 
                                               placeholder="Ej: 504 9867-7890"
                                               pattern="504\s[0-9]{4}-[0-9]{4}"
                                               maxlength="13">
                                        <div class="valid-feedback">Teléfono válido</div>
                                        <div class="invalid-feedback">
                                            Formato: 504 XXXX-XXXX (Ej: 504 9867-7890)
                                        </div>
                                        <div class="form-text">Formato: 504 XXXX-XXXX (solo números)</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="correo" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="correo" name="correo" 
                                               placeholder="correo@gmail.com o correo@hotmail.com"
                                               pattern="[a-zA-Z0-9._%+-]+@(gmail|hotmail)\.com$"
                                               maxlength="50">
                                        <div class="valid-feedback">Correo válido</div>
                                        <div class="invalid-feedback">
                                            Solo se permiten correos de Gmail (@gmail.com) o Hotmail (@hotmail.com)
                                        </div>
                                        <div class="form-text">Solo se aceptan correos @gmail.com y @hotmail.com</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <textarea class="form-control" id="direccion" name="direccion" 
                                                  rows="1" placeholder="Dirección completa del proveedor"
                                                  maxlength="255"></textarea>
                                        <div class="form-text">Máximo 255 caracteres</div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary me-md-2" onclick="cancelarRegistro()">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="btnRegistrar" disabled>
                                                <i class="bi bi-check-circle"></i> Registrar Proveedor
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

     <script>
    // Variables para controlar validaciones
    let nombreValido = false;
    let telefonoValido = false;
    let nombreUnico = false;
    let contactoUnico = true; // Opcional
    let correoUnico = true; // Opcional

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Formulario cargado');
        
        const form = document.getElementById('formRegistrarProveedor');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Formulario enviado');
            registrarProveedor();
        });
        
        // Validación en tiempo real para campos requeridos
        document.getElementById('nombre').addEventListener('input', function() {
            console.log('Validando nombre...');
            validarNombre();
            verificarBoton();
        });
        
        document.getElementById('telefono').addEventListener('input', function() {
            console.log('Validando teléfono...');
            validarTelefono();
            verificarBoton();
        });
        
        // Validar unicidad cuando el usuario sale del campo
        document.getElementById('nombre').addEventListener('blur', function() {
            console.log('Validando unicidad nombre...');
            if (nombreValido) {
                validarNombreUnico();
            }
        });
        
        document.getElementById('contacto').addEventListener('blur', function() {
            const contacto = document.getElementById('contacto').value.trim();
            if (contacto) {
                console.log('Validando unicidad contacto...');
                validarContactoUnico();
            }
        });
        
        document.getElementById('correo').addEventListener('blur', function() {
            const correo = document.getElementById('correo').value.trim();
            if (correo) {
                console.log('Validando unicidad correo...');
                validarCorreoUnico();
            }
        });
    });

    function validarNombre() {
        const nombreInput = document.getElementById('nombre');
        const nombre = nombreInput.value.trim();
        
        console.log('Nombre:', nombre);
        
        // Validar formato (solo letras y espacios)
        const letrasRegex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{5,50}$/;
        nombreValido = letrasRegex.test(nombre);
        
        console.log('Nombre válido:', nombreValido);
        
        if (nombreValido) {
            nombreInput.classList.remove('is-invalid');
            nombreInput.classList.add('is-valid');
        } else {
            nombreInput.classList.remove('is-valid');
            nombreInput.classList.add('is-invalid');
        }
        
        return nombreValido;
    }

    function validarTelefono() {
        const telefonoInput = document.getElementById('telefono');
        const telefono = telefonoInput.value.trim();
        
        console.log('Teléfono:', telefono);
        
        // Validar formato específico: 504 XXXX-XXXX
        const telefonoRegex = /^504\s[0-9]{4}-[0-9]{4}$/;
        telefonoValido = telefonoRegex.test(telefono);
        
        console.log('Teléfono válido:', telefonoValido);
        
        if (telefonoValido) {
            telefonoInput.classList.remove('is-invalid');
            telefonoInput.classList.add('is-valid');
        } else {
            telefonoInput.classList.remove('is-valid');
            telefonoInput.classList.add('is-invalid');
        }
        
        return telefonoValido;
    }

    async function validarNombreUnico() {
        const nombreInput = document.getElementById('nombre');
        const nombre = nombreInput.value.trim();
        
        if (!nombre) return;
        
        console.log('Validando nombre único:', nombre);
        
        try {
            const response = await fetch(`/sistema/public/index.php?route=compras&caso=validarProveedor&campo=nombre&valor=${encodeURIComponent(nombre)}`);
            const data = await response.json();
            
            console.log('Respuesta validación nombre:', data);
            
            nombreUnico = data.disponible;
            
            if (nombreUnico) {
                nombreInput.classList.remove('is-invalid');
                nombreInput.classList.add('is-valid');
            } else {
                nombreInput.classList.remove('is-valid');
                nombreInput.classList.add('is-invalid');
            }
            
            verificarBoton();
            
        } catch (error) {
            console.error('Error validando nombre:', error);
            // En caso de error, asumimos que está disponible para no bloquear al usuario
            nombreUnico = true;
            verificarBoton();
        }
    }

    async function validarContactoUnico() {
        const contactoInput = document.getElementById('contacto');
        const contacto = contactoInput.value.trim();
        
        if (!contacto) {
            contactoUnico = true;
            verificarBoton();
            return;
        }
        
        console.log('Validando contacto único:', contacto);
        
        try {
            const response = await fetch(`/sistema/public/index.php?route=compras&caso=validarProveedor&campo=contacto&valor=${encodeURIComponent(contacto)}`);
            const data = await response.json();
            
            console.log('Respuesta validación contacto:', data);
            
            contactoUnico = data.disponible;
            
            if (contactoUnico) {
                contactoInput.classList.remove('is-invalid');
                contactoInput.classList.add('is-valid');
            } else {
                contactoInput.classList.remove('is-valid');
                contactoInput.classList.add('is-invalid');
            }
            
            verificarBoton();
            
        } catch (error) {
            console.error('Error validando contacto:', error);
            contactoUnico = true;
            verificarBoton();
        }
    }

    async function validarCorreoUnico() {
        const correoInput = document.getElementById('correo');
        const correo = correoInput.value.trim();
        
        if (!correo) {
            correoUnico = true;
            verificarBoton();
            return;
        }
        
        console.log('Validando correo único:', correo);
        
        try {
            const response = await fetch(`/sistema/public/index.php?route=compras&caso=validarProveedor&campo=correo&valor=${encodeURIComponent(correo)}`);
            const data = await response.json();
            
            console.log('Respuesta validación correo:', data);
            
            correoUnico = data.disponible;
            
            if (correoUnico) {
                correoInput.classList.remove('is-invalid');
                correoInput.classList.add('is-valid');
            } else {
                correoInput.classList.remove('is-valid');
                correoInput.classList.add('is-invalid');
            }
            
            verificarBoton();
            
        } catch (error) {
            console.error('Error validando correo:', error);
            correoUnico = true;
            verificarBoton();
        }
    }

    function verificarBoton() {
        const btnRegistrar = document.getElementById('btnRegistrar');
        
        console.log('Estado validaciones:', {
            nombreValido,
            telefonoValido,
            nombreUnico,
            contactoUnico,
            correoUnico
        });
        
        // Solo requerimos que nombre y teléfono sean válidos y únicos
        const formularioValido = nombreValido && 
                               telefonoValido && 
                               nombreUnico;
        
        console.log('Formulario válido:', formularioValido);
        console.log('Botón habilitado:', formularioValido);
        
        btnRegistrar.disabled = !formularioValido;
    }

    function validarFormularioCompleto() {
        console.log('Validando formulario completo...');
        
        // Solo validamos campos requeridos
        if (!nombreValido) {
            alert('Por favor ingrese un nombre válido para el proveedor (5-50 letras).');
            return false;
        }
        
        if (!telefonoValido) {
            alert('Por favor ingrese un teléfono válido (formato: 504 XXXX-XXXX).');
            return false;
        }
        
        if (!nombreUnico) {
            alert('Este nombre de proveedor ya existe en el sistema.');
            return false;
        }
        
        console.log('Formulario validado correctamente');
        return true;
    }

    function registrarProveedor() {
        console.log('Iniciando registro de proveedor...');
        
        const btnRegistrar = document.getElementById('btnRegistrar');
        const originalText = btnRegistrar.innerHTML;
        
        // Mostrar loading
        btnRegistrar.innerHTML = '<span class="loading-spinner"></span> Registrando...';
        btnRegistrar.disabled = true;
        
        // Obtener datos del formulario
        const formData = new FormData(document.getElementById('formRegistrarProveedor'));
        const datos = Object.fromEntries(formData);
        
        // Limpiar espacios en blanco
        Object.keys(datos).forEach(key => {
            if (typeof datos[key] === 'string') {
                datos[key] = datos[key].trim();
            }
        });
        
        console.log('Datos a enviar:', datos);
        
        fetch('/sistema/public/index.php?route=compras&caso=registrarProveedor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            console.log('Respuesta HTTP:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Respuesta del servidor:', data);
            
            if (data.status === 201) {
                // Registro exitoso
                alert(data.message);
                window.location.href = '/sistema/public/gestion-proveedores';
            } else {
                // Error
                alert(data.message || 'Error al registrar el proveedor');
                btnRegistrar.innerHTML = originalText;
                btnRegistrar.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error en fetch:', error);
            alert('Error de conexión. Intente nuevamente.');
            btnRegistrar.innerHTML = originalText;
            btnRegistrar.disabled = false;
        });
    }

    function cancelarRegistro() {
        if (confirm('¿Está seguro que desea cancelar el registro? Los datos no guardados se perderán.')) {
            window.location.href = '/sistema/public/gestion-proveedores';
        }
    }
</script>
</body>
</html>