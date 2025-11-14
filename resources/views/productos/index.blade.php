@extends('adminlte::page')

@section('title', 'Gestión de Productos')

@section('content_header')
    <h1><i class="fas fa-boxes me-2"></i> Gestión de Productos</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- 1. Mensajes de éxito/alerta --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Manejo global de errores --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> Hay errores en el formulario. Por favor verifica los datos.
        </div>
    @endif

    {{-- 2. Tarjeta con tabla --}}
    <div class="card shadow-lg">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Productos registrados</h3>
            <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Producto
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="productos-table" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th class="text-center" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($productos as $producto)
                            <tr>
                                <td>{{ $producto->id_producto }}</td>
                                <td>{{ $producto->nombre }}</td>
                                <td>${{ number_format($producto->precio_compra, 2) }}</td>
                                <td>${{ number_format($producto->precio_venta, 2) }}</td>
                                <td>{{ $producto->stock }}</td>
                                <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                                <td>{{ $producto->proveedor->nombre ?? 'Sin proveedor' }}</td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-info text-white"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditar{{ $producto->id_producto }}"
                                            title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        <button class="btn btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEliminar{{ $producto->id_producto }}"
                                            title="Eliminar">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modales dentro del bucle --}}
                            @include('productos.partials.modal-editar', compact('producto', 'categorias', 'proveedores'))
                            @include('productos.partials.modal-eliminar', ['producto' => $producto])
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>

{{-- Modal para crear producto --}}
@include('productos.partials.modal-agregar', compact('categorias', 'proveedores'))

@stop

@section('js')
<script>
    $(document).ready(function() {
        // Solo inicializa DataTables si existen filas
        if ($('#productos-table tbody tr').length > 0) {
            $('#productos-table').DataTable({
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
