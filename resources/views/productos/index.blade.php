@extends('adminlte::page')

@section('title', 'Gestión de Productos')

@section('content_header')
    <h1><i class="fas fa-boxes me-2"></i> Gestión de Productos</h1>
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
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> Hay errores en el formulario. Por favor verifica los datos.
        </div>
    @endif

    <div class="card shadow-lg">

        {{-- ENCABEZADO --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Productos Registrados</h3>

            <button class="btn btn-primary btn-sm ms-auto"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAgregar">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Producto
            </button>
        </div>

        {{-- TABLA --}}
        <div class="card-body">
            <div class="table-responsive">
                <table id="productos-table" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Tipo</th>
                            <th>Stock</th>
                            <th>Unidad</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                            <tr>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $producto->descripcion }}</td>
                                <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                                <td>{{ ucfirst($producto->tipo) }}</td>
                                <td class="{{ $producto->stock_actual < 5 ? 'text-danger fw-bold' : '' }}">
                                    {{ $producto->stock_actual }}
                                </td>
                                <td>{{ $producto->unidad_medida }}</td>
                                <td>${{ number_format($producto->precio_compra ?? 0, 2) }}</td>
                                <td>${{ number_format($producto->precio_venta ?? 0, 2) }}</td>

                                {{-- Estado --}}
                                <td class="text-center">
                                    <span class="badge {{ $producto->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $producto->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Editar --}}
                                        <button class="btn btn-info btn-sm text-white"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditar{{ $producto->id_producto }}"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- Cambiar estado --}}
                                        <form action="{{ route('productos.toggleEstado', $producto->id_producto) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="Cambiar Estado">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                        </form>

                                        {{-- Eliminar --}}
                                        <button class="btn btn-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEliminar{{ $producto->id_producto }}"
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modales individuales --}}
                            {{-- Proveedores ya no se pasan en el compact --}}
                            @include('productos.partials.modal-editar', compact('producto', 'categorias')) 
                            @include('productos.partials.modal-eliminar', ['producto' => $producto])

                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Modal agregar producto --}}
{{-- Proveedores ya no se pasan en el compact --}}
@include('productos.partials.modal-agregar', compact('categorias'))

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
        if ($('#productos-table tbody tr').length > 0) {
            $('#productos-table').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
                // Configuración de DOM para DataTables
                dom: '<"row"<"col-sm-12 d-flex justify-content-start"f>>rt'
                     + '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });
        }
    });
</script>
@stop