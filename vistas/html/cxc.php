<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
/* Connect To Database*/
require_once "../db.php"; //Contiene las variables de configuracion para conectar a la base de datos
require_once "../php_conexion.php"; //Contiene funcion que conecta a la base de datos
//Inicia Control de Permisos
include "../permisos.php";
$user_id = $_SESSION['id_users'];
get_cadena($user_id);
$modulo = "Clientes";
permisos($modulo, $cadena_permisos);
//Finaliza Control de Permisos
$title  = "Clientes";
$ventas = 1;
?>

<?php require 'includes/header_start.php';?>

<?php require 'includes/header_end.php';?>

<!-- Begin page -->
<div id="wrapper">

	<?php require 'includes/menu.php';?>

	<!-- ============================================================== -->
	<!-- Start right Content here -->
	<!-- ============================================================== -->
	<div class="content-page">
		<!-- Start content -->
		<div class="content">
			<div class="container">
<?php if ($permisos_ver == 1) {
    include "../modal/ver_datos.php";
    ?>
				<div class="col-lg-12">
					<div class="portlet">
						<div class="portlet-heading bg-secondtabla">
							<h3 class="portlet-title">
								Cuentas por Cobrar
							</h3>
						
							<div class="clearfix"></div>
						</div>
						<div id="bg-primary" class="panel-collapse collapse show">
							<div class="portlet-body">
								<form class="form-horizontal" role="form" id="datos_cotizacion">
									<div class="form-group row">
										<div class="col-md-6">
											<div class="input-group">
												<input type="text" class="form-control" id="q" placeholder="Nombre del cliente o # factura" onkeyup='load(1);'>
												<span class="input-group-btn">
													<button type="button" class="btn btn-info waves-effect waves-light" onclick='load(1);'>
														<span class="fa fa-search" ></span> Buscar</button>
													</span>
												</div>
											</div>
											<div class="col-md-3">
												<span id="loader"></span>
											</div>

										</div>
									</form>
									<div id="resultados_ajax"></div>
									<div class="datos_ajax_delete"></div><!-- Datos ajax Final -->
									<div class='outer_div'></div><!-- Carga los datos ajax -->



								</div>
							</div>
						</div>
					</div>
					<?php
} else {
    ?>
		<section class="content">
			<div class="alert alert-danger" align="center">
				<h3>Acceso denegado! </h3>
				<p>No cuentas con los permisos necesario para acceder a este módulo.</p>
			</div>
		</section>
		<?php
}
?>

				</div>
				<!-- end container -->
			</div>
			<!-- end content -->

			<?php require 'includes/pie.php';?>

		</div>
		<!-- ============================================================== -->
		<!-- End Right content here -->
		<!-- ============================================================== -->


	</div>
	<!-- END wrapper -->

	<?php require 'includes/footer_start.php'
?>
	<!-- ============================================================== -->
	<!-- Todo el codigo js aqui-->
	<!-- ============================================================== -->
	<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="../../js/cxc.js"></script>
	<script>
	$(document).ready( function () {
		$(".UpperCase").on("keypress", function () {
			$input=$(this);
			setTimeout(function () {
				$input.val($input.val().toUpperCase());
			},50);
		})
	})
</script>
	<script>
		function obtener_datos(numero_factura) {
			$(".outer_datos").load("../modal/datos.php?numero_factura=" + numero_factura);
		}
	</script>

	<?php require 'includes/footer_end.php'
?>

