<div class="modal fade" id="modalEliminarProveedor{{ $proveedor->id_proveedor }}" tabindex="-1"
     aria-labelledby="modalEliminarProveedorLabel{{ $proveedor->id_proveedor }}" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarProveedorLabel{{ $proveedor->id_proveedor }}">
                    <i class="fas fa-trash-alt me-2"></i> Eliminar Proveedor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-2">¿Estás seguro de que deseas eliminar este proveedor?</p>

                <div class="alert alert-warning">
                    <strong>{{ $proveedor->nombre }}</strong><br>
                    <small>{{ $proveedor->correo }}</small>
                </div>

                <p class="text-danger fw-bold">Esta acción no se puede deshacer.</p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <form action="{{ route('proveedores.destroy', $proveedor->id_proveedor) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Eliminar
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
