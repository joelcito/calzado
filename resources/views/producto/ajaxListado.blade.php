<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-7 gy-5" id="kt_table_productos">
        <thead>
            <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                <th>Imagen</th>
                <th>Codigo</th>
                <th>Tipo</th>
                <th>Nombre</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Minimo Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $productos as $prod)
                <tr>
                    <td class="text-center align-middle">
                        @if($prod->imagen && Storage::disk('public')->exists($prod->imagen))
                        <img src="{{ asset('storage/' . $prod->imagen) }}" alt="{{ $prod->nombre }}" class="rounded"
                            style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                        <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" alt="Sin imagen" class="rounded"
                            style="width: 50px; height: 50px; object-fit: cover;">
                        @endif
                    </td>
                    <td>{{ $prod->codigo }}</td>
                    <td>
                        @if ($prod->tipo == 'PETSHOP')
                            <small class="badge badge-success">{{ $prod->tipo }}</small>
                        @elseif($prod->tipo == 'PROPIO')
                            <small class="badge badge-info">{{ $prod->tipo }}</small>
                        @endif
                    </td>
                    <td>{{ $prod->nombre }}</td>
                    <td>{{ $prod->precio_compra }}</td>
                    <td>{{ $prod->precio_venta }}</td>
                    <td>{{ $prod->minimo_stock }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown"
                                data-bs-display="static" aria-expanded="false">
                                Opciones
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                <li><button class="dropdown-item" type="button" onclick="adicionarStockSucursal({{ json_encode($prod) }})"><i class="fa fa-calendar-plus"></i> Stock-Sucursal</button></li>
                                @rol(1)
                                {{-- <li><button class="dropdown-item" type="button" onclick="transferenciaSucursal({{ json_encode($prod) }})"><i class="fa fa-arrow-right"></i> Transferencia</button></li> --}}
                                <li><button class="dropdown-item" type="button" onclick="editarProducto({{ json_encode($prod) }})"><i class="fa fa-edit"></i> Editar</button></li>
                                <li><button class="dropdown-item" type="button" onclick="eliminarProducto({{ json_encode($prod) }})"><i class="fa fa-trash"></i> Eliminar</button></li>
                                @endrol
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
        //$.fn.dataTable.ext.errMode = 'throw';
        $.fn.dataTable.ext.errMode = 'none';

        $('#kt_table_productos').DataTable({
            deferRender: true,
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
            searching: false,
            responsive: false
        });


    });
</script>
