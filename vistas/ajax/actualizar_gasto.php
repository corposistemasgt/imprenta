<?php
    include 'conexionpdo.php';
    $pdo = new Conexion();
    try
    {
        $sql = $pdo->prepare("update egresos set referencia_egreso=:referencia,monto=:monto,descripcion_egreso=:descripcion where id_egreso=:id");
        $sql->bindValue(':id',$_POST['id']);
        $sql->bindValue(':referencia',$_POST['referencia']);  
        $sql->bindValue(':monto',$_POST['monto']);  
        $sql->bindValue(':descripcion',$_POST['descripcion']);           
        $sql ->execute();
        http_response_code(200);
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al actualzar gasto".$e;
    }
?>