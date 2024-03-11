<?php
    include '../../../vistas/ajax/conexionpdo.php';
    $idfactura=$_GET['id_factura'];
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
        "ciudad":"'.$value['ciudad'].'","regimen":"'.$value['regimen'].'","documento":"ticket"';
    }
    $retorno.='}],"factura":[{';
    $sql = $pdo->prepare("select factura_nombre_cliente,factura_nit_cliente,
    monto_factura from facturas_ventas where id_factura=:id");
    $sql->bindValue(':id',$idfactura);
    $sql ->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    foreach ($sql as $value) 
    {
        $retorno.='"nombre":"'.$value['factura_nombre_cliente'].'","nit":"'.$value['factura_nit_cliente'].'",
        "monto":"'.$value['monto_factura'].'","open":"'.$_GET['open'].'"';
    }
    $retorno.='}],"detalle":[';
    $sql = $pdo->prepare("select nombre_producto,precio_venta,cantidad,esgenerico from detalle_fact_ventas,productos where productos.id_producto=detalle_fact_ventas.id_producto and id_factura=:id");
    $sql->bindValue(':id',$idfactura);
    $sql ->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    foreach ($sql as $value) 
    {
        $retorno.='{"producto":"'.trim(str_replace ( '"', '', $value['nombre_producto'])).'","precio":"'.$value['precio_venta'].'","cantidad":"'.$value['cantidad'].'","generico":"'.$value['esgenerico'].'"},';
    }
    $retorno=trim($retorno, ',').']}]';
    echo base64_encode($retorno);
    //echo $retorno;
?>