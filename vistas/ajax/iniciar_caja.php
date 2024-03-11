<?php

    include 'conexionpdo.php';
    $pdo = new Conexion();
    $fecha=date('Y-m-d H:i:s');
    try
    {   $query="select * from apertura_caja  where idcierre=0";
        $sql = $pdo->prepare($query);
        $sql ->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $resultado=json_encode($sql->fetchAll());
        
        if(strcmp($resultado, "[]") ==0)
        { 

            $sss ="insert into apertura_caja(monto,fecha,idcierre,idusuario,idsucursal) 
            values(".$_POST['efectivo'].",'".$fecha."',0,".$_POST['idusuario'].",".$_POST['sucursal'].")";  
            $sql = $pdo->prepare($sss);     
            $sql ->execute();
            http_response_code(200);
        }
        else
        {

            http_response_code(400);
            echo "La Caja  ya ha sido inicializada anteriormente";
        }
      
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al actualzar gasto".$e;
    }
?>