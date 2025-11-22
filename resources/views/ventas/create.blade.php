@extends('adminlte::page')

@section('title', 'Registrar Venta')

@section('content_header')
    <h1><i class="fas fa-cash-register"></i> Registrar Nueva Venta</h1>
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
                <h3 class="card-title">Datos Generales de la Venta</h3>
            </div>
            
            <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
                @csrf
                <div class="card-body">
                    
                    <div class="row">
                        {{-- Fecha de Venta --}}
                        <div class="col-md-6 form-group">
                            <label for="fecha">Fecha de Venta</label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <hr>

                    {{-- ÚNICO SELECTOR DE PRODUCTOS VISIBLE --}}
                    <h4 class="mt-4">Seleccionar Producto</h4>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            {{-- Solo la etiqueta Producto que vimos en la imagen --}}
                            <label for="selectProducto">Producto</label>
                            
                            {{-- EL SELECT QUE INICIALIZA SELECT2 --}}
                            <select id="selectProducto" class="form-control select2" style="width: 100%;">
                                <option value="">Seleccione un producto...</option>
                                @foreach($productos as $p)
                                    <option value="{{ $p->id_producto }}"
                                        data-precio="{{ $p->precio_venta }}"
                                        data-nombre="{{ $p->nombre }}"
                                        data-stock="{{ $p->stock_actual }}"> 
                                        {{ $p->nombre }} - ${{ number_format($p->precio_venta, 2, ',', '.') }} (Stock: {{ $p->stock_actual }})
                                    </option>
                                @endforeach
                            </select>
                            {{-- NO hay otro <p> o texto estático debajo de este select --}}
                        </div>
                    </div>


                    {{-- Tabla de detalles de productos --}}
                    <h4 class="mt-4">Detalle de Productos a Vender</h4>
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
                                <td colspan="3" class="text-right"><strong>TOTAL VENTA:</strong></td>
                                <td>
                                    <input type="text" id="total_display" class="form-control" readonly value="0.00">
                                    <input type="hidden" id="total" name="total" value="0.00"> 
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                {{-- FOOTER CON BOTONES --}}
                <div class="card-footer d-flex justify-content-between">
                    <button type="button" id="btnAgregarProducto" class="btn btn-secondary"><i class="fas fa-plus"></i> Agregar Producto</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarVenta"><i class="fas fa-save"></i> Registrar Venta</button>
                </div>
            </form>
        </div>
    </div>
@stop

@push('js')

<script>
    // Referencias del DOM
    const tbody = document.querySelector('#tablaProductos tbody');
    const selectProducto = document.querySelector('#selectProducto');
    const btnAgregarProducto = document.querySelector('#btnAgregarProducto');
    const totalDisplay = document.querySelector('#total_display');
    const totalHidden = document.querySelector('#total');

    // Estado local para evitar duplicados
    let productosAgregados = {}; // {id_producto: indice_fila}

    // Inicializa Select2
    $(document).ready(function() {
        $('#selectProducto').select2({
            theme: 'bootstrap4',
            placeholder: "Seleccione un producto...",
            allowClear: true
        });
    });

    // Función para calcular subtotal de una fila
    function calcularSubtotal(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) return;

        const cantidadInput = row.querySelector('.cantidad-item');
        const precioInput = row.querySelector('.precio-item');
        
        let cantidad = Math.max(1, parseFloat(cantidadInput.value)) || 1;
        
        const precio = parseFloat(precioInput.value) || 0;
        const subtotal = cantidad * precio;
        
        row.querySelector('.subtotal-item').value = subtotal.toFixed(2);
        cantidadInput.value = cantidad;
        
        actualizarTotal();
    }

    // Función para actualizar el total general de la venta
    function actualizarTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-item').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        totalDisplay.value = total.toFixed(2);
        totalHidden.value = total.toFixed(2);
    }

    // *** EVENTO PRINCIPAL: AGREGAR PRODUCTO ***
    btnAgregarProducto.addEventListener('click', function() {
        const id = selectProducto.value;
        if (!id) {
            alert('Por favor, seleccione un producto.');
            return;
        }

        const option = selectProducto.options[selectProducto.selectedIndex];
        const precio = parseFloat(option.getAttribute('data-precio'));
        const nombre = option.getAttribute('data-nombre');
        const stock = parseInt(option.getAttribute('data-stock'));

        // 1. Manejo de Stock y Duplicados
        if (stock <= 0) {
            alert('Error: Este producto no tiene stock disponible.');
            return;
        }

        const index = productosAgregados[id];

        if (index !== undefined) {
            // Producto ya existe, incrementar cantidad y validar stock
            const inputCantidad = document.querySelector(`tr[data-id="${id}"] .cantidad-item`);
            let nuevaCantidad = (parseInt(inputCantidad.value) || 0) + 1;

            if (nuevaCantidad > stock) {
                alert(`Advertencia: Stock máximo alcanzado (${stock} unidades).`);
                nuevaCantidad = stock;
            }

            inputCantidad.value = nuevaCantidad;
            calcularSubtotal(id);
            // Limpiar select
            $('#selectProducto').val(null).trigger('change'); 
            return;
        }

        // 2. Agregar nueva fila
        
        const productIndex = id; 
        productosAgregados[id] = productIndex; 

        const row = document.createElement('tr');
        row.setAttribute('data-id', id);

        const subtotalInicial = precio * 1;

        row.innerHTML = `
            <td>${nombre}
                <input type="hidden" name="productos[${productIndex}][id_producto]" value="${id}">
            </td>
            <td>
                <input type="number" name="productos[${productIndex}][cantidad]" class="form-control cantidad-item" 
                       min="1" max="${stock}" step="any" value="1" required>
            </td>
            <td>
                <input type="number" name="productos[${productIndex}][precio_unitario]" class="form-control precio-item" 
                       min="0.01" step="0.01" required value="${precio.toFixed(2)}">
            </td>
            <td>
                <input type="text" class="form-control subtotal-item" readonly value="${subtotalInicial.toFixed(2)}">
                <input type="hidden" name="productos[${productIndex}][subtotal]" value="${subtotalInicial.toFixed(2)}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm eliminar-row"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(row);

        // 3. Adjuntar Eventos
        const cantidadInput = row.querySelector('.cantidad-item');
        const precioInput = row.querySelector('.precio-item');
        const deleteButton = row.querySelector('.eliminar-row');

        // Eventos de cálculo
        cantidadInput.addEventListener('input', function() { 
            let currentQuantity = parseInt(this.value) || 0;
            if (currentQuantity > stock) {
                 alert(`Advertencia: La cantidad máxima permitida es ${stock}.`);
                 this.value = stock;
            } else if (currentQuantity < 1) {
                 this.value = 1;
            }
            calcularSubtotal(id);
        });
        
        precioInput.addEventListener('input', function() { calcularSubtotal(id); });
        
        // Evento de eliminación
        deleteButton.addEventListener('click', function() { 
            row.remove(); 
            delete productosAgregados[id];
            actualizarTotal(); 
        });

        // Limpiar select
        $('#selectProducto').val(null).trigger('change'); 
        actualizarTotal();
    });

    // 4. Prevenir envío si la tabla está vacía
    document.getElementById('formVenta').addEventListener('submit', function(e) {
        if (Object.keys(productosAgregados).length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto para registrar la venta.');
        }
    });
</script>
@endpush