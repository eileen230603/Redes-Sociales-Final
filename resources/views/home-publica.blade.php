@extends('adminlte::page')

@section('title', 'UNI2 • Página Pública')

@section('content_header')
    <h1><i class="fas fa-globe-americas"></i> Bienvenido a UNI2</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">Últimos Eventos</div>
                <div class="card-body">
                    <ul>
                        <li>Campaña Médica</li>
                        <li>Festival de Reciclaje</li>
                        <li>Charla de Inclusión</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">Organizaciones Destacadas</div>
                <div class="card-body">
                    <ul>
                        <li>Fundación Esperanza Viva</li>
                        <li>Manos Solidarias</li>
                        <li>Guardianes del Bosque</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
console.log("✅ Página pública cargada correctamente");
</script>
@stop
