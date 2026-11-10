<table style="width: 100%; border-collapse: collapse; margin: 0 0 {{ ($compacto ?? false) ? '4px' : '14px' }}; border-bottom: {{ ($compacto ?? false) ? '0' : '2px solid #17354f' }}; page-break-inside: avoid;">
    <tr>
        <td style="width: {{ ($compacto ?? false) ? '44px' : '76px' }}; border: 0; padding: 0 12px 8px 0; background: white; vertical-align: middle; text-align: left;">
            @include('partials.logoDocumento', ['soloImagen' => true, 'alto' => ($compacto ?? false) ? 36 : 58])
        </td>
        <td style="border: 0; padding: 0 0 8px; background: white; vertical-align: middle; text-align: left;">
            <div style="font-family: DejaVu Sans, sans-serif; font-size: {{ ($compacto ?? false) ? '11px' : '17px' }}; font-weight: bold; color: #17354f; margin-bottom: 5px; line-height: 1.2;">{{ $tituloDocumento }}</div>
            @if ($detalleDocumento ?? false)
                <div style="font-size: 9px; color: #546577; line-height: 1.5;">{{ $detalleDocumento }}</div>
            @endif
            @if ($mostrarFecha ?? true)
                <div style="font-size: 8px; color: #788491; margin-top: 3px;">Generado: {{ now()->format('d/m/Y H:i') }}</div>
            @endif
        </td>
    </tr>
</table>
