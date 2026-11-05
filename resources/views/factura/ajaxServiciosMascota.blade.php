<div style="overflow-x: auto;">
    @forelse ($detalles as $de)
        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end" style="background-color: #a954ee;background-image:url('assets/media/patterns/vector-1.png');">
            <div class="card-header pt-5">
                <!--begin::Title-->
                <div class="card-title d-flex flex-column">
                    <!--begin::Amount-->
                    <span class="fw-bold text-white me-2 lh-1 ls-n2">{{ $de->producto->nombre }} ({{ \Carbon\Carbon::parse($de->fecha)->format('d/m/Y') }})</span>
                    <!--end::Amount-->
                    <!--begin::Subtitle-->
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Precio del Servicio: {{ $de->precio }} Bs</span>
                    <!--end::Subtitle-->
                </div>
                <!--end::Title-->
            </div>
        </div>
    @empty
        <h4 class="text-danger">Aun no realizo servicio alguno.</h4>
    @endforelse
</div>