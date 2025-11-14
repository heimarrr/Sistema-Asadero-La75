<div class="modal fade" id="modalCrearProveedor" tabindex="-1" aria-labelledby="modalCrearProveedorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCrearProveedorLabel">
                    <i class="fas fa-truck me-2"></i> Registrar Proveedor
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('proveedores.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    {{-- Teléfono --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" required>
                    </div>

                    {{-- Dirección --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="direccion" class="form-control" required>
                    </div>

                    {{-- Correo --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
