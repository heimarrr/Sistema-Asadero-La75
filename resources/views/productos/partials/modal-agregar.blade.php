{{-- Este modal requiere las variables $categorias y $proveedores --}}
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('productos.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAgregarLabel">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label for="nombre">Nombre del Producto</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>

                    {{-- Precio de Venta --}}
                    <div class="mb-3">
                        <label for="precio">Precio de Venta</label>
                        <input type="number" id="precio" name="precio" step="0.01" class="form-control" min="0" required>
                    </div>

                    {{-- Stock Inicial --}}
                    <div class="mb-3">
                        <label for="stock">Stock Inicial</label>
                        <input type="number" id="stock" name="stock" class="form-control" min="0" required>
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

                    {{-- Proveedor --}}
                    <div class="mb-3">
                        <label for="id_proveedor">Proveedor</label>
                        <select id="id_proveedor" name="id_proveedor" class="form-control" required>
                            <option value="">Seleccione Proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id_proveedor }}">{{ $proveedor->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>