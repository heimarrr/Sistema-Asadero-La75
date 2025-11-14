<div class="modal fade" id="modalCrearCategoria" tabindex="-1" aria-labelledby="modalCrearCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCrearCategoriaLabel">
                        <i class="fas fa-plus-circle me-2"></i> Nueva Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label for="nombre_categoria" class="form-label">Nombre *</label>
                        <input 
                            type="text" 
                            name="nombre" 
                            id="nombre_categoria" 
                            class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre') }}"
                            required
                        >
                        @error('nombre')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label for="descripcion_categoria" class="form-label">Descripción</label>
                        <textarea 
                            name="descripcion" 
                            id="descripcion_categoria" 
                            class="form-control @error('descripcion') is-invalid @enderror"
                        >{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
