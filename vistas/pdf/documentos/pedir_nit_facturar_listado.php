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
$id_factura = $_GET['id_factura'];
//$imprimir_desde_listado = $_GET['listado'];//valor 1 para si     0 para no
$sql_count  = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
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
    $nitExistente  = $row['factura_nit_cliente'];
    $nombreExistente = $row['factura_nombre_cliente'];    
}


if(trim($guidExistente) === ""){
    $arreglo = array(
        "nitExistente" => $nitExistente,
        "nombreExistente" => $nombreExistente,
        "certificado"     => "0",
    );
}else{
    $arreglo = array(
        "nitExistente" => $nitExistente,
        "nombreExistente" => $nombreExistente,
        "certificado"     => "1",
    );
    //certificado 0 significa que SI está certificada
}

echo json_encode($arreglo);
exit;

