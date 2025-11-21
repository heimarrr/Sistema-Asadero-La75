@extends('adminlte::page')

@section('title', 'Reportes')

@section('content_header')
    <h1 class="text-center mb-4">
        <i class="fas fa-chart-pie"></i> Módulo de Reportes
    </h1>
@stop

@section('content')

<style>
    .report-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px !important;
    }
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .icon-large {
        font-size: 40px;
    }
</style>

<div class="row justify-content-center">

    {{-- REPORTE DE VENTAS --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('reportes.ventas') }}" class="text-dark">
            <div class="card report-card border-left-primary">
                <div class="card-body text-center">
                    <i class="fas fa-cash-register icon-large text-primary"></i>
                    <h4 class="mt-3">Reporte de Ventas</h4>
                    <p class="text-muted">Visualiza ingresos, productos vendidos y totales.</p>
                </div>
            </div>
        </a>
    </div>

    {{-- REPORTE DE COMPRAS --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('reportes.compras') }}" class="text-dark">
            <div class="card report-card border-left-success">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart icon-large text-success"></i>
                    <h4 class="mt-3">Reporte de Compras</h4>
                    <p class="text-muted">Consulta gastos, proveedores y productos adquiridos.</p>
                </div>
            </div>
        </a>
    </div>

    {{-- REPORTE DE INVENTARIO --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('reportes.inventario') }}" class="text-dark">
            <div class="card report-card border-left-warning">
                <div class="card-body text-center">
                    <i class="fas fa-boxes icon-large text-warning"></i>
                    <h4 class="mt-3">Reporte de Inventario</h4>
                    <p class="text-muted">Stock actual, productos críticos y movimientos.</p>
                </div>
            </div>
        </a>
    </div>

</div>

@endsection
