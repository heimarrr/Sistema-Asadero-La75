@extends('adminlte::page')

@section('title', 'Gestión de Compras')

@section('content_header')
    <h1><i class="fas fa-shopping-cart me-2"></i> Gestión de Compras</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- Mensajes de éxito/alerta --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif


    {{-- Tarjeta principal --}}
    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Listado de Compras</h3>

            {{-- Botón Crear --}}
            <a href="{{ route('compras.create') }}" class="btn btn-primary btn-sm ms-auto">
                <i class="fas fa-plus me-1"></i> Nueva Compra
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="compras-table" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="all">ID</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($compras as $compra)
                            <tr>
                                <td>{{ $compra->id_compra }}</td>
                                <td>{{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</td>
                                {{-- Asegúrate de que las relaciones proveedor y usuario existan y estén cargadas (Eager Loading) --}}
                                <td>{{ $compra->proveedor->nombre ?? 'N/A' }}</td>
                                <td>{{ $compra->usuario->name ?? 'N/A' }}</td>
                                <td>$ {{ number_format($compra->total, 2) }}</td>
                                <td class="text-center">
                                    {{-- El status 1 es Válida (verde), cualquier otro (0) es Anulada (rojo/gris) --}}
                                    <span class="badge {{ $compra->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $compra->status == 1 ? 'Válida' : 'Anulada' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Ver Detalles (Show) --}}
                                        <a href="{{ route('compras.show', $compra->id_compra) }}" class="btn btn-info btn-sm text-white" title="Ver Detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>

                                        {{-- Botón Anular (Solo si está Válida) --}}
                                        @if ($compra->status == 1)
                                            <button class="btn btn-danger btn-sm"
                                                data-toggle="modal"
                                                data-target="#modalAnularCompra{{ $compra->id_compra }}"
                                                title="Anular Compra">
                                                <i class="fas fa-times-circle"></i> Anular
                                            </button>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-times-circle"></i> Anulada
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No hay compras registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- MODALES DINÁMICOS PARA ANULAR (Solo si hay compras) --}}
@if ($compras->isNotEmpty())
    @foreach ($compras as $compra)
        @if ($compra->status == 1)
            <div class="modal fade" id="modalAnularCompra{{ $compra->id_compra }}" tabindex="-1" role="dialog" aria-labelledby="modalAnularCompraLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="modalAnularCompraLabel">Confirmar Anulación</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('compras.destroy', $compra->id_compra) }}" method="POST">
                            @csrf
                            @method('DELETE') {{-- O POST si usas una ruta custom como 'compras.anular' --}}
                            <div class="modal-body">
                                <p>¿Está seguro que desea **anular** la compra **N° {{ $compra->id_compra }}** al proveedor **{{ $compra->proveedor->nombre ?? 'N/A' }}**?</p>
                                <p class="text-danger">Esta acción es irreversible y afectará el stock de los productos.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Sí, Anular Compra</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

@stop

@section('js')
<script>
    $(document).ready(function() {
        // Inicializa DataTables solo si hay filas, para evitar errores
        if ($('#compras-table tbody tr').length > 0 && $('#compras-table tbody tr:first td').text().trim() !== 'No hay compras registradas.') {
            $('#compras-table').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                order: [[0, 'desc']], // Ordenar por ID (fecha) descendente por defecto
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                // Configuración de DOM adaptada para AdminLTE
                dom: '<"row"<"col-sm-12 d-flex justify-content-start"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });
        }
    });
</script>
@stop