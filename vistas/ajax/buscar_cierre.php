<?php

/*-------------------------
Punto de Ventas
---------------------------*/
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once "../db.php";
require_once "../php_conexion.php";
//Inicia Control de Permisos
include "../permisos.php";
$user_id = $_SESSION['id_users'];
get_cadena($user_id);
$modulo = "Gastos";
permisos($modulo, $cadena_permisos);
//Finaliza Control de Permisos
//Archivo de funciones PHP
require_once "../funciones.php";
$id_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);

$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    // escaping, additionally removing everything that could be (html/javascript-) code
    
    $q        = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $aColumns = array('fecha'); //Columnas de busqueda
    $sTable   = "cierre";
    $sWhere   = "";
    if ($_GET['q'] != "") {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    }
    $sWhere .= " order by fecha";
    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../html/cierre.php';
    //main query to fetch the data
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    //loop through fetched data
    if ($numrows > 0) {

        ?>
        	

        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <tr  class="info">
                    <th>Id</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Efectivo</th>
                    <th>Diferencia</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th class='text-right'>Acciones</th>

                </tr>
                <?php
while ($row = mysqli_fetch_array($query)) {
            $idcierre          = $row['idcierre'];
            $fecha              = $row['fecha'];
            $monto              = $row['monto'];
            $efectivo           = $row['efectivo'];
            $diferencia         = $row['diferencia'];
            $estado             = $row['estado'];
            $usuario            = $row['idusuario'];;

            ?>

    <input type="hidden" value="<?php echo $fecha; ?>" id="referencia_egreso<?php echo $fecha; ?>">
    <input type="hidden" value="<?php echo $montoo; ?>" id="descripcion_egreso<?php echo $monto ?>">
    <input type="hidden" value="<?php echo $efectivo; ?>" id="monto<?php echo $efectivo; ?>">

    <tr>
        <td><span class="badge badge-purple"><?php echo $idcierre; ?></span></td>
        <td><?php echo $fecha; ?></td>
        <td><?php echo $monto; ?></td>
        <td><?php echo $efectivo ?></td>
        <td><?php echo $diferencia; ?></td>
        <td><?php echo $estado; ?></td>
        <td><?php echo $usuario; ?></td>

        <td >
            <div class="btn-group dropdown">
                <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                <div class="dropdown-menu dropdown-menu-right">                  
                   <a class="dropdown-item" href="#"  onclick="abri('<?php echo $idcierre; ?>');"><i class='fa fa-edit'></i> Ver Facturas</a>            
               </div>
           </div>

       </td>

   </tr>
   <?php
}
        ?>
<tr>
    <td colspan="7">
        <span class="pull-right">
            <?php
echo paginate($reload, $page, $total_pages, $adjacents);
        ?></span>
        </td>
    </tr>
</table>
</div>



<div id="verCierre" class="modal fade" tabindex="-1"  aria-hidden="true" style="display: none;">

		<div class="modal-dialog modal-lg" style="  max-width: 100%; width: 75%;" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"></i> Ventas del Cierre </h4>
				</div>
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
                            include 'conexionpdo.php';
                            $pdo = new Conexion();
                        
                        
                            $sql = $pdo->prepare("select * from facturas_ventas LEFT JOIN clientes on 
                            facturas_ventas.id_cliente=clientes.id_cliente left JOIN users on facturas_ventas.id_vendedor=users.id_users 
                            where idcierre=:id");
                            $sql->bindValue(':id',$idcierre);
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
                                    $simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
                                ?>
                            <tr>
                                <td><label class='badge badge-purple'><?php echo $numero_factura; ?></label></td>
                                <td><?php echo $fecha. $idcierre; ?></td>
                                <td><?php echo $nombre_cliente; ?></td>
                                <td><?php echo $nit_cliente; ?></td>
                                <td><?php echo $nombre_vendedor; ?></td>
                                <td><span class="badge <?php echo $label_class; ?>"><?php echo $text_estado; ?></span></td>
                                <td class='text-left'><b><?php echo $simbolo_moneda . '' . number_format($total_venta, 2); ?></b></td>
                               

                            </tr>
                            <?php
                                }
                            ?>
                            <tr>
                                <td colspan="7">
                                    <span class="pull-right">
                                        <?php echo paginate($reload, $page, $total_pages, $adjacents);
                                        ?>
                                     </span>
                                </td>
                            </tr>
                        </table>
                    </div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

    <script>
        function abri(a)
        { 

            $('#verCierre').modal({ show:true });
        }
    </script>


<?php
}
//Este else Fue agregado de Prueba de prodria Quitar
  
// fin else
}
?>