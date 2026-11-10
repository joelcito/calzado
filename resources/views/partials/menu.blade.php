<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
        <!--begin::Scroll wrapper-->
        <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true"
            data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">
                <!--begin:Menu item-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        {{-- <span class="menu-heading fw-bold text-uppercase fs-7">MENUS</span> --}}
                        <span class="fs-7 text-white fw-boldn">MENUS</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ Request::is('usuario/*', 'rol/*', 'sucursal/*', 'puntoVenta/*', 'categoria/*', 'subCategoria/*', 'producto/listado', 'producto/listadoSerie', 'cliente/*', 'urlApiServicio/*', 'productoImportacion/*', 'transferencia/*', 'nit/*', 'qr/*', 'proveedor/*' , 'categoriaIngresoSalida/*') ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fa fa-university"></i>
                            </span>
                            <span class="menu-title text-white">ADMINISTRACION</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'rol.listado' ? 'active' : '' }}"
                                    href="{{ route('rol.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Roles</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="{{ url('sucursal/listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Sucursales</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="{{ url('usuario/listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Usuario</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="{{ url('cliente/listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Cliente</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="{{ url('categoria/listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Categoria Producto</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="{{ url('producto/listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Productos</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="{{ url('categoriaIngresoSalida/listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Categoria Ingreso Salida</span>
                                </a>
                            </div>
                            {{--
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'usuario.listado' ? 'active' : '' }}"
                                    href="{{ route('usuario.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Usuarios</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('nit/*') ? 'active' : '' }}"
                                    href="{{ route('nit.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Nit's</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'proveedor.listado' ? 'active' : '' }}"
                                    href="{{ route('proveedor.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Proveedores</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'sucursal.listado' ? 'active' : '' }}" href="{{ route('sucursal.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Sucursales</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'cliente.listado' ? 'active' : '' }}"
                                    href="{{ route('cliente.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Clientes</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'categoria.listado' ? 'active' : '' }}"
                                    href="{{ route('categoria.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Categorias</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'subCategoria.listado' ? 'active' : '' }}"
                                    href="{{ route('subCategoria.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Sub Categorias</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'urlApiServicio.listado' ? 'active' : '' }}"
                                    href="{{ route('urlApiServicio.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Url Api Servicios</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'transferencia.listado' ? 'active' : '' }}"
                                    href="{{ route('transferencia.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Traspasos</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'producto.listado' ? 'active' : '' }}"
                                    href="{{ route('producto.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Productos Sin Serie</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'producto.listadoSerie' ? 'active' : '' }}"
                                    href="{{ route('producto.listadoSerie') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Productos Con Serie</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'productoImportacion.listado' ? 'active' : '' }}"
                                    href="{{ route('productoImportacion.listado', ['nit_id' => 0]) }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Productos Importados</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'pago.listadoDeuda' ? 'active' : '' }}"
                                    href="{{ route('pago.listadoDeuda') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Cuentas por Cobrar</span>
                                </a>
                            </div> --}}
                        </div>
                    </div>
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ Request::is('caja/*', 'factura/*', 'pago/*') ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fa fa-university"></i>
                            </span>
                            <span class="menu-title text-white">VENTAS</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <div class="menu-sub menu-sub-accordion">
                            {{-- <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'caja.listado' ? 'active' : '' }}"
                                    href="{{ route('caja.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Cajas del Dia</span>
                                </a>
                            </div> --}}

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'factura.formulario' ? 'active' : '' }}" href="{{ url('factura/formulario') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Nueva venta</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'factura.listado' ? 'active' : '' }}" href="{{ url('factura/listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Listado Facturas</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'pago.listadoDeuda' ? 'active' : '' }}" href="{{ url('pago/listadoDeuda') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Cuentas por Cobrar</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'pago.listado' ? 'active' : '' }}"
                                    href="{{ route('pago.listado') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title text-white">Ventas del Dia</span>
                                </a>
                            </div>


                        </div>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ Request::is('reporte/*') ? 'active' : '' }}" href="{{ route('reporte.listado') }}">
                            <span class="menu-icon">
                                <i class="fa fa-book"></i>
                            </span>
                            <span class="menu-title text-white">REPORTES</span>
                        </a>
                    </div>
                <!--end:Menu item-->
            </div>
            <!--end::Menu-->
        </div>
        <!--end::Scroll wrapper-->
    </div>
    <!--end::Menu wrapper-->
</div>
