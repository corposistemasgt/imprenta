<?php
/*-------------------------
Punto de Ventas
---------------------------*/
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
//Inicia Control de Permisos
include "../permisos.php";
$user_id = $_SESSION['id_users'];
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {

    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$user_id."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];

    // escaping, additionally removing everything that could be (html/javascript-) code
    $q        = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $aColumns = array('codigo_producto', 'nombre_producto'); //Columnas de busqueda
    //$sTable   = " productos left join stock on productos.id_producto = stock.id_producto_stock left join perfil on id_perfil =  id_sucursal_stock ";
    $sTable   = " productos ,stock ";
    $sWhere   = " where productos.id_producto =stock.id_producto_stock and stock.id_sucursal_stock =".$id_sucursal;
    if ($_GET['q'] != "") {
        $sWhere .= " and (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    }
    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 5; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
   
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../venta/prueba.php';
    //main query to fetch the data
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
   // echo $sql;
    $query = mysqli_query($conexion, $sql);
    //loop through fetched data
    if ($numrows > 0) {

        ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-sm">
            <tr  class="info">
                <th></th>
                <th>COD.</th>
                <th class='text-center'>PRODUCTOS</th>
                <th class='text-center'>STOCK</th>
                <th class='text-center'>CANT</th>
                <th class='text-center'>COSTO</th>
                <th class='text-center' style="width: 36px;"></th>
            </tr>
            <?php
while ($row = mysqli_fetch_array($query)) {
            $id_producto     = $row['id_producto'];
            $codigo_producto = $row['codigo_producto'];
            $nombre_producto = $row['nombre_producto'];


            /////para consultar cantidades existentes
            $consultaCantidadSQL   = "SELECT * FROM stock where id_producto_stock = $id_producto and id_sucursal_stock = $id_sucursal ";
            //echo($consultaCantidadSQL." -SQL ");
            $cantidadSQL = mysqli_query($conexion, $consultaCantidadSQL);
            $cantidadStock = 0;

            while($linea = mysqli_fetch_array($cantidadSQL)){
                $cantidadStock      = $linea['cantidad_stock'];
            }

            $stock_producto  = $cantidadStock;
            $costo_producto  = $row["costo_producto"];
            $costo_producto  = number_format($costo_producto, 2, '.', '');
            $image_path      = $row['image_path'];
            ?>
                <tr>
                    <td class='text-center'>
                    <?php
if ($image_path == null) {
                echo '<img src="../../img/productos/default.jpg" class="" width="60">';
            } else {
                echo '<img src="' . $image_path . '" class="" width="60">';
            }

            ?>
                        <!--<img src="<?php echo $image_path; ?>" alt="Product Image" class='rounded-circle' width="60">-->
                    </td>
                    <td><?php echo $codigo_producto; ?></td>
                    <td><?php echo $nombre_producto; ?></td>
                    <td class="text-center"><?php echo stock($stock_producto); ?></td>
                    <td class='col-xs-1' width="15%">
                        <div class="pull-right">
                            <input type="text" class="form-control" style="text-align:center" id="cantidad_<?php echo $id_producto; ?>"  value="1" >
                        </div>
                    </td>
                    <td class='col-xs-2' width="15%"><div class="pull-right">
                        <input type="text" class="form-control" style="text-align:right" id="costo_producto_<?php echo $id_producto; ?>"  value="<?php echo $costo_producto; ?>" >
                    </div></td>
                    <td class='text-center'>
                        <a class='btn btn-success' href="#" title="Agregar a Factura" onclick="agregar('<?php echo $id_producto ?>')"><i class="fa fa-plus"></i>
                        </a>
                    </td>
                </tr>
                <?php
}
        ?>
            <tr>
                <td colspan=6><span class="pull-right">
                    <?php
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
          <strong>Aviso!</strong> No hay Registro de Producto
      </div>
      <?php
}
// fin else
}
?>