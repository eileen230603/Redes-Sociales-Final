@extends('layouts.adminlte-externo')

@section('page_title', 'Mi Perfil')

@section('css')
<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #34495e;
        --accent-color: #3498db;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --light-bg: #f8f9fa;
        --border-color: #e1e8ed;
        --text-primary: #2c3e50;
        --text-secondary: #7f8c8d;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    }

    body {
        background-color: var(--light-bg);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
    }

    .profile-header h2 {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .profile-header p {
        font-size: 0.95rem;
        opacity: 0.85;
        font-weight: 400;
    }

    .info-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .info-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .card-header {
        background: white;
        border-bottom: 2px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    .card-header h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .card-header.bg-info {
        background: linear-gradient(135deg, #3498db15 0%, #2980b915 100%) !important;
        border-bottom-color: var(--accent-color);
    }

    .card-header.bg-success {
        background: linear-gradient(135deg, #27ae6015 0%, #229a5615 100%) !important;
        border-bottom-color: var(--success-color);
    }

    .card-header.bg-primary {
        background: linear-gradient(135deg, #2c3e5015 0%, #34495e15 100%) !important;
        border-bottom-color: var(--primary-color);
    }

    .card-header.bg-warning {
        background: linear-gradient(135deg, #f39c1215 0%, #e67e2215 100%) !important;
        border-bottom-color: var(--warning-color);
    }

    .card-header .text-white {
        color: var(--text-primary) !important;
    }

    .info-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        transition: background-color 0.2s ease;
    }

    .info-item:hover {
        background-color: #f8f9fa;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .info-label i {
        color: var(--accent-color);
        margin-right: 0.5rem;
        font-size: 0.85rem;
    }

    .info-value {
        font-size: 1rem;
        color: var(--text-primary);
        font-weight: 500;
        line-height: 1.5;
    }

    .icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.15);
        margin-right: 1.25rem;
        backdrop-filter: blur(10px);
    }

    .btn-edit {
        background: var(--primary-color);
        border: none;
        padding: 0.875rem 2.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        color: white;
    }

    .btn-edit:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .badge-status {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .badge-success {
        background-color: #27ae6020;
        color: var(--success-color);
    }

    .badge-danger {
        background-color: #e74c3c20;
        color: var(--danger-color);
    }

    .loading-spinner {
        color: var(--accent-color);
    }

    .form-control {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-group label {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .form-group label i {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    .btn-success {
        background: var(--success-color);
        border: none;
        border-radius: 8px;
        font-weight: 600;
        padding: 0.875rem 2.5rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background: #229a56;
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-secondary {
        background: #95a5a6;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        padding: 0.875rem 2.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 8px;
        border: none;
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
    }

    .alert-danger {
        background: linear-gradient(135deg, #e74c3c15 0%, #c0392b15 100%);
        color: var(--danger-color);
    }

    .card-warning.card-outline {
        border: 2px solid var(--warning-color);
        border-radius: 12px;
    }

    hr {
        border-top: 2px solid var(--border-color);
        margin: 2rem 0;
    }

    h5 {
        font-weight: 600;
        font-size: 1.1rem;
    }

    h5.text-primary {
        color: var(--accent-color) !important;
    }

    h5.text-success {
        color: var(--success-color) !important;
    }

    h5.text-info {
        color: var(--accent-color) !important;
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    .text-primary {
        color: var(--accent-color) !important;
    }

    .card-body {
        padding: 0;
    }

    /* Avatar Styles */
    .avatar-container {
        position: relative;
        width: 120px;
        height: 120px;
    }

    .avatar-img, .avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: var(--shadow-md);
    }

    .avatar-placeholder {
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .avatar-upload-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        background: var(--accent-color);
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        border: 3px solid white;
    }

    .avatar-upload-btn:hover {
        background: #2980b9;
        transform: scale(1.1);
    }

    .card-body p {
        padding: 1.5rem;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-header {
            padding: 1.5rem 1rem;
        }

        .icon-wrapper {
            width: 48px;
            height: 48px;
        }

        .profile-header h2 {
            font-size: 1.5rem;
        }

        .btn-edit,
        .btn-success,
        .btn-secondary {
            padding: 0.75rem 2rem;
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Loading State -->
            <div id="loadingMessage" class="text-center py-5">
                <div class="spinner-border loading-spinner" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando información del perfil...</p>
            </div>

            <!-- Profile Content -->
            <div id="profileContent" style="display: none;">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <!-- Avatar -->
                            <div class="avatar-container mr-4">
                                <img id="avatarPreview" src="" alt="Foto de perfil" class="avatar-img" style="display: none;">
                                <div id="avatarPlaceholder" class="avatar-placeholder">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                                <label for="fotoPerfilInput" class="avatar-upload-btn" title="Cambiar foto de perfil">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="fotoPerfilInput" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="display: none;">
                            </div>
                            <div>
                                <h2 class="mb-0" id="header_nombre_completo">Mi Perfil</h2>
                                <p class="mb-0">Información completa de tu cuenta</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Foto de Perfil Card -->
                <div class="card info-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-image mr-2"></i> Foto de Perfil
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Subir archivo -->
                        <div class="form-group mb-4">
                            <label for="fotoPerfilFile">
                                <i class="fas fa-upload mr-2"></i> Subir Imagen desde Dispositivo
                            </label>
                            <input type="file" class="form-control" id="fotoPerfilFile" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                            <small class="form-text text-muted">
                                Formatos permitidos: JPEG, PNG, JPG, GIF, WEBP. Tamaño máximo: 5MB
                            </small>
                        </div>

                        <!-- Preview de archivo seleccionado -->
                        <div id="filePreviewContainer" class="mb-4" style="display: none;">
                            <label class="d-block mb-2"><strong>Vista Previa:</strong></label>
                            <div class="position-relative d-inline-block">
                                <img id="filePreviewImg" src="" alt="Preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #007bff;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0; border-radius: 50%; width: 30px; height: 30px; padding: 0;" onclick="removeFilePreview()" title="Eliminar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Agregar por URL -->
                        <div class="form-group mt-4">
                            <label for="fotoPerfilUrl">
                                <i class="fas fa-link mr-2"></i> O Agregar Imagen por URL (Opcional)
                            </label>
                            <div class="input-group">
                                <input type="url" class="form-control" id="fotoPerfilUrl" placeholder="https://ejemplo.com/imagen.jpg">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="btnAgregarUrl">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Ingresa la URL completa de una imagen en internet
                            </small>
                        </div>

                        <!-- Preview de URL agregada -->
                        <div id="urlPreviewContainer" class="mb-4" style="display: none;">
                            <label class="d-block mb-2"><strong>Imagen desde URL:</strong></label>
                            <div class="position-relative d-inline-block">
                                <img id="urlPreviewImg" src="" alt="Preview URL" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #28a745;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0; border-radius: 50%; width: 30px; height: 30px; padding: 0;" onclick="removeUrlPreview()" title="Eliminar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-lg" onclick="guardarFotoPerfil()">
                                <i class="fas fa-save mr-2"></i> Guardar Foto de Perfil
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Información de Usuario -->
                    <div class="col-md-6">
                        <div class="card info-card">
                            <div class="card-header bg-info text-white">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-user mr-2"></i> Información de Usuario
                                </h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user-tag"></i> Nombre de Usuario
                                    </div>
                                    <div class="info-value" id="nombre_usuario">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-envelope"></i> Correo Electrónico
                                    </div>
                                    <div class="info-value" id="correo_electronico">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user-tie"></i> Tipo de Usuario
                                    </div>
                                    <div class="info-value" id="tipo_usuario">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-alt"></i> Fecha de Registro
                                    </div>
                                    <div class="info-value" id="fecha_registro">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-check-circle"></i> Estado
                                    </div>
                                    <div class="info-value" id="estado">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Personal -->
                    <div class="col-md-6">
                        <div class="card info-card">
                            <div class="card-header bg-success text-white">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-user-friends mr-2"></i> Información Personal
                                </h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user"></i> Nombres
                                    </div>
                                    <div class="info-value" id="nombres">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user"></i> Apellidos
                                    </div>
                                    <div class="info-value" id="apellidos">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-birthday-cake"></i> Fecha de Nacimiento
                                    </div>
                                    <div class="info-value" id="fecha_nacimiento">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-envelope"></i> Email
                                    </div>
                                    <div class="info-value" id="email">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-phone"></i> Teléfono
                                    </div>
                                    <div class="info-value" id="phone_number">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-12">
                        <div class="card info-card">
                            <div class="card-header bg-primary text-white">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-align-left mr-2"></i> Descripción
                                </h4>
                            </div>
                            <div class="card-body">
                                <p class="mb-0" id="descripcion" style="line-height: 1.8; color: #495057;">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón de Editar -->
                <div class="text-center mt-4 mb-4">
                    <button type="button" class="btn btn-edit text-white" onclick="toggleEditMode()">
                        <i class="fas fa-edit mr-2"></i> Editar Perfil
                    </button>
                </div>
            </div>

            <!-- Formulario de Edición -->
            <div id="editForm" style="display: none;">
                <div class="card card-warning card-outline">
                    <div class="card-header bg-warning">
                        <h3 class="card-title mb-0" style="font-weight: 600; color: var(--text-primary);">
                            <i class="fas fa-edit mr-2"></i> Editar Perfil
                        </h3>
                    </div>
                    <div class="card-body" style="padding: 2rem;">
                        <form id="profileForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-user mr-2"></i> Información de Usuario
                                    </h5>
                                    <div class="form-group">
                                        <label><i class="fas fa-user-tag mr-1"></i> Nombre de Usuario</label>
                                        <input type="text" class="form-control" id="edit_nombre_usuario" name="nombre_usuario" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope mr-1"></i> Correo Electrónico</label>
                                        <input type="email" class="form-control" id="edit_correo_electronico" name="correo_electronico" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-success mb-3">
                                        <i class="fas fa-lock mr-2"></i> Cambiar Contraseña (Opcional)
                                    </h5>
                                    <div class="form-group">
                                        <label><i class="fas fa-key mr-1"></i> Contraseña Actual</label>
                                        <input type="password" class="form-control" id="edit_contrasena_actual" name="contrasena_actual">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-key mr-1"></i> Nueva Contraseña</label>
                                        <input type="password" class="form-control" id="edit_nueva_contrasena" name="nueva_contrasena" minlength="6">
                                        <small class="form-text text-muted">Mínimo 6 caracteres</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-info mb-3">
                                        <i class="fas fa-user-friends mr-2"></i> Información Personal
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-user mr-1"></i> Nombres</label>
                                        <input type="text" class="form-control" id="edit_nombres" name="nombres" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-user mr-1"></i> Apellidos</label>
                                        <input type="text" class="form-control" id="edit_apellidos" name="apellidos">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-birthday-cake mr-1"></i> Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="edit_fecha_nacimiento" name="fecha_nacimiento">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope mr-1"></i> Email</label>
                                        <input type="email" class="form-control" id="edit_email" name="email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-phone mr-1"></i> Teléfono</label>
                                        <input type="text" class="form-control" id="edit_phone_number" name="phone_number">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-align-left mr-1"></i> Descripción</label>
                                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="4" placeholder="Describe tu perfil..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-center">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-save mr-2"></i> Guardar Cambios
                                </button>
                                <button type="button" class="btn btn-secondary btn-lg px-5 ml-2" onclick="toggleEditMode()">
                                    <i class="fas fa-times mr-2"></i> Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let profileData = null;
let isEditMode = false;

document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    
    if (!token) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión Expirada',
            text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    await loadProfile();
});

async function loadProfile() {
    const token = localStorage.getItem('token');
    const loadingMessage = document.getElementById('loadingMessage');
    const profileContent = document.getElementById('profileContent');

    try {
        const res = await fetch(`${API_BASE_URL}/api/perfil`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            loadingMessage.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error: ${data.error || 'Error al cargar el perfil'}
                </div>
            `;
            return;
        }

        profileData = data.data;
        displayProfile(profileData);
        loadingMessage.style.display = 'none';
        profileContent.style.display = 'block';

    } catch (error) {
        console.error('Error:', error);
        loadingMessage.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar el perfil.
            </div>
        `;
    }
}

function displayProfile(data) {
    // Header
    if (data.integrante_externo) {
        const nombres = data.integrante_externo.nombres || '';
        const apellidos = data.integrante_externo.apellidos || '';
        const nombreCompleto = `${nombres} ${apellidos}`.trim() || 'Mi Perfil';
        document.getElementById('header_nombre_completo').textContent = nombreCompleto;
    }

    // Mostrar foto de perfil
    const fotoPerfil = data.integrante_externo?.foto_perfil || data.foto_perfil;
    if (fotoPerfil) {
        const avatarImg = document.getElementById('avatarPreview');
        const avatarPlaceholder = document.getElementById('avatarPlaceholder');
        avatarImg.src = fotoPerfil;
        avatarImg.style.display = 'block';
        avatarPlaceholder.style.display = 'none';
    }

    // Información de usuario
    document.getElementById('nombre_usuario').textContent = data.nombre_usuario || '-';
    document.getElementById('correo_electronico').textContent = data.correo_electronico || '-';
    document.getElementById('tipo_usuario').textContent = data.tipo_usuario || '-';
    document.getElementById('fecha_registro').textContent = data.fecha_registro 
        ? new Date(data.fecha_registro).toLocaleDateString('es-ES', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        }) 
        : '-';
    document.getElementById('estado').innerHTML = data.activo 
        ? '<span class="badge badge-success badge-status">Activo</span>' 
        : '<span class="badge badge-danger badge-status">Inactivo</span>';

    // Información de Integrante Externo
    if (data.integrante_externo) {
        document.getElementById('nombres').textContent = data.integrante_externo.nombres || '-';
        document.getElementById('apellidos').textContent = data.integrante_externo.apellidos || '-';
        
        // Formatear fecha de nacimiento correctamente (sin problemas de zona horaria)
        if (data.integrante_externo.fecha_nacimiento) {
            const fechaStr = data.integrante_externo.fecha_nacimiento;
            // Extraer solo la fecha (YYYY-MM-DD) sin la hora
            const fechaSolo = fechaStr.split('T')[0] || fechaStr.split(' ')[0];
            const partes = fechaSolo.split('-');
            if (partes.length === 3) {
                const fecha = new Date(parseInt(partes[0]), parseInt(partes[1]) - 1, parseInt(partes[2]));
                document.getElementById('fecha_nacimiento').textContent = fecha.toLocaleDateString('es-ES', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            } else {
                document.getElementById('fecha_nacimiento').textContent = fechaSolo;
            }
        } else {
            document.getElementById('fecha_nacimiento').textContent = '-';
        }
        
        document.getElementById('email').textContent = data.integrante_externo.email || '-';
        document.getElementById('phone_number').textContent = data.integrante_externo.phone_number || '-';
        document.getElementById('descripcion').textContent = data.integrante_externo.descripcion || 'No hay descripción disponible.';
    }
}

// Variables para manejar previews (similar a mega-eventos)
let selectedFile = null;
let selectedUrl = null;

// Función para guardar foto de perfil (mejorada, similar a mega-eventos)
async function guardarFotoPerfil() {
    const token = localStorage.getItem('token');
    const fotoFile = document.getElementById('fotoPerfilFile').files[0];
    const fotoUrlInput = document.getElementById('fotoPerfilUrl').value.trim();

    // Usar el archivo seleccionado o la URL agregada
    const fotoUrl = selectedUrl || fotoUrlInput;

    if (!fotoFile && !fotoUrl) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin foto',
            text: 'Por favor, selecciona una imagen o ingresa una URL'
        });
        return;
    }

    try {
        const formData = new FormData();
        
        if (fotoFile) {
            // Validar tamaño (5MB)
            if (fotoFile.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'El archivo no debe exceder 5MB'
                });
                return;
            }
            formData.append('foto_perfil', fotoFile);
        } else if (fotoUrl) {
            // Enviar URL (similar a imagenes_urls en mega-eventos)
            formData.append('foto_perfil_url', fotoUrl);
        }

        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const res = await fetch(`${API_BASE_URL}/api/perfil`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Error al guardar la foto de perfil'
            });
            return;
        }

        Swal.fire({
            icon: 'success',
            title: '¡Foto guardada!',
            text: 'Tu foto de perfil ha sido actualizada correctamente',
            timer: 2000,
            showConfirmButton: false
        });

        // Recargar perfil
        await loadProfile();
        
        // Limpiar formulario y previews
        document.getElementById('fotoPerfilFile').value = '';
        document.getElementById('fotoPerfilUrl').value = '';
        removeFilePreview();
        removeUrlPreview();
        selectedFile = null;
        selectedUrl = null;

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo guardar la foto de perfil'
        });
    }
}

// Preview de imagen al seleccionar archivo (mejorado)
document.getElementById('fotoPerfilFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        selectedFile = file;
        selectedUrl = null; // Limpiar URL si se selecciona archivo
        removeUrlPreview();
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.getElementById('filePreviewContainer');
            const previewImg = document.getElementById('filePreviewImg');
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
            
            // También actualizar el avatar principal
            const avatarImg = document.getElementById('avatarPreview');
            const avatarPlaceholder = document.getElementById('avatarPlaceholder');
            if (avatarImg && avatarPlaceholder) {
                avatarImg.src = e.target.result;
                avatarImg.style.display = 'block';
                avatarPlaceholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
});

// Función para eliminar preview de archivo
function removeFilePreview() {
    document.getElementById('fotoPerfilFile').value = '';
    document.getElementById('filePreviewContainer').style.display = 'none';
    selectedFile = null;
}

// Botón para agregar URL (similar a mega-eventos)
document.getElementById('btnAgregarUrl').addEventListener('click', function() {
    const urlInput = document.getElementById('fotoPerfilUrl');
    const url = urlInput.value.trim();
    
    if (!url) {
        Swal.fire({
            icon: 'warning',
            title: 'URL vacía',
            text: 'Por favor, ingresa una URL válida'
        });
        return;
    }
    
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
        Swal.fire({
            icon: 'error',
            title: 'URL inválida',
            text: 'La URL debe comenzar con http:// o https://'
        });
        return;
    }
    
    // Validar que sea una URL válida
    try {
        new URL(url);
    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'URL inválida',
            text: 'Por favor, ingresa una URL válida'
        });
        return;
    }
    
    selectedUrl = url;
    selectedFile = null; // Limpiar archivo si se agrega URL
    removeFilePreview();
    
    // Mostrar preview
    const previewContainer = document.getElementById('urlPreviewContainer');
    const previewImg = document.getElementById('urlPreviewImg');
    previewImg.src = url;
    previewContainer.style.display = 'block';
    
    // Validar que la imagen se pueda cargar
    previewImg.onerror = function() {
        Swal.fire({
            icon: 'warning',
            title: 'Imagen no encontrada',
            text: 'La URL no apunta a una imagen válida. Se guardará de todas formas.'
        });
    };
    
    // También actualizar el avatar principal
    const avatarImg = document.getElementById('avatarPreview');
    const avatarPlaceholder = document.getElementById('avatarPlaceholder');
    if (avatarImg && avatarPlaceholder) {
        avatarImg.src = url;
        avatarImg.style.display = 'block';
        avatarPlaceholder.style.display = 'none';
        avatarImg.onerror = function() {
            avatarImg.style.display = 'none';
            avatarPlaceholder.style.display = 'flex';
        };
    }
    
    // Limpiar el input
    urlInput.value = '';
});

// Permitir agregar URL con Enter
document.getElementById('fotoPerfilUrl').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('btnAgregarUrl').click();
    }
});

// Función para eliminar preview de URL
function removeUrlPreview() {
    document.getElementById('fotoPerfilUrl').value = '';
    document.getElementById('urlPreviewContainer').style.display = 'none';
    selectedUrl = null;
}

function toggleEditMode() {
    isEditMode = !isEditMode;
    const profileContent = document.getElementById('profileContent');
    const editForm = document.getElementById('editForm');

    if (isEditMode) {
        profileContent.style.display = 'none';
        editForm.style.display = 'block';
        populateEditForm();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        profileContent.style.display = 'block';
        editForm.style.display = 'none';
    }
}

function populateEditForm() {
    if (!profileData) return;

    document.getElementById('edit_nombre_usuario').value = profileData.nombre_usuario || '';
    document.getElementById('edit_correo_electronico').value = profileData.correo_electronico || '';

    if (profileData.integrante_externo) {
        document.getElementById('edit_nombres').value = profileData.integrante_externo.nombres || '';
        document.getElementById('edit_apellidos').value = profileData.integrante_externo.apellidos || '';
        
        // Extraer la fecha correctamente para el campo date (formato YYYY-MM-DD)
        if (profileData.integrante_externo.fecha_nacimiento) {
            const fechaStr = profileData.integrante_externo.fecha_nacimiento;
            // Extraer solo la fecha sin la hora (puede venir como 'YYYY-MM-DD' o 'YYYY-MM-DD HH:MM:SS' o 'YYYY-MM-DDTHH:MM:SS')
            let fechaFormato = fechaStr.split('T')[0] || fechaStr.split(' ')[0];
            // Asegurarse de que tenga el formato correcto YYYY-MM-DD
            if (fechaFormato.match(/^\d{4}-\d{2}-\d{2}$/)) {
                document.getElementById('edit_fecha_nacimiento').value = fechaFormato;
            } else {
                document.getElementById('edit_fecha_nacimiento').value = '';
            }
        } else {
            document.getElementById('edit_fecha_nacimiento').value = '';
        }
        
        document.getElementById('edit_email').value = profileData.integrante_externo.email || '';
        document.getElementById('edit_phone_number').value = profileData.integrante_externo.phone_number || '';
        document.getElementById('edit_descripcion').value = profileData.integrante_externo.descripcion || '';
    }
}

document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const token = localStorage.getItem('token');
    const formData = new FormData(e.target);
    const data = {};

    // Solo incluir campos que tienen valor
    for (let [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            data[key] = value.trim();
        }
    }

    // Validar que si se proporciona nueva contraseña, también se proporcione la actual
    if (data.nueva_contrasena && !data.contrasena_actual) {
        Swal.fire({
            icon: 'error',
            title: 'Error de Validación',
            text: 'Debe proporcionar la contraseña actual para cambiar la contraseña.',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Guardando cambios...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const res = await fetch(`${API_BASE_URL}/api/perfil`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();

        if (!res.ok || !result.success) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.error || 'Error al actualizar el perfil',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Éxito
        Swal.fire({
            icon: 'success',
            title: '¡Perfil Actualizado!',
            text: 'Tu perfil se ha actualizado correctamente.',
            confirmButtonText: 'Perfecto',
            timer: 2000,
            timerProgressBar: true
        }).then(async () => {
            await loadProfile();
            toggleEditMode();
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor. Por favor, intenta nuevamente.',
            confirmButtonText: 'Entendido'
        });
    }
});
</script>
@endsection