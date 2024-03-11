<?php
/*-------------------------
Punto de Ventas
---------------------------*/
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once "../db.php"; //Contiene las variables de configuracion para conectar a la base de datos
require_once "../php_conexion.php"; //Contiene funcion que conecta a la base de datos
//Archivo de funciones PHP
require_once "../funciones.php";
//Inicia Control de Permisos
include "../permisos.php";
$user_id = $_SESSION['id_users'];

get_cadena($user_id);
$user_sucursal = get_Sucursal($user_id);
//echo("<br> sucursal del usuario = ".$user_sucursal."");
$modulo = "Ventas";
permisos($modulo, $cadena_permisos);
//Finaliza Control de Permisos
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $daterange      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
    // escaping, additionally removing everything that could be (html/javascript-) code
    $q      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $sTable = "detalle_traslado left join tbl_traslado on detalle_traslado.id_traslado = tbl_traslado.id left join productos on detalle_traslado.id_producto = productos.id_producto
     left join users on tbl_traslado.id_usuario = users.id_users left join perfil on tbl_traslado.id_sucursal_destino = perfil.id_perfil ";
     //$user_sucursal = 0;
     if($user_sucursal != 0){
        $sWhere = " WHERE tbl_traslado.id_sucursal_origen = '".$user_sucursal."' ";
        /*if ($_GET['q'] != "") {
            $sWhere .= " AND (tbl_traslado.id = '$q' OR perfil.giro_empresa like '%$q%' )";
        }*/
    }else{
        /*if ($_GET['q'] != "") {
            //echo("la q no esta vacia ");
            $sWhere .= " AND (tbl_traslado.id = '$q' OR perfil.giro_empresa like '%$q%' )";
        }*/
    }

    if (!empty($daterange)) {
        list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
        list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
        $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
        list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
        $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";

        $sWhere .= " and tbl_traslado.fecha between '$fecha_inicial' and '$fecha_final' ";
    }
   

    //facturas_ventas.id_cliente=clientes.id_cliente and <- esto estaba
    $sWhere .= "" ;//" WHERE  facturas_ventas.id_vendedor=users.id_users";
    /*if ($_GET['q'] != "") {
        $sWhere .= " and  (clientes.nombre_cliente like '%$q%' or facturas_ventas.numero_factura like '%$q%')";
    }*/

    $sWhere .= " order by tbl_traslado.id desc";
    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
    $sentencia = "SELECT count(*) AS numrows FROM $sTable  $sWhere";
    //echo("<br> $sentencia <br>");
    $count_query = mysqli_query($conexion, $sentencia);
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../reportes/facturas.php';
    //main query to fetch the data
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    //echo($sql."--sql123");
    $query = mysqli_query($conexion, $sql);
    //loop through fetched data
    if ($numrows > 0) {
        echo mysqli_error($conexion);
        ?>
        <div class="table-responsive">
          <table class="table table-sm table-striped">
             <tr  class="info">
                <th># Traslado</th>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Destino</th>
                <th>Usuario</th>
                <!--<th class='text-center'>Total</th>
                <th class='text-center'>Acciones</th>-->

            </tr>
            <?php
while ($row = mysqli_fetch_array($query)) {
            //$id_factura       = $row['id_factura'];
            $numero_factura   = $row['id_traslado'];//$id_factura;//$row['numero_factura'];
            $fecha            = date("d/m/Y", strtotime($row['fecha']));
            $nit_cliente = "";
            $booleanClienteBD = true;
            /*if( !is_null($row['factura_nombre_cliente']) && !is_null($row['factura_nit_cliente']) && strtoupper($row['factura_nit_cliente']) != "CF")
            {
                //echo("entra a isnull");
                $nombre_cliente = $row['factura_nombre_cliente'];
                $nit_cliente = $row['factura_nit_cliente'];
                $booleanClienteBD = false;

            }else{
                $nombre_cliente   = $row['factura_nombre_cliente'];
                $nit_cliente = "CF";
            }*/
            $nombre_cliente   = $row['nombre_producto'];
            //$telefono_cliente = $row['telefono_cliente'];
            //$email_cliente    = $row['email_cliente'];
            $canitdad         = $row['cantidad'];
            $nombre_vendedor  = $row['nombre_users'] . " " . $row['apellido_users'];
            $giro_empresa     =$row['giro_empresa'];
            //$estado_factura   = $row['estado_factura'];
            /*if ($estado_factura == 1) {
                $text_estado = "Pagada";
                $label_class = 'badge-success';} else {
                $text_estado = "Pendiente";
                $label_class = 'badge-danger';}*/
            //$total_venta    = $row['monto_factura'];
            $simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
            ?>
                        <tr>
                         <td><label class='badge badge-purple'><?php echo $numero_factura; ?></label></td>
                         <td><?php echo $fecha; ?></td>
                         <td><?php echo $nombre_cliente; ?></td>
                         <td><?php echo $canitdad; ?></td>
                         <td><?php echo $giro_empresa; ?></td>
                         <td><?php echo $nombre_vendedor; ?></td>
                         <!--<td><span class="badge <?php echo $label_class; ?>"><?php echo $text_estado; ?></span></td>
                         <td class='text-left'><b><?php echo $simbolo_moneda . '' . number_format($total_venta, 2); ?></b></td>
                         <td class="text-center">-->
                          <!--<div class="btn-group dropdown">
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                            <div class="dropdown-menu dropdown-menu-right">-->
                               <?php if ($permisos_editar == 1 || 1== 1) {?>
                               <!--<a class="dropdown-item" href="editar_venta.php?id_factura=<?php echo $id_factura; ?>"><i class='fa fa-edit'></i> Editar</a>-->
                               <!--<a class="dropdown-item" href="#" onclick="print_ticket('<?php echo $id_factura; ?>')"><i class='fa fa-print'></i> Imprimir Ticket</a>
                               <a class="dropdown-item" href="#" onclick="imprimir_factura('<?php echo $id_factura; ?>');"><i class='fa fa-print'></i> Imprimir Factura</a>-->
                               <!--<a class="dropdown-item" href="#" onclick="anular_factura('<?php echo $id_factura; ?>');"><i class='fa fa-print'></i> Anular Venta</a>-->
                               <?php }
            if ($permisos_eliminar == 1) {?>
           <!-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $row['id_factura']; ?>"><i class='fa fa-trash'></i> Anular Factura</a>
                               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $row['id_factura']; ?>"><i class='fa fa-trash'></i> Eliminar</a>-->
                               <?php }?>


                           </div>
                       </div>

                   </td>

               </tr>
               <?php
}
        ?>
           <tr>
              <td colspan=7><span class="pull-right"><?php
echo paginate($reload, $page, $total_pages, $adjacents);
        ?></span></td>
            </tr>
        </table>
    </div>
    <?php
}
//Este else Fue agregado de Prueba de prodria Quitar
    else {
        ?>
    <div class="alert alert-warning alert-dismissible" role="alert" align="center">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Aviso!</strong> No hay Registro de Traslados
  </div>
  <?php
}
// fin else
}
?>