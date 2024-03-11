<?php
session_start();

if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../../login.php");
    exit;
}

//include '../../ajax/is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
include "../../db.php";
include "../../php_conexion.php";
//Archivo de funciones PHP
include "../../funciones.php";
$id_factura = intval($_POST['id_factura']);

$sql_count  = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
$count      = mysqli_num_rows($sql_count);
if ($count == 0) {
    echo "<script>alert('Factura no encontrada')</script>";
    echo "<script>window.close();</script>";
    exit;
}

$sql_factura    = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
$rw_factura     = mysqli_fetch_array($sql_factura);
$numero = $rw_factura['numero_certificacion'];
$id_cliente     = $rw_factura['id_cliente'];
$guid    = $rw_factura['guid_factura'];
$serie   = $rw_factura['serie_factura'];
$fecha_certificacion  = $rw_factura['fechaCertificacion'];
$fecha_emision        = $rw_factura['fecha_factura'];
$nit_cliente          = $rw_factura['factura_nit_cliente'];
$FechaEmision         = $rw_factura['fecha_emision'];    
$estado               = $rw_factura['estado_factura'];    
$motivo_anulacion = "correcion datos";
//echo("<br>".$guid." -- el guid <br>");
if(trim($guid) === ""){
    //echo("<br>EL GUID ESTA VACIO<br>");
    //aqui1 //1aqui deberia devolver y cambiar de estado (LISTO)
    //y luego al certificar no se puede certificar una factura que tenga estado 3 FALTA
    $id_vendedor    = intval($_SESSION['id_users']);
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users inner join perfil on id_perfil = sucursal_users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    if($estado == 1){
        actualizar_estado_documento($id_factura,$id_sucursal );
    }else{
        echo("El documento ya ha sido anulado anteriormente");
    }
    
}
else{
    $id_vendedor    = intval($_SESSION['id_users']);
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users inner join perfil on id_perfil = sucursal_users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $requestor   = $rw['requestor'];
    $nit_emisor   = $rw['fiscal_empresa'];

    $motivo = "Error en datos";
    $date_added     = date("Y-m-dTH:i:s");
    if($estado == 3){
        echo("<script>alert('El documento ya ha sido anulado')</script>");
    }else{
       
      echo anularFactura($guid,$nit_emisor, $motivo,$nit_cliente,$date_added,$FechaEmision,$requestor, $id_factura, $id_sucursal);
    }
}





