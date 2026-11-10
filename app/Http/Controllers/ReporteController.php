<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use App\Utils\CuentaHermano;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
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
        $duenios = User::whereIn('id', DB::table('productos')->select('usuario_duenio_id')
            ->union(DB::table('detalles')->select('usuario_duenio_id'))
            ->union(DB::table('pagos')->select('usuario_duenio_id')))->orderBy('name')->get();

        return view('reporte.listado', compact('sucursales', 'productos', 'duenios'));
    }

    // ============================================================
    // FILTRO FECHAS
    // ============================================================

    private function fechas(Request $request)
    {
        $request->validate([
            'fecha_inicio' => ['nullable', 'date_format:Y-m-d'],
            'fecha_fin' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:fecha_inicio'],
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursales,id'],
            'usuario_duenio_id' => ['nullable', 'integer', 'min:0'],
        ]);
        if ($request->filled('usuario_duenio_id') && (int) $request->usuario_duenio_id !== 0) {
            $request->validate(['usuario_duenio_id' => ['exists:users,id']]);
        }
        $fechaInicio = $request->fecha_inicio
            ? $request->fecha_inicio.' 00:00:00'
            : now()->startOfMonth()->format('Y-m-d H:i:s');

        $fechaFin = $request->fecha_fin
            ? $request->fecha_fin.' 23:59:59'
            : now()->endOfMonth()->format('Y-m-d H:i:s');

        return [
            $fechaInicio,
            $fechaFin,
        ];
    }

    private function filtrosReporte(Request $request): string
    {
        [$inicio, $fin] = $this->fechas($request);
        $duenio = $request->filled('usuario_duenio_id')
            ? ((int) $request->usuario_duenio_id === 0 ? 'Sin asignar' : User::findOrFail($request->usuario_duenio_id)->name)
            : 'Todos';
        $sucursal = $request->filled('sucursal_id') ? Sucursal::withTrashed()->findOrFail($request->sucursal_id)->nombre : 'Todas';

        return 'Desde: '.substr($inicio, 0, 10).' | Hasta: '.substr($fin, 0, 10).' | Dueño: '.$duenio.' | Sucursal: '.$sucursal;
    }

    private function facturasReporte(Request $request): Collection
    {
        [$inicio, $fin] = $this->fechas($request);

        return Factura::with(['cliente', 'sucursal', 'detalles.duenio', 'detalles.producto' => fn ($query) => $query->withTrashed()])
            ->whereNull('estado')->whereBetween('fecha', [$inicio, $fin])
            ->when($request->filled('sucursal_id'), fn ($query) => $query->where('sucursal_id', $request->sucursal_id))
            ->orderBy('fecha')->orderBy('id')->get();
    }

    private function ventasPorDuenio(Request $request): Collection
    {
        [$inicio, $fin] = $this->fechas($request);
        $facturas = $this->facturasReporte($request)->keyBy('id');
        $pagos = Pago::with('usuario')->whereIn('factura_id', $facturas->keys())->where('fecha', '<=', $fin)
            ->where('estado', 'INGRESO')->orderBy('fecha')->orderBy('id')->get();
        $reparto = (new CuentaHermano)->calcular($facturas, $pagos, $inicio, $fin)['ventas'];

        return $reparto->filter(fn ($fila) => ! $request->filled('usuario_duenio_id') || $fila['hermano_id'] === (int) $request->usuario_duenio_id)
            ->map(function ($fila) use ($facturas) {
                $factura = $facturas[$fila['factura_id']];

                return (object) [
                    'id' => $factura->id, 'factura' => $factura, 'fecha' => $factura->fecha,
                    'numero_factura' => $factura->numero_factura, 'numero_recibo' => $factura->numero_recibo,
                    'nombres' => $factura->cliente?->nombres, 'ap_paterno' => $factura->cliente?->ap_paterno,
                    'ap_materno' => $factura->cliente?->ap_materno, 'numero_celular' => $factura->cliente?->numero_celular,
                    'nit' => $factura->nit ?: $factura->cliente?->cedula, 'razon_social' => $factura->razon_social,
                    'sucursal' => $factura->sucursal?->nombre, 'estado' => $factura->estado,
                    'estado_pago' => $fila['pendiente'] > 0 ? 'DEUDA' : 'PAGADO',
                    'total' => $fila['vendido'] / 100, 'pagado' => $fila['cobrado'] / 100, 'saldo' => $fila['pendiente'] / 100,
                    'duenio_id' => $fila['hermano_id'], 'duenio' => $fila['hermano'],
                ];
            })->values();
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
            range('A', $ultimaColumna) as $columna
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
        foreach ($spreadsheet->getAllSheets() as $hoja) {
            $logo = new Drawing;
            $logo->setName('Logo San Cayetano Importaciones');
            $logo->setDescription('Logo San Cayetano Importaciones');
            $logo->setPath(public_path('assets/img/logo_calzado.png'));
            $logo->setHeight(64);
            $logo->setCoordinates('A1');
            $logo->setOffsetX(8);
            $logo->setOffsetY(6);
            $logo->setWorksheet($hoja);
            $hoja->getRowDimension(1)->setRowHeight(58);
            $hoja->getRowDimension(2)->setRowHeight(28);
            $ultimaColumna = $hoja->getHighestColumn();
            $hoja->getStyle('A1:'.$ultimaColumna.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $hoja->getStyle('A1:'.$ultimaColumna.'1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF17354F');
            $hoja->getStyle('A2:'.$ultimaColumna.'2')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
            $hoja->getStyle('A2:'.$ultimaColumna.'2')->getFont()->setSize(9)->getColor()->setARGB('FF546577');
            $hoja->getStyle('A3:'.$ultimaColumna.'3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF17354F');
            $hoja->getStyle('A3:'.$ultimaColumna.'3')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $hoja->getRowDimension(3)->setRowHeight(25);
            $hoja->freezePane('A4');
            $hoja->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(1, 3);
        }

        $writer = new Xlsx($spreadsheet);

        $archivo = tempnam(storage_path('app'), 'reporte_');

        $writer->save($archivo);

        return response()
            ->download($archivo, $nombre)
            ->deleteFileAfterSend(true);
    }

    // ============================================================
    // ============================================================
    // VENTAS
    // ============================================================
    // ============================================================

    private function consultaVentas(Request $request): Collection
    {
        return $this->ventasPorDuenio($request);
    }

    public function ventasPdf(Request $request)
    {
        $datos = $this->consultaVentas($request);

        $total = $datos
            ->where('estado', '!=', 'Anulado')
            ->sum('total');

        $filtros = $this->filtrosReporte($request);

        return Pdf::loadView(
            'reporte.pdf.ventas',
            compact(
                'datos',
                'filtros',
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

        $excel = new Spreadsheet;

        $hoja = $excel->getActiveSheet();

        $hoja->setTitle('Ventas');

        $hoja->mergeCells('A1:J1');

        $hoja->setCellValue(
            'A1',
            'REPORTE DE VENTAS'
        );

        $hoja->mergeCells('A2:J2')->setCellValue('A2', $this->filtrosReporte($request));

        $hoja->fromArray([

            'N°',
            'Fecha',
            'Factura',
            'Cliente',
            'NIT/CI',
            'Sucursal',
            'Estado Pago',
            'Estado',
            'Total Bs',
            'Dueño',

        ], null, 'A3');

        $fila = 4;

        $total = 0;

        foreach ($datos as $index => $dato) {

            $cliente =
                trim(
                    ($dato->nombres ?? '').' '.
                        ($dato->ap_paterno ?? '').' '.
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

                $dato->total,
                $dato->duenio,

            ], null, 'A'.$fila);

            if ($dato->estado != 'ANULADO') {

                $total += $dato->total;
            }

            $fila++;
        }

        $hoja->setCellValue(
            'H'.$fila,
            'TOTAL'
        );

        $hoja->setCellValue(
            'I'.$fila,
            $total
        );

        $this->estiloExcel(
            $hoja,
            'J',
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

    private function consultaCuentasCobrar(Request $request): Collection
    {
        return $this->ventasPorDuenio($request)->filter(fn ($fila) => $fila->saldo > 0)->values();
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

        $filtros = $this->filtrosReporte($request);

        return Pdf::loadView(
            'reporte.pdf.cuentas-cobrar',
            compact(
                'datos',
                'filtros',
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
            new Spreadsheet;

        $hoja =
            $excel->getActiveSheet();

        $hoja->mergeCells('A1:J1');

        $hoja->setCellValue(
            'A1',
            'CUENTAS POR COBRAR'
        );

        $hoja->mergeCells('A2:J2')->setCellValue('A2', $this->filtrosReporte($request));

        $hoja->fromArray([

            'N°',
            'Fecha',
            'Documento',
            'Cliente',
            'Celular',
            'Sucursal',
            'Total',
            'Pagado',
            'Saldo',
            'Dueño',

        ], null, 'A3');

        $fila = 4;

        foreach ($datos as $i => $dato) {

            $cliente = trim(

                ($dato->nombres ?? '').' '.
                    ($dato->ap_paterno ?? '').' '.
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

                $dato->saldo,
                $dato->duenio,

            ], null, 'A'.$fila);

            $fila++;
        }

        $hoja->setCellValue(
            'F'.$fila,
            'TOTALES'
        );

        $hoja->setCellValue(
            'G'.$fila,
            $datos->sum('total')
        );

        $hoja->setCellValue(
            'H'.$fila,
            $datos->sum('pagado')
        );

        $hoja->setCellValue(
            'I'.$fila,
            $datos->sum('saldo')
        );

        $this->estiloExcel(
            $hoja,
            'J',
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
            new Spreadsheet;

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
            'Estado',

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

                $estado,

            ], null, 'A'.$fila);

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

        $fechaInicio = $request->fecha_inicio.' 00:00:00';
        $fechaFin = $request->fecha_fin.' 23:59:59';

        // ============================================================
        // PRODUCTO
        // ============================================================

        $producto = DB::table('productos')
            ->where('id', $request->producto_id)
            ->whereNull('deleted_at')
            ->first();

        if (! $producto) {
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
                    $fechaFin,
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
                'kardex_'.($producto->codigo ?? $producto->id).'.pdf'
            );
    }

    public function kardexExcel(Request $request)
    {
        $datos =
            $this->consultaKardex($request);

        $excel =
            new Spreadsheet;

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
            'Saldo',

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

                $saldo,

            ], null, 'A'.$fila);

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

    private function consultaFlujo(Request $request): array
    {
        [$inicio, $fin] = $this->fechas($request);
        $resumen = collect();
        $movimientos = collect();
        $sucursales = Sucursal::withTrashed()->when($request->filled('sucursal_id'), fn ($query) => $query->whereKey($request->sucursal_id))->get();
        foreach ($sucursales as $sucursal) {
            $cuenta = (new CuentaHermano)->obtener($sucursal->id, $inicio, $fin);
            $resumen = $resumen->concat($cuenta['resumen']->values());
            $movimientos = $movimientos->concat($cuenta['movimientos']->map(fn ($fila) => $fila + ['sucursal' => $sucursal->nombre]));
        }
        if ($request->filled('usuario_duenio_id')) {
            $id = (int) $request->usuario_duenio_id;
            $resumen = $resumen->where('hermano_id', $id);
            $movimientos = $movimientos->where('hermano_id', $id);
        }
        $pagos = Pago::with('categoria')->whereIn('id', $movimientos->pluck('pago_id')->unique())->get()->keyBy('id');
        $datos = $movimientos->sortBy('fecha')->values()->map(fn ($fila) => (object) [
            'fecha' => $fila['fecha'], 'duenio' => $fila['hermano'], 'sucursal' => $fila['sucursal'],
            'categoria' => $fila['tipo'] === 'COBRO VENTA' ? 'VENTA' : $pagos->get($fila['pago_id'])?->categoria?->nombre,
            'tipo_movimiento' => $fila['salida'] > 0 ? 'SALIDA' : 'INGRESO',
            'descripcion' => $fila['descripcion'], 'tipo_pago' => $fila['tipo_pago'],
            'monto' => ($fila['ingreso'] + $fila['salida']) / 100,
            'ingreso' => $fila['ingreso'] / 100, 'salida' => $fila['salida'] / 100,
        ]);
        $saldoAnterior = $resumen->sum('anterior') / 100;
        $totalIngresos = $datos->sum('ingreso');
        $totalSalidas = $datos->sum('salida');
        $saldo = $resumen->sum('disponible') / 100;
        $medios = collect(CuentaHermano::MEDIOS)->mapWithKeys(fn ($medio) => [$medio => $resumen->sum(fn ($fila) => $fila['medios'][$medio] ?? 0) / 100]);

        return compact('datos', 'saldoAnterior', 'totalIngresos', 'totalSalidas', 'saldo', 'medios');
    }

    public function flujoEfectivoPdf(Request $request)
    {
        $datos = $this->consultaFlujo($request);
        $datos['filtros'] = $this->filtrosReporte($request);

        return Pdf::loadView('reporte.pdf.flujo-efectivo', $datos)->setPaper('letter', 'landscape')->stream('flujo_efectivo.pdf');
    }

    public function flujoEfectivoExcel(Request $request)
    {
        $reporte = $this->consultaFlujo($request);
        $excel = new Spreadsheet;
        $hoja = $excel->getActiveSheet();
        $hoja->mergeCells('A1:J1')->setCellValue('A1', 'FLUJO DE EFECTIVO POR DUEÑO');
        $hoja->mergeCells('A2:J2')->setCellValue('A2', $this->filtrosReporte($request));
        $hoja->fromArray(['N°', 'Fecha', 'Dueño', 'Categoría', 'Tipo', 'Descripción', 'Forma Pago', 'Sucursal', 'Ingreso Bs', 'Salida Bs'], null, 'A3');
        $fila = 4;
        foreach ($reporte['datos'] as $i => $dato) {
            $hoja->fromArray([$i + 1, $dato->fecha, $dato->duenio, $dato->categoria, $dato->tipo_movimiento, $dato->descripcion, $dato->tipo_pago, $dato->sucursal, $dato->ingreso, $dato->salida], null, 'A'.$fila);
            $fila++;
        }
        $hoja->setCellValue('H'.$fila, 'TOTALES');
        $hoja->setCellValue('I'.$fila, $reporte['totalIngresos']);
        $hoja->setCellValue('J'.$fila, $reporte['totalSalidas']);
        $hoja->fromArray([
            ['Saldo anterior', $reporte['saldoAnterior']],
            ['Total ingresos', $reporte['totalIngresos']],
            ['Total salidas', $reporte['totalSalidas']],
            ['Disponible final', $reporte['saldo']],
        ], null, 'A'.($fila + 2));
        foreach ($reporte['medios'] as $medio => $monto) {
            $hoja->fromArray([[$medio.' disponible', $monto]], null, 'A'.($fila + 6));
            $fila++;
        }
        $this->estiloExcel($hoja, 'J', $fila + 5);
        $hoja->getStyle('I4:J'.($fila + 5))->getNumberFormat()->setFormatCode('#,##0.00');

        return $this->descargarExcel($excel, 'flujo_efectivo.xlsx');
    }

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
            new Spreadsheet;

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
            'Estado',

        ], null, 'A3');

        $fila = 4;

        foreach ($datos as $i => $dato) {

            $cliente = trim(

                ($dato->nombres ?? '').' '.
                    ($dato->ap_paterno ?? '').' '.
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

                $dato->estado,

            ], null, 'A'.$fila);

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

        $filtros = $this->filtrosReporte($request);

        return Pdf::loadView(
            'reporte.pdf.utilidades',
            compact(
                'datos',
                'filtros',
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

        $excel = new Spreadsheet;

        $hoja = $excel->getActiveSheet();

        $hoja->setTitle('Utilidades');

        $hoja->mergeCells('A1:O1');

        $hoja->setCellValue(
            'A1',
            'REPORTE DE UTILIDADES'
        );

        $hoja->mergeCells('A2:O2')->setCellValue('A2', $this->filtrosReporte($request));

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
            'Utilidad',
            'Dueño',

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

                $dato->utilidad,
                $dato->duenio,

            ], null, 'A'.$fila);

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
            'K'.$fila,
            'TOTALES'
        );

        $hoja->setCellValue(
            'L'.$fila,
            $totalVenta
        );

        $hoja->setCellValue(
            'M'.$fila,
            $totalCosto
        );

        $hoja->setCellValue(
            'N'.$fila,
            $totalUtilidad
        );

        $this->estiloExcel(
            $hoja,
            'O',
            $fila
        );

        return $this->descargarExcel(
            $excel,
            'reporte_utilidades.xlsx'
        );
    }

    private function consultaUtilidades(Request $request): Collection
    {
        $datos = collect();
        foreach ($this->ventasPorDuenio($request) as $venta) {
            $detalles = $venta->factura->detalles->filter(fn ($detalle) => (int) $detalle->usuario_duenio_id === $venta->duenio_id);
            $pesos = $detalles->mapWithKeys(fn ($detalle) => [$detalle->id => max(0, (int) round($detalle->importe * 100))])->all();
            $netos = CuentaHermano::repartir((int) round($venta->total * 100), $pesos);
            foreach ($detalles as $detalle) {
                if ($request->filled('tipo') && $detalle->producto?->tipo !== $request->tipo) {
                    continue;
                }
                $neto = $netos[$detalle->id] / 100;
                $costo = round($detalle->precio_compra * $detalle->cantidad, 2);
                $bruto = round($detalle->precio * $detalle->cantidad, 2);
                $datos->push((object) [
                    'fecha' => $venta->fecha, 'numero_factura' => $venta->numero_factura, 'numero_recibo' => $venta->numero_recibo,
                    'codigo' => $detalle->producto?->codigo, 'nombre_producto' => $detalle->nombre_producto,
                    'tipo' => $detalle->producto?->tipo ?? 'PRODUCTO', 'sucursal' => $venta->sucursal,
                    'cantidad' => $detalle->cantidad, 'precio_compra' => $detalle->precio_compra, 'precio' => $detalle->precio,
                    'descuento' => round($bruto - $neto, 2), 'venta_neta' => $neto, 'costo' => $costo,
                    'utilidad' => round($neto - $costo, 2), 'duenio' => $venta->duenio,
                ]);
            }
        }

        return $datos;
    }
}
