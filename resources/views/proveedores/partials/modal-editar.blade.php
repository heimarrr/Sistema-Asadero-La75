<div class="modal fade" id="modalEditarProveedor{{ $proveedor->id_proveedor }}" tabindex="-1"
     aria-labelledby="modalEditarProveedorLabel{{ $proveedor->id_proveedor }}" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalEditarProveedorLabel{{ $proveedor->id_proveedor }}">
                    <i class="fas fa-edit me-2"></i> Editar Proveedor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('proveedores.update', $proveedor->id_proveedor) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                               value="{{ $proveedor->nombre }}" required>
                    </div>

                    {{-- Teléfono --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="{{ $proveedor->telefono }}" required>
                    </div>

                    {{-- Dirección --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="{{ $proveedor->direccion }}" required>
                    </div>

                    {{-- Correo --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo</label>
                        <input type="email" name="correo" class="form-control"
                               value="{{ $proveedor->correo }}" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-save me-1"></i> Actualizar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
