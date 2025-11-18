@extends('adminlte::page')

@section('title', 'Gestión de Categorías')

@section('content_header')
    <h1><i class="fas fa-tags me-2"></i> Gestión de Categorías</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- Mensajes de éxito/alerta --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Manejo de errores --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> Hay errores en el formulario. Por favor, revísalos.
        </div>
    @endif

    {{-- Tarjeta principal --}}
    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Listado de Categorías</h3>

            {{-- Botón Crear --}}
            <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#modalCrearCategoria">
                <i class="fas fa-plus me-1"></i> Nueva Categoría
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="categorias-table" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="all">ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categorias as $categoria)
                            <tr>
                                <td>{{ $categoria->id_categoria }}</td>
                                <td>{{ $categoria->nombre }}</td>
                                <td>{{ Str::limit($categoria->descripcion, 50) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $categoria->status ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $categoria->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Editar --}}
                                        <button class="btn btn-info btn-sm text-white"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarCategoria{{ $categoria->id_categoria }}">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        {{-- Cambiar estado --}}
                                        <form action="{{ route('categorias.toggleEstado', $categoria->id_categoria) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="Cambiar Estado">
                                                <i class="fas fa-exchange-alt"></i> {{ $categoria->status ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>

                                        {{-- Eliminar --}}
                                        <button class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEliminarCategoria{{ $categoria->id_categoria }}">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No hay categorías registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modales --}}
@include('categorias.partials.modal-crear')

@if ($categorias->isNotEmpty())
    @foreach ($categorias as $categoria)
        @include('categorias.partials.modal-editar', ['categoria' => $categoria])
        @include('categorias.partials.modal-eliminar', ['categoria' => $categoria])
    @endforeach
@endif

@stop

@section('js')
<script>
    $(document).ready(function() {
        if ($('#categorias-table tbody tr').length > 0) {
            $('#categorias-table').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                dom: '<"row"<"col-sm-12 d-flex justify-content-start"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });
        }
    });
</script>
@stop
