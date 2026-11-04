<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Nombres</th>
                <th>Ap Paterno</th>
                <th>Ap Materno</th>
                <th>Sucursal</th>
                <th>Rol</th>
                <th>Correo</th>
                <th>Celular</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->nombres }}</td>
                    <td>{{ $usuario->ap_paterno }}</td>
                    <td>{{ $usuario->ap_materno }}</td>
                    <td>{{ $usuario->sucursal?->nombre }}</td>
                    <td>
                        @if ($usuario->rol)
                            @if ($usuario->rol->nombre == "ADMINISTRADOR")
                                <span class="badge badge-success">{{ $usuario->rol->nombre }}</span>
                            @elseif($usuario->rol->nombre == "CAJA Y VENTAS")
                                <span class="badge badge-warning">{{ $usuario->rol->nombre }}</span>
                            @elseif($usuario->rol->nombre == "VENTAS")
                                <span class="badge badge-primary">{{ $usuario->rol->nombre }}</span>
                            @else
                                {{ $usuario->rol->nombre }}
                            @endif
                        @endif
                    </td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->celular }}</td>
                    <td>
                        {{-- @rol(1) --}}
                        <button class="btn btn-icon btn-sm btn-info btn-circle" title="Restablecer Contraseña"
                            onclick="abrirModalResetPassword({{ $usuario->id }})">
                            <i class="fa fa-key"></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-warning btn-circle" title="Editar usuario"
                            onclick="editarUsuario({{ json_encode($usuario) }})"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Eliminar usuario"
                            onclick="eliminarUsuario({{ json_encode($usuario) }})"><i class="fa fa-trash"></i></button>
                        {{-- @endrol --}}
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
    <!--end::Table-->
</div>

<script>
    $(document).ready(function() {
        $('#kt_table_users').DataTable({
            lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
            language: {
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros por página',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
            },
            order: [],
            //  searching: true,
            responsive: true
        });


    });
</script>
