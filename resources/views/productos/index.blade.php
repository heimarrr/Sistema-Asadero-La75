@extends('adminlte::page')

@section('title', 'Gestión de Productos')

@section('content_header')
    <h1>Gestión de Productos</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- 1. Mensajes de éxito --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 2. Tarjeta principal con la tabla --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Productos registrados</h3>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="fas fa-plus"></i> Crear nuevo
            </button>
        </div>

        <div class="card-body">
            <table id="productos-table" class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nro</th>
                        <th>Nombre</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
                        <tr>
                            <td>{{ $producto->id_producto }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>${{ number_format($producto->precio_venta, 2) }}</td>
                            <td>{{ $producto->stock }}</td>
                            <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                            <td>{{ $producto->proveedor->nombre ?? 'Sin proveedor' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="#" class="btn btn-success" title="Ver">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <button class="btn btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#modalEditar{{ $producto->id_producto }}" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#modalEliminar{{ $producto->id_producto }}" title="Eliminar">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- 4. LLAMADA A PARTIALS DENTRO DEL BUCLE --}}
                        @include('productos.partials.modal-editar', compact('producto', 'categorias', 'proveedores'))
                        @include('productos.partials.modal-eliminar', ['producto' => $producto])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 3. LLAMADA A PARTIAL FUERA DEL BUCLE --}}
@include('productos.partials.modal-agregar', compact('categorias', 'proveedores'))

@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#productos-table').DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
            },
        });
    });
</script>
@stop
