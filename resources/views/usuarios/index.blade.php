@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <h1><i class="fas fa-users me-2"></i> Gestión de Usuarios</h1>
@stop

@section('content')
<div class="container-fluid mt-3">

    {{-- Mensajes de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> Hay errores en el formulario. Por favor, revísalos.
        </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Usuarios Registrados</h3>
            <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="usuarios-table" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-center" style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id_usuario }}</td>
                                <td>{{ $usuario->nombre }}</td>
                                <td>{{ $usuario->usuario }}</td>
                                <td>{{ $usuario->correo }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $usuario->rol->nombre ?? 'N/A' }}</span></td>
                                
                                {{-- Estado --}}
                                <td class="text-center">
                                    @if($usuario->estado)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">

                                        {{-- Editar --}}
                                        <button class="btn btn-info text-white"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditar{{ $usuario->id_usuario }}" title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        {{-- Cambiar estado --}}
                                        <form action="{{ route('usuarios.toggleEstado', $usuario->id_usuario) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="Cambiar Estado">
                                                <i class="fas fa-exchange-alt"></i> {{ $usuario->estado ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>

                                        {{-- Eliminar --}}
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
                                <td colspan="7" class="text-muted text-center">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modales --}}
@include('usuarios.partials.modal-crear', ['roles' => $roles])

@foreach ($usuarios as $usuario)
    @include('usuarios.partials.modal-editar', ['usuario' => $usuario, 'roles' => $roles])
    @include('usuarios.partials.modal-eliminar', ['usuario' => $usuario])
@endforeach

@stop

@section('js')
<script>
    // Función para cerrar modal manualmente si es necesario
    function cerrarModalManual(buttonElement) {
        let modalElement = buttonElement.closest('.modal');
        if (modalElement) {
            let modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.hide();
            setTimeout(() => {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }, 300);
        }
    }

    $(document).ready(function() {
        if ($('#usuarios-table tbody tr').length > 0) {
            $('#usuarios-table').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                language: { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
                "dom": '<"row"<"col-sm-12 d-flex justify-content-start"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });
        }
    });
</script>
@stop
