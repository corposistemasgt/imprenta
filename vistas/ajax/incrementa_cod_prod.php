<?php
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once "../db.php";
require_once "../php_conexion.php";

$consulta = "SELECT RIGHT(codigo_producto,6) as codigo FROM productos
  ORDER BY CAST(RIGHT(codigo_producto,6) AS SIGNED INTEGER) DESC LIMIT 1";
$query_id = mysqli_query($conexion, $consulta)
or die('error ' . mysqli_error($conexion));
$count = mysqli_num_rows($query_id);
$codigo = 1;
if ($count != 0) {

    $data_id = mysqli_fetch_assoc($query_id);
    //echo("la consulta = ".$consulta);
    //echo("<br>el codigo de la bd = ".$data_id['codigo']);
    if($data_id)
    {
      if(is_numeric($data_id['codigo']))
      {
        $codigo  = $data_id['codigo'] + 1;
      }
      
    }
    
} else {
    $codigo = 1;
}

$buat_id = str_pad($codigo, 5, '0', STR_PAD_LEFT);
$codigo  = "$buat_id";

echo '<input type="text" class="form-control" autocomplete="off" id="codigo" value="' . $codigo . '" name="codigo" >';
