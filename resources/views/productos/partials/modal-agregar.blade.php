{{-- Este modal requiere la variable $categorias --}}
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('productos.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAgregarLabel">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label for="nombre">Nombre del Producto</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="2"></textarea>
                    </div>

                    {{-- Tipo --}}
                    <div class="mb-3">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo" class="form-control" required>
                            <option value="insumo" selected>Insumo</option>
                            <option value="venta">Venta</option>
                        </select>
                    </div>

                    {{-- Precio de Compra --}}
                    <div class="mb-3">
                        <label for="precio_compra">Precio de Compra</label>
                        <input type="number" id="precio_compra" name="precio_compra" step="0.01" class="form-control" min="0" nullable>
                    </div>

                    {{-- Precio de Venta --}}
                    <div class="mb-3">
                        <label for="precio_venta">Precio de Venta</label>
                        <input type="number" id="precio_venta" name="precio_venta" step="0.01" class="form-control" min="0" nullable>
                    </div>

                    {{-- Stock Inicial --}}
                    <div class="mb-3">
                        <label for="stock_actual">Stock Inicial</label>
                        <input type="number" id="stock_actual" name="stock_actual" class="form-control" min="0" required>
                    </div>

                    {{-- Unidad de Medida --}}
                    <div class="mb-3">
                        <label for="unidad_medida">Unidad de Medida</label>
                        <input type="text" id="unidad_medida" name="unidad_medida" class="form-control" required>
                    </div>

                    {{-- Categoría --}}
                    <div class="mb-3">
                        <label for="id_categoria">Categoría</label>
                        <select id="id_categoria" name="id_categoria" class="form-control" required>
                            <option value="">Seleccione Categoría</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id_categoria }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>