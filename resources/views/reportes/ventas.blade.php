@extends('adminlte::page')

@section('title', 'Reporte de Ventas')

@section('content_header')
<h1><i class="fas fa-cash-register"></i> Reporte de Ventas</h1>
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

@if(isset($ventas))
<div class="card">
    <div class="card-body">

        {{-- TOTAL VENDIDO EN COP --}}
        <h4>Total vendido: 
            <strong>${{ number_format($total, 0, ',', '.') }} COP</strong>
        </h4>

        {{-- PRODUCTO MÁS VENDIDO --}}
        @if($productoMasVendido)
        <p>Producto más vendido: 
            <strong>
                {{ $productoMasVendido->producto->nombre }}
                ({{ number_format($productoMasVendido->cantidad_vendida, 0, ',', '.') }} Ventas)
            </strong>
        </p>
        @endif

        {{-- TABLA --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Total (COP)</th>
                </tr>
            </thead>

            <tbody>
                @foreach($ventas as $v)
                <tr>
                    <td>{{ $v->fecha }}</td>
                    <td>{{ $v->usuario->nombre ?? 'N/A' }}</td>
                    <td>${{ number_format($v->total, 0, ',', '.') }} COP</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endif

@endsection
