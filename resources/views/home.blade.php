@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="text-center mb-4">
        <i class="fas fa-tachometer-alt"></i> Dashboard Principal
    </h1>
@stop

@section('content')

<style>
    .dash-card {
        transition: .2s ease;
        border-radius: 12px !important;
    }
    .dash-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 22px rgba(0,0,0,0.15);
    }
    .icon-big {
        font-size: 40px;
    }
</style>

{{-- TARJETAS SUPERIORES --}}
<div class="row">

    {{-- VENTAS DEL DÍA --}}
    <div class="col-md-3 mb-4">
        <div class="card dash-card border-left-primary">
            <div class="card-body text-center">
                <i class="fas fa-cash-register icon-big text-primary"></i>
                <h4 class="mt-3">Ventas Hoy</h4>
                <h3 class="font-weight-bold text-primary">
                    ${{ number_format($ventasHoy ?? 0, 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>

    {{-- COMPRAS DEL MES --}}
    <div class="col-md-3 mb-4">
        <div class="card dash-card border-left-success">
            <div class="card-body text-center">
                <i class="fas fa-shopping-cart icon-big text-success"></i>
                <h4 class="mt-3">Compras Mes</h4>
                <h3 class="font-weight-bold text-success">
                    ${{ number_format($comprasMes ?? 0, 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>

    {{-- PRODUCTOS EN STOCK --}}
    <div class="col-md-3 mb-4">
        <div class="card dash-card border-left-warning">
            <div class="card-body text-center">
                <i class="fas fa-boxes icon-big text-warning"></i>
                <h4 class="mt-3">Productos en Stock</h4>
                <h3 class="font-weight-bold text-warning">
                    {{ $totalProductos ?? 0 }}
                </h3>
            </div>
        </div>
    </div>

    {{-- PRODUCTOS CRÍTICOS --}}
    <div class="col-md-3 mb-4">
        <div class="card dash-card border-left-danger">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle icon-big text-danger"></i>
                <h4 class="mt-3">Stock Crítico</h4>
                <h3 class="font-weight-bold text-danger">
                    {{ $productosCriticos ?? 0 }}
                </h3>
            </div>
        </div>
    </div>

</div>

{{-- NUEVAS SECCIONES --}}
<div class="row">

    {{-- 🔄 Comparativa compras vs ventas --}}
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-exchange-alt"></i> Comparativa Compras vs Ventas
            </div>
            <div class="card-body">
                <p><strong>Ventas:</strong> ${{ number_format($totalVentas ?? 0, 0, ',', '.') }}</p>
                <p><strong>Compras:</strong> ${{ number_format($totalCompras ?? 0, 0, ',', '.') }}</p>

                @php
                    $diferencia = ($totalVentas ?? 0) - ($totalCompras ?? 0);
                @endphp

                <p class="mt-3">
                    <strong>Diferencia:</strong>
                    <span class="{{ $diferencia >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($diferencia, 0, ',', '.') }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- 👤 Ventas por usuario --}}
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-user"></i> Ventas por Usuario
            </div>
            <div class="card-body">
                @foreach ($ventasPorUsuario ?? [] as $usuario)
                    <p>
                        <strong>{{ $usuario->name }}:</strong>
                        ${{ number_format($usuario->total ?? 0, 0, ',', '.') }}
                    </p>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 🐔 Top productos vendidos/comprados --}}
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <i class="fas fa-drumstick-bite"></i> Top Productos Vendidos
            </div>
            <div class="card-body">
                @foreach ($topVendidos ?? [] as $prod)
                    <p>
                        <strong>{{ $prod->nombre }}:</strong>
                        {{ $prod->cantidad }} unidades
                    </p>
                @endforeach
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-header bg-success text-white">
                <i class="fas fa-box-open"></i> Top Productos Comprados
            </div>
            <div class="card-body">
                @foreach ($topComprados ?? [] as $prod)
                    <p>
                        <strong>{{ $prod->nombre }}:</strong>
                        {{ $prod->cantidad }} unidades
                    </p>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection
