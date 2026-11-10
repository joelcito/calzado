@unless ($soloImagen ?? false)
    <div style="text-align: center; margin-bottom: {{ ($compacto ?? false) ? '4px' : '12px' }}; padding-bottom: {{ ($compacto ?? false) ? '0' : '8px' }}; border-bottom: {{ ($compacto ?? false) ? '0' : '1px solid #17354f' }}; page-break-inside: avoid;">
@endunless
    <img class="{{ $clase ?? '' }}" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/logo_calzado.png'))) }}" alt="San Cayetano Importaciones" style="height: {{ $alto ?? 64 }}px; width: auto; max-width: 100%; vertical-align: middle;">
@unless ($soloImagen ?? false)
    </div>
@endunless
