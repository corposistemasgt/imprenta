<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}

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
									Detalles de Factura # <?php echo $_GET['numero']?>
								</h3>
									
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">
								<?php
                    include '../ajax/conexionpdo.php';
                    $pdo = new Conexion(); 
					$sql = $pdo->prepare("select factura_nombre_cliente,factura_nit_cliente, serie_factura, guid_factura,
					numero_certificacion, fechaCertificacion,monto_factura from facturas_ventas where id_factura =:id");
                    $sql->bindValue(':id',$_GET['id']);
                    $sql ->execute();
                    $sql->setFetchMode(PDO::FETCH_ASSOC);
                        foreach ($sql as $row) 
						{
                            $nombre        = $row['factura_nombre_cliente'];
                            $nit           = $row['factura_nit_cliente'];
                            $serie         = $row['serie_factura'];
                            $numero        = $row['numero_certificacion'];    
							$autorizacion  = $row['guid_factura'];
							$fecha         = $row['fechaCertificacion'];
							$monto         = $row['monto_factura']; 
						}					
						
					?>
                


							
                    <div class="row">
						<div class="col-md-9">
                        	<label for="field-2" class="control-label">Nombre Cliente:</label>
						    <label for="field-2" class="control-label"><?php echo $nombre;?></label>                   
                        </div>
						<div class="col-md-3">                   
                        	<label for="field-2" class="control-label">Nit:</label>
							<label for="field-2" class="control-label"><?php echo $nit; ?>:</label>                           
                        </div>
					</div>

						<?php 
						
						if(strcmp($autorizacion,'')!=0)
						{
						?>

						<div class="row">
                       
						
						<div class="col-md-2">
                        	<label for="field-2" class="control-label">Serie:</label>
						    <label for="field-2" class="control-label"><?php echo $serie;?></label>                   
                        </div>
						<div class="col-md-2">                   
                        	<label for="field-2" class="control-label">Numero:</label>
							<label for="field-2" class="control-label"><?php echo $numero;?></label>                           
                        </div>
						<div class="col-md-5">                   
                        	<label for="field-2" class="control-label">Autorizacion:</label>
							<label for="field-2" class="control-label"><?php echo $autorizacion;?></label>                           
                        </div>
						<div class="col-md-3">                   
                        	<label for="field-2" class="control-label">Fecha:</label>
							<label for="field-2" class="control-label"><?php $date = strtotime($fecha); echo date('d/m/Y h:i:s', $date);?></label>                           
                        </div>
                    </div>



						<?php	
						}
						?>		
					
						
					

					
                             
                    
                              
		<form class="form-horizontal" method="post" id="guardar_gasto" name="guardar_gasto">			
            <div class="table-responsive" style="padding-top: 20px; padding-right: 20px; padding-left: 20px;">
                <table class="table table-sm table-striped" >
                    <tr  class="info">
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Descuento</th>
                        <th>Precio Venta</th>
                    </tr>
                    <?php
                    
                
                
                    $sql = $pdo->prepare("select nombre_producto,cantidad,desc_venta,precio_venta from detalle_fact_ventas,productos 
                    where detalle_fact_ventas.id_producto =productos.id_producto and id_factura =:id");
                    $sql->bindValue(':id',$_GET['id']);
                    $sql ->execute();
                    $sql->setFetchMode(PDO::FETCH_ASSOC);

                        foreach ($sql as $row) {
                            $nombre     = $row['nombre_producto'];
                            $cantidad   = $row['cantidad'];
                            $descuento   = $row['desc_venta'];
                            $precio     = $row['precio_venta'];                        
                            $simbolo_moneda = "Q. ";
                        ?>
                    <tr>
                        
                        <td><?php echo $nombre ?></td>
                        <td><?php echo $cantidad; ?></td>
                        <td><?php echo $simbolo_moneda . '' . number_format($descuento, 2); ?></td>
                        <td><?php echo $simbolo_moneda . '' . number_format($precio, 2); ?></td>                    
                    </tr>
                    <?php
                        }

						$sql = $pdo->prepare("select producto,precio,cantidad from productoslibres 
						where idfactura =:id");
						$sql->bindValue(':id',$_GET['id']);
						$sql ->execute();
						$sql->setFetchMode(PDO::FETCH_ASSOC);
	
							foreach ($sql as $row) {
								$nombre     = $row['producto'];
								$cantidad   = $row['cantidad'];
								$descuento   = '0';
								$precio     = $row['precio'];                        
								$simbolo_moneda = "Q. ";
							?>
						<tr>
							
							<td><?php echo $nombre ?></td>
							<td><?php echo $cantidad; ?></td>
							<td><?php echo $simbolo_moneda . '' . number_format($descuento, 2); ?></td>
							<td><?php echo $simbolo_moneda . '' . number_format($precio, 2); ?></td>                    
						</tr>
						<?php
							}
                    ?>
                    
                </table>
           
        </form>
									</div>

									
								</div>

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
<!-- Todo el codigo js aqui -->
<!-- ============================================================== -->
<?php require 'includes/footer_end.php'
?>

