<div class="modal fade" id="modalEliminarCategoria{{ $categoria->id_categoria }}" tabindex="-1" aria-labelledby="modalEliminarCategoriaLabel{{ $categoria->id_categoria }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarCategoriaLabel{{ $categoria->id_categoria }}">
                    <i class="fas fa-trash-alt me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="lead">
                    ¿Estás seguro de que deseas eliminar la categoría
                    <strong>"{{ $categoria->nombre }}"</strong>?
                </p>

                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta acción es <strong>irreversible</strong>.
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>

                <form action="{{ route('categorias.destroy', $categoria->id_categoria) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Eliminar Categoría
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>
