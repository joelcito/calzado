

<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<title>SISTEMA DE CONTROL DE VENTAS</title>
		<meta charset="utf-8" />
        @yield('metadatos')
        <meta name="description" content="Sistema de control de inventario moderno y eficiente, ideal para empresas en Bolivia. Gestiona productos, stock, proveedores y ventas en tiempo real. Compatible con normativas locales y con facturación electrónica integrada. Solución completa para pymes, emprendimientos y grandes negocios que buscan automatizar su gestión de inventario y mejorar la rentabilidad.">
        <meta name="keywords" content="sistema de inventario, control de inventario, gestión de stock, software de inventario, administración de productos, control de almacenes, proveedores, sistema ERP, ventas, compras, reportes, facturación electrónica, software empresarial, inventario Bolivia, administración, Bootstrap 5, Angular, VueJs, React, Laravel, Django, Asp.Net Core, Rails, Spring, Blazor, Node.js, control empresarial, desarrollo web, plantillas de administración, dashboards, logística, gestión empresarial">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_BO" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="Sistema de Control de Inventario en Línea - Gestión Simplificada para Empresas" />
		<meta property="og:url" content="http://importadoraclick.com/" />
		<meta property="og:site_name" content="Sistema de Inventario en Línea" />
		<link rel="canonical" href="http://importadoraclick.com/" />
        <link rel="shortcut icon" href="{{ asset('assets/img/logo_calzado.png') }}" />
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Vendor Stylesheets(used for this page only)-->
		<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Vendor Stylesheets-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

        <style>
            .clase-icono {
                background-color: white;                              /* Fondo blanco */
                color           : black;                              /* Ícono negro */
                padding         : 10px;                               /* Ajuste de padding si es necesario */
                border-radius   : 50%;                                /* Si quieres que el fondo sea circular */
                transition      : background-color 0.3s, color 0.3s;  /* Suave transición */
            }

            .menu-link:hover .clase-icono {
                background-color: black;  /* Fondo negro al hacer hover */
                color           : white;  /* Ícono blanco al hacer hover */
            }

            .menu-item.menu-accordion.hover.show .menu-link .clase-icono {
                background-color: black;
                color: white;
            }

        </style>
		<!--end::Global Stylesheets Bundle-->
		@section('css')
		@show
		<script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::App-->
		<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
			<!--begin::Page-->
			<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
				<!--begin::Header-->
                @include('partials.header')
				<!--end::Header-->
				<!--begin::Wrapper-->
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
					<!--begin::Sidebar-->
					<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
						<!--begin::Logo-->
						<div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
							<!--begin::Logo image-->
							<a href="{{ url('home') }}">
								<div class="row mt-4">
									<div class="col-md-12">
                                        <center>
                                            <img alt="Logo" src="{{asset('assets/img/logo_calzado.png')}}" width="50%" />
                                        </center>
									</div>
                                    <!--
									<div class="col-md-8">
                                        <h2 class="text-white mt-6">INVENTARIO</h2>
									</div>
                                    -->
								</div>
							</a>
							<!--end::Logo image-->
							<!--begin::Sidebar toggle-->
							<!--begin::Minimized sidebar setup:
                                if (isset($_COOKIE["sidebar_minimize_state"]) && $_COOKIE["sidebar_minimize_state"] === "on") {
                                    1. "src/js/layout/sidebar.js" adds "sidebar_minimize_state" cookie value to save the sidebar minimize state.
                                    2. Set data-kt-app-sidebar-minimize="on" attribute for body tag.
                                    3. Set data-kt-toggle-state="active" attribute to the toggle element with "kt_app_sidebar_toggle" id.
                                    4. Add "active" class to to sidebar toggle element with "kt_app_sidebar_toggle" id.
                                }
                            -->
							<div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
								<i class="ki-duotone ki-black-left-line fs-3 rotate-180">
									<span class="path1"></span>
									<span class="path2"></span>
								</i>
							</div>
							<!--end::Sidebar toggle-->
						</div>
						<!--end::Logo-->
						<!--begin::sidebar menu-->
						@include('partials.menu')
						<!--end::sidebar menu-->
						<!--begin::Footer-->
						<!--end::Footer-->
					</div>
					<!--end::Sidebar-->
					<!--begin::Main-->
					<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
						<!--begin::Content wrapper-->
						<div class="d-flex flex-column flex-column-fluid">
							<div id="kt_app_content" class="app-content flex-column-fluid">
								<!--begin::Content container-->
								<div id="kt_app_content_container" class="app-container container-fluid">
									@yield('content')
								</div>
								<!--end::Content container-->
							</div>
							<!--end::Content-->
						</div>
						<!--end::Content wrapper-->
						<!--begin::Footer-->
						@include('partials.footer')
						<!--end::Footer-->
					</div>
					<!--end:::Main-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::App-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/ajaxGlobal.js') }}"></script>
		@section('js')
        @show
        @section('formularioBusquedaJs')
        @show
		<!--end::Custom Javascript-->
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>

