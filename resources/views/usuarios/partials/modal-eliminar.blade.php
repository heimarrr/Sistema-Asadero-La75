{{-- NOTA: Este archivo debe incluirse dentro del bucle @foreach de index.blade.php --}}

<div class="modal fade" id="modalEliminar{{ $usuario->id_usuario }}" tabindex="-1" aria-labelledby="modalEliminarLabel{{ $usuario->id_usuario }}" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            
            {{-- Formulario que enviará la solicitud DELETE --}}
            <form action="{{ route('usuarios.destroy', $usuario->id_usuario) }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalEliminarLabel{{ $usuario->id_usuario }}">
                        <i class="fas fa-exclamation-triangle me-1"></i> Confirmar Eliminación
                    </h5>
                    {{-- 💡 MANTENEMOS EL data-bs-dismiss (es el estándar de B5) --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                
                <div class="modal-body text-center">
                    <p>¿Estás seguro de que deseas **eliminar** al usuario?</p>
                    <p class="fw-bold text-danger">{{ $usuario->nombre }}</p>
                    <small class="text-muted">Esta acción es irreversible.</small>
                </div>
                
                <div class="modal-footer justify-content-center">
                    {{-- 💡 CORRECCIÓN/MEJORA: Agregamos un 'onclick' para forzar el cierre con JS --}}
                    <button type="button" 
                            class="btn btn-secondary" 
                            data-bs-dismiss="modal" 
                            onclick="document.getElementById('modalEliminar{{ $usuario->id_usuario }}').classList.remove('show'); document.getElementById('modalEliminar{{ $usuario->id_usuario }}').style.display = 'none'; document.body.classList.remove('modal-open');">
                        Cancelar
                    </button>
                    
                    {{-- El botón que envía el formulario DELETE --}}
                    <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>