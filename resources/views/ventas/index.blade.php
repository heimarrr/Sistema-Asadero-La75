@extends('adminlte::page')

@section('title', 'Gestión de Ventas')

@section('content_header')
    <h1><i class="fas fa-cash-register me-2"></i> Gestión de Ventas</h1>
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
            <h3 class="card-title mb-0">Listado de Ventas</h3>

            {{-- Botón Crear --}}
            <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm ms-auto">
                <i class="fas fa-plus me-1"></i> Nueva Venta
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="ventas-table" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="all">ID</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $venta)
                            <tr>
                                <td>{{ $venta->id_venta }}</td>
                                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $venta->usuario->nombre ?? 'N/A' }}</td>

                                <td>$ {{ number_format($venta->total, 2) }}</td>

                                <td class="text-center">
                                    <span class="badge {{ $venta->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $venta->status == 1 ? 'Activa' : 'Anulada' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Ver detalle --}}
                                        <a href="{{ route('ventas.show', $venta->id_venta) }}" 
                                           class="btn btn-info btn-sm text-white"
                                           title="Ver Detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>

                                        {{-- Anular --}}
                                        @if ($venta->status == 1)
                                            <button class="btn btn-danger btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#modalAnularVenta{{ $venta->id_venta }}"
                                                    title="Anular Venta">
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
                                    No hay ventas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- MODALES DINÁMICOS PARA ANULAR --}}
@if ($ventas->isNotEmpty())
    @foreach ($ventas as $venta)
        @if ($venta->status == 1)
            <div class="modal fade" id="modalAnularVenta{{ $venta->id_venta }}" tabindex="-1" role="dialog" aria-labelledby="modalAnularVentaLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="modalAnularVentaLabel">Confirmar Anulación</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form action="{{ route('ventas.destroy', $venta->id_venta) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body">
                                <p>¿Está seguro que desea <strong>anular</strong> la venta 
                                    <strong>N° {{ $venta->id_venta }}</strong>?</p>
                                <p class="text-danger">Esta acción afectará el control de inventario.</p>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger">
                                    Sí, Anular Venta
                                </button>
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
        if ($('#ventas-table tbody tr').length > 0 &&
            $('#ventas-table tbody tr:first td').text().trim() !== 'No hay ventas registradas.') {
            
            $('#ventas-table').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                order: [[0, 'desc']],
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                dom: '<"row"<"col-sm-12 d-flex justify-content-start"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });
        }
    });
</script>
@stop
