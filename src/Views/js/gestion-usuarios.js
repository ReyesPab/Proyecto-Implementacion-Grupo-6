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
            const response = await fetch('index.php?route=user&caso=listar');
            const data = await response.json();
            
            if (data.status === 200) {
                this.inicializarTabla(data.data.usuarios);
            }
        } catch (error) {
            console.error('Error cargando usuarios:', error);
        }
    }

    inicializarTabla(usuarios) {
        this.tabla = $('#tablaUsuarios').DataTable({
            data: usuarios,
            columns: [
                { data: 'Usuario' },
                { data: 'Nombre_Usuario' },
                { data: 'Rol' },
                { data: 'Correo_Electronico' },
                { 
                    data: 'Estado_Usuario',
                    render: function(data, type, row) {
                        const badgeClass = data === 'Activo' ? 'bg-success' : 
                                         data === 'Bloqueado' ? 'bg-danger' : 
                                         data === 'Nuevo' ? 'bg-warning' : 'bg-secondary';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: 'Fecha_Creacion',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('es-ES');
                    }
                },
                { 
                    data: 'Fecha_Vencimiento',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('es-ES') : 'N/A';
                    }
                },
                {
                    data: 'Id_Usuario',
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" onclick="gestionUsuarios.resetPassword(${data})">
                                    Reset PW
                                </button>
                                <button class="btn btn-sm btn-outline-warning" onclick="gestionUsuarios.editarUsuario(${data})">
                                    Editar
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            }
        });
    }

    configurarEventos() {
        document.getElementById('btnResetPassword').addEventListener('click', () => this.confirmarResetPassword());
        document.getElementById('reset_autogenerar').addEventListener('change', (e) => this.toggleAutogenerarReset(e));
    }

    async resetPassword(idUsuario) {
        try {
            const response = await fetch('index.php?route=user&caso=generar-password');
            const data = await response.json();
            
            if (data.status === 200) {
                document.getElementById('reset_id_usuario').value = idUsuario;
                document.getElementById('reset_nueva_password').value = data.data.password;
                document.getElementById('reset_confirmar_password').value = data.data.password;
                
                const modal = new bootstrap.Modal(document.getElementById('modalResetPassword'));
                modal.show();
            }
        } catch (error) {
            console.error('Error:', error);
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
        if (data.nueva_password.length < 5 || data.nueva_password.length > 10) {
            alert('La contraseña debe tener entre 5 y 10 caracteres');
            return;
        }
        
        if (/\s/.test(data.nueva_password)) {
            alert('La contraseña no puede contener espacios');
            return;
        }
        
        if (data.nueva_password !== document.getElementById('reset_confirmar_password').value) {
            alert('Las contraseñas no coinciden');
            return;
        }
        
        try {
            data.modificado_por = '<?php echo $_SESSION["usuario"] ?? "ADMIN"; ?>';
            
            const response = await fetch('index.php?route=user&caso=resetear-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.status === 200) {
                alert(result.message);
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

    editarUsuario(idUsuario) {
        // Redirigir a página de edición o abrir modal
        window.location.href = `index.php?view=editar-usuario&id=${idUsuario}`;
    }
}

// Instancia global
const gestionUsuarios = new GestionUsuarios();