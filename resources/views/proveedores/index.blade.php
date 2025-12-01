@extends('adminlte::page')

@section('title', 'Gestión de Proveedores')

@section('content_header')
    <h1><i class="fas fa-truck me-2"></i> Gestión de Proveedores</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Errores --}}
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


    <div class="card shadow-lg">

        {{-- ENCABEZADO --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Proveedores Registrados</h3>

            <button class="btn btn-primary btn-sm ms-auto"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCrearProveedor">
                <i class="fas fa-plus me-1"></i> Nuevo Proveedor
            </button>
        </div>

        {{-- TABLA --}}
        <div class="card-body">
            <div class="table-responsive">
                <table id="proveedores-table" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Correo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($proveedores as $proveedor)
                            <tr>
                                <td>{{ $proveedor->id_proveedor }}</td>
                                <td>{{ $proveedor->nombre }}</td>
                                <td>{{ $proveedor->telefono }}</td>
                                <td>{{ $proveedor->direccion }}</td>
                                <td>{{ $proveedor->correo }}</td>

                                {{-- Estado --}}
                                <td class="text-center">
                                    <span class="badge {{ $proveedor->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $proveedor->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">

                                        {{-- Editar --}}
                                        <button class="btn btn-info btn-sm text-white"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditarProveedor{{ $proveedor->id_proveedor }}"
                                                title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        {{-- Cambiar estado --}}
                                        <form action="{{ route('proveedores.toggleEstado', $proveedor->id_proveedor) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="Cambiar Estado">
                                                <i class="fas fa-exchange-alt"></i> Desactivar
                                            </button> 
                                        </form>

                                        {{-- Eliminar --}}
                                        <button class="btn btn-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEliminarProveedor{{ $proveedor->id_proveedor }}"
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>

                                    </div>
                                </td>
                            </tr>

                            {{-- Modales individuales --}}
                            @include('proveedores.partials.modal-editar', ['proveedor' => $proveedor])
                            @include('proveedores.partials.modal-eliminar', ['proveedor' => $proveedor])

                        @empty
                            <tr>
                                <td colspan="7" class="text-muted text-center">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal crear --}}
@include('proveedores.partials.modal-crear')

@stop

@section('css')
<style>
    .dataTables_filter input {
        width: 400px !important; /* Ajusta el ancho */
        height: 35px;            /* Alto opcional */
        font-size: 14px;         /* Texto */
        border-radius: 8px;      /* Bordes */
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        if ($('#proveedores-table tbody tr').length > 0) {
            $('#proveedores-table').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                dom: '<"row"<"col-sm-12 d-flex justify-content-start"f>>rt'
                     + '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });
        }
    });
</script>
@stop
