<!--begin::Alert-->
<div class="alert alert-dismissible bg-light-danger d-flex flex-center flex-column py-10 px-10 px-lg-20 mb-10">

    <!--begin::Icon-->
    <i class="ki-duotone ki-information-5 fs-5tx text-danger mb-5"><span class="path1"></span><span
            class="path2"></span><span class="path3"></span></i>
    <!--end::Icon-->

    <!--begin::Wrapper-->
    <div class="text-center">
        <!--begin::Title-->
        <h1 class="fw-bold mb-5">No se selecciono ningun NIT</h1>
        <!--end::Title-->

        <!--begin::Separator-->
        <div class="separator separator-dashed border-danger opacity-25 mb-5"></div>
        <!--end::Separator-->

        <!--begin::Content-->
        <div class="mb-9 text-gray-900">
            Selecciono un <strong>NIT</strong> para comenzar con la facturacion.<br />
        </div>
        <!--end::Content-->
        <form action="{{ route('factura.seleccionarNit') }}" method="POST">
            @csrf
            <select name="nit_id" id="nit_id" class="form-select form-select-sm" onchange="sacarSucursales()"
                required>
                <option value="">Seleccione Nit</option>
                @foreach ($nits as $nit)
                    <option value="{{ $nit->id }}">{{ $nit->numero }}</option>
                @endforeach
            </select>
            <div class="separator-content my-1"></div>
            <select name="sucursal_id_seleccionar_nit" id="sucursal_id_seleccionar_nit"
                class="form-select form-select-sm" onchange="sacarPuntoVentas()" required>
            </select>
            <div class="separator-content my-1"></div>
            <select name="punto_venta_id_seleccionar_nit" id="punto_venta_id_seleccionar_nit"
                class="form-select form-select-sm" required>
            </select>
            <!--begin::Buttons-->
            <div class="d-flex flex-center flex-wrap">
                {{-- <a href="#" class="btn btn-outline btn-outline-danger btn-active-danger m-2">Cancel</a> --}}
                <button type="submit" class="btn btn-success m-2">Seleccionar</button>
            </div>
            <!--end::Buttons-->
        </form>
    </div>
    <!--end::Wrapper-->
</div>
<!--end::Alert-->
