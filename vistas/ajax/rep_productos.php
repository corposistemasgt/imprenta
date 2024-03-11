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

$sqlUsuarioACT        = mysqli_query($conexion, "select * from users inner join perfil on sucursal_users = perfil.id_perfil 
inner join user_group on users.cargo_users = user_group.user_group_id 
where id_users = '".$user_id."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $nombreSucursal = $rw['giro_empresa'];
    $nombreCargo      = $rw['name'];
    

$action  = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {

    $id_categoria = intval($_REQUEST['categoria']);
    $tables       = " productos left join lineas on productos.id_linea_producto = lineas.id_linea";
    $campos       = "*";
    $sWhere       = "";
    if ($id_categoria > 0) {
        $sWhere .= " WHERE productos.id_linea_producto = '" . $id_categoria . "' ";
    }
    $sWhere .= " order by productos.id_producto ";

    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables $sWhere ");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../rep_ventas.php';
    //main query to fetch the data
    $query = mysqli_query($conexion, "SELECT $campos FROM  $tables $sWhere LIMIT $offset,$per_page");
    //echo("<br>"."SELECT $campos FROM  $tables $sWhere LIMIT $offset,$per_page"."<br>");
    //loop through fetched data

    if ($numrows > 0) {
        ?>

        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-sm ">
                <tr>
                    <th class='text-center'>Codigo</th>
                    <th>Nombre</th>
                    <th>Categoria</th>
                    <th>Sucursal</th>
                    <th>Stock</th>
                    <th class='text-left'>Costo </th>
                    <th class='text-left'>Precio V. </th>
                    <th class='text-left'>Precio M. </th>
                    <th class='text-left'>Precio E. </th>
                    <th class='text-center'>Estado </th>
                    <th>Agregado </th>

                </tr>
                <?php
$finales = 0;
        while ($row = mysqli_fetch_array($query)) {
            $id_producto = $row['id_producto'];
            $codigo           = $row['codigo_producto'];
            $nombre_producto  = $row['nombre_producto'];
            $nombre_linea     = $row['nombre_linea'];
            //$stock_producto   = $row['cantidad_stock'];
            $costo_producto   = $row['costo_producto'];
            $precio_venta     = $row['valor1_producto'];
            $precio_mayorista = $row['valor2_producto'];
            $precio_especial  = $row['valor3_producto'];
            //$giro_empresa     = $row['giro_empresa'];
            $estado_producto  = $row['estado_producto'];
            $date_added       = date('d/m/Y', strtotime($row['date_added']));
            /*$sql               = mysqli_query($conexion, "select nombre_cliente from clientes where id_cliente='" . $id_cliente . "'");
            $rw                = mysqli_fetch_array($sql);
            $cliente           = $rw['nombre_cliente'];*/
            if ($estado_producto == 1) {
                $estado = "<label class='badge badge-success'>Activo</label>";
            } else {
                $estado = "<label class='badge badge-danger'>Inactivo</label>";
            }
            $simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
            
            $consultaCantidadSQL   = "SELECT * FROM stock left join perfil on stock.id_sucursal_stock = perfil.id_perfil  where id_producto_stock = '$id_producto' and id_sucursal_stock = '$id_sucursal'";

            $cantidadSQL = mysqli_query($conexion, $consultaCantidadSQL);
            $stock_producto = 0;
            $sucursal_producto = "-";
            while($linea = mysqli_fetch_array($cantidadSQL)){
                $stock_producto      = $linea['cantidad_stock'];
                $sucursal_producto    = $linea['giro_empresa'];
            }
            
            ?>
                    <tr>
                        <td class='text-center'><label class='badge badge-purple'><?php echo $codigo; ?></label></td>
                        <td class='text-left'><?php echo $nombre_producto; ?></td>
                        <td class='text-left'><?php echo $nombre_linea; ?></td>
                        <td class='text-center'><?php echo $nombreSucursal ?></td>
                        <td class='text-center'><?php echo $stock_producto ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($costo_producto, 2); ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($precio_venta, 2); ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($precio_mayorista, 2); ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($precio_especial, 2); ?></td>
                        <td class='text-center'><?php echo $estado; ?></td>
                        <td class='text-center'><?php echo $date_added; ?></td>
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

