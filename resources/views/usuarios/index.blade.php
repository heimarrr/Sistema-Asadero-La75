@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    {{-- ✅ Uso de ícono y etiqueta limpia para el encabezado --}}
    <h1 class="mb-4"><i class="fas fa-users me-2"></i> Gestión de Usuarios</h1>
@stop

@section('content')
<div class="card shadow-lg"> {{-- 💡 Usar shadow-lg para un look más destacado --}}
    
    {{-- 🔍 Tarjeta para Búsqueda (Mejora UX) --}}
    <div class="card-header border-0 pb-0">
        <div class="d-flex justify-content-end">
            {{-- 💡 Botón Crear: Usar data-bs-toggle (Bootstrap 5) y mover el botón a un bloque de herramientas --}}
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
            </button>
        </div>
    </div>
    
    <div class="card-body">
        
        {{-- ✅ Manejo de Alertas: Usar la clase 'close' de Bootstrap 5 para el botón de cierre --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                {{-- AdminLTE con Bootstrap 5 a veces requiere data-bs-dismiss --}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        {{-- 💡 Manejo de Errores de Validación Global --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i> Hay errores en el formulario. Por favor, revísalos.
            </div>
        @endif

        {{-- 💡 DataTable Wrapper para Búsqueda, Ordenamiento y Paginación automática --}}
        <div class="table-responsive">
            <table id="usuarios-table" class="table table-bordered table-striped table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th class="text-start">Nombre</th>
                        <th class="text-start">Usuario</th>
                        <th class="text-start">Correo</th>
                        <th>Rol</th>
                        <th style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id_usuario }}</td>
                            <td class="text-start">{{ $usuario->nombre }}</td>
                            <td class="text-start">{{ $usuario->usuario }}</td>
                            <td class="text-start">{{ $usuario->correo }}</td>
                            {{-- ✅ Corrección de Columna de Rol (Usando 'nombre') --}}
                            <td><span class="badge bg-secondary">{{ $usuario->rol->nombre ?? 'N/A' }}</span></td> 
                            <td>
                                {{-- Botón Editar --}}
                                <button class="btn btn-warning btn-xs me-1" 
                                    data-bs-toggle="modal" {{-- 💡 Usar data-bs-toggle --}}
                                    data-bs-target="#modalEditar{{ $usuario->id_usuario }}"> {{-- 💡 Usar data-bs-target --}}
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Botón Eliminar --}}
                                <button class="btn btn-danger btn-xs"
                                    data-bs-toggle="modal" {{-- 💡 Usar data-bs-toggle --}}
                                    data-bs-target="#modalEliminar{{ $usuario->id_usuario }}"> {{-- 💡 Usar data-bs-target --}}
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- ✅ Inclusión de Paginación (Solo si hay páginas) --}}
    @if ($usuarios->hasPages())
        <div class="card-footer clearfix">
            {{ $usuarios->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Inclusión de Modales --}}
@include('usuarios.partials.modal-crear', ['roles' => $roles])

@foreach ($usuarios as $usuario)
    @include('usuarios.partials.modal-editar', ['usuario' => $usuario, 'roles' => $roles])
    @include('usuarios.partials.modal-eliminar', ['usuario' => $usuario])
@endforeach

@stop

@section('js')
    <script>
        // 💡 Función JavaScript para cerrar y limpiar el fondo de forma robusta (Soluciona el bloqueo de fondo)
        function cerrarModalManual(buttonElement) {
            let modalElement = buttonElement.closest('.modal');
            if (modalElement) {
                let modal = bootstrap.Modal.getInstance(modalElement);
                if (!modal) {
                    modal = new bootstrap.Modal(modalElement);
                }
                modal.hide();

                // Limpieza Adicional: Espera un momento y elimina el backdrop y la clase modal-open
                setTimeout(() => {
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                }, 300);
            }
        }
        
        // 💡 Integración de DataTables (si se quiere usar la funcionalidad de búsqueda/ordenamiento)
        $(document).ready(function() {
            // Inicializa DataTables solo si hay elementos en la tabla
            if ($('#usuarios-table tbody tr').length > 0) {
                 $('#usuarios-table').DataTable({
                    "paging": false, // Desactivar la paginación de DataTables si usas Laravel
                    "searching": true, // Activar la barra de búsqueda
                    "ordering": true,  // Activar ordenamiento de columnas
                    "info": false,     // Ocultar info de 1 a X de Y entradas
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                    }
                });
            }
        });
    </script>
@stop