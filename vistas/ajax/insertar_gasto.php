<?php
    include 'conexionpdo.php';
    $pdo = new Conexion();
    try
    {
        $fecha=date('Y').'-'.date('m').'-'.date('d');
        $sql = $pdo->prepare("insert into  egresos (referencia_egreso,monto,descripcion_egreso,fecha_added,users)
        values(:referencia,:monto,:descripcion,:fecha,:user)");
        $sql->bindValue(':id',$_POST['id']);
        $sql->bindValue(':referencia',$_POST['referencia']);  
        $sql->bindValue(':monto',$_POST['monto']);  
        $sql->bindValue(':descripcion',$_POST['descripcion']); 
        $sql->bindValue(':fecha',$fecha); 
        $sql->bindValue(':user',$_POST['user']);           
        $sql ->execute();
        http_response_code(200);
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al insetar gasto".$e;
    }
?>