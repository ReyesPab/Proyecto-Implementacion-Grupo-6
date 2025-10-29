<?php
// use statements deben ir AL INICIO
use App\models\comprasModel;

// Obtener datos necesarios para el formulario
try {
    // Ruta CORRECTA para el modelo - desde Views/compras hasta models
    require_once __DIR__ . '/../../models/comprasModel.php';
    
    $comprasModel = new comprasModel();
    $proveedores = $comprasModel->obtenerProveedores();
    
    // Inicializar materiaPrima como array vacío - se cargará dinámicamente por proveedor
    $materiaPrima = [];
    
} catch (Exception $e) {
    error_log("Error al cargar datos para compras: " . $e->getMessage());
    $proveedores = [];
    $materiaPrima = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registrar Compra - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .main {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .pagetitle {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }
        
        .pagetitle h1 {
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        
        .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.12);
        }
        
        .card-body {
            padding: 30px;
        }
        
        .card-title {
            color: var(--primary-color);
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-bg);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table-detalles {
            margin-bottom: 0;
        }
        
        .table-detalles th {
            background: linear-gradient(135deg, var(--primary-color), #34495e);
            color: white;
            font-weight: 600;
            padding: 15px 12px;
            border: none;
            font-size: 0.9rem;
        }
        
        .table-detalles td {
            padding: 15px 12px;
            vertical-align: middle;
            border-color: var(--border-color);
        }
        
        .table-detalles tbody tr {
            transition: background-color 0.3s ease;
        }
        
        .table-detalles tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .table-detalles tfoot {
            background-color: var(--light-bg);
        }
        
        .btn {
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        
        .btn-agregar {
            background: linear-gradient(135deg, var(--success-color), #219653);
            color: white;
        }
        
        .btn-agregar:hover {
            background: linear-gradient(135deg, #219653, #1e8449);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }
        
        .btn-eliminar {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
        }
        
        .btn-eliminar:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }
        
        .btn-eliminar:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #2471a3);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .total-section {
            background: linear-gradient(135deg, #fff, var(--light-bg));
            padding: 20px;
            border-radius: 10px;
            border: 2px solid var(--border-color);
            text-align: center;
        }
        
        .total-section label {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1rem;
            margin-bottom: 10px;
            display: block;
        }
        
        #total-compra {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success-color);
            margin-top: 5px;
        }
        
        .unidad-display {
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .text-end {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main {
                padding: 15px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .pagetitle h1 {
                font-size: 1.8rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 10px;
                justify-content: center;
            }
            
            .text-end {
                text-align: center;
            }
            
            .table-responsive {
                font-size: 0.85rem;
            }
            
            #total-compra {
                font-size: 1.5rem;
            }
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card {
            animation: fadeIn 0.5s ease;
        }
        
        /* Estados de validación */
        .form-control:invalid {
            border-color: var(--danger-color);
        }
        
        .form-control:valid {
            border-color: var(--success-color);
        }
        
        /* Loading state */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        /* Badge para precios de referencia */
        .price-badge {
            background: var(--warning-color);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 5px;
        }
    </style>
</head>

<body>
 <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Registrar Compra</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=compras">Compras</a></li>
                    <li class="breadcrumb-item active">Registrar</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Información de la Compra</h5>
                            
                            <form id="formCompra">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="proveedor" class="form-label">Proveedor *</label>
                                        <select class="form-select" id="proveedor" name="proveedor" required>
                                            <option value="">Seleccionar proveedor...</option>
                                            <?php foreach ($proveedores as $proveedor): ?>
                                                <option value="<?php echo $proveedor['ID_PROVEEDOR']; ?>">
                                                    <?php echo htmlspecialchars($proveedor['NOMBRE']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="observaciones" class="form-label">Observaciones</label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales..."></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h5 class="card-title">Detalles de la Compra</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-detalles">
                                                <thead>
                                                    <tr>
                                                        <th width="40%">Materia Prima</th>
                                                        <th width="15%">Cantidad</th>
                                                        <th width="15%">Precio Unitario (L)</th>
                                                        <th width="15%">Subtotal (L)</th>
                                                        <th width="15%">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="detalles-body">
                                                    <!-- Las filas de detalles se agregarán aquí dinámicamente -->
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5" class="text-center">
                                                            <button type="button" class="btn btn-agregar btn-sm" onclick="agregarDetalle()">
                                                                <i class="bi bi-plus-circle"></i> Agregar Material
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="total-section text-center">
                                            <label>Total de la Compra:</label>
                                            <div id="total-compra">L 0.00</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 text-end">
                                        <button type="button" class="btn btn-secondary" onclick="cancelarCompra()">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Registrar Compra
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

 
    <script>
// Variables globales
let contadorDetalles = 0;
let detalles = [];
let materiaPrimaPorProveedor = {};

document.addEventListener('DOMContentLoaded', function() {
    // Agregar primera fila vacía
    agregarDetalle();
    
    // Configurar formulario
    document.getElementById('formCompra').addEventListener('submit', registrarCompra);
    
    // Event listener para cambio de proveedor
    document.getElementById('proveedor').addEventListener('change', function() {
        const idProveedor = this.value;
        cargarMateriaPrimaPorProveedor(idProveedor);
    });
});

// Función para cargar materia prima cuando se selecciona un proveedor
// En tu archivo HTML - actualizar la URL del fetch
// Función para cargar materia prima cuando se selecciona un proveedor
function cargarMateriaPrimaPorProveedor(idProveedor) {
    if (!idProveedor) {
        console.log('DEBUG: No hay proveedor seleccionado');
        actualizarOpcionesMateriaPrima([]);
        return;
    }
    
    console.log('DEBUG: Cargando materia prima para proveedor:', idProveedor);
    
    // Mostrar loading
    const selects = document.querySelectorAll('.materia-prima');
    selects.forEach(select => {
        select.innerHTML = '<option value="">Cargando productos...</option>';
        select.disabled = true;
    });
    
    // Construir URL correctamente
    const url = `/sistema/public/index.php?route=compras&caso=obtenerMateriaPrimaProveedor&id_proveedor=${idProveedor}`;
    console.log('DEBUG: URL de petición:', url);
    
    // Hacer petición al servidor
    fetch(url)
    .then(response => {
        console.log('DEBUG: Response status:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('DEBUG: Datos recibidos del servidor:', data);
        
        if (data.success && data.data && data.data.length > 0) {
            console.log('DEBUG: Productos cargados exitosamente:', data.data.length);
            materiaPrimaPorProveedor[idProveedor] = data.data;
            actualizarOpcionesMateriaPrima(data.data);
        } else {
            console.log('DEBUG: No hay productos o error en la respuesta');
            actualizarOpcionesMateriaPrima([]);
            if (!data.success) {
                alert('Error: ' + (data.message || 'No se pudieron cargar los productos'));
            } else {
                alert('Este proveedor no tiene productos asignados');
            }
        }
    })
    .catch(error => {
        console.error('DEBUG: Error en fetch:', error);
        actualizarOpcionesMateriaPrima([]);
        alert('Error al cargar los productos del proveedor: ' + error.message);
    })
    .finally(() => {
        // Habilitar selects
        const selects = document.querySelectorAll('.materia-prima');
        selects.forEach(select => {
            select.disabled = false;
        });
    });
}

// Función para actualizar todas las opciones de materia prima
function actualizarOpcionesMateriaPrima(materiaPrimaLista) {
    const selects = document.querySelectorAll('.materia-prima');
    
    selects.forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Seleccionar material...</option>';
        
        if (materiaPrimaLista && materiaPrimaLista.length > 0) {
            materiaPrimaLista.forEach(mp => {
                const option = document.createElement('option');
                option.value = mp.ID_MATERIA_PRIMA;
                option.textContent = `${mp.NOMBRE} - ${mp.UNIDAD || mp.UNIDAD_MEDIDA}`;
                option.setAttribute('data-unidad', mp.UNIDAD || mp.UNIDAD_MEDIDA);
                option.setAttribute('data-precio', mp.PRECIO_SUGERIDO || mp.PRECIO_PROMEDIO || '0');
                select.appendChild(option);
            });
            
            // Restaurar valor anterior si existe en la nueva lista
            if (currentValue && Array.from(select.options).some(opt => opt.value === currentValue)) {
                select.value = currentValue;
                // Disparar el evento change para actualizar la unidad
                const event = new Event('change');
                select.dispatchEvent(event);
            }
        } else {
            select.innerHTML = '<option value="">No hay productos disponibles</option>';
        }
    });
}

function agregarDetalle() {
    contadorDetalles++;
    const tbody = document.getElementById('detalles-body');
    
    const tr = document.createElement('tr');
    tr.id = `detalle-${contadorDetalles}`;
    
    // Obtener el proveedor seleccionado
    const proveedorSelect = document.getElementById('proveedor');
    const idProveedor = proveedorSelect.value;
    
    // Determinar qué lista de materia prima usar
    let listaMateriaPrima = [];
    if (idProveedor && materiaPrimaPorProveedor[idProveedor]) {
        listaMateriaPrima = materiaPrimaPorProveedor[idProveedor];
    }
    
    tr.innerHTML = `
        <td>
            <select class="form-select materia-prima" name="detalles[${contadorDetalles}][id_materia_prima]" required onchange="actualizarUnidad(this)">
                <option value="">${listaMateriaPrima.length > 0 ? 'Seleccionar material...' : 'Seleccione un proveedor primero'}</option>
                ${listaMateriaPrima.length > 0 ? listaMateriaPrima.map(mp => 
                    `<option value="${mp.ID_MATERIA_PRIMA}" 
                            data-unidad="${mp.UNIDAD || mp.UNIDAD_MEDIDA}"
                            data-precio="${mp.PRECIO_SUGERIDO || mp.PRECIO_PROMEDIO || '0'}">
                        ${mp.NOMBRE} - ${mp.UNIDAD || mp.UNIDAD_MEDIDA}
                    </option>`
                ).join('') : ''}
            </select>
            <small class="text-muted unidad-display" id="unidad-${contadorDetalles}"></small>
        </td>
        <td>
            <input type="number" class="form-control cantidad" name="detalles[${contadorDetalles}][cantidad]" 
                   step="0.01" min="0.01" required onchange="calcularSubtotal(this)" oninput="calcularSubtotal(this)">
        </td>
        <td>
            <input type="number" class="form-control precio" name="detalles[${contadorDetalles}][precio_unitario]" 
                   step="0.01" min="0.01" required onchange="calcularSubtotal(this)" oninput="calcularSubtotal(this)">
        </td>
        <td>
            <span class="subtotal fw-bold text-success">L 0.00</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-eliminar btn-sm" onclick="eliminarDetalle(${contadorDetalles})" ${contadorDetalles === 1 ? 'disabled' : ''}>
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </td>
    `;
    
    tbody.appendChild(tr);
    
    // Inicializar el detalle con valores por defecto
    detalles.push({
        id: contadorDetalles,
        id_materia_prima: '',
        cantidad: 0,
        precio_unitario: 0,
        subtotal: 0
    });
}

function eliminarDetalle(id) {
    const tr = document.getElementById(`detalle-${id}`);
    if (tr) {
        tr.remove();
        detalles = detalles.filter(d => d.id !== id);
        calcularTotal();
        
        // Si no quedan detalles, agregar uno nuevo
        if (detalles.length === 0) {
            agregarDetalle();
        }
        
        // Habilitar botones de eliminar si hay más de una fila
        const deleteButtons = document.querySelectorAll('.btn-eliminar');
        if (deleteButtons.length === 1) {
            deleteButtons[0].disabled = true;
        }
    }
}

function actualizarUnidad(select) {
    const id = select.name.match(/\[(\d+)\]/)[1];
    const selectedOption = select.options[select.selectedIndex];
    const unidadDisplay = document.getElementById(`unidad-${id}`);
    
    if (selectedOption.value) {
        const precioSugerido = selectedOption.getAttribute('data-precio');
        const unidad = selectedOption.getAttribute('data-unidad');
        unidadDisplay.textContent = `Unidad: ${unidad} | Precio sug: L ${parseFloat(precioSugerido || 0).toFixed(2)}`;
        
        // Actualizar el array de detalles con la materia prima seleccionada
        const detalleIndex = detalles.findIndex(d => d.id == id);
        if (detalleIndex !== -1) {
            detalles[detalleIndex].id_materia_prima = selectedOption.value;
        }
        
        // Auto-completar precio unitario con precio sugerido
        const row = select.closest('tr');
        const precioInput = row.querySelector('.precio');
        if (precioInput && !precioInput.value && precioSugerido && precioSugerido !== '0') {
            precioInput.value = parseFloat(precioSugerido).toFixed(2);
            calcularSubtotal(precioInput);
        }
    } else {
        unidadDisplay.textContent = '';
        
        // Limpiar la materia prima en el array de detalles
        const detalleIndex = detalles.findIndex(d => d.id == id);
        if (detalleIndex !== -1) {
            detalles[detalleIndex].id_materia_prima = '';
        }
    }
}

function calcularSubtotal(input) {
    const row = input.closest('tr');
    const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
    const precio = parseFloat(row.querySelector('.precio').value) || 0;
    const subtotal = cantidad * precio;
    
    row.querySelector('.subtotal').textContent = `L ${subtotal.toFixed(2)}`;
    
    // Actualizar array de detalles
    const id = parseInt(row.id.split('-')[1]);
    const detalleIndex = detalles.findIndex(d => d.id === id);
    if (detalleIndex !== -1) {
        detalles[detalleIndex].cantidad = cantidad;
        detalles[detalleIndex].precio_unitario = precio;
        detalles[detalleIndex].subtotal = subtotal;
    }
    
    calcularTotal();
}

function calcularTotal() {
    const total = detalles.reduce((sum, detalle) => sum + detalle.subtotal, 0);
    document.getElementById('total-compra').textContent = `L ${total.toFixed(2)}`;
}

function cancelarCompra() {
    if (confirm('¿Está seguro de que desea cancelar la compra? Se perderán todos los datos ingresados.')) {
        window.location.href = '/sistema/public/index.php?route=compras';
    }
}

function registrarCompra(event) {
    event.preventDefault();
    
    console.log('DEBUG: Iniciando registro de compra...');
    
    // Validaciones básicas
    const proveedor = document.getElementById('proveedor').value;
    if (!proveedor) {
        alert('Debe seleccionar un proveedor');
        return;
    }
    
    // Recolectar detalles válidos correctamente
    const detallesValidos = [];
    const detallesRows = document.querySelectorAll('#detalles-body tr');
    
    detallesRows.forEach((row, index) => {
        const idMateriaPrima = row.querySelector('.materia-prima').value;
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precioUnitario = parseFloat(row.querySelector('.precio').value) || 0;
        
        console.log(`DEBUG Fila ${index}:`, { idMateriaPrima, cantidad, precioUnitario });
        
        if (idMateriaPrima && cantidad > 0 && precioUnitario > 0) {
            detallesValidos.push({
                id_materia_prima: parseInt(idMateriaPrima),
                cantidad: cantidad,
                precio_unitario: precioUnitario
            });
        }
    });
    
    console.log('DEBUG: Detalles válidos:', detallesValidos);
    
    if (detallesValidos.length === 0) {
        alert('Debe agregar al menos un detalle de compra válido');
        return;
    }

    // Preparar datos CORRECTAMENTE
    const formData = {
        id_proveedor: parseInt(proveedor),
        id_usuario: <?php echo $_SESSION['id_usuario'] ?? 1; ?>,
        observaciones: document.getElementById('observaciones').value,
        detalles: detallesValidos,
        creado_por: '<?php echo $_SESSION['usuario'] ?? "SISTEMA"; ?>',
        caso: 'registrar'
    };

    console.log('DEBUG: Enviando datos JSON:', JSON.stringify(formData, null, 2));
    
    // Mostrar loading
    const submitBtn = document.querySelector('#formCompra button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
    submitBtn.disabled = true;
    
    // ENVIAR COMO JSON
    const url = '/sistema/public/index.php?route=compras';
    
    console.log('DEBUG: URL:', url);
    console.log('DEBUG: Method: POST');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('DEBUG: Response status:', response.status, response.statusText);
        if (!response.ok) {
            // Si es error 405, mostrar mensaje específico
            if (response.status === 405) {
                throw new Error('Método no permitido. El servidor no acepta solicitudes POST.');
            }
            throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('DEBUG: Respuesta del servidor:', data);
        
        if (data.status === 201) {
            alert('✅ ' + data.message);
            window.location.href = '/sistema/public/index.php?route=consultar-compras';
        } else {
            alert('❌ Error: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('DEBUG: Error completo:', error);
        alert('Error al registrar la compra: ' + error.message);
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>
 <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>