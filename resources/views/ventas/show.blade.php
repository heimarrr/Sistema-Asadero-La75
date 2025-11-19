@extends('adminlte::page')

@section('title', 'Detalle de Venta')

@section('content_header')
    <h1><i class="fas fa-receipt me-2"></i> Detalle de Venta</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Tarjeta --}}
    <div class="card shadow-lg">
        <div class="card-header bg-dark text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-shopping-basket me-2"></i> Información de la Venta
            </h3>
        </div>

        <div class="card-body">

            {{-- Información general --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <p><strong>ID Venta:</strong> {{ $venta->id_venta }}</p>
                </div>

                <div class="col-md-4">
                    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</p>
                </div>

                <div class="col-md-4">
                    <p><strong>Usuario:</strong> {{ $venta->usuario->nombre ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="row mb-3">

                <div class="col-md-4">
                    <p><strong>Cliente:</strong> {{ $venta->cliente ?? 'Público general' }}</p>
                </div>

                <div class="col-md-4">
                    <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
                </div>

                <div class="col-md-4">
                    <p><strong>Estado:</strong>
                        <span class="badge {{ $venta->status == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $venta->status == 1 ? 'Válida' : 'Anulada' }}
                        </span>
                    </p>
                </div>
            </div>

            <hr>

            {{-- Tabla de productos vendidos --}}
            <h4 class="mb-3"><i class="fas fa-list me-2"></i> Productos Vendidos</h4>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
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
                                <td>{{ $detalle->producto->nombre ?? 'N/A' }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>$ {{ number_format($detalle->precio_venta, 2) }}</td>
                                <td>$ {{ number_format($detalle->cantidad * $detalle->precio_venta, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="4" class="text-end">TOTAL:</th>
                            <th>$ {{ number_format($venta->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>

            {{-- Botón Anular (solo si está válida) --}}
            @if ($venta->status == 1)
                <button class="btn btn-danger"
                    data-toggle="modal"
                    data-target="#modalAnularVenta{{ $venta->id_venta }}">
                    <i class="fas fa-times-circle me-1"></i> Anular Venta
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Modal Anular --}}
@if ($venta->status == 1)
<div class="modal fade" id="modalAnularVenta{{ $venta->id_venta }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Anulación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('ventas.destroy', $venta->id_venta) }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <p>¿Está seguro que desea anular la venta <strong>N° {{ $venta->id_venta }}</strong>?</p>
                    <p class="text-danger">Esto revertirá el stock de los productos.</p>
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
