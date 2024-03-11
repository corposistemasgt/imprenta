<?php
include "is_logged.php"; //Archivo comprueba si el usuario esta logueado
/* Connect To Database*/
require_once "../db.php";
require_once "../php_conexion.php";
#require_once "../libraries/inventory.php"; //Contiene funcion que controla stock en el inventario
//Inicia Control de Permisos
include "../permisos.php";
//Archivo de funciones PHP
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$action  = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {


    

    $daterange   = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
    $employee_id = intval($_REQUEST['employee_id']);
    $tables      = "cierre,users";
    $campos      = "*";
    $sWhere      = "";//and facturas_ventas.estado_factura <> 3
    $sWhere2     = "";
    if ($employee_id > 0) {
        $sWhere .= " idusuario = '" . $employee_id . "' and ";
    }
    if (!empty($daterange)) {
        list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
        list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
        $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
        list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
        $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";

        $sWhere.= " fecha between '$fecha_inicial' and '$fecha_final' ";
    }
    //$//sWhere .= " and cierre.idusuario=users.id_users  order by fecha";


    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables where $sWhere ");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../rep_cierres.php';
    //main query to fetch the data
    $query = mysqli_query($conexion,"select * from cierre left join users on cierre.idusuario =users.id_users  where ".$sWhere." order by fecha LIMIT 0,100;");
    //echo ;
    //loop through fetched data

    if ($numrows > 0) {
        ?>

        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-sm">
                <tr>
                <th>Id</th>
                    <th>Fecha y Hora</th>
                    <th>Monto</th>
                    <th>Efectivo</th>
                    <th>Diferencia</th>
                    <th>Usuario</th>
                    <th class='text-left'>Acciones</th>
                </tr>
                <?php

while ($row = mysqli_fetch_array($query)) {
    $idcierre          = $row['idcierre'];
    $fecha              = $row['fecha'];
    $monto              = $row['monto'];
    $efectivo           = $row['efectivo'];
    $diferencia         = $row['diferencia'];
    $usuario            = "Cierre General";
    //  echo "|".$row['nombre_users']."|";
    if(strcmp($row['nombre_users'],"")==0)
    {
      
    }
    else
    {
        $usuario = $row['nombre_users'].' '.$row['apellido_users'] ;
    }

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
<td><?php echo $usuario; ?></td>

<td >
    <div class="btn-group dropdown">
        <button type="button"  class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
        <div class="dropdown-menu dropdown-menu-right">                  
           <a class="dropdown-item" href="ver_cierre.php?id=<?php echo $idcierre; ?>"><i class='fa fa-list-alt'></i> Ver Facturas</a>
                        
       </div>
   </div>

</td>

</tr>
<?php
}
                    
                    ?>
                </table>
            </div>

            <div class="box-footer clearfix" align="right">

                <?php
  
        echo paginate($reload, $page, $total_pages, $adjacents);?>

            </div>

            <?php
}
}
?>

