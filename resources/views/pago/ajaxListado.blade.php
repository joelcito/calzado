<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-7 gy-4" id="kt_table_users">
        <thead>
            <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                <th width="10px">N° Caja</th>
                <th>Sucursal</th>
                <th>Nro Rec</th>
                <th>Fecha</th>
                <th>Descripcion</th>
                <th>Tipo Pago</th>
                <th width="10px">Monto Efectivo</th>
                <th width="10px">Monto Deposito</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @php
                $totalEfectivo      = 0;
                $totalTramsferencia = 0;
                $totalQR            = 0;
                $totalVenta         = 0;
                $totalOtrosIngresos = 0;
                $totalRecaudado     = 0;

                $totalSalida             = 0;
                $totalSalidaEfectivo     = 0;
                $totalSalidaQr           = 0;
                $totalSalidaTramsfrencia = 0;

            @endphp
            @forelse ($pagos as $pago)
                <tr class="{{ 'bg-light-warning' }}">
                    <td>{{ $pago->caja_id }}</td>
                    <td>{{ $pago?->sucursal?->nombre }}</td>
                    <td>{{ $pago->factura?->numero_recibo }}</td>
                    <td>{{ $pago->fecha }}</td>
                    <td>{{ $pago->descripcion }}</td>
                    <td>{{ $pago->tipo_pago }}</td>
                    <td>
                        @if ($pago->estado === 'INGRESO')
                            @if ($pago->tipo_pago === 'EFECTIVO')
                                {{ $pago->monto }}
                            @else
                                0.00
                            @endif
                        @elseif($pago->estado === 'SALIDA')
                            @if ($pago->tipo_pago === 'EFECTIVO')
                                {{ $pago->monto }}
                            @else
                                0.00
                            @endif
                        @endif
                    </td>
                    <td>
                        @if ($pago->estado === 'INGRESO')
                            @if ($pago->tipo_pago === 'TRANSFERENCIA' || $pago->tipo_pago === 'QR')
                                {{ $pago->monto }}
                            @else
                                0.00
                            @endif
                        @elseif($pago->estado === 'SALIDA')
                            @if ($pago->tipo_pago === 'TRANSFERENCIA' || $pago->tipo_pago === 'QR')
                                {{ $pago->monto }}
                            @else
                                0.00
                            @endif
                        @endif
                    </td>
                    <td>
                        @if ($pago->estado === 'INGRESO')
                            <span class="badge badge-success">{{ $pago->estado }}</span>
                        @else
                            <span class="badge badge-danger">{{ $pago->estado }}</span>
                        @endif
                    </td>
                    <td>{{ $pago->usuario->name }}</td>
                    <td>
                        <a target="_blank" href="{{url('pago/comprobantePago', [$pago->id])}}" class="btn btn-icon btn-info btn-sm" title="Imprimir Comprobante"><i class="fa fa-file"></i></a>
                    </td>
                </tr>
                @php
                    if($pago->estado === 'INGRESO'){
                        if ($pago->tipo_pago === 'EFECTIVO') {
                            $totalEfectivo = $totalEfectivo + $pago->monto;
                            if(is_null($pago->factura_id)){
                                $totalOtrosIngresos = $totalOtrosIngresos + $pago->monto;
                            }
                        }else if($pago->tipo_pago === 'QR'){
                            $totalQR = $totalQR + $pago->monto;
                            if(is_null($pago->factura_id)){
                                $totalOtrosIngresos = $totalOtrosIngresos + $pago->monto;
                            }
                        }else if($pago->tipo_pago === 'TRANSFERENCIA'){
                            $totalTramsferencia = $totalTramsferencia + $pago->monto;
                            if(is_null($pago->factura_id)){
                                $totalOtrosIngresos = $totalOtrosIngresos + $pago->monto;
                            }
                        }

                        if($pago->factura_id){
                            $totalVenta = $totalVenta + $pago->monto;
                        }

                        $totalRecaudado = $totalRecaudado + $pago->monto;
                    }elseif($pago->estado === 'SALIDA'){
                        if ($pago->tipo_pago === 'EFECTIVO') {
                            $totalSalidaEfectivo+=$pago->monto;
                        }else if($pago->tipo_pago === 'QR'){
                            $totalSalidaQr+=$pago->monto;
                        }else if($pago->tipo_pago === 'TRANSFERENCIA'){
                            $totalSalidaTramsfrencia+=$pago->monto;
                        }
                        $totalSalida = $totalSalida + $pago->monto;
                    }

                @endphp
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-light-dark">
                <th class="text-center" colspan="4"><b>TOTAL RECAUDADO</b></th>
                <th class="text-center" colspan="2">{{ number_format(($totalRecaudado - $totalSalida), 2) }}</th>
                <th class="text-center" colspan="3"><b>TOTAL VENTA</b></th>
                <th class="text-center" colspan="2">{{ number_format(($totalVenta), 2) }}</th>
            </tr>
            <tr>
                <th class="bg-light-success" colspan="2"><b>TOTAL INGRESO EFECTIVO</b></th>
                <th class="bg-light-success">{{ number_format(($totalEfectivo - $totalSalidaEfectivo), 2) }}</th>
                <th class="bg-light-success"><b>TOTAL INGRESO QR</b></th>
                <th class="bg-light-success">{{ number_format(($totalQR - $totalSalidaQr), 2) }}</th>
                <th class="bg-light-success"><b>TOTAL INGRESO TRANSFERENCIA</b></th>
                <th class="bg-light-success">{{ number_format(($totalTramsferencia - $totalSalidaTramsfrencia), 2) }}</th>

                <th class="bg-light-primary"><b>TOTAL OTROS INGRESOS</b></th>
                <th class="bg-light-primary">{{ number_format($totalOtrosIngresos, 2) }}</th>
                <th class="bg-light-primary"><b>TOTAL OTROS SALIDAS</b></th>
                <th class="bg-light-primary">{{ number_format($totalSalida, 2) }}</th>
            </tr>
        </tfoot>
    </table>
    <!--end::Table-->
</div>

<script>
    $(document).ready(function() {
        $('#kt_table_users').DataTable({
            lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            // dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
            dom: '<"dt-head row"><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
            lengthChange: false,
            searching: false,
            language: {
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
            },
            order: [],
            responsive: true,
        });
    });
</script>
