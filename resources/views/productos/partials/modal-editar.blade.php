{{-- Este modal requiere las variables $producto y $categorias --}}
<div class="modal fade" id="modalEditar{{ $producto->id_producto }}" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Producto: {{ $producto->nombre }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                               value="{{ old('nombre', $producto->nombre) }}" required>
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $producto->descripcion) }}</textarea>
                    </div>

                    {{-- Tipo --}}
                    <div class="mb-3">
                        <label>Tipo</label>
                        <select name="tipo" class="form-control" required>
                            <option value="insumo" {{ old('tipo', $producto->tipo) == 'insumo' ? 'selected' : '' }}>Insumo</option>
                            <option value="venta" {{ old('tipo', $producto->tipo) == 'venta' ? 'selected' : '' }}>Venta</option>
                        </select>
                    </div>

                    {{-- Precio de Compra --}}
                    <div class="mb-3">
                        <label>Precio de Compra</label>
                        <input type="number" name="precio_compra" step="0.01" class="form-control"
                               value="{{ old('precio_compra', $producto->precio_compra) }}" min="0" nullable>
                    </div>

                    {{-- Precio de Venta --}}
                    <div class="mb-3">
                        <label>Precio de Venta</label>
                        <input type="number" name="precio_venta" step="0.01" class="form-control"
                               value="{{ old('precio_venta', $producto->precio_venta) }}" min="0" nullable>
                    </div>

                    {{-- Stock Actual --}}
                    <div class="mb-3">
                        <label>Stock</label>
                        <input type="number" name="stock_actual" class="form-control"
                               value="{{ old('stock_actual', $producto->stock_actual) }}" min="0" required>
                    </div>

                    {{-- Unidad de Medida --}}
                    <div class="mb-3">
                        <label>Unidad de Medida</label>
                        <input type="text" name="unidad_medida" class="form-control"
                               value="{{ old('unidad_medida', $producto->unidad_medida) }}" required>
                    </div>

                    {{-- Categoría --}}
                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="id_categoria" class="form-control" required>
                            <option value="">Seleccione Categoría</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id_categoria }}"
                                    {{ old('id_categoria', $producto->id_categoria) == $categoria->id_categoria ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>