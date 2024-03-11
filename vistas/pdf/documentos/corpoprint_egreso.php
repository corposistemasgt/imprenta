<?php
    include '../../../vistas/ajax/conexionpdo.php';
    $referencia=$_POST['referencia'];    
    $pdo = new Conexion();
    $retorno='[';
    $sql = $pdo->prepare("select nombre_empresa,giro_empresa,fiscal_empresa,direccion,ciudad,regimen from perfil where id_perfil=1");
    $sql ->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    $retorno.='{"empresa":[{';
    //$empesa= json_encode($sql->fetchAll()); 
    foreach ($sql as $value) 
    {
        $retorno.='"razon":"'.$value['nombre_empresa'].'","empresa":"'.$value['giro_empresa'].'","nit":"'.$value['fiscal_empresa'].'","direccion":"'.$value['direccion'].'",
        "ciudad":"'.$value['ciudad'].'","regimen":"'.$value['regimen'].'","documento":"egreso"';
    }
    $retorno.='}],"egreso":[{';
    $sql = $pdo->prepare("select monto ,descripcion_egreso as descripcion, concat(nombre_users,' ',apellido_users) as nombre  
    from egresos,users where egresos.users =users.id_users and referencia_egreso =:id");
    $sql->bindValue(':id',$referencia);
    $sql ->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    foreach ($sql as $value) 
    {
        $retorno.='"referencia":"'.$referencia.'","monto":"'.$value['monto'].'","descripcion":"'.$value['descripcion'].'",
            "nombre":"'.$value['nombre'].'"},';
    }
    $retorno=trim($retorno, ',').']}]';
    echo base64_encode($retorno);
    //echo $retorno;
?>