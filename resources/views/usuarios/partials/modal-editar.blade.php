<div class="modal fade" id="modalEditar{{ $usuario->id_usuario }}" tabindex="-1" aria-labelledby="modalEditarLabel{{ $usuario->id_usuario }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('usuarios.update', $usuario->id_usuario) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEditarLabel{{ $usuario->id_usuario }}">Editar Usuario: {{ $usuario->nombre }}</h5>
                    {{-- Usamos el método onclick para forzar el cierre en caso de conflicto de JS --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" 
                            onclick="document.getElementById('modalEditar{{ $usuario->id_usuario }}').classList.remove('show'); document.getElementById('modalEditar{{ $usuario->id_usuario }}').style.display = 'none'; document.body.classList.remove('modal-open');" 
                            aria-label="Cerrar"></button>
                </div>
                
                <div class="modal-body">
                    
                    {{-- 1. Mostrar Errores de Validación (Si el formulario falla y regresa) --}}
                    {{-- Esto revisa si hay errores PARA ESTE ID específico --}}
                    @if ($errors->any() && old('_id_usuario_') == $usuario->id_usuario)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        {{-- 2. Reabrir el Modal Automáticamente después de fallar la validación --}}
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var modal = new bootstrap.Modal(document.getElementById('modalEditar{{ $usuario->id_usuario }}'));
                                modal.show();
                            });
                        </script>
                    @endif
                    
                    {{-- Campo oculto para identificar qué modal falló en la validación --}}
                    <input type="hidden" name="_id_usuario_" value="{{ $usuario->id_usuario }}">

                    <div class="mb-3">
                        <label for="nombreEdit{{ $usuario->id_usuario }}" class="form-label">Nombre</label>
                        {{-- 3. Usar old() o el valor original --}}
                        <input type="text" name="nombre" id="nombreEdit{{ $usuario->id_usuario }}" 
                            value="{{ old('nombre', $usuario->nombre) }}" 
                            class="form-control @error('nombre') is-invalid @enderror" required>
                        @error('nombre')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="usuarioEdit{{ $usuario->id_usuario }}" class="form-label">Usuario</label>
                        <input type="text" name="usuario" id="usuarioEdit{{ $usuario->id_usuario }}" 
                            value="{{ old('usuario', $usuario->usuario) }}" 
                            class="form-control @error('usuario') is-invalid @enderror" required>
                        @error('usuario')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="correoEdit{{ $usuario->id_usuario }}" class="form-label">Correo</label>
                        <input type="email" name="correo" id="correoEdit{{ $usuario->id_usuario }}" 
                            value="{{ old('correo', $usuario->correo) }}" 
                            class="form-control @error('correo') is-invalid @enderror" required>
                        @error('correo')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="contrasenaEdit{{ $usuario->id_usuario }}" class="form-label">Nueva Contraseña (opcional)</label>
                        {{-- NO se usa old() en las contraseñas --}}
                        <input type="password" name="contrasena" id="contrasenaEdit{{ $usuario->id_usuario }}" 
                            class="form-control @error('contrasena') is-invalid @enderror">
                        @error('contrasena')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="rolEdit{{ $usuario->id_usuario }}" class="form-label">Rol</label>
                        <select name="id_rol" id="rolEdit{{ $usuario->id_usuario }}" 
                            class="form-select @error('id_rol') is-invalid @enderror" required>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id_rol }}" 
                                    {{ (old('id_rol') == $rol->id_rol || $usuario->id_rol == $rol->id_rol) ? 'selected' : '' }}>
                                    {{ $rol->nombre_rol }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rol')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    {{-- Botón Cancelar con fix de JS --}}
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            onclick="document.getElementById('modalEditar{{ $usuario->id_usuario }}').classList.remove('show'); document.getElementById('modalEditar{{ $usuario->id_usuario }}').style.display = 'none'; document.body.classList.remove('modal-open');">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>