{{-- Este modal requiere la variable $producto --}}
<div class="modal fade" id="modalEliminar{{ $producto->id_producto }}" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalEliminarLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar el producto <strong>{{ $producto->nombre }}</strong> (Nro. {{ $producto->id_producto }})?
                    Esta acción es irreversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>