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
$modulo = "Productos";
permisos($modulo, $cadena_permisos);
//Finaliza Control de Permisos
$count      = mysqli_query($conexion, "select MAX(codigo_producto) as codigo from productos");
$rw         = mysqli_fetch_array($count);

//consulta para elegir el impuesto en la modal
$query    = $conexion->query("select * from impuestos");
$impuesto = array();
$idprede=0;
$prede='';
while ($r = $query->fetch_object()) {$impuesto[] = $r;}
$query    = $conexion->query("select id_perfil,giro_empresa  from  users,perfil where id_users =".$user_id." 
and users.sucursal_users =perfil.id_perfil ");
while ($rw = mysqli_fetch_array($query))
{
	$idprede=$rw['id_perfil'];
	$prede=$rw['giro_empresa'];
}
?>

<?php require 'includes/header_start.php';?>

<?php require 'includes/header_end.php';?>

<!-- Begin page -->
<div id="wrapper" class="forced enlarged"> <!-- DESACTIVA EL MENU -->

	<?php require 'includes/menu.php';?>

	<!-- ============================================================== -->
	<!-- Start right Content here -->
	<!-- ============================================================== -->
	<div class="content-page">
		<!-- Start content -->
		<div class="content">
			<div class="container">
				<?php if ($permisos_ver == 1) {
    ?>
					<div class="col-lg-12">
						<div class="portlet">
							<div class="portlet-heading bg-secondtabla">
								<h3 class="portlet-title">
									Productos
								</h3>
								
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">

									<?php
if ($permisos_editar == 1) {
        include '../modal/registro_producto.php';
        include "../modal/editar_producto.php";
        include "../modal/eliminar_producto.php";
    }
    ?>

									<form class="form-horizontal" role="form" id="datos_cotizacion">
										<div class="form-group row">
											<div class="col-md-3">
												<div class="input-group">
													<input type="text" class="form-control" id="q" placeholder="Código o Nombre" onkeyup='load(1);'>
												</div>
											</div>
												<div class="col-md-3">
												<div class="input-group">
													<select name='categoria' id='categoria' class="form-control" onchange="load(1);">
														<option value="">-- Selecciona Linea --</option>
														<option value="">Todos</option>
														<?php

																$query_categoria = mysqli_query($conexion, "select * from lineas order by nombre_linea");
																while ($rw = mysqli_fetch_array($query_categoria)) {
														?>
																		<option value="<?php echo $rw['id_linea']; ?>"><?php echo $rw['nombre_linea']; ?></option>
																		<?php
																}
  																		?>
													</select>
													<span class="input-group-btn">
														<button class="btn btn-outline-info btn-rounded waves-effect waves-light" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
													</span>
												</div>
												
											</div>
											<div class="col-md-3">
												<div class="input-group">
													<select name='sucursals' id='sucursals' class="form-control" onchange="load(1);" >
														<option value="<?php echo $idprede;?>">-- Selecciona Sucursal --</option>
														
														<?php

														$query_categoria = mysqli_query($conexion, "select * from perfil order by giro_empresa ");
														while ($rw = mysqli_fetch_array($query_categoria)) {
															?>
															<option value="<?php echo $rw['id_perfil']; ?>"><?php echo $rw['giro_empresa']; ?></option>
															<?php
															}
																?>
													</select>
													<span class="input-group-btn">
														<button class="btn btn-info waves-effect waves-light" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
													</span>
												</div>
											</div>

												<div class="col-md-3">
													<div class="btn-group pull-right">
														<button type="button" class="btn btn-success btn-rounded waves-effect waves-light" data-toggle="modal" data-target="#nuevoProducto"><i class="fa fa-plus"></i> Agregar</button>
													</div>
													
													<div class="btn-group pull-left">
														<?php if ($permisos_editar == 1) {?>
														<div class="btn-group dropup">
															<button aria-expanded="false" class="btn btn-outline-default btn-rounded waves-effect waves-light" data-toggle="dropdown" type="button">
																<i class='fa fa-file-text'></i> Reporte
																<span class="caret">
																</span>
															</button>
															<div class="dropdown-menu">
																<a class="dropdown-item" href="#" onclick="reporte();">
																	<i class='fa fa-file-pdf-o'></i> PDF
																</a>
																<a class="dropdown-item" href="#" onclick="reporte_excel();">
																	<i class='fa fa-file-excel-o'></i> Excel
																</a>
															</div>
														</div>
														<?php }?>
													</div>
												

											</div>
										</form>
									



									</div>
									<div class="datos_ajax_delete"></div><!-- Datos ajax Final -->
										<div class='outer_div'></div><!-- Carga los datos ajax -->
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
							
						<div class="card">
							<div class="card-header">
								<b>Importar Inventario desde Excel</b>
							</div>
							<div class="card-body">
							
						<div class="row">
							<div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
								<input class="form-control" type="file" id="txt_archivo" name="txt_archivo" accept=".csv, .xlsx, .xls">
							</div>
							<div class="col-sm-6 col-md-6 col-lg-2 col-xl-2">
								<button class="btn btn-danger" style="width:100%" onclick="CargarExcel()">
									Cargar Excel
								</button>
							</div>
							<div class="col-sm-6 col-md-6 col-lg-2 col-xl-2 text-right">
								<button class="btn btn-primary" style="width:100%" disabled id="btn_guardar" onclick="RegistrarExcel()">
									Guardar Datos
								</button>
							</div>
						</div>

								<div class="col-lg-12" id="div_tabla"><br>
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

	<?php require 'includes/footer_start.php'
	?>
	<!-- ============================================================== -->
	<!-- Todo el codigo js aqui-->
	<!-- ============================================================== -->
	<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="../../js/productos.js?ver=1.1"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
		function precio_venta(){
			var profit = $("#utilidad").val();
			var buying_price = $("#costo").val();

			var parametros = {"utilidad":profit,"costo":buying_price};
			$.ajax({
				dataType: "json",
				type:"POST",
				url:'../ajax/precio.php',
				data: parametros,
				success:function(data){
          //$("#datos").html(data).fadeIn('slow');
          $.each(data, function(index, element) {
          	var precio= element.precio;
          	$("#precio").val(precio);
          });


      }
  })
		}
		function precio_venta_edit(){
			var profit = $("#mod_utilidad").val();
			var buying_price = $("#mod_costo").val();

			var parametros = {"mod_utilidad":profit,"mod_costo":buying_price};
			$.ajax({
				dataType: "json",
				type:"POST",
				url:'../ajax/precio.php',
				data: parametros,
				success:function(data){
          //$("#datos").html(data).fadeIn('slow');
          $.each(data, function(index, element) {
          	var mod_precio= element.mod_precio;
          	$("#mod_precio").val(mod_precio);
          });


      }
  })
		}

	</script>
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
		function upload_image(product_id){
			$("#load_img").text('Cargando...');
			var inputFileImage = document.getElementById("imagefile");
			var file = inputFileImage.files[0];
			var data = new FormData();
			data.append('imagefile',file);



			$.ajax({
					url: "../ajax/imagen_product_ajax.php",        // Url to which the request is send
					type: "POST",             // Type of request to be send, called as method
					data: data, 			  // Data sent to server, a set of key/value pairs (i.e. form fields and values)
					contentType: false,       // The content type used when sending data to the server.
					cache: false,             // To unable request pages to be cached
					processData:false,        // To send DOMDocument or non processed data file it is set to false
					success: function(data)   // A function to be called if request succeeds
					{
						console.log(data);
						$("#load_img").html(data);

					}
				});

		}
		function upload_image_mod(id_producto){
			$("#load_img_mod").text('Cargando...');
			var inputFileImage = document.getElementById("imagefile_mod");
		
			var file = inputFileImage.files[0];
			
			var data = new FormData();
			data.append('imagefile_mod',file);
			data.append('id_producto',id_producto);



			$.ajax({
					url: "../ajax/imagen_product_ajax2.php",        // Url to which the request is send
					type: "POST",             // Type of request to be send, called as method
					data: data, 			  // Data sent to server, a set of key/value pairs (i.e. form fields and values)
					contentType: false,       // The content type used when sending data to the server.
					cache: false,             // To unable request pages to be cached
					processData:false,        // To send DOMDocument or non processed data file it is set to false
					success: function(data)   // A function to be called if request succeeds
					{
						$("#load_img_mod").html(data);

					}
				});

		}
	</script> 
	<script>
		function carga_img(id_producto) {
			$(".outer_img").load("../ajax/img.php?id_producto=" + id_producto);
		}
		function reporte_excel(){
			var q=$("#q").val();
			var sucursal = $("#sucursals").val();
			console.log("-"+sucursal+"");
			window.location.replace("../excel/rep_productos.php?q="+q+"&sucursalid="+sucursal);
			//VentanaCentrada('../excel/rep_gastos.php?daterange='+daterange+"&employee_id="+employee_id,'Reporte','','500','25','true');+"&tipo="+tipo
		}
		function reporte(){
				var daterange=$("#range").val();
				var categoria=$("#categoria").val();
				VentanaCentrada('../pdf/documentos/rep_productos.php?daterange='+daterange+"&categoria="+categoria,'Reporte','','800','600','true');
			}
	</script>

	<script>
		$('input[type="file"]').on('change', function(){
			var ext = $( this ).val().split('.').pop();
			if ($( this ).val() != '') {
			if(ext == "xls" || ext == "xlsx" || ext == "csv"){
			}
			else
			{
				$( this ).val('');
				Swal.fire("Mensaje De Error","Extensión no permitida: " + ext+"","error");
			}
			}
		});

		function CargarExcel(){
			var excel = $("#txt_archivo").val();
			if(excel == "")
			{
				return Swal.fire("Advertencia","No ha seleccionado ningún archivo excel","warning");
			}
			var formData = new FormData();
			var files = $("#txt_archivo")[0].files[0];
			formData.append('archivoexcel',files);
			$.ajax({
				url:'importar_excel_ajax.php',
				type: 'POST',
				data: formData,
				contentType: false,
				processData: false,
				success: function(resp){
					$("#div_tabla").html(resp);
					document.getElementById('btn_guardar').disabled = false;
				}
			});
			return false;
		}
	</script>
	<script>
		
		function RegistrarExcel()
		{
			var contador = 0;
			var arreglo_categoria = new Array();
			var arreglo_id = new Array();
			var arreglo_nombre = new Array();
			var arreglo_descripcion = new Array();
			var arreglo_iva = new Array();
			var arreglo_costo = new Array();
			var arreglo_precio1 = new Array();
			var arreglo_precio2 = new Array();
			var arreglo_precio3 = new Array();
			var arreglo_precio4 = new Array();
			var arreglo_cantInventario = new Array();
			var arreglo_cantMin = new Array();
			var arreglo_esGen = new Array();
			var arreglo_ven = new Array();
			var arreglo_prov = new Array();

			$("#tabla_detalle tbody#tbody_tabla_detalle tr").each(function(){
				arreglo_categoria.push($(this).find('td').eq(0).text().trim());
				arreglo_id.push($(this).find('td').eq(1).text().trim());
				arreglo_nombre.push($(this).find('td').eq(2).text().trim());
				arreglo_descripcion.push($(this).find('td').eq(3).text().trim());
				arreglo_iva.push($(this).find('td').eq(4).text().trim());
				arreglo_costo.push($(this).find('td').eq(5).text().trim());
				arreglo_precio1.push($(this).find('td').eq(6).text().trim());
				arreglo_precio2.push($(this).find('td').eq(7).text().trim());
				arreglo_precio3.push($(this).find('td').eq(8).text().trim());
				arreglo_precio4.push($(this).find('td').eq(9).text().trim());
				arreglo_cantInventario.push($(this).find('td').eq(10).text().trim());
				arreglo_cantMin.push($(this).find('td').eq(11).text().trim());
				arreglo_esGen.push($(this).find('td').eq(12).text().trim());
				arreglo_ven.push($(this).find('td').eq(13).text().trim());
				arreglo_prov.push($(this).find('td').eq(14).text().trim());
				contador++;
			});
			alert(contador);
			if(contador == 0)
			{
				return Swal.fire("Advertencia","el archivo no tiene datos","warning");
			}

	
			var idproductos = arreglo_id.toString();
			var categorias = arreglo_categoria.toString();
			var nombres = arreglo_nombre.toString();
			var descripciones = arreglo_descripcion.toString();
			var ivas = arreglo_iva.toString();
			var costos = arreglo_costo.toString();
			var precios1 = arreglo_precio1.toString();
			var precios2 = arreglo_precio2.toString();
			var precios3 = arreglo_precio3.toString();
			var precios4 = arreglo_precio4.toString();
			var inventarios = arreglo_cantInventario.toString();
			var minimos = arreglo_cantMin.toString();
			var esgenerico = arreglo_esGen.toString();
			var ven = arreglo_ven.toString();
			var prov = arreglo_prov.toString();
			$.ajax({
				url: 'controlador_registros.php',
				type: 'post',
				data:{
					 id: idproductos,
					 cat: categorias,
					 nomb: nombres,
					 desc: descripciones,
					 iva: ivas,
					 cost: costos,
					 p1: precios1,
					 p2: precios2,
					 p3: precios3,
					 p4: precios4,
					 inv: inventarios,
					 min: minimos,
					 esgen: esgenerico,
					 ven: ven,
					 prov: prov

				},
				success:function(){
					//alert("success");
				},
				error: function(){
					//alert("error");
				}
			})
			alert(idproductos+" - "+esgenerico);



			
		}
	</script>
<?php require 'includes/footer_end.php'
?>

