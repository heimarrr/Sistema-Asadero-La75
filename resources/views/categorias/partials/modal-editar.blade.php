<div class="modal fade" 
     id="modalEditarCategoria{{ $categoria->id_categoria }}" 
     tabindex="-1" 
     aria-labelledby="modalEditarCategoriaLabel{{ $categoria->id_categoria }}" 
     aria-hidden="true">
     
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('categorias.update', $categoria->id_categoria) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark" id="modalEditarCategoriaLabel{{ $categoria->id_categoria }}">
                        <i class="fas fa-edit me-2"></i> Editar Categoría
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label for="nombre_edit_{{ $categoria->id_categoria }}" class="form-label">Nombre *</label>
                        <input 
                            type="text" 
                            name="nombre" 
                            id="nombre_edit_{{ $categoria->id_categoria }}"
                            class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre', $categoria->nombre) }}" 
                            required
                        >
                        @error('nombre')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label for="descripcion_edit_{{ $categoria->id_categoria }}" class="form-label">Descripción</label>
                        <textarea
                            name="descripcion"
                            id="descripcion_edit_{{ $categoria->id_categoria }}"
                            class="form-control @error('descripcion') is-invalid @enderror"
                        >{{ old('descripcion', $categoria->descripcion) }}</textarea>
                        @error('descripcion')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
