<script>
    function modalIngresoSalida(tipo, hermanoId = '') {
        const formulario = document.getElementById('formularioIngresoSalida');
        formulario.reset();
        $('#tipo').val(tipo);
        $('#movimiento_duenio_id').val(hermanoId);
        $('#text_tipoo_modal').text(tipo);
        const categorias = tipo === 'INGRESO' ? @json($categoriasIngreso) : @json($categoriasSalida);
        $('#categoria_id').empty().append(new Option('Seleccione', ''));
        categorias.forEach(categoria => $('#categoria_id').append(new Option(categoria.nombre, categoria.id)));
        $('#boton-agrega-sale-dinero').prop('disabled', false).text('Guardar');
        $('#modalIngreso').modal('show');
    }

    function guardarTipoIngresoSalida() {
        const formulario = document.getElementById('formularioIngresoSalida');
        if (!formulario.reportValidity()) return;
        const boton = $('#boton-agrega-sale-dinero');
        boton.prop('disabled', true).text('Guardando...');
        $.ajax({
            url: "{{ url('pago/guardarTipoIngresoSalida') }}",
            type: 'POST',
            dataType: 'json',
            data: $(formulario).serialize(),
            success: function(resultado) {
                if (resultado.estado) {
                    $('#modalIngreso').modal('hide');
                    Swal.fire({ icon: 'success', text: resultado.mensaje, timer: 1800, showConfirmButton: false });
                    ajaxListado();
                } else {
                    Swal.fire({ icon: 'error', text: resultado.mensaje });
                }
            },
            error: function(xhr) {
                const errores = xhr.responseJSON?.errors;
                const mensaje = errores ? Object.values(errores).flat().join('\n') : 'No se pudo guardar el movimiento. Intente nuevamente.';
                Swal.fire({ icon: 'error', text: mensaje });
            },
            complete: function() { boton.prop('disabled', false).text('Guardar'); }
        });
    }
</script>
