<div style="overflow-x: auto;">
    <!--begin::Table-->
    {{-- <table class="table align-middle table-row-dashed fs-9 gy-5" id="kt_table_users"> --}}
        <table class="table table-row-dashed fs-9 gy-1" id="kt_table_users">
            <thead>
                <tr class="text-start text-muted fw-bold fs-9 text-uppercase gs-0">
                    <th>Sucursal</th>
                    <th class="bg-light-dark">Fecha Apertura</th>
                    <th class="bg-light-dark">Usuario Apertura</th>
                    <th class="bg-light-dark">Monto Apertura</th>
                    <th class="bg-light-dark">Descripcion Apertura</th>

                    <th class="bg-light-warning">Fecha Cierre</th>
                    <th class="bg-light-warning">Usuario Cierre</th>
                    <th class="bg-light-warning">Monto Cierre</th>
                    <th class="bg-light-warning">Descripcion Cierre</th>

                    <th class="bg-light-info">Total Recaudado</th>
                    <th class="bg-light-info">Total Efectivo</th>
                    <th class="bg-light-info">Total QR</th>
                    <th class="bg-light-info">Total Transferencia</th>
                    <th class="bg-light-info">Total Otros Ingresos</th>
                    <th class="bg-light-info">Total Salida</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @forelse ( $cajas as $caja)
                <tr>
                    <td>{{ $caja->sucursal?->nombre }}</td>
                    <td class="bg-light-dark">{{ $caja->fecha_apertura }}</td>
                    <td class="bg-light-dark">{{ $caja->usuario->name }}</td>
                    <td class="bg-light-dark">{{ $caja->monto_apertura }}</td>
                    <td class="bg-light-dark">{{ $caja->descripcion }}</td>

                    <td class="bg-light-warning">{{ $caja->fecha_cierre }}</td>
                    <td class="bg-light-warning">{{ $caja->usuarioCierre?->name }}</td>
                    <td class="bg-light-warning">{{ $caja->monto_cierre }}</td>
                    <td class="bg-light-warning">{{ $caja->descripcion_cierre }}</td>

                    <td class="bg-light-info">{{ $caja->total_ingreso }}</td>
                    <td class="bg-light-info">{{ $caja->venta_contado }}</td>
                    <td class="bg-light-info">{{ $caja->total_qr }}</td>
                    <td class="bg-light-info">{{ $caja->total_transferencia }}</td>
                    <td class="bg-light-info">{{ $caja->otro_ingreso }}</td>
                    <td class="bg-light-info">{{ $caja->total_salida }}</td>
                    <td>
                        @if ($caja->saldo == 0)
                        <span class="badge badge-success">{{ $caja->saldo }}</span>
                        @elseif($caja->saldo < 0) <span class="badge badge-danger">{{ $caja->saldo }}</span>
                            @elseif($caja->saldo > 0)
                            <span class="badge badge-warning">{{ $caja->saldo }}</span>
                            @endif
                    </td>
                    <td>
                        @if ($caja->estado == 'Cerrado')
                        <span class="badge badge-danger">{{ $caja->estado }}</span>
                        @else
                        <span class="badge badge-success">{{ $caja->estado }}</span>
                        @endif
                    </td>
                    <td class="d-flex gap-1 align-items-center">
                        <button class="btn btn-icon btn-sm btn-info btn-circle tamanio_boton" title="Estado de caja"
                            onclick="verCaja({{ $caja->id }})"><i class="fa fa-eye"></i></button>
                        @rol(1)
                        <button class="btn btn-icon btn-sm btn-warning btn-circle tamanio_boton" title="Editar caja"
                            onclick="editarCaja({{ json_encode($caja) }})"><i class="fa fa-edit"></i></button>
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
