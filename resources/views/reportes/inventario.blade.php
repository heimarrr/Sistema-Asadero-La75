@extends('adminlte::page')

@section('title', 'Inventario')

@section('content_header')
<h1><i class="fas fa-boxes"></i> Inventario</h1>
@stop

@section('content')

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Stock</th>
            <th>Unidad</th>
        </tr>
    </thead>

    <tbody>
        @foreach($productos as $p)
        <tr>
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->categoria->nombre }}</td>
            <td>{{ $p->stock_actual }}</td>
            <td>{{ $p->unidad_medida }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
