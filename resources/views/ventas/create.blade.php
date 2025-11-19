@extends('adminlte::page')

@section('content')
<div class="container">

    <h2>Registrar Venta</h2>

    <form action="{{ route('ventas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Producto</label>
            <select id="selectProducto" class="form-control">
                <option value="">Seleccione...</option>
                @foreach($productos as $p)
                    <option value="{{ $p->id_producto }}"
                        data-precio="{{ $p->precio_venta }}">
                        {{ $p->nombre }} - ${{ number_format($p->precio_venta, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tabla productos -->
        <table class="table table-bordered" id="tablaProductos">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Quitar</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <h3>Total: $<span id="totalVenta">0</span></h3>
        <input type="hidden" name="total" id="inputTotal">

        <button type="submit" class="btn btn-primary">Guardar Venta</button>
    </form>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const productos = @json($productos);
    const tbody = document.querySelector("#tablaProductos tbody");
    const select = document.querySelector("#selectProducto");
    const totalVenta = document.querySelector("#totalVenta");
    const inputTotal = document.querySelector("#inputTotal");

    select.addEventListener("change", () => {
        const id = select.value;
        if (!id) return;

        const prod = productos.find(p => p.id_producto == id);

        const fila = `
            <tr data-id="${prod.id_producto}">
                <td>${prod.nombre}
                    <input type="hidden" name="productos[][id_producto]" value="${prod.id_producto}">
                </td>

                <td>
                    <input type="number" class="form-control cantidad" name="productos[][cantidad]" value="1" min="1">
                </td>

                <td>${prod.precio_venta}
                    <input type="hidden" name="productos[][precio]" value="${prod.precio_venta}">
                </td>

                <td class="subtotal">${prod.precio_venta}</td>

                <td>
                    <button type="button" class="btn btn-danger btnQuitar">X</button>
                </td>
            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', fila);
        calcularTotal();
    });

    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("btnQuitar")) {
            e.target.closest("tr").remove();
            calcularTotal();
        }
    });

    document.addEventListener("input", (e) => {
        if (e.target.classList.contains("cantidad")) {
            const tr = e.target.closest("tr");
            const precio = parseFloat(tr.querySelector("[name$='[precio]']").value);
            const cantidad = parseFloat(e.target.value);
            const subtotal = precio * cantidad;

            tr.querySelector(".subtotal").textContent = subtotal;
            tr.querySelector("[name$='[subtotal]']").value = subtotal;
            calcularTotal();
        }
    });

    function calcularTotal() {
        let total = 0;
        document.querySelectorAll(".subtotal").forEach(s => {
            total += parseFloat(s.textContent);
        });

        totalVenta.textContent = total;
        inputTotal.value = total;
    }
</script>
@endpush
