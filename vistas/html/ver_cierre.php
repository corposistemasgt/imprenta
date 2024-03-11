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
									Facturas de Cierre de Caja
								</h3>
									
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">

								<form class="form-horizontal" method="post" id="guardar_gasto" name="guardar_gasto">			
            <div class="table-responsive" style="padding-top: 20px; padding-right: 20px; padding-left: 20px;">
                <table class="table table-sm table-striped" >
                    <tr  class="info">
                        <th># Factura</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>NIT</th>
                        <th>Vendedor</th>
                        <th>Estado</th>
                        <th class='text-center'>Total</th>
                    </tr>
                    <?php
                    include '../ajax/conexionpdo.php';
                    $pdo = new Conexion();
                
                
                    $sql = $pdo->prepare("select * from facturas_ventas LEFT JOIN clientes on 
                    facturas_ventas.id_cliente=clientes.id_cliente left JOIN users on facturas_ventas.id_vendedor=users.id_users 
                    where idcierre=:id");
                    $sql->bindValue(':id',$_GET['id']);
                    $sql ->execute();
                    $sql->setFetchMode(PDO::FETCH_ASSOC);

                        foreach ($sql as $row) {
                            $id_factura       = $row['id_factura'];
                            $numero_factura   = $row['numero_factura'];
                            $fecha            = date("d/m/Y", strtotime($row['fecha_factura']));
                            $nit_cliente = "";
                            $booleanClienteBD = true;
                            if( !is_null($row['factura_nombre_cliente']) && !is_null($row['factura_nit_cliente']) && strtoupper($row['factura_nit_cliente']) != "CF")
                            {
                                $nombre_cliente = $row['factura_nombre_cliente'];
                                $nit_cliente = $row['factura_nit_cliente'];
                                $booleanClienteBD = false;

                            }
                            else
                            {
                                $nombre_cliente   = $row['factura_nombre_cliente'];
                                $nit_cliente = "CF";
                            }
    
                            $telefono_cliente = $row['telefono_cliente'];
                            $email_cliente    = $row['email_cliente'];
                            $nombre_vendedor  = $row['nombre_users'] . " " . $row['apellido_users'];
                            $estado_factura   = $row['estado_factura'];
                            $estaAnulada      = $row['estado_documento'];
                            if ($estado_factura == 1) 
                            {
                                $text_estado = "Pagada";
                                $label_class = 'badge-success';
                            } else 
                            {
                                $text_estado = "Pendiente";
                                $label_class = 'badge-danger';
                            }
     
                            if($estaAnulada == "anulado")
                            {
                                $text_estado = "anulada";
                                $label_class = 'badge-danger';
                            }    
                            $total_venta    = $row['monto_factura'];
                            $simbolo_moneda = "Q. "
                        ?>
                    <tr>
                        <td><label class='badge badge-purple'><?php echo $numero_factura; ?></label></td>
                        <td><?php echo $fecha ?></td>
                        <td><?php echo $nombre_cliente; ?></td>
                        <td><?php echo $nit_cliente; ?></td>
                        <td><?php echo $nombre_vendedor; ?></td>
                        <td><span class="badge <?php echo $label_class; ?>"><?php echo $text_estado; ?></span></td>
                        <td class='text-left'><b><?php echo $simbolo_moneda . '' . number_format($total_venta, 2); ?></b></td>
						<td class="text-center">
                          <div class="btn-group dropdown">
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                            <div class="dropdown-menu dropdown-menu-right">                            
                               <a class="dropdown-item" href="#" onclick="print_ticket('<?php echo $id_factura; ?>')"><i class='fa fa-print'></i> Imprimir Ticket</a>
                               <a class="dropdown-item" href="#" onclick="pedirNit('<?php echo $id_factura; ?>');"><i class='fa fa-print'></i> Imprimir Factura</a>                           
							   <a class="dropdown-item" href="#" onclick="anular_factura('<?php echo $id_factura; ?>', '<?php echo $numero_factura; ?>');"><i class='fa fa-print'></i> Anular Documento</a>
            				   <a class="dropdown-item" href="ver_factura.php?id=<?php echo $id_factura; ?>&numero=<?php echo $numero_factura;?>"><i class='fa fa-list-alt'></i> Ver Detalles de Facturacion</a>
                               


                           </div>
                       </div>

                   </td>

                    </tr>
                    <?php
                        }
                    ?>
                   
                </table>
            </div>
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

