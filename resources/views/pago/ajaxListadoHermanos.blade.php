<h4 class="mb-4">Resumen por hermano (Bs)</h4>
<div class="table-responsive mb-7">
    <table class="table align-middle table-row-dashed fs-7 gy-4">
        <thead><tr class="text-muted fw-bold text-uppercase">
            <th>Hermano</th><th class="text-end">Vendido</th><th class="text-end">Pendiente de cobro</th>
            <th class="text-end">Saldo anterior</th><th class="text-end">Cobrado</th><th class="text-end">Otros ingresos / devoluciones</th><th class="text-end">Salidas</th><th class="text-end">Disponible final</th>
            <th class="text-end">Efectivo disponible</th><th class="text-end">QR disponible</th><th class="text-end">Transferencia disponible</th>
        </tr></thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($resumen as $fila)
                <tr>
                    <td>{{ $fila['hermano'] }}</td>
                    @foreach (['vendido', 'pendiente', 'anterior', 'cobrado', 'ingresos', 'salidas', 'disponible'] as $campo)
                        <td class="text-end {{ $campo === 'disponible' ? 'fw-bold bg-light-success' : '' }}">{{ number_format($fila[$campo] / 100, 2) }}</td>
                    @endforeach
                    @foreach (['EFECTIVO', 'QR', 'TRANSFERENCIA'] as $medio)
                        <td class="text-end">{{ number_format($fila['medios'][$medio] / 100, 2) }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="11" class="text-center">No hay datos.</td></tr>
            @endforelse
        </tbody>
        <tfoot><tr class="fw-bold bg-light-info"><td>TOTAL</td>
            @foreach (['vendido', 'pendiente', 'anterior', 'cobrado', 'ingresos', 'salidas', 'disponible'] as $campo)
                <td class="text-end">{{ number_format($resumen->sum($campo) / 100, 2) }}</td>
            @endforeach
            @foreach (['EFECTIVO', 'QR', 'TRANSFERENCIA'] as $medio)
                <td class="text-end">{{ number_format($resumen->sum(fn ($fila) => $fila['medios'][$medio]) / 100, 2) }}</td>
            @endforeach
        </tr></tfoot>
    </table>
</div>
<h4 class="mb-4">Movimientos del período (Bs)</h4>
<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-7 gy-4" id="tabla_movimientos_hermanos">
        <thead><tr class="text-muted fw-bold text-uppercase"><th>Fecha</th><th>Hermano</th><th>Recibo</th><th>Descripción</th><th>Movimiento</th><th>Tipo pago</th><th class="text-end">Ingreso</th><th class="text-end">Salida</th><th>Registrado por</th><th>Acción</th></tr></thead>
        <tbody class="text-gray-600 fw-semibold">
            @foreach ($movimientos as $fila)
                <tr>
                    <td>{{ $fila['fecha'] }}</td><td>{{ $fila['hermano'] }}</td><td>{{ $fila['recibo'] }}</td><td>{{ $fila['descripcion'] }}</td>
                    <td><span class="badge {{ $fila['salida'] > 0 ? 'badge-danger' : 'badge-success' }}">{{ $fila['tipo'] }}</span></td><td>{{ $fila['tipo_pago'] }}</td>
                    <td class="text-end">{{ number_format($fila['ingreso'] / 100, 2) }}</td><td class="text-end">{{ number_format($fila['salida'] / 100, 2) }}</td><td>{{ $fila['usuario'] }}</td>
                    <td><a href="{{ route('pago.comprobantePago', $fila['pago_id']) }}" target="_blank" rel="noopener" class="btn btn-icon btn-info btn-sm" title="Comprobante del movimiento completo"><i class="fa fa-file"></i></a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $('#tabla_movimientos_hermanos').DataTable({
        pageLength: 25, order: [], searching: false,
        language: {
            emptyTable: 'No hay movimientos en este período.',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Sin registros', lengthMenu: 'Mostrar _MENU_ registros',
            paginate: { next: 'Siguiente', previous: 'Anterior' }
        }
    });
</script>
