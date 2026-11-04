<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="../../../" />
    <title>INVENTARIO | BOLIVIA</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="El sistema de facturación en línea más avanzado, diseñado para adaptarse a las necesidades de empresas en Bolivia y confiado por miles de usuarios. Incluye gestión completa de facturas, múltiples métodos de pago, soporte para impuestos locales y compatibilidad con facturación electrónica según las normativas bolivianas. Ideal para pymes, emprendedores y grandes empresas que buscan optimizar su gestión administrativa. Obtén acceso ahora y disfruta de la facilidad de factura." />
    <meta name="keywords"
        content="sistema de facturación, sistema de inventario, facturación electrónica, software de facturación, gestión de inventario, control de stock, administración de ventas, gestión de clientes, control de proveedores, generación de reportes, impresión de facturas, soporte para impuestos, sistema en línea, facturación Bolivia, Bootstrap 5, Angular, VueJs, React, Laravel, Django, Asp.Net Core, Rails, Spring, Blazor, Node.js, administración empresarial, desarrollo web, plantillas de administración, dashboards, comercio electrónico, ERP, software empresarial" />
    {{--        <meta name="viewport" content="width=device-width, initial-scale=1" /> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta property="og:locale" content="en_BO" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Sistema de Control de Inventario en Línea - Gestión Simplificada para Empresas" />
    <meta property="og:url" content="http://importadoraclick.com/" />
    <meta property="og:site_name" content="Sistema de Inventario en Línea" />
    <link rel="canonical" href="http://importadoraclick.com/" />
    <link rel="shortcut icon" href="{{ asset('assets/img/Logo_Click_Icono.png') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page bg image-->
        <style>
            body {
                background-image: url('{{ asset('assets/media/auth/bg6.jpg') }}');
            }

            [data-bs-theme="dark"] body {
                background-image: url('{{ asset('assets/media/auth/bg6-dark.jpg') }}');
            }
        </style>
        <!--end::Page bg image-->
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-column-fluid flex-lg-row">
            <!--begin::Aside-->
            <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
                <!--begin::Aside-->
                <div class="d-flex flex-center flex-lg-start flex-column">
                    <!--begin::Logo-->
                    <img alt="Logo" src="{{ asset('assets/img/Logo_Click_Transparente.png') }}" width="80%" />
                    <!--end::Logo-->
                    <!--begin::Title-->
                    <br>
                    {{-- <h1 class="text-white fw-normal m-0">FACTUTACIÓN EN LINEA BOLIVIA SENCILLO Y RAPIDO</h1> --}}
                    <!--end::Title-->
                </div>
                <!--begin::Aside-->
            </div>
            <!--begin::Aside-->
            <!--begin::Body-->
            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20"
                style="align-items: center">
                <!--begin::Card-->
                {{--                    <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-500px p-5"> --}}
                <div
                    class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-400px h-md-500px mb-0 p-5">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">
                        <!--begin::Form-->
                        <form class="form w-100" action="{{ route('login') }}" method="POST">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3">Iniciar Session</h1>
                                <!--end::Title-->
                            </div>
                            <!--begin::Heading-->
                            <!--begin::Separator-->
                            <div class="separator separator-content my-20">
                                <span class="w-200px text-gray-500 fw-semibold fs-7">Escribé tus credenciales</span>
                            </div>
                            <!--end::Separator-->
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input type="text" placeholder="Correo" name="email" autocomplete="off"
                                    class="form-control bg-transparent" />
                                <!--end::Email-->
                            </div>
                            <!--end::Input group=-->
                            <div class="fv-row mb-3">
                                <!--begin::Password-->
                                <input type="password" placeholder="Contraseña" name="password" autocomplete="off"
                                    class="form-control bg-transparent" />
                                <!--end::Password-->
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">Iniciar Session</span>
                                </button>
                            </div>
                            <!--end::Submit button-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <!--end::Global Javascript Bundle-->
    <!--begin::Custom Javascript(used for this page only)-->
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
