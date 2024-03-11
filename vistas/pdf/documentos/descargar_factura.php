<?php 
    require_once "../../db.php";
    require_once "../../php_conexion.php";
    $id_vendedor = 1;
    $id_factura = $_GET["id_factura"];
    $sql_count  = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
    $count      = mysqli_num_rows($sql_count);
    if ($count == 0) {
        echo "<script>alert('Factura no encontrada')</script>";
        echo "<script>window.close();</script>";
        exit;
    }
    $sentenciaSQL = "select * from facturas_ventas where id_factura='" . $id_factura . "'";
    $sql_factura    = mysqli_query($conexion, $sentenciaSQL);
    $rw_factura     = mysqli_fetch_array($sql_factura);
    $numero_factura = $rw_factura['numero_factura'];
    $id_cliente     = $rw_factura['id_cliente'];
    $id_vendedor    = $rw_factura['id_vendedor'];
    $fecha_factura  = $rw_factura['fecha_factura'];
    $condiciones    = $rw_factura['condiciones'];
    $guid           = $rw_factura['guid_factura'];
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users inner join perfil on id_perfil = sucursal_users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from perfil where id_perfil = '".$id_sucursal."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $Requestor = $rw['requestor'];
    $Entity    = $rw['fiscal_empresa'];
    $url_ws ="https://app.corposistemasgt.com/webservicefront/factwsfront.asmx?WSDL";


    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://app.corposistemasgt.com/webservicefront/factwsfront.asmx?WSDL=null',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
        <soap:Body>
            <RequestTransaction xmlns="http://www.fact.com.mx/schema/ws">
                <Requestor>'.$Requestor.'</Requestor>
                <Transaction>GET_DOCUMENT</Transaction>
                <Country>GT</Country>
                <Entity>'.$Entity.'</Entity>
                <User>'. $Requestor.'</User>
                <UserName>ADMINISTRADOR</UserName>
                <Data1>'.$guid.'</Data1>
                <Data2> </Data2>
                <Data3>PDF</Data3>
            </RequestTransaction>
        </soap:Body>
    </soap:Envelope>
    ',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: text/xml'
      ),
    ));  
    $response = curl_exec($curl);
    curl_close($curl);
    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
    $xml = new SimpleXMLElement($response);
    $bodys = $xml->xpath('//soapBody')[0];
    $array = json_decode(json_encode((array)$bodys));             
    $n=$array->{'RequestTransactionResponse'}; 
    $n=json_encode($n);
    $r = json_decode($n);
    $r=$r->{'RequestTransactionResult'}; 
    $s=json_encode($r);
    $s = json_decode($s);
    $p=$s;
    $s=$s->{'Response'}; 
    $rd=$p->{'ResponseData'}; 
    $s=json_encode($s);
    $s = json_decode($s);
    $resultado=$s->{'Result'};
    $detalle=$s->{'Description'};
    echo 'si';
    if(strcmp($resultado,"true")==0)
    {  
        echo 'si aca';
        $s=json_encode($rd);
        $s = json_decode($s);
        $pdfB64=$s->{'ResponseData3'};
        $decoded = base64_decode($pdfB64);
        echo $decoded;
        $file = 'invoice.pdf';
        file_put_contents($file, $decoded);
        date_default_timezone_set('America/Guatemala'); 
        $invoice_date = date('dmy-H-i-s');
        $nombre = "dte E ".$invoice_date.".pdf";
        if (file_exists($file)) {
  
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename='.$nombre);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($decoded));
            ob_clean();
            flush();
            echo $decoded;
            exit;
        }
        
    }
    else
    {
        $detalle=base64_encode($detalle);
        echo json_encode(array("resultado"=>"false","detalle"=>$detalle));
    }
                                
?>