@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    {{-- ✅ Encabezado de la página --}}
    <h1><i class="fas fa-users me-2"></i> Gestión de Usuarios</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- 1. Mensajes de éxito/alerta --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            {{-- Usar data-bs-dismiss para Bootstrap 5 --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 💡 Manejo de Errores de Validación Global (Si se redirige con errores) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> Hay errores en el formulario. Por favor, revísalos.
        </div>
    @endif

    {{-- 2. Tarjeta principal con la tabla --}}
    <div class="card shadow-lg">
        {{-- Encabezado de la tarjeta con título y botón de crear --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Usuarios Registrados</h3>
            {{-- Botón Crear: Usar data-bs-toggle --}}
            <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
            </button>
    </div>

        <div class="card-body">
            {{-- 💡 DataTable Wrapper para Búsqueda, Ordenamiento y Paginación automática --}}
            <div class="table-responsive">
                <table id="usuarios-table" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th class="text-center" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id_usuario }}</td>
                                <td>{{ $usuario->nombre }}</td>
                                <td>{{ $usuario->usuario }}</td>
                                <td>{{ $usuario->correo }}</td>
                                {{-- ✅ Columna de Rol (Usando 'nombre' y badge) --}}
                                <td class="text-center"><span class="badge bg-secondary">{{ $usuario->rol->nombre ?? 'N/A' }}</span></td>
                                <td class="text-center">
                                    {{-- Agrupar botones para una mejor presentación --}}
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Botón Editar --}}
                                        <button class="btn btn-info text-white"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditar{{ $usuario->id_usuario }}" title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        {{-- Botón Eliminar --}}
                                        <button class="btn btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEliminar{{ $usuario->id_usuario }}" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </div>
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
        
        {{-- ✅ Se elimina el card-footer de paginación de Laravel si se usa DataTables con paginación --}}
        
    </div>
</div>

{{-- Inclusión de Modales (Fuera de la tarjeta principal) --}}
@include('usuarios.partials.modal-crear', ['roles' => $roles])

@foreach ($usuarios as $usuario)
    @include('usuarios.partials.modal-editar', ['usuario' => $usuario, 'roles' => $roles])
    @include('usuarios.partials.modal-eliminar', ['usuario' => $usuario])
@endforeach

@stop

@section('js')
    <script>
        // 💡 Función JavaScript para cerrar y limpiar el fondo de forma robusta (Se mantiene por si es necesario para modales anidados)
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
        
        // 💡 Configuración de DataTables Completa
        $(document).ready(function() {
             // Solo inicializa DataTables si hay filas
             if ($('#usuarios-table tbody tr').length > 0) {
                $('#usuarios-table').DataTable({
                    responsive: true,
                    lengthChange: false, // Mostrar selector de número de registros
                    autoWidth: false,
                    paging: true, // Activar la paginación de DataTables
                    searching: true, // Activar la barra de búsqueda
                    ordering: true, // Activar ordenamiento de columnas
                    info: true, // Mostrar info de entradas
                    // Adaptar la configuración de idioma para DataTables completo
                    language: {
                        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" // URL más moderna
                    },
                    // Personalizar el layout de DataTables (L: length, f: filter, t: table, i: info, p: pagination)
                    "dom": '<"row"<"col-sm-12 d-flex justify-content-start"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                });
             }
        });
    </script>
@stop