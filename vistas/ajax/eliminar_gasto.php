<?php

    include 'conexionpdo.php';
    $pdo = new Conexion();
    try
    {
        $sql = $pdo->prepare("delete from egresos where id_egreso=:id");
        $sql->bindValue(':id',$_POST['id']);          
        $sql ->execute();
        http_response_code(200);
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al eliminar  gasto".$e;
    }
?>