@extends('adminlte::page')

@section('title', 'Reporte de Compras')

@section('content_header')
<h1><i class="fas fa-shopping-cart"></i> Reporte de Compras</h1>
@stop

@section('content')

<form method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-4">
            <label>Fecha Inicio</label>
            <input type="date" name="inicio" class="form-control" value="{{ request('inicio') }}">
        </div>

        <div class="col-md-4">
            <label>Fecha Fin</label>
            <input type="date" name="fin" class="form-control" value="{{ request('fin') }}">
        </div>

        <div class="col-md-4">
            <label>&nbsp;</label>
            <button class="btn btn-info btn-block">Generar</button>
        </div>
    </div>
</form>

@if(isset($compras))
<div class="card">
    <div class="card-body">

        {{-- TOTAL EN COP --}}
        <h4>Total gastado:
            <strong>${{ number_format($total, 0, ',', '.') }} COP</strong>
        </h4>

        {{-- PRODUCTO MÁS COMPRADO --}}
        @if($productoMasComprado)
        <p>Producto más comprado:
            <strong>
                {{ $productoMasComprado->producto->nombre }}
                ({{ number_format($productoMasComprado->total_comprado, 0, ',', '.') }} Ventas)
            </strong>
        </p>
        @endif

        {{-- TABLA --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Usuario</th>
                    <th>Total (COP)</th>
                </tr>
            </thead>

            <tbody>
                @foreach($compras as $c)
                <tr>
                    <td>{{ $c->fecha }}</td>
                    <td>{{ $c->proveedor->nombre }}</td>
                    <td>{{ $c->usuario->nombre }}</td>
                    <td>${{ number_format($c->total, 0, ',', '.') }} COP</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endif

@endsection
