<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestión de Usuarios</h1>
            <a href='/sistema/public/crear-usuario' class="btn btn-primary">+ Crear Usuario</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando usuarios...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar los usuarios. Verifica la consola para más detalles.
                </div>
                
<table id="tablaUsuarios" class="table table-striped table-bordered" style="display: none;">
    <thead>
        <tr>
            <th>Usuario</th>
            <th>Nombre de Usuario</th>  <!-- Cambiado aquí -->
            <th>Rol</th>
            <th>Correo Electrónico</th>
            <th>Estado</th>
            <th>Fecha de Creación</th>  <!-- Cambiado aquí -->
            <th>Fecha de Vencimiento</th>  <!-- Cambiado aquí -->
            <th>Acciones</th>
        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargan via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Resetear Contraseña -->
    <div class="modal fade" id="modalResetPassword" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resetear Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formResetPassword">
                        <input type="hidden" id="reset_id_usuario" name="ID_USUARIO">
                        <div class="mb-3">
                            <label for="reset_nueva_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="reset_nueva_password" name="NUEVA_PASSWORD" required minlength="5" maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label for="reset_confirmar_password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="reset_confirmar_password" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="reset_autogenerar">
                            <label class="form-check-label" for="reset_autogenerar">
                                Autogenerar contraseña robusta
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnResetPassword">Resetear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="modalEliminarUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eliminar_id_usuario">
                    <p>¿Está seguro que desea eliminar al usuario: <strong id="eliminar_nombre_usuario"></strong>?</p>
                    <p class="text-danger"><small>Nota: Esta acción solo eliminará visualmente el usuario de la tabla. Los datos permanecerán en la base de datos.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Vendor JS Files -->
<script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/php-email-form/validate.js"></script>
<script src="/sistema/src/Views/assets/vendor/aos/aos.js"></script>
<script src="/sistema/src/Views/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="/sistema/src/Views/assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File -->
<script src="/sistema/src/Views/assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<script>
    class GestionUsuarios {
        constructor() {
            this.tabla = null;
            this.init();
        }

        async init() {
            await this.cargarUsuarios();
            this.configurarEventos();
        }

async cargarUsuarios() {
    try {
        console.log("🔍 Iniciando carga de usuarios...");
        
        const response = await fetch('index.php?route=user&caso=listar');
        console.log("📦 Respuesta HTTP:", response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log("📄 Respuesta cruda:", text);
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error("❌ Error parseando JSON:", e);
            throw new Error("Respuesta no es JSON válido");
        }
        
        console.log("📊 Datos JSON recibidos:", data);
        
        // Verificar estructura de datos
        if (data && data.status === 200 && data.data && data.data.usuarios) {
            console.log("✅ Usuarios encontrados:", data.data.usuarios.length);
            console.log("📋 Primer usuario:", data.data.usuarios[0]);
            this.mostrarUsuarios(data.data.usuarios);
        } else {
            console.error("❌ Estructura de datos inesperada:", data);
            throw new Error("Estructura de respuesta inesperada");
        }
        
    } catch (error) {
        console.error('❌ Error cargando usuarios:', error);
        this.mostrarError(error.message);
    }
}

mostrarError(mensaje) {
    const loadingMessage = document.getElementById('loadingMessage');
    const errorMessage = document.getElementById('errorMessage');
    
    loadingMessage.style.display = 'none';
    errorMessage.textContent = `Error: ${mensaje}`;
    errorMessage.style.display = 'block';
}

        mostrarUsuarios(usuarios) {
            const loadingMessage = document.getElementById('loadingMessage');
            const errorMessage = document.getElementById('errorMessage');
            const tabla = document.getElementById('tablaUsuarios');

            // Ocultar mensajes
            loadingMessage.style.display = 'none';
            errorMessage.style.display = 'none';

            if (!usuarios || usuarios.length === 0) {
                console.log("📭 No hay usuarios para mostrar");
                errorMessage.textContent = "No hay usuarios registrados en el sistema";
                errorMessage.style.display = 'block';
                return;
            }

            console.log("🔄 Mostrando", usuarios.length, "usuarios en la tabla");

            // Mostrar tabla
            tabla.style.display = 'table';

            // Limpiar tabla existente
            if (this.tabla) {
                this.tabla.destroy();
            }

            // Inicializar DataTable
this.tabla = $('#tablaUsuarios').DataTable({
        data: usuarios,
        columns: [
            { 
                data: 'USUARIO',
                defaultContent: 'N/A',
                title: 'Usuario'
            },
            { 
                data: 'NOMBRE_USUARIO',
                defaultContent: 'N/A',
                title: 'Nombre de Usuario'  // Cambiado aquí
            },
            { 
                data: 'ROL',
                defaultContent: 'N/A',
                title: 'Rol'
            },
            { 
                data: 'CORREO_ELECTRONICO',
                defaultContent: 'N/A',
                title: 'Correo Electrónico',
                render: function(data) {
                    return data || 'N/A';
                }
            },
            { 
                data: 'ESTADO_USUARIO',
                defaultContent: 'N/A',
                title: 'Estado',
                render: function(data) {
                    const estado = data || 'N/A';
                    const badgeClass = estado === 'Activo' ? 'bg-success' : 
                                     estado === 'Bloqueado' ? 'bg-danger' : 
                                     estado === 'Nuevo' ? 'bg-warning' : 'bg-secondary';
                    return `<span class="badge ${badgeClass}">${estado}</span>`;
                }
            },
            { 
                data: 'FECHA_CREACION',
                defaultContent: 'N/A',
                title: 'Fecha de Creación'  // Cambiado aquí
            },
            { 
                data: 'FECHA_VENCIMIENTO',
                defaultContent: 'N/A',
                title: 'Fecha de Vencimiento'  // Cambiado aquí
            },
            {
                data: 'ID_USUARIO',
                title: 'Acciones',
                render: function(data, type, row) {
                    if (!data) return '<span class="text-muted">Sin acciones</span>';
                    
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="gestionUsuarios.resetPassword(${data})" title="Resetear Contraseña">
                                <i class="bi bi-key"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="gestionUsuarios.editarUsuario(${data})" title="Editar Usuario">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="gestionUsuarios.eliminarUsuario(${data}, '${row.USUARIO || ''}', '${(row.NOMBRE_USUARIO || '').replace(/'/g, "\\'")}')" title="Eliminar Usuario">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                            `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'asc']]
            });

            console.log("✅ Tabla inicializada correctamente");
        }

        mostrarError() {
            document.getElementById('loadingMessage').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'block';
        }

        configurarEventos() {
            document.getElementById('btnResetPassword').addEventListener('click', () => this.confirmarResetPassword());
            document.getElementById('reset_autogenerar').addEventListener('change', (e) => this.toggleAutogenerarReset(e));
            document.getElementById('btnConfirmarEliminar').addEventListener('click', () => this.confirmarEliminarUsuario());
        }

        async resetPassword(ID_USUARIO) {
            try {
                const response = await fetch('index.php?route=user&caso=generar-password');
                const data = await response.json();
                
                if (data.status === 200) {
                    document.getElementById('reset_id_usuario').value = ID_USUARIO;
                    document.getElementById('reset_nueva_password').value = data.data.password;
                    document.getElementById('reset_confirmar_password').value = data.data.password;
                    
                    const modal = new bootstrap.Modal(document.getElementById('modalResetPassword'));
                    modal.show();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al generar contraseña');
            }
        }

        editarUsuario(ID_USUARIO) {
            window.location.href = '/sistema/public/crear-usuario?editar=' + ID_USUARIO;
        }

        eliminarUsuario(ID_USUARIO, usuario, nombre) {
            document.getElementById('eliminar_id_usuario').value = ID_USUARIO;
            document.getElementById('eliminar_nombre_usuario').textContent = `${usuario} - ${nombre}`;
            
            const modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
            modal.show();
        }

        async confirmarEliminarUsuario() {
            const ID_USUARIO = document.getElementById('eliminar_id_usuario').value;
            
            try {
                // Buscar y eliminar la fila
                const datos = this.tabla.rows().data();
                let filaIndex = -1;
                
                datos.each((index, row) => {
                    if (row.ID_USUARIO == ID_USUARIO) {
                        filaIndex = index;
                        return false;
                    }
                });
                
                if (filaIndex !== -1) {
                    this.tabla.row(filaIndex).remove().draw();
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarUsuario'));
                    modal.hide();
                    
                    alert('Usuario eliminado visualmente de la tabla.');
                } else {
                    alert('No se pudo encontrar el usuario en la tabla.');
                }
            } catch (error) {
                console.error('Error eliminando usuario:', error);
                alert('Error al eliminar el usuario');
            }
        }

        async toggleAutogenerarReset(e) {
            const passwordInput = document.getElementById('reset_nueva_password');
            const confirmInput = document.getElementById('reset_confirmar_password');
            
            if (e.target.checked) {
                try {
                    const response = await fetch('index.php?route=user&caso=generar-password');
                    const data = await response.json();
                    
                    if (data.status === 200) {
                        passwordInput.value = data.data.password;
                        confirmInput.value = data.data.password;
                    }
                } catch (error) {
                    console.error('Error generando password:', error);
                }
            } else {
                passwordInput.value = '';
                confirmInput.value = '';
            }
        }

        async confirmarResetPassword() {
            const form = document.getElementById('formResetPassword');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Validaciones
            if (data.NUEVA_PASSWORD.length < 5 || data.NUEVA_PASSWORD.length > 10) {
                alert('La contraseña debe tener entre 5 y 10 caracteres');
                return;
            }
            
            if (/\s/.test(data.NUEVA_PASSWORD)) {
                alert('La contraseña no puede contener espacios');
                return;
            }
            
            if (data.NUEVA_PASSWORD !== document.getElementById('reset_confirmar_password').value) {
                alert('Las contraseñas no coinciden');
                return;
            }
            
            try {
                data.MODIFICADO_POR = 'ADMIN';
                
                const response = await fetch('index.php?route=user&caso=resetear-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.status === 200) {
                    alert('Contraseña reseteada exitosamente');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalResetPassword'));
                    modal.hide();
                    this.cargarUsuarios();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error resetando password:', error);
                alert('Error de conexión');
            }
        }
    }

    // Instancia global
    const gestionUsuarios = new GestionUsuarios();
</script>

<style>
/* ✅ ESTILOS MEJORADOS PARA TABLA MÁS ESTÉTICA */
.btn-group-sm .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    border-radius: 0.2rem;
}

.bi {
    font-size: 0.8rem;
}

.btn-group {
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    gap: 1px;
}

.btn-outline-primary:hover { background-color: #0d6efd; color: white; }
.btn-outline-warning:hover { background-color: #ffc107; color: black; }
.btn-outline-danger:hover { background-color: #dc3545; color: white; }

/* Tabla general */
#tablaUsuarios {
    font-size: 0.85rem !important; /* Tamaño de letra reducido */
    width: 100% !important;
}

#tablaUsuarios th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.9rem;
    padding: 10px 8px;
}

#tablaUsuarios td {
    vertical-align: middle;
    word-wrap: break-word;
    word-break: break-word;
    padding: 8px 6px;
    line-height: 1.2;
}

.badge {
    font-size: 0.7em;
    padding: 4px 6px;
}

/* Columnas específicas con mejor manejo de texto */
#tablaUsuarios td:nth-child(1) { /* USUARIO */ 
    min-width: 100px;
    max-width: 120px;
}

#tablaUsuarios td:nth-child(2) { /* NOMBRE_USUARIO */
    min-width: 160px;
    max-width: 200px;
}

#tablaUsuarios td:nth-child(3) { /* ROL */
    min-width: 120px;
    max-width: 150px;
}

#tablaUsuarios td:nth-child(4) { /* CORREO_ELECTRONICO */
    min-width: 180px;
    max-width: 220px;
}

#tablaUsuarios td:nth-child(5) { /* ESTADO_USUARIO */
    min-width: 80px;
    max-width: 100px;
}

#tablaUsuarios td:nth-child(6), /* FECHA_CREACION */
#tablaUsuarios td:nth-child(7) { /* FECHA_VENCIMIENTO */
    min-width: 95px;
    max-width: 110px;
    white-space: nowrap;
}

/* Columna de acciones compacta */
#tablaUsuarios th:last-child,
#tablaUsuarios td:last-child {
    width: 105px !important;
    min-width: 105px !important;
    max-width: 105px !important;
    padding: 4px 2px !important;
}

/* Botones ultra compactos */
.btn-group-sm .btn.border-0 {
    padding: 1px 2px;
    margin: 0;
    border: 1px solid transparent !important;
    font-size: 0.7rem;
}

.btn-group-sm .btn.border-0:hover {
    border: 1px solid currentColor !important;
}

/* Mejorar responsividad */
@media (max-width: 768px) {
    #tablaUsuarios {
        font-size: 0.8rem !important;
    }
    
    #tablaUsuarios th,
    #tablaUsuarios td {
        padding: 6px 4px;
    }
}

/* Header de tabla más claro */
.card-header {
    background-color: #e9ecef;
    border-bottom: 1px solid #dee2e6;
}
</style>