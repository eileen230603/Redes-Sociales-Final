@extends('layouts.adminlte')

@section('title', 'Configuración - Parámetros del Sistema')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-sliders-h mr-2"></i>
                        Configuración del Sistema
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home-ong">Inicio</a></li>
                        <li class="breadcrumb-item active">Configuración</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Filtros y Búsqueda -->
            <div class="card card-primary card-outline mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-2"></i>Filtros y Búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Categoría</label>
                                <select id="filtroCategoria" class="form-control">
                                    <option value="">Todas las categorías</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Grupo</label>
                                <select id="filtroGrupo" class="form-control">
                                    <option value="">Todos los grupos</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Buscar</label>
                                <input type="text" id="buscar" class="form-control" placeholder="Buscar por código, nombre o descripción...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" onclick="aplicarFiltros()">
                                    <i class="fas fa-search mr-2"></i>Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Crear -->
            <div class="mb-3">
                <button type="button" class="btn btn-success" onclick="abrirModalCrear()">
                    <i class="fas fa-plus mr-2"></i>Nuevo Parámetro
                </button>
            </div>

            <!-- Tabla de Parámetros -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>Parámetros del Sistema
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary" id="contadorParametros">0</span>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Grupo</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Estado</th>
                                <th style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaParametros">
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalParametro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title" id="modalTitulo">
                    <i class="fas fa-plus mr-2"></i>Nuevo Parámetro
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formParametro">
                <div class="modal-body">
                    <input type="hidden" id="parametroId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="codigo" required 
                                       placeholder="ej: max_eventos_por_ong" maxlength="100">
                                <small class="form-text text-muted">Código único del parámetro (sin espacios, usar guiones bajos)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" required 
                                       placeholder="Nombre descriptivo" maxlength="200">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" rows="2" 
                                  placeholder="Descripción detallada del parámetro"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="categoria">Categoría <span class="text-danger">*</span></label>
                                <select class="form-control" id="categoria" required>
                                    <option value="general">General</option>
                                    <option value="eventos">Eventos</option>
                                    <option value="usuarios">Usuarios</option>
                                    <option value="notificaciones">Notificaciones</option>
                                    <option value="seguridad">Seguridad</option>
                                    <option value="sistema">Sistema</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo">Tipo <span class="text-danger">*</span></label>
                                <select class="form-control" id="tipo" required onchange="cambiarTipo()">
                                    <option value="texto">Texto</option>
                                    <option value="numero">Número</option>
                                    <option value="booleano">Booleano (Sí/No)</option>
                                    <option value="json">JSON</option>
                                    <option value="fecha">Fecha</option>
                                    <option value="select">Select (Lista)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="grupo">Grupo</label>
                                <input type="text" class="form-control" id="grupo" 
                                       placeholder="Grupo (opcional)" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valor">Valor</label>
                                <input type="text" class="form-control" id="valor" 
                                       placeholder="Valor actual">
                                <div id="valorBooleano" style="display: none;">
                                    <select class="form-control" id="valorBooleanoSelect">
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valorDefecto">Valor por Defecto</label>
                                <input type="text" class="form-control" id="valorDefecto" 
                                       placeholder="Valor por defecto">
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="opcionesGroup" style="display: none;">
                        <label for="opciones">Opciones (JSON Array)</label>
                        <textarea class="form-control" id="opciones" rows="3" 
                                  placeholder='["Opción 1", "Opción 2", "Opción 3"]'></textarea>
                        <small class="form-text text-muted">Para tipo "select", ingrese un array JSON con las opciones</small>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="orden">Orden</label>
                                <input type="number" class="form-control" id="orden" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="editable" checked>
                                <label class="form-check-label" for="editable">Editable</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="visible" checked>
                                <label class="form-check-label" for="visible">Visible</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="requerido">
                                <label class="form-check-label" for="requerido">Requerido</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ayuda">Texto de Ayuda</label>
                        <textarea class="form-control" id="ayuda" rows="2" 
                                  placeholder="Texto de ayuda para el usuario"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Solo Valor -->
<div class="modal fade" id="modalValor" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Editar Valor
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formValor">
                <div class="modal-body">
                    <input type="hidden" id="valorParametroId">
                    <input type="hidden" id="valorTipo">
                    
                    <div class="form-group">
                        <label id="valorLabel">Valor</label>
                        <input type="text" class="form-control" id="valorInput">
                        <div id="valorBooleanoModal" style="display: none;">
                            <select class="form-control" id="valorBooleanoSelectModal">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <small class="form-text text-muted" id="valorAyuda"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .badge-parametro {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/configuracion.js') }}"></script>
@endsection

