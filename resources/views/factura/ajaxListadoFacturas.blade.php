<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-8 gy-2" id="kt_table_facturas">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Sucursal</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Numero</th>
                <th>Usuario Registro</th>
                <th>Usuario Vendedor</th>
                <th>Estado</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $facturas as $fac)
                <tr>
                    <td>{{ $fac->sucursal->nombre }}</td>
                    <td>{{ $fac->nombres . ' ' . $fac->ap_paterno . ' ' . $fac->ap_materno }}</td>
                    <td>{{ $fac->fecha }}</td>
                    <td>{{ $fac->total }}</td>
                    <td>
                        @if ($fac->uso_cafc == 'Si')
                            <span class="text-primary">N° Cafc: </span>{{ $fac->numero_cafc }}
                        @else
                            @if ($fac->facturado == 'Si')
                                <span class="text-success">FAC: </span>{{ $fac->numero_factura }}
                            @else
                                <span class="text-primary">REC: </span>{{ $fac->numero_recibo }}
                            @endif
                        @endif
                    </td>
                    <td>
                        {{ $fac->usuarioCreador->nombres . ' ' . $fac->usuarioCreador->ap_paterno." ".$fac->usuarioCreador->ap_materno }}
                    </td>
                    <td>
                        {{ $fac->vendedor?->nombres . ' ' . $fac->vendedor?->ap_paterno." ".$fac->vendedor?->ap_materno }}
                    </td>
                    <td>
                        @if (is_null($fac->estado))
                            <span class="badge badge-success">VIGENTE</span>
                        @elseif($fac->estado == 'Anulado')
                            <span class="badge badge-danger">ANULADO</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('factura/imprimeRecibo', [$fac->id]) }}" target="_blank"
                            class="btn btn-info btn-sm btn-icon tamanio_boton" title="Imprime Recibo"><i
                                class="fa fa-file-pdf"></i></a>
                        <a href="{{ url('factura/imprimeReciboRollo', [$fac->id]) }}" target="_blank"
                            class="btn btn-primary btn-sm btn-icon tamanio_boton" title="Imprime Rollo"><i
                                class="fa fa-file-pdf"></i></a>
                        @rol(1)
                        <button class="btn btn-danger btn-sm btn-icon tamanio_boton"
                            onclick="anularRecibo('{{ $fac->numero_recibo }}', '{{ $fac->id }}')"><i class="fa fa-trash"></i></button>
                        @endrol
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#kt_table_facturas').DataTable({
            lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            // dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
            dom: 't<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
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
