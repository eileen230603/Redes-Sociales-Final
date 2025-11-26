@extends('layouts.adminlte')

@section('page_title', 'Editar Evento')

@section('content_body')
<div class="container-fluid">
    <div class="card border-0 mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);">
        <div class="card-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border-radius: 12px 12px 0 0; padding: 1.25rem 1.5rem;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h3 class="mb-1" style="font-weight: 700; font-size: 1.4rem;">
                        <i class="far fa-edit mr-2"></i> Editar evento
                    </h3>
                    <p class="mb-0" style="font-size: 0.9rem; opacity: 0.9;">
                        Modifica la información, ubicación, imágenes y configuración de tu evento.
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('ong.eventos.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color: #FFFFFF; border-radius: 999px; border: none; padding: 0.5rem 1.25rem;">
                        <i class="far fa-arrow-left mr-1"></i> Volver a eventos
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <form id="editEventForm">
                <!-- Información básica -->
                <div class="mb-4 pb-3" style="border-bottom: 1px solid #F5F5F5;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-info-circle mr-2" style="color: #00A36C;"></i> Información básica
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Completa los datos generales del evento.</p>
                </div>

                <div class="form-group mb-3">
                    <label for="titulo" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Título del evento *</label>
                    <input type="text" id="titulo" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                </div>

                <div class="form-group mb-3">
                    <label for="descripcion" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Descripción *</label>
                    <textarea id="descripcion" class="form-control" rows="3" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;"></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="tipo_evento" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Tipo de evento *</label>
                    <select id="tipo_evento" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                        <option value="conferencia">Conferencia</option>
                        <option value="taller">Taller</option>
                        <option value="seminario">Seminario</option>
                        <option value="voluntariado">Voluntariado</option>
                        <option value="cultural">Cultural</option>
                        <option value="deportivo">Deportivo</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <!-- Fechas -->
                <div class="mb-4 pb-3 mt-4" style="border-top: 1px solid #F5F5F5; padding-top: 1.5rem; border-bottom: 1px solid #F5F5F5;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-calendar-alt mr-2" style="color: #00A36C;"></i> Fechas del evento
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Define las fechas de inicio, fin y límite de inscripción.</p>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4 mb-3">
                        <label for="fecha_inicio" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Fecha de inicio *</label>
                        <input type="datetime-local" id="fecha_inicio" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                    </div>

                    <div class="form-group col-md-4 mb-3">
                        <label for="fecha_fin" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Fecha de finalización *</label>
                        <input type="datetime-local" id="fecha_fin" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                    </div>

                    <div class="form-group col-md-4 mb-3">
                        <label for="fecha_limite_inscripcion" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Límite para inscribirse</label>
                        <input type="datetime-local" id="fecha_limite_inscripcion" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                    </div>
                </div>

                <!-- Configuración -->
                <div class="mb-4 pb-3 mt-4" style="border-top: 1px solid #F5F5F5; padding-top: 1.5rem; border-bottom: 1px solid #F5F5F5;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-cog mr-2" style="color: #00A36C;"></i> Configuración
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Ajusta la capacidad, estado y ubicación del evento.</p>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4 mb-3">
                        <label for="capacidad_maxima" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Capacidad máxima</label>
                        <input type="text" id="capacidad_maxima" class="form-control" pattern="[0-9]*" inputmode="numeric" placeholder="Solo números" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                        <small class="form-text text-muted" style="font-size: 0.8rem;">Ingrese solo números (sin letras, símbolos ni espacios)</small>
                    </div>

                    <div class="form-group col-md-4 mb-3">
                        <label for="estado" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Estado del evento *</label>
                        <select id="estado" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                            <option value="borrador">Borrador</option>
                            <option value="publicado">Publicado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="mb-4 pb-3 mt-4" style="border-top: 1px solid #F5F5F5; padding-top: 1.5rem; border-bottom: 1px solid #F5F5F5;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-map-marker-alt mr-2" style="color: #00A36C;"></i> Ubicación
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Especifica la ciudad y dirección del evento.</p>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6 mb-3">
                        <label for="ciudad" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Ciudad</label>
                        <input type="text" id="ciudad" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label for="direccion" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Dirección</label>
                        <input type="text" id="direccion" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                    </div>
                </div>

                <!-- IMÁGENES -->
                <div class="mb-4 pb-3 mt-4" style="border-top: 1px solid #F5F5F5; padding-top: 1.5rem;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-images mr-2" style="color: #00A36C;"></i> Imágenes promocionales
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Sube imágenes que representen tu evento para destacarlo en la plataforma.</p>
                </div>
                
                <!-- Imágenes existentes -->
                <div id="imagenesExistentes" class="mb-4">
                    <p class="text-muted">Cargando imágenes...</p>
                </div>

                <!-- Subir nuevas imágenes desde dispositivo -->
                <div class="form-group mb-3">
                    <label for="nuevasImagenes" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Subir nuevas imágenes desde dispositivo</label>
                    <input type="file" id="nuevasImagenes" multiple accept="image/*" class="form-control-file" style="border-radius: 8px; padding: 0.5rem;">
                    <small class="form-text text-muted" style="font-size: 0.8rem;">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB por imagen.</small>
                </div>

                <!-- Preview de nuevas imágenes -->
                <div id="previewNuevasImagenes" class="d-flex flex-wrap gap-2 mb-4"></div>

                <!-- Agregar imagen por URL -->
                <div class="form-group mb-3">
                    <label for="imagen_url_edit" style="color: #0C2B44; font-weight: 600; font-size: 0.9rem;">Agregar imagen por URL (Opcional)</label>
                    <div class="input-group">
                        <input type="url" id="imagen_url_edit" class="form-control" placeholder="https://ejemplo.com/imagen.jpg" style="border-radius: 8px 0 0 8px; border: 1px solid #dee2e6; padding: 0.6rem 0.9rem;">
                        <div class="input-group-append">
                            <button type="button" class="btn" id="btnAgregarUrlEdit" style="background: #00A36C; color: white; border: none; border-radius: 0 8px 8px 0; padding: 0.6rem 1rem;">
                                <i class="far fa-plus mr-2"></i>Agregar
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted" style="font-size: 0.8rem;">Ingresa una URL válida de una imagen (JPG, PNG, GIF, WEBP).</small>
                </div>

                <div id="urlImagesContainerEdit" class="d-flex flex-wrap gap-2 mb-3"></div>

                <!-- Botones -->
                <div class="mt-4 pt-3 d-flex justify-content-end" style="border-top: 1px solid #F5F5F5;">
                    <button type="button" class="btn btn-outline-secondary mr-2" onclick="window.location.href='{{ route('ong.eventos.index') }}'" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500;">
                        <i class="far fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn" style="background: #00A36C; color: white; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600; min-width: 190px;">
                        <i class="far fa-check-circle mr-1"></i> Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.15rem rgba(0, 163, 108, 0.2);
    }

    #imagenesExistentes .imagen-item {
        position: relative;
        display: inline-block;
        margin: 8px;
    }
    #imagenesExistentes .imagen-item img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #F5F5F5;
        cursor: pointer;
        transition: transform 0.2s;
        box-shadow: 0 2px 4px rgba(12, 43, 68, 0.08);
    }
    #imagenesExistentes .imagen-item img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(12, 43, 68, 0.15);
    }
    #imagenesExistentes .imagen-item .btn-eliminar {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s;
    }
    #imagenesExistentes .imagen-item .btn-eliminar:hover {
        background: rgba(220, 53, 69, 1);
        transform: scale(1.1);
    }
    #previewNuevasImagenes img,
    #urlImagesContainerEdit img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #00A36C;
        margin: 8px;
        transition: transform 0.2s;
    }
    #previewNuevasImagenes img:hover,
    #urlImagesContainerEdit img:hover {
        transform: scale(1.05);
    }
    .image-preview-wrapper-url {
        position: relative;
        display: inline-block;
        margin: 5px;
        border: 2px solid #00A36C;
        border-radius: 8px;
        overflow: hidden;
    }
    .image-preview-wrapper-url .remove-image {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        border: none;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }
    .image-preview-wrapper-url .remove-image:hover {
        background: #c82333;
        transform: scale(1.1);
    }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/edit-event.js') }}"></script>
@endpush
