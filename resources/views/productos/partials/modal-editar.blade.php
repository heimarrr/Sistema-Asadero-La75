{{-- Este modal requiere las variables $producto, $categorias y $proveedores --}}
<div class="modal fade" id="modalEditar{{ $producto->id_producto }}" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Producto: {{ $producto->nombre }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                            value="{{ old('nombre', $producto->nombre) }}" required>
                    </div>
                    
                    {{-- Precio --}}
                    <div class="mb-3">
                        <label>Precio</label>
                        <input type="number" name="precio" step="0.01" class="form-control"
                            value="{{ old('precio', $producto->precio) }}" required>
                    </div>
                    
                    {{-- Stock --}}
                    <div class="mb-3">
                        <label>Stock</label>
                        <input type="number" name="stock" class="form-control"
                            value="{{ old('stock', $producto->stock) }}" required>
                    </div>

                    {{-- Categoría --}}
                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="id_categoria" class="form-control" required>
                            <option value="">Seleccione</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id_categoria }}"
                                    {{ old('id_categoria', $producto->id_categoria) == $categoria->id_categoria ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Proveedor --}}
                    <div class="mb-3">
                        <label>Proveedor</label>
                        <select name="id_proveedor" class="form-control" required>
                            <option value="">Seleccione</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id_proveedor }}"
                                    {{ old('id_proveedor', $producto->id_proveedor) == $proveedor->id_proveedor ? 'selected' : '' }}>
                                    {{ $proveedor->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>