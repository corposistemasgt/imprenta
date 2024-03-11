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
//echo("llega a pedir nit factura");
//$id_factura = $_REQUEST['id_factura'];

$id_factura    = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['idfactura'], ENT_QUOTES)));
//echo("-".$id_factura."- el id de la factura");
//$imprimir_desde_listado = $_GET['listado'];//valor 1 para si     0 para no

$ql_count  = mysqli_query($conexion, "select * from users where id_users = '".intval($_SESSION['id_users'])."'");
$rw         = mysqli_fetch_array($ql_count);
$id_sucursal = $rw['sucursal_users'];



$sql_count  = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
$count      = mysqli_num_rows($sql_count);



if ($count == 0) {
    echo "<script>alert('Factura no encontrada')</script>";
    echo "<script>window.close();</script>";
    exit;
}

$nit_nuevo = $_POST['rnc'];
$direccion_nueva = $_POST['direccion_cliente'];
$nombre_nuevo =  $_POST['nombre_cliente'];
$correo_nuevo =  $_POST['correo_cliente'];
$telefono_nuevo =  $_POST['telefono_cliente'];

$sqlactualizar = "update facturas_ventas set factura_nit_cliente = '$nit_nuevo', 
factura_nombre_cliente = '$nombre_nuevo',factura_direccion_cliente = '$direccion_nueva',
factura_numero_cliente = '$telefono_nuevo',correos = '$correo_nuevo'  where id_factura = '$id_factura'";
$resultado = mysqli_query($conexion, $sqlactualizar);


if ($resultado === true) { 
    echo json_encode(array("resultado"=>"true","sucursal"=>$id_sucursal));
} else {
   echo json_encode(array("resultado"=>"e","sucursal"=>$id_sucursal));
}


exit;

