<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_facturas">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Sucursal</th>
                <th>Fecha Venta</th>
                <th>Usu Venta</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>A Cuenta</th>
                <th>Saldo</th>
                <th>Razon Social</th>
                <th>Nit</th>
                <th>N° Factura/Recibo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($facturas as $factura)
                <tr>
                    <td>{{ optional($factura->sucursal)->nombre }}</td>
                    <td>{{ date('d/m/Y H:i:s', strtotime($factura->fecha)) }}</td>
                    <td>{{ $factura->usuarioCreador->nombres . ' ' . $factura->usuarioCreador->ap_paterno }}</td>
                    <td>{{ optional($factura->cliente)->nombres . ' ' . optional($factura->cliente)->ap_paterno . ' ' . optional($factura->cliente)->ap_materno }}
                    </td>
                    <td>{{ number_format($factura->total, 2) }}</td>
                    <td>{{ number_format($factura->pagos->sum('monto'), 2) }}</td>
                    <td>{{ number_format($factura->total - $factura->pagos->sum('monto'), 2) }}</td>
                    <td>{{ optional($factura->cliente)->razon_social }}</td>
                    <td>{{ optional($factura->cliente)->nit }}</td>
                    <td>
                        @if ($factura->facturado == 'Si')
                            <span class="text-success">FAC: </span>{{ $factura->numero_factura }}
                        @else
                            <span class="text-primary">REC: </span>{{ $factura->numero_recibo }}
                        @endif
                        {{-- {{ $factura->numero_factura ?? $factura->numero_recibo }} --}}
                    </td>
                    <td>
                        <button class="btn btn-icon btn-sm btn-info btn-circle" title="Registrar Pago"
                            onclick="registrarPago({{ json_encode($factura) }})"><i class="fa fa-dollar"></i></button>
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
        $('#kt_table_facturas').DataTable({
            lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            dom: 't<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
            language: {
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                search: '',
                lengthMenu: '',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
            },
            searching: false,   // 🔥 desactiva cuadro de búsqueda
            lengthChange: false, // 🔥 oculta "Mostrar _MENU_"
            order: [],
            responsive: true
        });


    });
</script>
