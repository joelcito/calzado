<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Sucursal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController extends Controller
{
    // ============================================================
    // LISTADO
    // ============================================================

    public function listado()
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        $productos = Producto::where('control_stock', 1)->orderBy('nombre')->get();

        return view('reporte.listado', compact('sucursales', 'productos'));
    }


    // ============================================================
    // FILTRO FECHAS
    // ============================================================

    private function fechas(Request $request)
    {
        $fechaInicio = $request->fecha_inicio
            ? $request->fecha_inicio . ' 00:00:00'
            : now()->startOfMonth()->format('Y-m-d H:i:s');

        $fechaFin = $request->fecha_fin
            ? $request->fecha_fin . ' 23:59:59'
            : now()->endOfMonth()->format('Y-m-d H:i:s');

        return [
            $fechaInicio,
            $fechaFin
        ];
    }


    // ============================================================
    // ESTILO GENERAL EXCEL
    // ============================================================

    private function estiloExcel($hoja, $ultimaColumna, $ultimaFila)
    {
        $hoja->getStyle(
            "A1:{$ultimaColumna}1"
        )->getFont()->setBold(true)->setSize(14);


        $hoja->getStyle(
            "A3:{$ultimaColumna}3"
        )->getFont()->setBold(true);


        $hoja->getStyle(
            "A3:{$ultimaColumna}{$ultimaFila}"
        )->getBorders()->getAllBorders()->setBorderStyle(
            Border::BORDER_THIN
        );


        $hoja->getStyle(
            "A3:{$ultimaColumna}{$ultimaFila}"
        )->getAlignment()->setVertical(
            Alignment::VERTICAL_CENTER
        );


        foreach (
            range('A', $ultimaColumna)
            as $columna
        ) {

            $hoja
                ->getColumnDimension($columna)
                ->setAutoSize(true);
        }
    }


    private function descargarExcel(
        Spreadsheet $spreadsheet,
        string $nombre
    ) {

        $writer = new Xlsx($spreadsheet);

        $archivo = storage_path(
            'app/' . $nombre
        );

        $writer->save($archivo);

        return response()
            ->download($archivo)
            ->deleteFileAfterSend(true);
    }


    // ============================================================
    // ============================================================
    // VENTAS
    // ============================================================
    // ============================================================

    private function consultaVentas(Request $request)
    {
        [$inicio, $fin] = $this->fechas($request);


        $query = DB::table('facturas as f')

            ->leftJoin(
                'clientes as c',
                'c.id',
                '=',
                'f.cliente_id'
            )

            ->leftJoin(
                'sucursales as s',
                's.id',
                '=',
                'f.sucursal_id'
            )

            ->whereNull('f.deleted_at')

            ->whereBetween(
                'f.fecha',
                [$inicio, $fin]
            );


        if ($request->filled('sucursal_id')) {

            $query->where(
                'f.sucursal_id',
                $request->sucursal_id
            );
        }


        return $query
            ->select(
                'f.id',
                'f.numero_factura',
                'f.numero_recibo',
                'f.fecha',
                'f.razon_social',
                'f.nit',
                'f.total',
                'f.estado_pago',
                'f.estado',

                'c.nombres',
                'c.ap_paterno',
                'c.ap_materno',

                's.nombre as sucursal'
            )

            ->orderBy('f.fecha')
            ->get();
    }


    public function ventasPdf(Request $request)
    {
        $datos = $this->consultaVentas($request);

        $total = $datos
            ->where('estado', '!=', 'Anulado')
            ->sum('total');


        return Pdf::loadView(
            'reporte.pdf.ventas',
            compact(
                'datos',
                'total',
                'request'
            )
        )
            ->setPaper('letter', 'landscape')
            ->stream('reporte_ventas.pdf');
    }


    public function ventasExcel(Request $request)
    {
        $datos = $this->consultaVentas($request);


        $excel = new Spreadsheet();

        $hoja = $excel->getActiveSheet();

        $hoja->setTitle('Ventas');


        $hoja->mergeCells('A1:I1');

        $hoja->setCellValue(
            'A1',
            'REPORTE DE VENTAS'
        );


        $hoja->fromArray([

            'N°',
            'Fecha',
            'Factura',
            'Cliente',
            'NIT/CI',
            'Sucursal',
            'Estado Pago',
            'Estado',
            'Total Bs'

        ], null, 'A3');


        $fila = 4;

        $total = 0;


        foreach ($datos as $index => $dato) {

            $cliente =
                trim(
                    ($dato->nombres ?? '') . ' ' .
                        ($dato->ap_paterno ?? '') . ' ' .
                        ($dato->ap_materno ?? '')
                );


            $hoja->fromArray([

                $index + 1,

                $dato->fecha,

                $dato->numero_factura
                    ?? $dato->numero_recibo,

                $cliente
                    ?: $dato->razon_social,

                $dato->nit,

                $dato->sucursal,

                $dato->estado_pago,

                $dato->estado,

                $dato->total

            ], null, 'A' . $fila);


            if ($dato->estado != 'ANULADO') {

                $total += $dato->total;
            }


            $fila++;
        }


        $hoja->setCellValue(
            'H' . $fila,
            'TOTAL'
        );

        $hoja->setCellValue(
            'I' . $fila,
            $total
        );


        $this->estiloExcel(
            $hoja,
            'I',
            $fila
        );


        return $this->descargarExcel(
            $excel,
            'reporte_ventas.xlsx'
        );
    }


    // ============================================================
    // ============================================================
    // CUENTAS POR COBRAR
    // ============================================================
    // ============================================================

    private function consultaCuentasCobrar(Request $request)
    {
        $query = DB::table('facturas as f')
            ->leftJoin('clientes as c', 'c.id', '=', 'f.cliente_id')
            ->leftJoin('sucursales as s', 's.id', '=', 'f.sucursal_id')
            ->leftJoinSub(
                DB::table('pagos')
                    ->whereNull('deleted_at')
                    ->select(
                        'factura_id',
                        DB::raw(
                            'SUM(monto) as pagado'
                        )
                    )
                    ->groupBy('factura_id'),
                'p',
                function ($join) {
                    $join->on('p.factura_id', '=', 'f.id');
                }
            )
            ->whereNull('f.deleted_at')
            ->whereNull('f.estado')
            ->whereRaw('f.total > COALESCE(p.pagado, 0)');

        if ($request->filled('sucursal_id')) {

            $query->where(
                'f.sucursal_id',
                $request->sucursal_id
            );
        }


        return $query

            ->select(

                'f.id',

                'f.fecha',

                'f.numero_factura',

                'f.numero_recibo',

                'f.total',

                'f.estado_pago',

                'c.nombres',

                'c.ap_paterno',

                'c.ap_materno',

                'c.numero_celular',

                's.nombre as sucursal',

                DB::raw(
                    'COALESCE(p.pagado,0) as pagado'
                ),

                DB::raw(
                    '(f.total - COALESCE(p.pagado,0)) as saldo'
                )
            )

            ->orderBy('f.fecha')
            ->get();
    }


    public function cuentasCobrarPdf(Request $request)
    {
        $datos =
            $this->consultaCuentasCobrar($request);


        $total =
            $datos->sum('total');

        $pagado =
            $datos->sum('pagado');

        $saldo =
            $datos->sum('saldo');


        return Pdf::loadView(
            'reporte.pdf.cuentas-cobrar',
            compact(
                'datos',
                'total',
                'pagado',
                'saldo'
            )
        )
            ->setPaper('letter', 'landscape')
            ->stream(
                'cuentas_por_cobrar.pdf'
            );
    }


    public function cuentasCobrarExcel(Request $request)
    {
        $datos =
            $this->consultaCuentasCobrar($request);


        $excel =
            new Spreadsheet();

        $hoja =
            $excel->getActiveSheet();


        $hoja->mergeCells('A1:I1');

        $hoja->setCellValue(
            'A1',
            'CUENTAS POR COBRAR'
        );


        $hoja->fromArray([

            'N°',
            'Fecha',
            'Documento',
            'Cliente',
            'Celular',
            'Sucursal',
            'Total',
            'Pagado',
            'Saldo'

        ], null, 'A3');


        $fila = 4;


        foreach ($datos as $i => $dato) {

            $cliente = trim(

                ($dato->nombres ?? '') . ' ' .
                    ($dato->ap_paterno ?? '') . ' ' .
                    ($dato->ap_materno ?? '')
            );


            $hoja->fromArray([

                $i + 1,

                $dato->fecha,

                $dato->numero_factura
                    ?? $dato->numero_recibo,

                $cliente,

                $dato->numero_celular,

                $dato->sucursal,

                $dato->total,

                $dato->pagado,

                $dato->saldo

            ], null, 'A' . $fila);


            $fila++;
        }


        $hoja->setCellValue(
            'F' . $fila,
            'TOTALES'
        );

        $hoja->setCellValue(
            'G' . $fila,
            $datos->sum('total')
        );

        $hoja->setCellValue(
            'H' . $fila,
            $datos->sum('pagado')
        );

        $hoja->setCellValue(
            'I' . $fila,
            $datos->sum('saldo')
        );


        $this->estiloExcel(
            $hoja,
            'I',
            $fila
        );


        return $this->descargarExcel(
            $excel,
            'cuentas_por_cobrar.xlsx'
        );
    }


    // ============================================================
    // ============================================================
    // INVENTARIO
    // ============================================================
    // ============================================================

    private function consultaInventario(Request $request)
    {
        $query = DB::table('productos as p')

            ->leftJoin(
                'movimientos as m',
                function ($join) use ($request) {

                    $join->on(
                        'm.producto_id',
                        '=',
                        'p.id'
                    );

                    $join->whereNull(
                        'm.deleted_at'
                    );


                    if (
                        $request->filled(
                            'sucursal_id'
                        )
                    ) {

                        $join->where(
                            'm.sucursal_id',
                            '=',
                            $request->sucursal_id
                        );
                    }
                }
            )

            ->whereNull('p.deleted_at')

            ->where(
                'p.control_stock',
                1
            );


        return $query

            ->select(

                'p.id',

                'p.codigo',

                'p.nombre',

                'p.precio_compra',

                'p.precio_venta',

                'p.minimo_stock',

                DB::raw(
                    'COALESCE(SUM(m.ingreso),0) as ingresos'
                ),

                DB::raw(
                    'COALESCE(SUM(m.salida),0) as salidas'
                ),

                DB::raw(
                    '(COALESCE(SUM(m.ingreso),0) - COALESCE(SUM(m.salida),0)) as stock'
                )

            )

            ->groupBy(
                'p.id',
                'p.codigo',
                'p.nombre',
                'p.precio_compra',
                'p.precio_venta',
                'p.minimo_stock'
            )

            ->orderBy('p.nombre')

            ->get();
    }


    public function inventarioPdf(Request $request)
    {
        $datos =
            $this->consultaInventario($request);


        return Pdf::loadView(
            'reporte.pdf.inventario',
            compact('datos')
        )
            ->setPaper('letter', 'landscape')
            ->stream('inventario.pdf');
    }


    public function inventarioExcel(Request $request)
    {
        $datos =
            $this->consultaInventario($request);


        $excel =
            new Spreadsheet();

        $hoja =
            $excel->getActiveSheet();


        $hoja->mergeCells('A1:H1');

        $hoja->setCellValue(
            'A1',
            'REPORTE DE INVENTARIO'
        );


        $hoja->fromArray([

            'N°',
            'Código',
            'Producto',
            'P. Compra',
            'P. Venta',
            'Stock Mínimo',
            'Stock Actual',
            'Estado'

        ], null, 'A3');


        $fila = 4;


        foreach ($datos as $i => $dato) {

            $estado =
                $dato->stock <= $dato->minimo_stock
                ? 'STOCK BAJO'
                : 'DISPONIBLE';


            $hoja->fromArray([

                $i + 1,

                $dato->codigo,

                $dato->nombre,

                $dato->precio_compra,

                $dato->precio_venta,

                $dato->minimo_stock,

                $dato->stock,

                $estado

            ], null, 'A' . $fila);


            $fila++;
        }


        $this->estiloExcel(
            $hoja,
            'H',
            $fila - 1
        );


        return $this->descargarExcel(
            $excel,
            'inventario.xlsx'
        );
    }


    // ============================================================
    // ============================================================
    // KARDEX
    // ============================================================
    // ============================================================

    private function consultaKardex(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'sucursal_id' => 'nullable|integer|exists:sucursales,id',
        ]);

        $fechaInicio = $request->fecha_inicio . ' 00:00:00';
        $fechaFin = $request->fecha_fin . ' 23:59:59';


        // ============================================================
        // PRODUCTO
        // ============================================================

        $producto = DB::table('productos')
            ->where('id', $request->producto_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$producto) {
            abort(404, 'Producto no encontrado.');
        }


        // ============================================================
        // SALDO ANTERIOR
        // Todo lo que entró - todo lo que salió ANTES de fecha_inicio
        // ============================================================

        $querySaldoAnterior = DB::table('movimientos')
            ->where('producto_id', $request->producto_id)
            ->whereNull('deleted_at')
            ->where('fecha', '<', $fechaInicio);


        if ($request->filled('sucursal_id')) {

            $querySaldoAnterior->where(
                'sucursal_id',
                $request->sucursal_id
            );
        }


        $saldoAnterior = $querySaldoAnterior
            ->selectRaw('
            COALESCE(SUM(ingreso), 0)
            -
            COALESCE(SUM(salida), 0)
            AS saldo
        ')
            ->value('saldo');


        // ============================================================
        // MOVIMIENTOS DEL PRODUCTO EN EL RANGO SELECCIONADO
        // ============================================================

        $query = DB::table('movimientos as m')

            ->leftJoin(
                'detalles as d',
                'd.id',
                '=',
                'm.detalle_id'
            )

            ->leftJoin(
                'facturas as f',
                'f.id',
                '=',
                'd.factura_id'
            )

            ->leftJoin(
                'sucursales as s',
                's.id',
                '=',
                'm.sucursal_id'
            )

            ->where(
                'm.producto_id',
                $request->producto_id
            )

            ->whereNull('m.deleted_at')

            ->whereBetween(
                'm.fecha',
                [
                    $fechaInicio,
                    $fechaFin
                ]
            );


        // ============================================================
        // FILTRO SUCURSAL
        // ============================================================

        if ($request->filled('sucursal_id')) {

            $query->where(
                'm.sucursal_id',
                $request->sucursal_id
            );
        }


        // ============================================================
        // OBTENER MOVIMIENTOS
        // ============================================================

        $movimientos = $query
            ->select(
                'm.id',
                'm.fecha',
                'm.ingreso',
                'm.salida',
                'm.precio_compra',
                'm.precio_venta',
                'm.descripcion',
                'm.detalle_id',

                'd.factura_id',

                'f.numero_factura',
                'f.numero_recibo',

                's.nombre as sucursal'
            )

            ->orderBy('m.fecha', 'asc')
            ->orderBy('m.id', 'asc')

            ->get();


        // ============================================================
        // IMPORTANTE:
        // DEVOLVEMOS UN ARRAY, NO SOLO $movimientos
        // ============================================================

        return [
            'producto' => $producto,
            'saldoAnterior' => (float) ($saldoAnterior ?? 0),
            'movimientos' => $movimientos,
        ];
    }


    public function kardexPdf(Request $request)
    {
        $resultado = $this->consultaKardex($request);

        $producto = $resultado['producto'];

        $saldoAnterior = $resultado['saldoAnterior'];

        $datos = $resultado['movimientos'];


        return Pdf::loadView(
            'reporte.pdf.kardex',
            compact(
                'producto',
                'saldoAnterior',
                'datos',
                'request'
            )
        )
            ->setPaper('letter', 'landscape')
            ->stream(
                'kardex_' . ($producto->codigo ?? $producto->id) . '.pdf'
            );
    }


    public function kardexExcel(Request $request)
    {
        $datos =
            $this->consultaKardex($request);


        $excel =
            new Spreadsheet();

        $hoja =
            $excel->getActiveSheet();


        $hoja->mergeCells('A1:I1');

        $hoja->setCellValue(
            'A1',
            'REPORTE KARDEX'
        );


        $hoja->fromArray([

            'N°',
            'Fecha',
            'Código',
            'Producto',
            'Sucursal',
            'Descripción',
            'Ingreso',
            'Salida',
            'Saldo'

        ], null, 'A3');


        $fila = 4;

        $saldo = 0;


        foreach ($datos as $i => $dato) {

            $saldo +=
                ($dato->ingreso ?? 0)
                -
                ($dato->salida ?? 0);


            $hoja->fromArray([

                $i + 1,

                $dato->fecha,

                $dato->codigo,

                $dato->producto,

                $dato->sucursal,

                $dato->descripcion,

                $dato->ingreso,

                $dato->salida,

                $saldo

            ], null, 'A' . $fila);


            $fila++;
        }


        $this->estiloExcel(
            $hoja,
            'I',
            $fila - 1
        );


        return $this->descargarExcel(
            $excel,
            'kardex.xlsx'
        );
    }


    // ============================================================
    // ============================================================
    // FLUJO EFECTIVO
    // ============================================================
    // ============================================================

    private function consultaFlujo(Request $request)
    {
        [$inicio, $fin] = $this->fechas($request);

        $query = DB::table('pagos as p')
            ->leftJoin(
                'categoria_ingreso_salidas as c',
                'c.id',
                '=',
                'p.categoria_ingreso_salida_id'
            )
            ->leftJoin(
                'facturas as f',
                'f.id',
                '=',
                'p.factura_id'
            )
            ->leftJoin(
                'sucursales as s',
                's.id',
                '=',
                'p.sucursal_id'
            )
            ->whereNull('p.deleted_at')
            ->whereBetween(
                'p.fecha',
                [$inicio, $fin]
            );

        if ($request->filled('sucursal_id')) {
            $query->where(
                'p.sucursal_id',
                $request->sucursal_id
            );
        }

        return $query
            ->select(
                'p.id',
                'p.fecha',
                'p.factura_id',
                'p.categoria_ingreso_salida_id',
                'p.monto',
                'p.cambio',
                'p.descripcion',
                'p.apertura_caja',
                'p.tipo_pago',

                // ESTE ES TU CAMPO REAL
                'p.estado as tipo_movimiento',

                'c.nombre as categoria',

                'f.numero_factura',
                'f.numero_recibo',

                's.nombre as sucursal'
            )
            ->orderBy('p.fecha', 'asc')
            ->orderBy('p.id', 'asc')
            ->get();
    }


    public function flujoEfectivoPdf(Request $request)
    {
        $datos = $this->consultaFlujo($request);

        // APERTURA
        $totalApertura = $datos
            ->filter(function ($item) {
                return strtoupper(
                    trim($item->apertura_caja ?? '')
                ) === 'SI';
            })
            ->sum('monto');


        // INGRESOS, SIN CONTAR APERTURA
        $totalIngresos = $datos
            ->filter(function ($item) {

                return
                    $item->tipo_movimiento === 'INGRESO'
                    &&
                    strtoupper(
                        trim($item->apertura_caja ?? '')
                    ) !== 'SI';
            })
            ->sum('monto');


        // SALIDAS
        $totalSalidas = $datos
            ->where(
                'tipo_movimiento',
                'SALIDA'
            )
            ->sum('monto');


        // SALDO FINAL
        $saldo =
            $totalApertura
            + $totalIngresos
            - $totalSalidas;


        return Pdf::loadView(
            'reporte.pdf.flujo-efectivo',
            compact(
                'datos',
                'totalApertura',
                'totalIngresos',
                'totalSalidas',
                'saldo'
            )
        )
            ->setPaper('letter', 'landscape')
            ->stream('flujo_efectivo.pdf');
    }


    public function flujoEfectivoExcel(Request $request)
    {
        $datos =
            $this->consultaFlujo($request);


        $excel =
            new Spreadsheet();

        $hoja =
            $excel->getActiveSheet();


        $hoja->mergeCells('A1:H1');

        $hoja->setCellValue(
            'A1',
            'FLUJO DE EFECTIVO'
        );


        $hoja->fromArray([

            'N°',
            'Fecha',
            'Categoría',
            'Tipo',
            'Descripción',
            'Forma Pago',
            'Sucursal',
            'Monto'

        ], null, 'A3');


        $fila = 4;


        foreach ($datos as $i => $dato) {

            $hoja->fromArray([

                $i + 1,

                $dato->fecha,

                $dato->categoria,

                $dato->tipo_categoria,

                $dato->descripcion,

                $dato->tipo_pago,

                $dato->sucursal,

                $dato->monto

            ], null, 'A' . $fila);


            $fila++;
        }


        $this->estiloExcel(
            $hoja,
            'H',
            $fila - 1
        );


        return $this->descargarExcel(
            $excel,
            'flujo_efectivo.xlsx'
        );
    }


    // ============================================================
    // ============================================================
    // AGENDA
    // ============================================================
    // ============================================================

    private function consultaAgenda(Request $request)
    {
        [$inicio, $fin] =
            $this->fechas($request);


        $query =
            DB::table('agendas as a')

            ->leftJoin(
                'clientes as c',
                'c.id',
                '=',
                'a.cliente_id'
            )

            ->leftJoin(
                'users as u',
                'u.id',
                '=',
                'a.usuario_asignado_id'
            )

            ->leftJoin(
                'sucursales as s',
                's.id',
                '=',
                'a.sucursal_id'
            )

            ->whereNull(
                'a.deleted_at'
            )

            ->whereBetween(
                'a.fecha_inicio',
                [$inicio, $fin]
            );


        if ($request->filled('sucursal_id')) {

            $query->where(
                'a.sucursal_id',
                $request->sucursal_id
            );
        }


        return $query

            ->select(

                'a.id',

                'a.titulo',

                'a.descripcion',

                'a.fecha_inicio',

                'a.fecha_fin',

                'a.estado',

                'a.observacion',

                'c.nombres',

                'c.ap_paterno',

                'c.ap_materno',

                'c.numero_celular',

                'u.name as responsable',

                's.nombre as sucursal'

            )

            ->orderBy('a.fecha_inicio')

            ->get();
    }


    public function agendaPdf(Request $request)
    {
        $datos =
            $this->consultaAgenda($request);


        return Pdf::loadView(
            'reporte.pdf.agenda',
            compact('datos')
        )
            ->setPaper('letter', 'landscape')
            ->stream(
                'agenda.pdf'
            );
    }


    public function agendaExcel(Request $request)
    {
        $datos =
            $this->consultaAgenda($request);


        $excel =
            new Spreadsheet();

        $hoja =
            $excel->getActiveSheet();


        $hoja->mergeCells('A1:I1');

        $hoja->setCellValue(
            'A1',
            'REPORTE DE AGENDA'
        );


        $hoja->fromArray([

            'N°',
            'Inicio',
            'Fin',
            'Título',
            'Cliente',
            'Celular',
            'Responsable',
            'Sucursal',
            'Estado'

        ], null, 'A3');


        $fila = 4;


        foreach ($datos as $i => $dato) {

            $cliente = trim(

                ($dato->nombres ?? '') . ' ' .
                    ($dato->ap_paterno ?? '') . ' ' .
                    ($dato->ap_materno ?? '')
            );


            $hoja->fromArray([

                $i + 1,

                $dato->fecha_inicio,

                $dato->fecha_fin,

                $dato->titulo,

                $cliente,

                $dato->numero_celular,

                $dato->responsable,

                $dato->sucursal,

                $dato->estado

            ], null, 'A' . $fila);


            $fila++;
        }


        $this->estiloExcel(
            $hoja,
            'I',
            $fila - 1
        );


        return $this->descargarExcel(
            $excel,
            'agenda.xlsx'
        );
    }

    public function utilidadesPdf(Request $request)
    {
        $datos = $this->consultaUtilidades($request);

        $totalVenta = $datos->sum(function ($item) {
            return (float) $item->venta_neta;
        });

        $totalCosto = $datos->sum(function ($item) {
            return (float) $item->costo;
        });

        $totalUtilidad = $datos->sum(function ($item) {
            return (float) $item->utilidad;
        });


        // PRODUCTOS
        $utilidadProductos = $datos
            ->filter(function ($item) {

                return strtoupper(
                    trim($item->tipo ?? '')
                ) === 'PRODUCTO';
            })
            ->sum('utilidad');


        // SERVICIOS
        $utilidadServicios = $datos
            ->filter(function ($item) {

                return strtoupper(
                    trim($item->tipo ?? '')
                ) === 'SERVICIO';
            })
            ->sum('utilidad');


        return Pdf::loadView(
            'reporte.pdf.utilidades',
            compact(
                'datos',
                'totalVenta',
                'totalCosto',
                'totalUtilidad',
                'utilidadProductos',
                'utilidadServicios',
                'request'
            )
        )
            ->setPaper('letter', 'landscape')
            ->stream('reporte_utilidades.pdf');
    }

    public function utilidadesExcel(Request $request)
    {
        $datos = $this->consultaUtilidades($request);

        $excel = new Spreadsheet();

        $hoja = $excel->getActiveSheet();

        $hoja->setTitle('Utilidades');

        $hoja->mergeCells('A1:N1');

        $hoja->setCellValue(
            'A1',
            'REPORTE DE UTILIDADES'
        );

        $hoja->fromArray([

            'N°',
            'Fecha',
            'Documento',
            'Código',
            'Producto / Servicio',
            'Tipo',
            'Sucursal',
            'Cantidad',
            'P. Compra',
            'P. Venta',
            'Descuento',
            'Venta Neta',
            'Costo',
            'Utilidad'

        ], null, 'A3');


        $fila = 4;

        $totalVenta = 0;
        $totalCosto = 0;
        $totalUtilidad = 0;


        foreach ($datos as $i => $dato) {

            $hoja->fromArray([

                $i + 1,

                $dato->fecha,

                $dato->numero_factura
                    ?? $dato->numero_recibo,

                $dato->codigo,

                $dato->nombre_producto,

                $dato->tipo,

                $dato->sucursal,

                $dato->cantidad,

                $dato->precio_compra ?? 0,

                $dato->precio,

                $dato->descuento ?? 0,

                $dato->venta_neta,

                $dato->costo,

                $dato->utilidad

            ], null, 'A' . $fila);


            $totalVenta +=
                (float) $dato->venta_neta;

            $totalCosto +=
                (float) $dato->costo;

            $totalUtilidad +=
                (float) $dato->utilidad;

            $fila++;
        }


        // ========================================
        // TOTALES
        // ========================================

        $hoja->setCellValue(
            'K' . $fila,
            'TOTALES'
        );

        $hoja->setCellValue(
            'L' . $fila,
            $totalVenta
        );

        $hoja->setCellValue(
            'M' . $fila,
            $totalCosto
        );

        $hoja->setCellValue(
            'N' . $fila,
            $totalUtilidad
        );


        $this->estiloExcel(
            $hoja,
            'N',
            $fila
        );


        return $this->descargarExcel(
            $excel,
            'reporte_utilidades.xlsx'
        );
    }

    private function consultaUtilidades(Request $request)
    {
        [$inicio, $fin] = $this->fechas($request);

        $query = DB::table('detalles as d')

            ->join(
                'facturas as f',
                'f.id',
                '=',
                'd.factura_id'
            )

            ->leftJoin(
                'productos as p',
                'p.id',
                '=',
                'd.producto_id'
            )

            ->leftJoin(
                'sucursales as s',
                's.id',
                '=',
                'd.sucursal_id'
            )

            // DETALLES NO ELIMINADOS
            ->whereNull('d.deleted_at')

            // FACTURAS NO ELIMINADAS
            ->whereNull('f.deleted_at')

            // RANGO DE FECHAS
            ->whereBetween(
                'd.fecha',
                [$inicio, $fin]
            )

            // NO TOMAR FACTURAS ANULADAS
            ->where(function ($query) {

                $query->whereNull('f.estado')
                    ->orWhereRaw("UPPER(f.estado) <> 'ANULADO'");
            });


        // ============================================
        // SUCURSAL
        // ============================================

        if ($request->filled('sucursal_id')) {

            $query->where(
                'd.sucursal_id',
                $request->sucursal_id
            );
        }


        // ============================================
        // TIPO: PRODUCTO / SERVICIO
        // ============================================

        if ($request->filled('tipo')) {

            $query->where(
                'p.tipo',
                $request->tipo
            );
        }


        return $query

            ->select(

                'd.id',

                'd.factura_id',

                'd.fecha',

                'd.producto_id',

                'd.nombre_producto',

                'd.precio_compra',

                'd.precio',

                'd.cantidad',

                'd.descuento',

                'd.total',

                'd.importe',

                'p.codigo',

                'p.tipo',

                's.nombre as sucursal',

                'f.numero_factura',

                'f.numero_recibo',

                // ==========================
                // VENTA BRUTA
                // ==========================

                DB::raw('
                (COALESCE(d.precio,0) * COALESCE(d.cantidad,0))
                AS venta_bruta
            '),

                // ==========================
                // COSTO
                // ==========================

                DB::raw('
                (
                    COALESCE(d.precio_compra,0)
                    *
                    COALESCE(d.cantidad,0)
                ) AS costo
            '),

                // ==========================
                // VENTA NETA
                // ==========================

                DB::raw('
                (
                    (COALESCE(d.precio,0) * COALESCE(d.cantidad,0))
                    -
                    COALESCE(d.descuento,0)
                ) AS venta_neta
            '),

                // ==========================
                // UTILIDAD
                // ==========================

                DB::raw('
                (
                    (
                        COALESCE(d.precio,0)
                        *
                        COALESCE(d.cantidad,0)
                    )
                    -
                    COALESCE(d.descuento,0)
                    -
                    (
                        COALESCE(d.precio_compra,0)
                        *
                        COALESCE(d.cantidad,0)
                    )
                ) AS utilidad
            ')
            )

            ->orderBy('d.fecha', 'asc')

            ->get();
    }
}
