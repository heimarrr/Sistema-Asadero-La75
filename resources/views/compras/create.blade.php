@extends('adminlte::page')

@section('title', 'Registrar Compra')

@section('content_header')
    <h1><i class="fas fa-cart-plus"></i> Registrar Nueva Compra</h1>
@stop

@section('content')
    <div class="container-fluid">
        {{-- Mensajes de Notificación --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Contenedor principal con AdminLTE card --}}
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Datos Generales</h3>
            </div>
            
            <form action="{{ route('compras.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    
                    <div class="row">
                        {{-- Proveedor --}}
                        <div class="col-md-6 form-group">
                            <label for="id_proveedor">Proveedor</label>
                            <select name="id_proveedor" id="id_proveedor" class="form-control select2 @error('id_proveedor') is-invalid @enderror" required>
                                <option value="">Seleccione un proveedor</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id_proveedor }}" {{ old('id_proveedor') == $proveedor->id_proveedor ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_proveedor') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        {{-- Fecha de compra --}}
                        <div class="col-md-6 form-group">
                            <label for="fecha">Fecha de Compra</label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Tabla de detalles de productos --}}
                    <h4 class="mt-4">Detalle de Productos</h4>
                    <table class="table table-bordered table-striped" id="tablaProductos">
                        <thead>
                            <tr>
                                <th style="width: 40%">Producto</th>
                                <th style="width: 15%">Cantidad</th>
                                <th style="width: 20%">Precio Unitario</th>
                                <th style="width: 20%">Subtotal</th>
                                <th style="width: 5%">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Filas dinámicas se insertan aquí --}}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>TOTAL COMPRA:</strong></td>
                                <td>
                                    {{-- El input oculto que Laravel recibirá --}}
                                    <input type="text" id="total_display" class="form-control" readonly value="0.00">
                                    <input type="hidden" id="total" name="total_compra" value="0.00">
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="card-footer d-flex justify-content-between">
                    <button type="button" id="agregarProducto" class="btn btn-secondary"><i class="fas fa-plus"></i> Agregar Producto</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Compra</button>
                </div>
            </form>
        </div>
    </div>
@stop

{{-- Incluir scripts necesarios --}}
@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Productos pasados desde el controlador, asumiendo que tienen 'id_producto', 'nombre' y 'precio_compra'
    const productos = @json($productos);
    const tbody = document.querySelector('#tablaProductos tbody');
    let row_index = 0; // Contador para el índice del array de productos

    // Inicializa Select2 para el proveedor
    $(document).ready(function() {
        $('#id_proveedor').select2({
            theme: 'bootstrap4'
        });
    });


    document.getElementById('agregarProducto').addEventListener('click', function() {
        const index = row_index++;
        let row = document.createElement('tr');
        row.id = `row-${index}`;

        // Construye el Select de productos con el índice correcto
        let select = `<select name="productos[${index}][id_producto]" class="form-control producto-select" required data-index="${index}">`;
        select += '<option value="">Seleccione</option>';
        productos.forEach(p => {
            // Usamos 'precio_compra' o ajusta a tu campo real
            select += `<option value="${p.id_producto}" data-precio="${p.precio_compra || 0.00}">${p.nombre}</option>`;
        });
        select += '</select>';

        row.innerHTML = `
            <td>${select}</td>
            <td><input type="number" name="productos[${index}][cantidad]" class="form-control cantidad-item" min="1" step="any" value="1" required data-index="${index}"></td>
            <td><input type="number" name="productos[${index}][precio_unitario]" class="form-control precio-item" min="0.01" step="0.01" required value="0.00" data-index="${index}"></td>
            <td><input type="text" class="form-control subtotal-item" id="subtotal-${index}" readonly value="0.00"></td>
            <td><button type="button" class="btn btn-danger btn-sm eliminar-row" data-index="${index}"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(row);

        // Inicializa Select2 para el nuevo elemento (si lo usas)
        $(row.querySelector('.producto-select')).select2({
            theme: 'bootstrap4',
            placeholder: "Buscar producto"
        });


        // Adjuntar eventos a la nueva fila
        const cantidadInput = row.querySelector('.cantidad-item');
        const precioInput = row.querySelector('.precio-item');
        const selectProducto = row.querySelector('.producto-select');
        const deleteButton = row.querySelector('.eliminar-row');
        
        // 1. Evento de cambio en Select (para cargar precio)
        selectProducto.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const precio = parseFloat(selectedOption.dataset.precio || 0.00);
            precioInput.value = precio.toFixed(2);
            calcularSubtotal(index);
            actualizarTotal();
        });

        // 2. Evento de cambio en Cantidad
        cantidadInput.addEventListener('input', function() { calcularSubtotal(index); actualizarTotal(); });
        
        // 3. Evento de cambio en Precio
        precioInput.addEventListener('input', function() { calcularSubtotal(index); actualizarTotal(); });
        
        // 4. Evento de eliminación
        deleteButton.addEventListener('click', function() { 
            document.getElementById(`row-${index}`).remove(); 
            actualizarTotal(); 
        });

        actualizarTotal();
    });

    function calcularSubtotal(index) {
        const row = document.getElementById(`row-${index}`);
        if (!row) return;

        const cantidad = parseFloat(row.querySelector('.cantidad-item').value) || 0;
        const precio = parseFloat(row.querySelector('.precio-item').value) || 0;
        const subtotal = cantidad * precio;
        
        row.querySelector('.subtotal-item').value = subtotal.toFixed(2);
    }

    function actualizarTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-item').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        // Actualiza el campo visible y el campo oculto que se envía
        document.getElementById('total_display').value = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
    }
</script>
@endpush