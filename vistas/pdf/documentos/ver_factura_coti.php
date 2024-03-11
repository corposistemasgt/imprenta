<?php
/*-------------------------
Punto de Ventas
---------------------------*/
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../../login.php");
    exit;
}

/* Connect To Database*/
include "../../db.php";
include "../../php_conexion.php";
//Archivo de funciones PHP
include "../../funciones.php";
$id_factura = intval($_GET['id_factura']);
//$imprimir_desde_listado = $_GET['listado'];//valor 1 para si     0 para no
$sql_count  = mysqli_query($conexion, "select * from facturas_ventas where numero_factura='" . $id_factura . "'");
$count      = mysqli_num_rows($sql_count);



if ($count == 0) {
    echo "<script>alert('Factura no encontrada')</script>";
    echo "<script>window.close();</script>";
    exit;
}
//AQUI1 111 aqui tengo que verificar que si la factura no tiene guid y tiene estado cero que no descargue nada
while ($row = mysqli_fetch_array($sql_count)) {
    $estado     = $row["estado_factura"];
    $guidExistente = $row['guid_factura'];
    
}

if(trim($guidExistente) === "" && $estado == 3){
    echo("No se puede certificar esta venta porque fue anulada");
    exit;
}

/*if(trim($guidExistente) === ""){
    echo("No tiene GUID lo que significa que no está certificada");
    exit;
}*/

//echo("<br>En verdad no salió sale de ver factura.php en documentos"); quite este comentario
$sql_factura    = mysqli_query($conexion, "select * from facturas_ventas where numero_factura='" . $id_factura . "'");
$rw_factura     = mysqli_fetch_array($sql_factura);
$numero_factura = $rw_factura['numero_factura'];
$id_cliente     = $rw_factura['id_cliente'];
$id_vendedor    = $rw_factura['id_vendedor'];
$fecha_factura  = $rw_factura['fecha_factura'];
$condiciones    = $rw_factura['condiciones'];
$simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);

// get the HTML
ob_start();
include dirname(__FILE__) . '/res/ver_factura_html.php';