@extends('adminlte::page')

@section('title', 'Detalle de Venta')

@section('content_header')
    <h1><i class="fas fa-receipt me-2"></i> Detalle de Venta N° {{ $venta->id_venta }}</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Tarjeta Principal --}}
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-shopping-basket me-2"></i> Información de la Venta
            </h3>
        </div>

        <div class="card-body">

            {{-- Información general --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <p><strong>ID Venta:</strong> <span class="badge bg-secondary">{{ $venta->id_venta }}</span></p>
                </div>

                <div class="col-md-4">
                    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</p>
                </div>

                <div class="col-md-4">
                    {{-- Usamos el operador null-safe '?' si la relación 'usuario' pudiera ser nula --}}
                    <p><strong>Usuario:</strong> {{ $venta->usuario->nombre ?? 'N/A' }}</p> 
                </div>
            </div>

            <div class="row mb-3">
                {{-- ASUMCIÓN: Si el cliente está relacionado (cliente->nombre) o es un campo de la venta --}}
                <div class="col-md-4">
                    <p><strong>Cliente:</strong> 
                        {{ $venta->cliente->nombre ?? ($venta->cliente ?? 'Público general') }} 
                    </p>
                </div>

                <div class="col-md-4">
                    <p>
                        <strong>Estado:</strong>
                        <span class="badge {{ $venta->status == 1 ? 'bg-success' : 'bg-danger' }}">
                            <i class="fas {{ $venta->status == 1 ? 'fa-check' : 'fa-ban' }}"></i>
                            {{ $venta->status == 1 ? 'Válida' : 'Anulada' }}
                        </span>
                    </p>
                </div>
                
                <div class="col-md-4">
                    <p><strong>Total Venta:</strong> <strong class="text-primary">${{ number_format($venta->total, 2, ',', '.') }}</strong></p>
                </div>
            </div>

            <hr>

            {{-- Tabla de productos vendidos --}}
            <h4 class="mb-3"><i class="fas fa-boxes me-2"></i> Productos Vendidos</h4>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Detalle</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->detalleVentas as $detalle)
                            <tr>
                                <td>{{ $detalle->id_detalle_venta }}</td>
                                {{-- Usamos el operador null-safe '?' si la relación 'producto' pudiera ser nula --}}
                                <td>{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                {{-- Se corrige para usar precio_unitario del detalle --}}
                                <td>$ {{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                                {{-- Se corrige la fórmula del subtotal al vuelo --}}
                                <td>$ {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-primary">
                        <tr>
                            <th colspan="4" class="text-end">TOTAL VENTA:</th>
                            <th>$ {{ number_format($venta->total, 2, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

        {{-- Footer y botones de acción --}}
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Listado
            </a>

            {{-- Botón Anular (solo si está válida) --}}
            @if ($venta->status == 1)
                <button class="btn btn-danger"
                    data-toggle="modal"
                    data-target="#modalAnularVenta{{ $venta->id_venta }}">
                    <i class="fas fa-times-circle me-1"></i> Anular Venta
                </button>
            @else
                <button class="btn btn-dark" disabled>
                    <i class="fas fa-ban me-1"></i> Venta Anulada
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Modal Anular (Solo se renderiza si la venta es válida) --}}
@if ($venta->status == 1)
<div class="modal fade" id="modalAnularVenta{{ $venta->id_venta }}" tabindex="-1" aria-labelledby="modalAnularVentaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalAnularVentaLabel">Confirmar Anulación</h5>
                {{-- El atributo data-dismiss es correcto para AdminLTE/Bootstrap 4 --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('ventas.destroy', $venta->id_venta) }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <p>¿Está seguro que desea **anular** la venta <strong>N° {{ $venta->id_venta }}</strong>?</p>
                    <p class="text-danger">Esta acción es irreversible y **revertirá el stock** de los productos vendidos.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Sí, anular venta</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endif

@stop