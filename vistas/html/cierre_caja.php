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
$modulo = "Reportes";
permisos($modulo, $cadena_permisos);
//Finaliza Control de Permisos
$Reportes = 1;
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
				<?php if ($permisos_ver == 1) { ?>

	
					<div class="col-lg-12">
						<div class="portlet">
							<div class="portlet-heading bg-secondtabla">
								<h3 class="portlet-title">
									Cierre de Caja
								</h3>
								
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">
					
									<form class="form-horizontal" role="form" id="datos_cotizacion">

										<div class="form-group row" >


											<input style="visibility:collapse" type="text" class="form-control daterange pull-right" value="<?php echo "01" . date('/01/2023') . ' - ' . date('d/m/Y'); ?>" id="range" readonly>
											<div class="col-xs-5">
												<div class="input-group">
													<select id="employee_id" class='form-control' onchange="load(1);">
														<option value="">Selecciona Cajero</option>
														<option value="">Sucursal Actual</option>
														<?php $sql1 = mysqli_query($conexion, "select * from users");
    														while ($rw1 = mysqli_fetch_array($sql1)) {
        															?>
															<option value="<?php echo $rw1['id_users'] ?>"><?php echo $rw1['nombre_users'] . ' ' . $rw1['apellido_users']; ?></option>
															<?php }?>
													</select>
													<span class="input-group-btn">
														<button class="btn btn-primary" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
													</span>
												</div>

												
											</div>

											<div class="col-xs-1">
												<div id="loader" class="text-center"></div>
											</div>

											<div class="col-lg-12" style="text-align: right;">
																																				
												<button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#nuevaApertura"><i class="fa fa-money"></i>  Aperturar Caja</button>																								
												<button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#abrircaja"><i class="fa fa-money"></i>  Abrir CashBOX</button>																								
												
											</div>

										</div>
									</form>
									<div class="datos_ajax_delete"></div><!-- Carga los datos ajax -->
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

			
			<!-- end container -->
		</div>
		<!-- end content -->
	
		<?php require 'includes/pie.php';?>

	</div>
	<!-- ============================================================== -->
	<!-- End Right content here -->
	<!-- ============================================================== -->


</div>


<div class="modal fade" id="nuevaApertura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='fa fa-edit'></i> Inicializar Caja</h4>
            </div>
            <div class="modal-body" align="center">           
				<div  class="col-md-12">
					<div class="form-group">
						<label  for="fiscal"> Efectivo</label>
						<input type="text" class="form-control" autocomplete="off" id="txtefectivo" name="txtefectivo">
					</div>
				</div>
            </div>
            <div class="modal-footer">
                <button type="button" id="inicializar" class="btn btn-primary btn-block btn-lg waves-effect waves-light" onclick="inicializar(<?php echo $_SESSION['id_users']; ?>,<?php echo $sucursal ?>);" accesskey="t" ><span class="fa fa-list-alt"></span> Inicializar</button><br>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="abrircaja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='fa fa-edit'></i> Ingresa la Contraseña</h4>
            </div>
            <div class="modal-body" align="center">           
				<div  class="col-md-12">
					<div class="form-group">
						<label  for="fiscal"> Password</label>
						<input type="text" class="form-control" autocomplete="off" id="txtpass" name="txtpass">
					</div>
				</div>
            </div>
            <div class="modal-footer">
                <button type="button" id="inicializar" class="btn btn-primary btn-block btn-lg waves-effect waves-light" onclick="abrir();" accesskey="t" ><span class="fa fa-list-alt"></span> Abrir</button><br>
            </div>
        </div>
    </div>
</div>
<!-- END wrapper -->

<?php require 'includes/footer_start.php'
?>
<!-- ============================================================== -->
<!-- Todo el codigo js aqui -->
<!-- ============================================================== -->
<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
<script>
	$(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
	$(function() {
		load(1);

//Date range picker
$('.daterange').daterangepicker({
	buttonClasses: ['btn', 'btn-sm'],
	applyClass: 'btn-success',
	cancelClass: 'btn-default',
	locale: {
		format: "DD/MM/YYYY",
		separator: " - ",
		applyLabel: "Aplicar",
		cancelLabel: "Cancelar",
		fromLabel: "Desde",
		toLabel: "Hasta",
		customRangeLabel: "Custom",
		daysOfWeek: [
		"Do",
		"Lu",
		"Ma",
		"Mi",
		"Ju",
		"Vi",
		"Sa"
		],
		monthNames: [
		"Enero",
		"Febrero",
		"Marzo",
		"Abril",
		"Mayo",
		"Junio",
		"Julio",
		"Agosto",
		"Septiembre",
		"Octubre",
		"Noviembre",
		"Diciembre"
		],
		firstDay: 1
	},
	opens: "right"

});
});
	function load(page){
    var range=$("#range").val();
    var employee_id=$("#employee_id").val();
    var parametros = {"action":"ajax","page":page,'range':range,'employee_id':employee_id};
    $("#loader").fadeIn('slow');
    $.ajax({
      url:'../ajax/rep_cierre_caja.php',
      data: parametros,
      beforeSend: function(objeto){
        $("#loader").html("<img src='../../img/ajax-loader.gif'>");
      },
      success:function(data){
        $(".outer_div").html(data).fadeIn('slow');
        $("#loader").html("");
      }
    })
  }



  function inicializar(idusuario,sucu)
        { 
			console.log("inicializo");
            var efectivo=inputValue = document.getElementById("txtefectivo").value;
            console.log("inicializo"+idusuario+" "+efectivo);
            $.ajax({
                type:'POST',
                url: '../ajax/iniciar_caja.php',
                data: {efectivo:efectivo,idusuario:idusuario,sucursal:sucu },
                success:function(data){ 
		           //update(1);	
				   console.log(data.responseText);
                   $('#nuevaApertura').modal('hide');
                   $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
                   $('.modal-backdrop').remove();	            
	                swal("Exito", "Caja Inicializada", "success"); 
                },
                error:function(data,e){ swal("Error",data.responseText, "error"); console.log(data.responseText); }
            
            });
        }
		function abrir()
        { 
			console.log("inicializo");
            var pass=inputValue = document.getElementById("txtpass").value;
            
            $.ajax({
                type:'POST',
                url: '../ajax/abrir_caja.php',
                data: {pass:pass },
                success:function(data){ 
		           //update(1);	
				   console.log("corpoprint-caja"+data.trim()); 	
					//var newpage =window.open("http://corpo-sistemas.com/corpoprint_caja/"+data,"_blank")     
	                swal("Exito", "Caja Abierta", "success"); 
                },
                error:function(data,e){ swal("Error",data.responseText, "error"); console.log(data.responseText); }
            
            });
        }

</script>
<script>
  function reporte(){
    var daterange=$("#range").val();
    var employee_id=$("#employee_id").val();

    VentanaCentrada('../pdf/documentos/rep_corte_caja.php?daterange='+daterange+"&employee_id="+employee_id,'Reporte','','800','600','true');
  }
</script>

<?php require 'includes/footer_end.php'
?>

