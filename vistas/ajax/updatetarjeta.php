<?php
try 
{
    
    $token = $_POST ['token'];
    $usuario= $_POST ['iduser'];
    require_once 'conexionpdo.php';
    $pdo = new Conexion();

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://corpo-sistemas.com/corpoconnect/consultar.php?token='.$token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
   // echo $response;
    
    $response='['.$response.']';
    $lista= json_decode($response, true);
   
    if(is_array($lista))
    {
        foreach ($lista as $value) 
        { 
            if(strcmp($value['resultado'],'true')==0)
            {
                
                $guid= $value['guid'];
                $serie= $value['serie'];
                $numero= $value['numero'];
                $voucher= $value['voucher'];
                $codigo= $value['codigo_autorizacion'];
                $fecha= $value['fecha'];

                $query="select max(id_factura) as idfactura from facturas_ventas  where id_vendedor =:id ";
                $sql = $pdo->prepare($query);
                $sql->bindValue(':id',$usuario);
                $sql ->execute();
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                $idfactura='';
                foreach ($sql as $r)     
                {
                    $idfactura=$r['idfactura'];
                }  
                $query="update facturas_ventas set guid_factura=:guid,serie_factura=:serie,numero_certificacion=:numero,
                voucher=:voucher,codigoauto=:codigo,fechaCertificacion=:fecha1,fecha_emision=:fecha2 where id_factura =:id ";
                $sql = $pdo->prepare($query);
                $sql->bindValue(':guid',$guid);
                $sql->bindValue(':serie',$serie);
                $sql->bindValue(':numero',$numero);
                $sql->bindValue(':voucher',$voucher);
                $sql->bindValue(':codigo',$codigo);
                $sql->bindValue(':fecha1',$fecha);
                $sql->bindValue(':fecha2',$fecha);
                $sql->bindValue(':id',$idfactura);
                $sql ->execute();
                http_response_code(200);
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
