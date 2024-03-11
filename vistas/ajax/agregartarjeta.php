<?php
try 
{
    $corpocode="m1234";
    $session_id = $_POST ['sesion'];
    $terminal = $_POST ['terminal'];
    $factura=0;
    $cuotas=0;
    if(strcmp($_POST['factura'],'true'))
    {
        $factura=1;
    }
    require_once 'conexionpdo.php';
    $pdo = new Conexion();
    $nombre='Consumidor Final';
    $nit='CF';
    if(isset($_POST ['nombre']))
    {
        if(strcmp($_POST ['nombre'],'')!==0)
        {
            $nombre=$_POST ['nombre'];
        }
    }
    if(isset($_POST ['nit']))
    {
        if(strcmp($_POST ['nit'],'')!==0)
        {
            $nombre=$_POST ['nit'];
        }
    }
    $cliente=base64_encode('['.json_encode(array("nombre"=>$nombre,"nit"=>$nit,"direccion"=>'Ciudad')).']');
    $query="select nombre_producto as nombre,cantidad_tmp as cantidad,precio_tmp as precio  from tmp_ventas,productos where 
   tmp_ventas.id_producto=productos.id_producto and session_id =:id";
    $sql = $pdo->prepare($query);
    $sql->bindValue(':id',$session_id);
    $sql ->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    $envio='';
    foreach ($sql as $r)     
    {
        $envio.=json_encode(array("producto"=>$r['nombre'],"cantidad"=>$r['cantidad'],"precio"=>$r['precio'],"descuento"=>'0')).',';
    }  
    $envio=trim($envio, ',');
    $envio='['.$envio.']';
    $envio=base64_encode($envio);
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://corpo-sistemas.com/corpoconnect/insertar.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('terminal' => $terminal,'certificar' => $factura,'cuotas' => $cuotas,'corpoconnect' => $corpocode,
        'datos_cliente' => $cliente,'detalle_factura' => $envio),
    ));
    $response = curl_exec($curl);
    
    curl_close($curl);
    $response='['.$response.']';
    $lista= json_decode($response, true);
   
    if(is_array($lista))
    {
        foreach ($lista as $value) 
        { 
            if(strcmp($value['resultado'],'true')==0)
            {
                http_response_code(200);
                echo $value['token'];
            }
            else
            {
                http_response_code(400);
                echo "Error al enviar: ".$value['detalles'];
            }
        }
      
    }
   else
   {
    http_response_code(400);
    echo "Error al enviar: Sin conexeion";
   }
}
catch(Exception $e)
{
    http_response_code(400);
    echo "Error al actualzar gasto".$e;
}
 
?> 
