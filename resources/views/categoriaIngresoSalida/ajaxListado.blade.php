<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $categorias as $rol)
                <tr>
                    <td>{{ $rol->nombre }}</td>
                    <td>
                        @if ($rol->tipo == "INGRESO")
                            <span class="badge badge-success">{{ $rol->tipo }}</span>
                        @else
                            <span class="badge badge-danger">{{ $rol->tipo }}</span>
                        @endif
                    </td>
                    <td>
                        @rol(1)
                        <button class="btn btn-icon btn-sm btn-warning btn-circle" title="Editar rol" onclick="editarRol({{ json_encode($rol) }})"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Eliminar rol" onclick="eliminarRol({{ json_encode($rol) }})"><i class="fa fa-trash"></i></button>
                        @endrol
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
                    first : 'Primero',
                    last : 'Último',
                    next : 'Siguiente',
                    previous: 'Anterior'
                },
                search : 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros por página',
                info : 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
                },
                order:[],
                //  searching: true,
                responsive: true
            });


        });
</script>
