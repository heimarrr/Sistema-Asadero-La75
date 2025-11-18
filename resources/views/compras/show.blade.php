@extends('adminlte::page')

@section('title', 'Detalle de Compra #' . $compra->id_compra)

@section('content_header')
    <h1><i class="fas fa-file-invoice"></i> Detalle de Compra N° {{ $compra->id_compra }}</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Información General</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre ?? 'N/A' }}</p>
                    <p><strong>Fecha de Compra:</strong> {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Registrado por:</strong> {{ $compra->usuario->name ?? 'N/A' }}</p>
                    <p><strong>Estado:</strong> 
                        <span class="badge {{ $compra->status == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $compra->status == 1 ? 'Válida' : 'Anulada' }}
                        </span>
                    </p>
                </div>
            </div>
            <hr>

            <h4>Productos Adquiridos</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="bg-info">
                        <tr>
                            <th>Producto</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Precio Unitario</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compra->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}</td>
                                <td class="text-right">{{ number_format($detalle->cantidad, 2) }}</td>
                                <td class="text-right">$ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="text-right">$ {{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>TOTAL COMPRA:</strong></td>
                            <td class="text-right"><strong>$ {{ number_format($compra->total, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('compras.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Listado</a>
        </div>
    </div>
</div>
@stop