<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_mejores_vendedores">
        <thead>
            <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>#</th>
                <th>Vendedor</th>
                <th>Total Monto</th>
                <th>Total Ventas</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $mejoresVendedores as $nro=>$ve)
                <tr class="text-center">
                    <td>{{ $nro+1 }}</td>
                    <td>{{ optional($ve->vendedor)->name }}</td>
                    <td>{{ number_format($ve->total_monto, 2) }}</td>
                    <td>{{ $ve->total_facturas }}</td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#kt_table_mejores_vendedores').DataTable({
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
            responsive: true
        });

    });
</script>