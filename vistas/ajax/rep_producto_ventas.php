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
    
    //echo($_REQUEST['agrupar']." --agrupar");
    $daterange      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
    $id_categoria = intval($_REQUEST['categoria']);
    $id_sucursal =  intval($_REQUEST['sucursal']);
    $agrupar = $_REQUEST['agrupar'];
    if($agrupar == "si"){
        $campos       = "productos.codigo_producto, 
                     productos.nombre_producto, productos.id_producto, 
                     productos.id_linea_producto, lineas.id_linea, lineas.nombre_linea, 
                     productos.estado_producto, facturas_ventas.fecha_factura, 
                     facturas_ventas.id_factura, facturas_ventas.condiciones, detf.*, sum(detf.cantidad) as cantidad_vendida, 
                     sum(cantidad*precio_venta*desc_venta/100) as descuento, users.usuario_users, sum(detf.precio_venta*cantidad) as precio_venta,
                     sum(importe_venta) as importe_venta ";
    }else{
        $campos       = "productos.codigo_producto, 
        productos.nombre_producto, productos.id_producto, 
        productos.id_linea_producto, lineas.id_linea, lineas.nombre_linea, 
        productos.estado_producto, facturas_ventas.fecha_factura, 
        facturas_ventas.id_factura, facturas_ventas.condiciones, detf.*, detf.cantidad as cantidad_vendida, users.usuario_users ";
    }
    $tables       = "detalle_fact_ventas AS detf inner join facturas_ventas 
                     on detf.id_factura = facturas_ventas.id_factura inner join productos 
                     on detf.id_producto = productos.id_producto inner join lineas 
                     on productos.id_linea_producto = lineas.id_linea inner join users on facturas_ventas.id_vendedor = users.id_users ";

    $tablesCount  = "productos,  lineas";
    
    $sWhere      = "lineas.id_linea=productos.id_linea_producto and facturas_ventas.estado_factura <> 3 ";
 
    if (!empty($daterange)) {
        list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
        list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
        $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
        list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
        $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";
    
        $sWhere .= "and (facturas_ventas.fecha_factura between '$fecha_inicial' and '$fecha_final' )";
    }
    //echo($daterange);

    if ($id_categoria > 0) {
        $sWhere .= " and productos.id_linea_producto = '" . $id_categoria . "' ";
    }
    if ($id_sucursal > 0) {
        $sWhere .= " and facturas_ventas.id_sucursal = '" . $id_sucursal . "' ";
    }
    
    if($agrupar == "si"){
        $sWhere .= " group by productos.id_producto ";
    }

    $sWhere .= " order by (productos.id_producto) ";


    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables where $sWhere");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../rep_ventas.php';
    //main query to fetch the data
    //$conexion, "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page"
    $sentencia = "SELECT $campos FROM  $tables WHERE $sWhere LIMIT $offset,$per_page";
    
    //echo("<br></br>sentencia ajax:  $sentencia <br></br> ");
    $query = mysqli_query($conexion,$sentencia); 
    //loop through fetched data
    //echo($query."56465464654654");
    if ($numrows > 0) {
        ?>

        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-sm ">
                <tr>
                    <th class='text-center'>Codigo</th>
                    <th>Nombre</th>
                    <th>Categoria</th>    
                    <?php if($agrupar !== "si"){
                        //echo(" entra ");
                        echo("<th class='text-left'>Usuario</th>");
                    }
                    ?>

                     
                    <th class='text-left'>Fecha</th>                    
                    <th class='text-left'>Cantidad</th>
                    <?php if($agrupar == "si"){
                        //echo(" entra ");
                        echo("<th class='text-left'>Precio V. promedio</th>");
                    }else{
                        //echo(" NOO entra ");
                        echo("<th class='text-left'>Precio V.</th>");
                    }

                    ?>
                    
                    <th class='text-left'>Descuento Q</th>
                    <th class='text-left'>Importe V.</th>
                    <th class='text-left'>Pago</th>
                </tr>
                <?php
$finales = 0;
        while ($row = mysqli_fetch_array($query)) {
            $codigo           = $row['codigo_producto'];
            $nombre_producto  = $row['nombre_producto'];            
            $nombre_linea     = $row['nombre_linea'];
            $usuario          = $row['usuario_users'];
            $cantidad         = $row['cantidad_vendida'];
            if($agrupar == "si"){
                $fecha_factura         = "agrupado";
                $precio_venta     =$row['precio_venta']/$cantidad;
                
                $monto_descuento = $row['descuento'];
            }else{
                $fecha_factura         = $row['fecha_factura'];
                $precio_venta     = $row['precio_venta'];

                
                $porcentaje_descuento = $row['desc_venta'];
                $cantidad_sinDescuento = $cantidad*$precio_venta;
                $monto_descuento       = ($cantidad_sinDescuento*$porcentaje_descuento)/100;
            }
            
        
           
            
            $importe_venta    = $row['importe_venta'];
            $pago    = condicion($row['condiciones']);
            /*$sql               = mysqli_query($conexion, "select nombre_cliente from clientes where id_cliente='" . $id_cliente . "'");
            $rw                = mysqli_fetch_array($sql);
            $cliente           = $rw['nombre_cliente'];*/
        /*    if ($estado_producto == 1) {
                $estado = "<label class='badge badge-success'>Activo</label>";
            } else {
                $estado = "<label class='badge badge-danger'>Inactivo</label>";
            }*/
            $simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
            ?>
                    <tr>
                        <td class='text-center'><label class='badge badge-purple'><?php echo $codigo; ?></label></td>
                        <td class='text-left'><?php echo $nombre_producto; ?></td>
                        <td class='text-left'><?php echo $nombre_linea; ?></td>
                        
                       
                        <?php if($agrupar !== "si"){ ?>
                            <td class='text-left'><?php echo $usuario; ?></td>
                        <?php }?>
                        
                        <td class='text-left'><?php echo $fecha_factura; ?></td>
                        <td class='text-left'><?php echo $cantidad; ?></td>
                        <td class='text-left'><?php echo number_format($precio_venta, 2); ?></td>
                        
                        
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($monto_descuento, 2); ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($importe_venta, 2); ?></td>
                        <td class='text-left'><?php echo $pago; ?></td>
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

