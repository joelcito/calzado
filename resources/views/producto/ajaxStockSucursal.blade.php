<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_tabla_stock">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Nombre</th>
                <th>Stock</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $sucursales as $sucursal)
                <tr>
                    <td>{{ $sucursal->nombre }}</td>
                    <td>{{ (float)$sucursal->movimientos_sum_ingreso - (float)$sucursal->movimientos_sum_salida }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown"
                                data-bs-display="static" aria-expanded="false">
                                Opciones
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                {{-- @rol(1,5) --}}
                                <li><button class="dropdown-item" type="button" onclick="adicionarStockSucursalProducto({{ json_encode($sucursal) }}, {{ $producto }})"><i class="fa fa-calendar-plus"></i> Adicionar Stock</button></li>
                                {{-- @endrol --}}
                                <li><button class="dropdown-item" type="button" onclick="adicionarSalidaSucursalProducto({{ json_encode($sucursal) }}, {{ $producto }})"><i class="fa fa-calendar-minus"></i> Salida de Stock</button></li>
                            </ul>
                        </div>
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
            $('#kt_tabla_stock').DataTable({
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
