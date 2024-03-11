<?php

    include 'conexionpdo.php';
    $pdo = new Conexion();
    try
    {
        date_default_timezone_set('America/Guatemala'); 
        $fecha=date("Y-m-d H:i:s"); 
        $diferencia=$_POST['monto']-$_POST['efectivo'];
        $sql = $pdo->prepare("insert into cierre(fecha,monto,efectivo,diferencia,estado,idusuario) 
            values(:fecha,:monto,:efectivo,:diferencia,:estado,:idusuario)");
        $sql->bindValue(':fecha',$fecha);
        $sql->bindValue(':monto',$_POST['monto']);  
        $sql->bindValue(':efectivo',$_POST['efectivo']);  
        $sql->bindValue(':diferencia',$diferencia);  
        $sql->bindValue(':estado',"1");  
        $sql->bindValue(':idusuario',$_POST['idusuario']);          
        $sql ->execute();
        $idcierre=$pdo-> lastInsertId();
        $sucu=$_POST['sucursal'];
        $iuser= intval($_POST['idusuario']);
        if($iuser>0)
        {
            $sqlo = $pdo->prepare("update facturas_ventas set idcierre=:idcierre where idcierre=0 and id_vendedor=:ven");     
            $sqlo->bindValue(':idcierre',$idcierre);  
            $sqlo->bindValue(':ven',$iuser);   
            $sqlo ->execute();
    
            $sqlo = $pdo->prepare("update apertura_caja set idcierre=:idcierre where idcierre=0 and idusuario=:ven");     
            $sqlo->bindValue(':idcierre',$idcierre);  
            $sqlo->bindValue(':ven',$iuser);   
            $sqlo ->execute();
    
            $sqlo = $pdo->prepare("update egresos set idcierre=:idcierre where idcierre=0 and users=:ven");     
            $sqlo->bindValue(':idcierre',$idcierre); 
            $sqlo->bindValue(':ven',$iuser);    
            $sqlo ->execute();

            $sqlo = $pdo->prepare("update creditos_abonos set idcierre=:idcierre where idcierre=0 and  
            id_users_abono=:ven");     
            $sqlo->bindValue(':idcierre',$idcierre);
            $sqlo->bindValue(':ven',$iuser);     
            $sqlo ->execute();
        }
        else
        {
            $sqlo = $pdo->prepare("update facturas_ventas set idcierre=:idcierre where idcierre=0 and id_sucursal=:ven");     
            $sqlo->bindValue(':idcierre',$idcierre);  
            $sqlo->bindValue(':ven',$sucu);   
            $sqlo ->execute();
    
            $sqlo = $pdo->prepare("update apertura_caja set idcierre=:idcierre where idcierre=0 and idsucursal=:ven");     
            $sqlo->bindValue(':idcierre',$idcierre);  
            $sqlo->bindValue(':ven',$sucu);   
            $sqlo ->execute();
    
            $sqlo = $pdo->prepare("update egresos set idcierre=:idcierre where idcierre=0 and idsucursal=:ven");     
            $sqlo->bindValue(':idcierre',$idcierre); 
            $sqlo->bindValue(':ven',$sucu);    
            $sqlo ->execute();

            $sqlo = $pdo->prepare("update creditos_abonos set idcierre=:idcierre where idcierre=0 and  
            id_sucursal=:ven");     
            $sqlo->bindValue(':idcierre',$idcierre);
            $sqlo->bindValue(':ven',$sucu);     
            $sqlo ->execute();
        }
        
        http_response_code(200);
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al realizar el cierre de caja";
    }
?>