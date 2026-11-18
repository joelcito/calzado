@php
    $productos = [];
    $subtotal = 0;
    foreach ($factura->detalles as $detalle) {
        $importe = (float) $detalle->total - (float) $detalle->descuento;
        $subtotal += $importe;
        $productos[] = [
            'nombre' => ($detalle->producto?->codigo ? $detalle->producto->codigo.' - ' : '').$detalle->nombre_producto,
            'descripcion' => $detalle->descripcion_adicional ?? '',
            'precio' => number_format((float) $detalle->precio, 2),
            'cantidad' => number_format((float) $detalle->cantidad, 2),
            'total' => number_format($importe, 2),
        ];
    }
    $totales = [['SUBTOTAL:', 'Bs '.number_format($subtotal, 2)]];
    if ((float) $factura->descuento_adicional > 0) {
        $totales[] = ['DESCUENTO:', 'Bs '.number_format((float) $factura->descuento_adicional, 2)];
    }
    $pagos = [];
    $totalPagado = 0;
    foreach ($factura->pagos as $pago) {
        $totalPagado += (float) $pago->monto;
        $pagos[] = [$pago->tipo_pago.':', 'Bs '.number_format((float) $pago->monto, 2)];
    }
    $saldo = max(0, (float) $factura->total - $totalPagado);
    $recibo = [
        'titulo' => 'VENTA N.º '.$factura->numero_recibo,
        'sucursal' => $factura->sucursal->nombre ?? '',
        'fecha' => \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y H:i:s'),
        'impresion' => now()->format('d/m/Y H:i:s'),
        'vendedor' => $factura->vendedor->nombres ?? '',
        'cliente' => trim(($factura->cliente->nombres ?? '').' '.($factura->cliente->ap_paterno ?? '').' '.($factura->cliente->ap_materno ?? '')),
        'descripcion' => $factura->descripcion ?? '',
        'productos' => $productos,
        'totales' => $totales,
        'total' => 'Bs '.number_format((float) $factura->total, 2),
        'pagos' => $pagos,
        'pagado' => 'Bs '.number_format($totalPagado, 2),
        'saldo' => $saldo > 0 ? 'Bs '.number_format($saldo, 2) : null,
    ];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recibo de venta {{ $factura->numero_recibo }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px 16px 40px; font: 15px Arial, sans-serif; background: #eef2f6; color: #17354f; }
        main { max-width: 520px; margin: auto; }
        h1 { font-size: 21px; margin: 0 0 8px; }
        .acciones { display: flex; flex-wrap: wrap; gap: 10px; margin: 16px 0; }
        button, .acciones a { padding: 12px 16px; border: 0; border-radius: 6px; background: #17354f; color: white; text-decoration: none; font: inherit; cursor: pointer; }
        button:disabled { opacity: .5; cursor: wait; }
        #recibo { display: block; width: 100%; height: auto; background: white; box-shadow: 0 6px 28px #17354f18; }
        #recibo[hidden] { display: none; }
        #estado { line-height: 1.5; color: #546577; margin: 0; }
        #compartir { background: #167344; }
        .acciones a { background: white; color: #17354f; border: 1px solid #d3dce4; }
        @media (max-width: 480px) { .acciones > * { flex: 1 1 45%; text-align: center; font-size: 14px; } }
    </style>
</head>
<body>
<main>
    <h1>Venta registrada</h1>
    <p id="estado" role="status">Preparando la imagen del recibo…</p>
    <div class="acciones">
        <button id="descargar" disabled>Descargar imagen</button>
        <button id="compartir" disabled>Compartir imagen / WhatsApp</button>
        <a href="{{ route('factura.formulario') }}">Nueva venta</a>
        <a href="{{ route('factura.listado') }}">Ver ventas</a>
    </div>
    <img id="recibo" alt="Recibo de venta {{ $factura->numero_recibo }}" hidden>
    <noscript>Activa JavaScript para generar la imagen del recibo.</noscript>
</main>
<script>
    const datos = {{ \Illuminate\Support\Js::from($recibo) }};
    const logoRecibo = @json('data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/img/logo_calzado.png'))));
    const nombreArchivo = @json('venta-'.$factura->numero_recibo.'.png');
    const estado = document.getElementById('estado');
    const descargar = document.getElementById('descargar');
    const compartir = document.getElementById('compartir');
    let archivoImagen;
    let direccionImagen;

    function descargarImagen() {
        const enlace = document.createElement('a');
        enlace.href = direccionImagen;
        enlace.download = nombreArchivo;
        document.body.appendChild(enlace);
        enlace.click();
        enlace.remove();
    }

    descargar.addEventListener('click', descargarImagen);
    compartir.addEventListener('click', async function () {
        if (!navigator.canShare || !archivoImagen || !navigator.canShare({ files: [archivoImagen] })) {
            descargarImagen();
            estado.textContent = 'Adjunta la imagen descargada en WhatsApp. También puedes mantener presionado el recibo para guardarlo.';
            return;
        }
        compartir.disabled = true;
        try {
            await navigator.share({ files: [archivoImagen], title: 'Recibo de venta' });
        } catch (error) {
            if (error.name !== 'AbortError') {
                estado.textContent = 'No se pudo compartir. Descarga la imagen y adjúntala en WhatsApp.';
            }
        } finally {
            compartir.disabled = false;
        }
    });

    async function generarRecibo() {
      try {
        const lienzo = document.createElement('canvas');
        const contexto = lienzo.getContext('2d');
        const ancho = 800;
        const margen = 32;
        const operaciones = [];
        let y = 36;
        function texto(contenido, x, posicionY, tamano = 24, negrita = false, alineacion = 'left', color = '#111') {
            operaciones.push(() => {
                contexto.font = `${negrita ? 'bold ' : ''}${tamano}px Arial`;
                contexto.fillStyle = color;
                contexto.textAlign = alineacion;
                contexto.fillText(String(contenido), x, posicionY);
            });
        }
        function envolver(contenido, anchoMaximo, tamano = 24) {
            contexto.font = `${tamano}px Arial`;
            const lineas = [];
            for (const parrafo of String(contenido).split('\n')) {
                let linea = '';
                for (const palabra of parrafo.split(/\s+/)) {
                    if (linea && contexto.measureText(linea + ' ' + palabra).width > anchoMaximo) {
                        lineas.push(linea);
                        linea = '';
                    }
                    for (const caracter of (linea ? ' ' : '') + palabra) {
                        if (contexto.measureText(linea + caracter).width > anchoMaximo) {
                            lineas.push(linea);
                            linea = '';
                        }
                        linea += caracter;
                    }
                }
                lineas.push(linea);
            }
            return lineas;
        }
        function parrafo(contenido, x = margen, anchoMaximo = ancho - margen * 2, tamano = 24, negrita = false, alineacion = 'left') {
            for (const linea of envolver(contenido, anchoMaximo, tamano)) {
                texto(linea, x, y, tamano, negrita, alineacion);
                y += tamano + 10;
            }
        }
        function separador(discontinuo = true) {
            y += 12;
            const posicion = y;
            operaciones.push(() => {
                contexto.beginPath();
                contexto.strokeStyle = '#111';
                contexto.lineWidth = 2;
                contexto.setLineDash(discontinuo ? [9, 8] : []);
                contexto.moveTo(margen, posicion);
                contexto.lineTo(ancho - margen, posicion);
                contexto.stroke();
            });
            y += 20;
        }
        function importe(etiqueta, monto, destacado = false) {
            const tamano = destacado ? 30 : 24;
            const lineas = envolver(etiqueta, 420, tamano);
            for (const linea of lineas) {
                texto(linea, 495, y, tamano, true, 'right');
                y += tamano + 10;
            }
            const anchoMonto = contexto.measureText(monto).width;
            if (anchoMonto > 245) {
                texto(monto, ancho - margen, y, tamano, destacado, 'right');
                y += tamano + 10;
            } else {
                texto(monto, ancho - margen, y - tamano - 10, tamano, destacado, 'right');
            }
        }
        const logo = new Image();
        logo.src = logoRecibo;
        try {
            await logo.decode();
            operaciones.push(() => {
                const escala = Math.min(112 / logo.width, 112 / logo.height);
                contexto.drawImage(logo, margen, 32, logo.width * escala, logo.height * escala);
            });
        } catch (error) {
            // El recibo sigue disponible si el navegador no puede cargar el logo.
        }
        texto(datos.titulo, 202, 70, 34, true, 'left', '#17354f');
        y = 182;
        parrafo(datos.sucursal, ancho / 2, ancho - margen * 2, 26, true, 'center');
        parrafo('Fecha: ' + datos.fecha, ancho / 2, ancho - margen * 2, 24, false, 'center');
        parrafo('Impresión: ' + datos.impresion, ancho / 2, ancho - margen * 2, 24, false, 'center');
        separador();
        parrafo('Vendedor: ' + datos.vendedor);
        parrafo('Cliente: ' + datos.cliente);
        if (datos.descripcion) {
            parrafo('Descripción: ' + datos.descripcion);
        }
        separador();
        texto('DETALLE', margen, y, 23, true);
        texto('PRECIO', 490, y, 23, true, 'right');
        texto('CANT.', 590, y, 23, true, 'right');
        texto('TOTAL', ancho - margen, y, 23, true, 'right');
        y += 26;
        separador(false);
        for (const producto of datos.productos) {
            const inicio = y;
            parrafo(producto.nombre, margen, 330, 23);
            if (producto.descripcion) {
                parrafo(producto.descripcion, margen, 330, 21);
            }
            const valores = [[producto.precio, 490, 115], [producto.cantidad, 590, 86], [producto.total, ancho - margen, 160]];
            for (const [valor, x, espacio] of valores) {
                contexto.font = '23px Arial';
                const tamano = Math.min(23, 23 * espacio / contexto.measureText(valor).width);
                texto(valor, x, inicio, tamano, false, 'right');
            }
            y += 8;
        }
        separador();
        datos.totales.forEach(([etiqueta, monto]) => importe(etiqueta, monto));
        separador(false);
        importe('TOTAL:', datos.total, true);
        if (datos.pagos.length) {
            separador();
            datos.pagos.forEach(([etiqueta, monto]) => importe(etiqueta, monto));
            importe('TOTAL PAGADO:', datos.pagado);
        }
        if (datos.saldo) {
            separador();
            importe('SALDO PENDIENTE:', datos.saldo, true);
        }
        separador();
        y += 10;
        parrafo(datos.saldo ? 'VENTA CON SALDO PENDIENTE' : 'VENTA PAGADA', ancho / 2, ancho - margen * 2, 27, true, 'center');
        parrafo('Gracias por su preferencia', ancho / 2, ancho - margen * 2, 23, false, 'center');
        lienzo.width = ancho;
        lienzo.height = y + 48;
        contexto.fillStyle = '#fff';
        contexto.fillRect(0, 0, lienzo.width, lienzo.height);
        contexto.textBaseline = 'top';
        operaciones.forEach(dibujar => dibujar());
        lienzo.toBlob(function (imagen) {
            if (!imagen) {
                estado.textContent = 'No se pudo generar la imagen. Recarga esta página para reintentar; la venta ya está guardada.';
                return;
            }
            direccionImagen = URL.createObjectURL(imagen);
            if (typeof File !== 'undefined') {
                archivoImagen = new File([imagen], nombreArchivo, { type: 'image/png' });
            }
            const recibo = document.getElementById('recibo');
            recibo.src = direccionImagen;
            recibo.hidden = false;
            descargar.disabled = false;
            compartir.disabled = false;
            estado.textContent = 'Recibo listo. Descarga la imagen o compártela y elige WhatsApp.';
        }, 'image/png');
      } catch (error) {
        estado.textContent = 'No se pudo generar la imagen. Recarga esta página para reintentar; la venta ya está guardada.';
      }
    }
    generarRecibo();
</script>
</body>
</html>
