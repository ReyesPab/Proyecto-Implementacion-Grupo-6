<?php
// src/Views/compras/registrar-materia-prima.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registrar Materia Prima - Sistema de Gestión</title>
    
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
        
        .numeric-input {
            text-align: right;
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
                    <h1 class="h2 mb-0">Registrar Nueva Materia Prima</h1>
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
                                <i class="bi bi-box-seam me-2"></i>Información de la Materia Prima
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formRegistrarMateriaPrima" novalidate>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label required-label">Nombre de la Materia Prima</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                                               placeholder="Ingrese el nombre de la materia prima"
                                               minlength="3" maxlength="100"
                                               pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}">
                                        <div class="valid-feedback">Nombre válido</div>
                                        <div class="invalid-feedback">
                                            El nombre debe tener entre 3 y 100 caracteres (solo letras, números y espacios)
                                        </div>
                                        <div class="form-text">Mínimo 3 caracteres, máximo 100. Se permiten letras, números y espacios.</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                                  rows="2" placeholder="Descripción detallada de la materia prima"
                                                  maxlength="255"></textarea>
                                        <div class="form-text">Máximo 255 caracteres</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="id_proveedor" class="form-label required-label">Proveedor</label>
                                        <select class="form-select" id="id_proveedor" name="id_proveedor" required>
                                            <option value="">Seleccione un proveedor</option>
                                            <!-- Los proveedores se cargarán dinámicamente -->
                                        </select>
                                        <div class="valid-feedback">Proveedor válido</div>
                                        <div class="invalid-feedback">Por favor seleccione un proveedor</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="id_unidad_medida" class="form-label required-label">Unidad de Medida</label>
                                        <select class="form-select" id="id_unidad_medida" name="id_unidad_medida" required>
                                            <option value="">Seleccione una unidad</option>
                                            <!-- Las unidades se cargarán dinámicamente -->
                                        </select>
                                        <div class="valid-feedback">Unidad válida</div>
                                        <div class="invalid-feedback">Por favor seleccione una unidad de medida</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="minimo" class="form-label required-label">Stock Mínimo</label>
                                        <input type="number" class="form-control numeric-input" id="minimo" name="minimo" required 
                                               placeholder="0.00" min="0" step="0.01" value="0.00">
                                        <div class="valid-feedback">Valor válido</div>
                                        <div class="invalid-feedback">El stock mínimo debe ser un número positivo</div>
                                        <div class="form-text">Cantidad mínima en inventario</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="maximo" class="form-label required-label">Stock Máximo</label>
                                        <input type="number" class="form-control numeric-input" id="maximo" name="maximo" required 
                                               placeholder="0.00" min="0" step="0.01" value="0.00">
                                        <div class="valid-feedback">Valor válido</div>
                                        <div class="invalid-feedback">El stock máximo debe ser un número positivo</div>
                                        <div class="form-text">Cantidad máxima en inventario</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="precio_promedio" class="form-label required-label">Precio Promedio (L)</label>
                                        <input type="number" class="form-control numeric-input" id="precio_promedio" name="precio_promedio" required 
                                               placeholder="0.00" min="0.01" step="0.01" value="0.00">
                                        <div class="valid-feedback">Precio válido</div>
                                        <div class="invalid-feedback">El precio promedio debe ser mayor a 0</div>
                                        <div class="form-text">Precio unitario promedio en Lempiras</div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary me-md-2" onclick="cancelarRegistro()">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="btnRegistrar" disabled>
                                                <i class="bi bi-check-circle"></i> Registrar Materia Prima
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
    let proveedorValido = false;
    let unidadValido = false;
    let minimoValido = false;
    let maximoValido = false;
    let precioValido = false;
    let nombreUnico = false;

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Formulario cargado');
        
        // Cargar datos iniciales
        cargarProveedores();
        cargarUnidadesMedida();
        
        const form = document.getElementById('formRegistrarMateriaPrima');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Formulario enviado');
            registrarMateriaPrima();
        });
        
        // Validación en tiempo real
        document.getElementById('nombre').addEventListener('input', function() {
            validarNombre();
            verificarBoton();
        });
        
        document.getElementById('id_proveedor').addEventListener('change', function() {
            proveedorValido = this.value !== '';
            verificarBoton();
        });
        
        document.getElementById('id_unidad_medida').addEventListener('change', function() {
            unidadValido = this.value !== '';
            verificarBoton();
        });
        
        document.getElementById('minimo').addEventListener('input', function() {
            validarMinimo();
            verificarBoton();
        });
        
        document.getElementById('maximo').addEventListener('input', function() {
            validarMaximo();
            verificarBoton();
        });
        
        document.getElementById('precio_promedio').addEventListener('input', function() {
            validarPrecio();
            verificarBoton();
        });
        
        // Validar unicidad cuando el usuario sale del campo
        document.getElementById('nombre').addEventListener('blur', function() {
            if (nombreValido) {
                validarNombreUnico();
            }
        });
    });

    async function cargarProveedores() {
        try {
            const response = await fetch('/sistema/public/index.php?route=compras&caso=obtenerProveedoresActivos');
            const data = await response.json();
            
            if (data.status === 200 && data.data) {
                const select = document.getElementById('id_proveedor');
                select.innerHTML = '<option value="">Seleccione un proveedor</option>';
                
                data.data.forEach(proveedor => {
                    const option = document.createElement('option');
                    option.value = proveedor.ID_PROVEEDOR;
                    option.textContent = proveedor.NOMBRE;
                    select.appendChild(option);
                });
                
                console.log('Proveedores cargados:', data.data.length);
            }
        } catch (error) {
            console.error('Error cargando proveedores:', error);
        }
    }

    async function cargarUnidadesMedida() {
        try {
            const response = await fetch('/sistema/public/index.php?route=compras&caso=obtenerUnidadesMedida');
            const data = await response.json();
            
            if (data.status === 200 && data.data) {
                const select = document.getElementById('id_unidad_medida');
                select.innerHTML = '<option value="">Seleccione una unidad</option>';
                
                data.data.forEach(unidad => {
                    const option = document.createElement('option');
                    option.value = unidad.ID_UNIDAD_MEDIDA;
                    option.textContent = `${unidad.UNIDAD} - ${unidad.DESCRIPCION}`;
                    select.appendChild(option);
                });
                
                console.log('Unidades cargadas:', data.data.length);
            }
        } catch (error) {
            console.error('Error cargando unidades:', error);
        }
    }

    function validarNombre() {
        const nombreInput = document.getElementById('nombre');
        const nombre = nombreInput.value.trim();
        
        console.log('Nombre:', nombre);
        
        // Validar formato (letras, números y espacios)
        const regex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}$/;
        nombreValido = regex.test(nombre);
        
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

    function validarMinimo() {
        const minimoInput = document.getElementById('minimo');
        const minimo = parseFloat(minimoInput.value) || 0;
        
        minimoValido = minimo >= 0;
        
        if (minimoValido) {
            minimoInput.classList.remove('is-invalid');
            minimoInput.classList.add('is-valid');
        } else {
            minimoInput.classList.remove('is-valid');
            minimoInput.classList.add('is-invalid');
        }
        
        return minimoValido;
    }

    function validarMaximo() {
        const maximoInput = document.getElementById('maximo');
        const maximo = parseFloat(maximoInput.value) || 0;
        const minimo = parseFloat(document.getElementById('minimo').value) || 0;
        
        maximoValido = maximo > 0 && maximo > minimo;
        
        if (maximoValido) {
            maximoInput.classList.remove('is-invalid');
            maximoInput.classList.add('is-valid');
        } else {
            maximoInput.classList.remove('is-valid');
            maximoInput.classList.add('is-invalid');
        }
        
        return maximoValido;
    }

    function validarPrecio() {
        const precioInput = document.getElementById('precio_promedio');
        const precio = parseFloat(precioInput.value) || 0;
        
        precioValido = precio > 0;
        
        if (precioValido) {
            precioInput.classList.remove('is-invalid');
            precioInput.classList.add('is-valid');
        } else {
            precioInput.classList.remove('is-valid');
            precioInput.classList.add('is-invalid');
        }
        
        return precioValido;
    }

    async function validarNombreUnico() {
        const nombreInput = document.getElementById('nombre');
        const nombre = nombreInput.value.trim();
        
        if (!nombre) return;
        
        console.log('Validando nombre único:', nombre);
        
        try {
            // En una implementación real, aquí llamarías a un endpoint para validar unicidad
            // Por ahora asumimos que está disponible
            nombreUnico = true;
            verificarBoton();
            
        } catch (error) {
            console.error('Error validando nombre:', error);
            nombreUnico = true;
            verificarBoton();
        }
    }

    function verificarBoton() {
        const btnRegistrar = document.getElementById('btnRegistrar');
        
        console.log('Estado validaciones:', {
            nombreValido,
            proveedorValido,
            unidadValido,
            minimoValido,
            maximoValido,
            precioValido,
            nombreUnico
        });
        
        const formularioValido = nombreValido && 
                               proveedorValido && 
                               unidadValido && 
                               minimoValido && 
                               maximoValido && 
                               precioValido &&
                               nombreUnico;
        
        console.log('Formulario válido:', formularioValido);
        console.log('Botón habilitado:', formularioValido);
        
        btnRegistrar.disabled = !formularioValido;
    }

    function validarFormularioCompleto() {
        console.log('Validando formulario completo...');
        
        if (!nombreValido) {
            alert('Por favor ingrese un nombre válido para la materia prima (3-100 caracteres).');
            return false;
        }
        
        if (!proveedorValido) {
            alert('Por favor seleccione un proveedor.');
            return false;
        }
        
        if (!unidadValido) {
            alert('Por favor seleccione una unidad de medida.');
            return false;
        }
        
        if (!minimoValido) {
            alert('Por favor ingrese un stock mínimo válido (número positivo).');
            return false;
        }
        
        if (!maximoValido) {
            alert('Por favor ingrese un stock máximo válido (mayor que el mínimo).');
            return false;
        }
        
        if (!precioValido) {
            alert('Por favor ingrese un precio promedio válido (mayor a 0).');
            return false;
        }
        
        console.log('Formulario validado correctamente');
        return true;
    }

    function registrarMateriaPrima() {
        console.log('Iniciando registro de materia prima...');
        
        if (!validarFormularioCompleto()) {
            return;
        }
        
        const btnRegistrar = document.getElementById('btnRegistrar');
        const originalText = btnRegistrar.innerHTML;
        
        // Mostrar loading
        btnRegistrar.innerHTML = '<span class="loading-spinner"></span> Registrando...';
        btnRegistrar.disabled = true;
        
        // Obtener datos del formulario
        const formData = new FormData(document.getElementById('formRegistrarMateriaPrima'));
        const datos = Object.fromEntries(formData);
        
        // Convertir a números
        datos.minimo = parseFloat(datos.minimo);
        datos.maximo = parseFloat(datos.maximo);
        datos.precio_promedio = parseFloat(datos.precio_promedio);
        datos.id_proveedor = parseInt(datos.id_proveedor);
        datos.id_unidad_medida = parseInt(datos.id_unidad_medida);
        
        // Limpiar espacios en blanco
        Object.keys(datos).forEach(key => {
            if (typeof datos[key] === 'string') {
                datos[key] = datos[key].trim();
            }
        });
        
        console.log('Datos a enviar:', datos);
        
        fetch('/sistema/public/index.php?route=compras&caso=registrarMateriaPrima', {
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
                window.location.href = '/sistema/public/gestion-materia-prima';
            } else {
                // Error
                alert(data.message || 'Error al registrar la materia prima');
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
            window.location.href = '/sistema/public/gestion-materia-prima';
        }
    }
</script>
</body>
</html>