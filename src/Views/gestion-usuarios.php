<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestión de Usuarios</h1>
            <a href="index.php?route=crear-usuario" class="btn btn-primary">+ Crear Usuario</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="tablaUsuarios" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>USUARIO</th>
                            <th>NOMBRE_USUARIO</th>
                            <th>ROL</th>
                            <th>CORREO_ELECTRONICO</th>
                            <th>ESTADO_USUARIO</th>
                            <th>FECHA_CREACION</th>
                            <th>FECHA_VENCIMIENTO</th>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
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
        console.log("🔍 Solicitando usuarios...");
        const response = await fetch('index.php?route=user&caso=listar');
        const data = await response.json();
        
        console.log("📊 Respuesta completa:", data);
        
        if (data.status === 200) {
            console.log("✅ Usuarios recibidos:", data.data.usuarios);
            if (data.data.usuarios && data.data.usuarios.length > 0) {
                console.log("🔍 Primer usuario:", data.data.usuarios[0]);
                console.log("🔍 Campos disponibles:", Object.keys(data.data.usuarios[0]));
                
                // Debug: mostrar todos los campos con sus valores exactos
                const primerUsuario = data.data.usuarios[0];
                console.log("🎯 VALORES EXACTOS de campos:");
                Object.keys(primerUsuario).forEach(key => {
                    console.log(`   ${key}: "${primerUsuario[key]}" (tipo: ${typeof primerUsuario[key]})`);
                });
            }
            this.inicializarTabla(data.data.usuarios);
        } else {
            console.error('❌ Error al cargar usuarios:', data.message);
        }
    } catch (error) {
        console.error('💥 Error cargando usuarios:', error);
    }
}

            inicializarTabla(usuarios) {
    console.log("🎯 Inicializando tabla con usuarios:", usuarios);
    
    if (!usuarios || usuarios.length === 0) {
        console.log("⚠️ No hay usuarios para mostrar");
        $('#tablaUsuarios').DataTable({
            data: [],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            pageLength: 10,
            responsive: true
        });
        return;
    }

    if (this.tabla) {
        this.tabla.destroy();
    }

    // Función helper para obtener campos case-insensitive
    const getField = (obj, fieldName) => {
        const upperField = fieldName.toUpperCase();
        const lowerField = fieldName.toLowerCase();
        
        // Buscar en diferentes formatos de case
        return obj[upperField] || obj[lowerField] || obj[fieldName] || 
               obj[fieldName.toLowerCase()] || obj[fieldName.toUpperCase()] || 'N/A';
    };

    this.tabla = $('#tablaUsuarios').DataTable({
        data: usuarios,
        columns: [
            { 
                data: null,
                render: function(data, type, row) {
                    return getField(row, 'USUARIO');
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return getField(row, 'NOMBRE_USUARIO');
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return getField(row, 'ROL');
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const correo = getField(row, 'CORREO_ELECTRONICO');
                    return correo && correo !== 'N/A' ? correo : 'N/A';
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const estado = getField(row, 'ESTADO_USUARIO');
                    const badgeClass = estado === 'Activo' ? 'bg-success' : 
                                     estado === 'Bloqueado' ? 'bg-danger' : 
                                     estado === 'Nuevo' ? 'bg-warning' : 'bg-secondary';
                    return `<span class="badge ${badgeClass}">${estado}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const fecha = getField(row, 'FECHA_CREACION');
                    return fecha && fecha !== 'N/A' ? new Date(fecha).toLocaleDateString('es-ES') : 'N/A';
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const fecha = getField(row, 'FECHA_VENCIMIENTO');
                    return fecha && fecha !== 'N/A' ? new Date(fecha).toLocaleDateString('es-ES') : 'N/A';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    // Obtener ID_USUARIO de cualquier formato de case
                    const idUsuario = getField(row, 'ID_USUARIO');
                    console.log("🔧 Renderizando acciones para usuario ID:", idUsuario, "Datos completos:", row);
                    
                    if (idUsuario === 'N/A') {
                        return '<span class="text-muted">Sin ID</span>';
                    }
                    
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="gestionUsuarios.resetPassword(${idUsuario})">
                                Reset PW
                            </button>
                            <button class="btn btn-outline-secondary" onclick="gestionUsuarios.editarUsuario(${idUsuario})">
                                Editar
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
        responsive: true
    });
    
    console.log("✅ Tabla inicializada correctamente");
}

            configurarEventos() {
                document.getElementById('btnResetPassword').addEventListener('click', () => this.confirmarResetPassword());
                document.getElementById('reset_autogenerar').addEventListener('change', (e) => this.toggleAutogenerarReset(e));
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
                        this.cargarUsuarios(); // Recargar tabla
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
</body>
</html>