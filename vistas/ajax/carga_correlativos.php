<?php
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once "../db.php";
require_once "../php_conexion.php";
if (isset($_REQUEST['id_comp'])) {

    $user_id  = $_SESSION['id_users'];
    //codigo hecho por RZ
        
    $sql   = "SELECT * FROM  users  where id_users = '".$user_id."'";
    $query1 = mysqli_query($conexion, $sql);
    while ($row = mysqli_fetch_array($query1)) {
        $id_sucursalUsuario        = $row['sucursal_users'];
    }
    ///////// fin RZ


    $id_comp = intval($_REQUEST['id_comp']);

    $sql         = mysqli_query($conexion, "select * from comprobantes where  id_comp ='$id_comp'");
    $rw          = mysqli_fetch_array($sql);
    $serie_comp  = $rw['serie_comp'];
    $desde_comp  = $rw['desde_comp'];
    $hasta_comp  = $rw['hasta_comp'];
    $actual_comp = $rw['actual_comp'];
    $long_comp   = $rw['long_comp'];

    $consulta = "SELECT RIGHT(numero_factura,6) as factura FROM facturas_ventas WHERE id_comp_factura='$id_comp' AND id_sucursal = '$id_sucursalUsuario' ORDER BY factura DESC LIMIT 1";
    echo($consulta." -consulta en ajax cargar_correlativos.php <br>");
    $query = mysqli_query($conexion, $consulta) or die('error ' . mysqli_error($conexion));
    $count = mysqli_num_rows($query);
    if ($count != 0) {
        $row     = mysqli_fetch_assoc($query);
        $factura = $row['factura'] + 1;
    } else {
        $factura = $actual_comp;
    }
    $formato = str_pad($factura, $long_comp, "0", STR_PAD_LEFT);
    $factura = $id_sucursalUsuario.''.$serie_comp . '' . $formato;
    echo($factura." NUEVO NUM FACTURA1 ajax cargar_correlativos.php");
    echo '<input type="text" class="form-control" autocomplete="off" id="factura" value="' . $factura . '" placeholder="Factura" readonly name="factura" >';
}
