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
$sucursal=get_Sucursal($user_id);
get_cadena($user_id);
$modulo = "Gastos";
permisos($modulo, $cadena_permisos);
//Finaliza Control de Permisos
$title     = "Pagos";
$pacientes = 1;
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

				<div class="col-lg-12">
					<div class="portlet">
						<div class="portlet-heading bg-secondtabla">
							<h3 class="portlet-title">
								Control de Gastos
							</h3>
							<div class="portlet-widgets">
								<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
								<span class="divider"></span>
								
							</div>
							<div class="clearfix"></div>
						</div>
						<div id="bg-primary" class="panel-collapse collapse show">
							<div class="portlet-body">
<?php
if ($permisos_editar == 1) {
    include '../modal/registro_gasto.php';
    include "../modal/editar_gasto.php";
    include "../modal/eliminar_gasto.php";
}
?>
								<form class="form-horizontal" role="form" id="datos_cotizacion">
									<div class="form-group row">
										<div class="col-md-6">
											<div class="input-group">
												<input type="text" class="form-control" id="q" placeholder="Buscar por Referencia" onkeyup='load(1);'>
												<span class="input-group-btn">
													<button type="button" class="btn btn-info waves-effect waves-light" onclick='load(1);'>
														<span class="fa fa-search" ></span> Buscar</button>
													</span>
												</div>
											</div>
											<div class="col-md-3">
												<span id="loader"></span>
											</div>
											<div class="col-md-3">
												<div class="btn-group pull-right">
													<button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#nuevoGastoo"><i class="fa fa-plus"></i> Nuevo</button>
												</div>

											</div>

										</div>
									</form>
									<div class="datos_ajax_delete"></div><!-- Datos ajax Final -->
									<div class='outer_div'></div><!-- Carga los datos ajax -->

								</div>
							</div>
						</div>
					</div>


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

	<div id="nuevoGastoo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Nuevo Gastos</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="guardar_gasto" name="guardar_gasto">
						<div id="resultados_ajax"></div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="referencia" class="control-label">Referencia:</label>
									<input type="text" class="form-control" id="referencia" name="referencia"  autocomplete="off">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="monto" class="control-label">Monto Factura</label>	
									<input type="hidden" class="form-control" id="sucursal" name="sucursal" value='<?php echo $sucursal;?>'>
								
									<input type="text" class="form-control" id="monto" name="monto" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="descripcion" class="control-label">Descripción</label>
									<textarea class="form-control"  id="descripcion" name="descripcion" maxlength="255"  autocomplete="off" required></textarea>
								</div>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-primary waves-effect waves-light"  id="guardar_datos">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>



	<?php require 'includes/footer_start.php'
?>
	<!-- ============================================================== -->
	<!-- Todo el codigo js aqui
	<!-- ============================================================== -->
	<script type="text/javascript" src="../../js/gastos.js"></script>
	<script>
        function insertar_gastos(iduser)
        { 
            var id=inputValue = document.getElementById("mod_id").value;
            var monto=inputValue = document.getElementById("mod_monto").value;
            var descripcion=inputValue = document.getElementById("mod_descripcion").value;
            var referencia=inputValue = document.getElementById("mod_referencia").value;
            
            $.ajax({
                type:'POST',
                url: '../ajax/actualizar_gasto.php',
                data: {id:id,monto:monto,descripcion:descripcion,referencia:referencia, },
                success:function(data){ 
		           update(1);	
                   $('#editarGastoo').modal('hide');
                   $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
                   $('.modal-backdrop').remove();	            
	                swal("Exito", "Egreso Actualizado", "success"); 
                },
                error:function(data,e){ swal("Error",data.responseText, "error");  }
            
            });
        }
    </script>
	<?php require 'includes/footer_end.php'
?>

