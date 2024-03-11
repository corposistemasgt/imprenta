<?php
include "is_logged.php"; 
require_once "../db.php";
require_once "../php_conexion.php";
include "../permisos.php";
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$action  = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $meses =intval($_REQUEST['meses']);
    $id_categoria = intval($_REQUEST['categoria']);
    $id_sucursal =  intval($_REQUEST['sucursal']);
    $mes=date("m");
    $year=date("Y");
    $t=$mes+$meses;
    if($t>12)
    {
        $mes=$t-12;
        $year++;
    }
    $fecha_final=$year."-".$t."-31 00:00:00";
    $fecha_inicial=date("Y-m")."-01 00:00:00";
    $cadena = " from productos,lineas,stock where 
    stock.id_producto_stock=productos.id_producto and date_vence 
     between '$fecha_inicial' and '$fecha_final' and lineas.id_linea=productos.id_linea_producto";
     if($id_sucursal>0)
     {
        $cadena.=" and stock.id_sucursal_stock=".$id_sucursal;
     }
     if($id_categoria>0)
     {
        $cadena.=" and lineas.id_linea=".$id_categoria;
     }
    include 'pagination.php'; 
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows ".$cadena);
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../vencimientos.php';
    $sentencia = "SELECT * ".$cadena;
    $query = mysqli_query($conexion,$sentencia); 
    if ($numrows > 0) {
        ?>

        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-sm ">
                <tr>
                    <th class='text-center'>Codigo</th>
                    <th>Categoria</th>
                    <th>Producto</th>
                    <th>Vencimiento</th>    
                    <th class='text-left'>Stock</th>         
                    <th class='text-left'>Costo</th>
                    <th class='text-left'>Subtotal.</th>
                </tr>
                <?php
$finales = 0;
        while ($row = mysqli_fetch_array($query)) {
            $codigo           = $row['codigo_producto'];
            $nombre_linea     = $row['nombre_linea'];
            $nombre_producto  = $row['nombre_producto']; 
            $fecha = $row['date_vence'];
            $stock = $row['cantidad_stock'];              
            $costo=$row['costo_producto'];   
            $sub=$costo*$stock  ;     

     
            $simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
            ?>
                    <tr>
                        <td class='text-center'><label class='badge badge-purple'><?php echo $codigo; ?></label></td>
                        <td class='text-left'><?php echo $nombre_linea; ?></td>
                        <td class='text-left'><?php echo $nombre_producto; ?></td>                       
                        <td class='text-left'><?php echo $fecha; ?></td>
                        <td class='text-left'><?php echo $stock; ?></td>                                       
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($costo, 2); ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($sub, 2); ?></td>
                 
                    </tr>
                    <?php }?>
                </table>
            </div>

            <div class="box-footer clearfix" align="right">

                <?php
$inicios = $offset + 1;
        $finales += $inicios - 1;
        echo "Mostrando $inicios al $finales de $numrows registros";
        echo paginate($reload, $page, $total_pages, $adjacents);?>

            </div>

            <?php
}
}
?>

