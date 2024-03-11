<?php
function get_row($table, $row, $id, $equal)
{
    global $conexion;
    $query = mysqli_query($conexion, "select $row from $table where $id='$equal'");
    $rw    = mysqli_fetch_array($query);
    $value = $rw[$row];
    return $value;
}

function condicion($tipo)
{
    if ($tipo == 1) {
        return 'Efectivo';
    } elseif ($tipo == 2) {
        return 'Cheque';
    } elseif ($tipo == 3) {
        return 'Tarjeta';
    } elseif ($tipo == 4) {
        return 'Crédito';
    }
}
/*--------------------------------------------------------------*/
/* MODIFICAR LOS DATOS DEL GRAFICO
/*--------------------------------------------------------------*/
function monto($table, $mes, $periodo)
{
    global $conexion;
    $fecha_inicial = "$periodo-$mes-1";
    if ($mes == 1 or $mes == 3 or $mes == 5 or $mes == 7 or $mes == 8 or $mes == 10 or $mes == 12) {
        $dia_fin = 31;
    } else if ($mes == 2) {
        if ($periodo % 4 == 0) {
            $dia_fin = 29;
        } else {
            $dia_fin = 28;
        }
    } else {
        $dia_fin = 30;
    }
    $fecha_final = "$periodo-$mes-$dia_fin";

    $query = mysqli_query($conexion, "select sum(monto_factura) as monto from $table where fecha_factura between '$fecha_inicial' and '$fecha_final'");
    $row   = mysqli_fetch_array($query);
    $monto = floatval($row['monto']);
    return $monto;
}
function stock($stock)
{
    if ($stock == 0) {
        return '<span class="badge badge-danger">' . $stock . '</span>';
    } else if ($stock <= 3) {
        return '<span class="badge badge-warning">' . $stock . '</span>';
    } else {
        return '<span class="badge badge-primary">' . $stock . '</span>';
    }
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Pacientes
/*--------------------------------------------------------------*/
function total_clientes()
{
    global $conexion;
    $orderSql       = "SELECT * FROM clientes";
    $orderQuery     = $conexion->query($orderSql);
    $countPacientes = $orderQuery->num_rows;

    echo '' . $countPacientes . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Creditos
/*--------------------------------------------------------------*/
function total_creditos()
{
    $id_moneda    = get_row('perfil', 'moneda', 'id_perfil', 1);
    $fecha_actual = date('Y-m-d');
    global $conexion;
    $orderSql     = "SELECT * FROM facturas_ventas where date(fecha_factura) = '$fecha_actual' and estado_factura=2";
    $orderQuery   = $conexion->query($orderSql);
    $totalRevenue = 0;
    while ($orderResult = $orderQuery->fetch_assoc()) {
        $totalRevenue += $orderResult['monto_factura'];
    }

    echo '' . $id_moneda . '' . number_format($totalRevenue, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Abonos a proveedores
/*--------------------------------------------------------------*/
function total_cxp()
{
    $id_moneda    = get_row('perfil', 'moneda', 'id_perfil', 1);
    $fecha_actual = date('Y-m-d');
    global $conexion;
    //---------------------------------------------------------------------------------------
    $abonoSql    = "SELECT * FROM creditos_abonos_prov where date(fecha_abono) = '$fecha_actual'";
    $abonoQuery  = $conexion->query($abonoSql);
    $total_abono = 0;
    while ($abonoResult = $abonoQuery->fetch_assoc()) {
        $total_abono += $abonoResult['abono'];
    }

    echo '' . $id_moneda . '' . number_format($total_abono, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Abonos a proveedores
/*--------------------------------------------------------------*/
function total_cxc()
{
    $id_moneda    = get_row('perfil', 'moneda', 'id_perfil', 1);
    $fecha_actual = date('Y-m-d');
    global $conexion;
    //---------------------------------------------------------------------------------------
    $abonoSql    = "SELECT * FROM creditos_abonos where date(fecha_abono) = '$fecha_actual'";
    $abonoQuery  = $conexion->query($abonoSql);
    $total_abono = 0;
    while ($abonoResult = $abonoQuery->fetch_assoc()) {
        $total_abono += $abonoResult['abono'];
    }

    echo '' . $id_moneda . '' . number_format($total_abono, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Ingresos
/*--------------------------------------------------------------*/
function total_ingresos()
{
    $id_moneda    = get_row('perfil', 'moneda', 'id_perfil', 1);
    $fecha_actual = date('Y-m-d');
    global $conexion;
    $orderSql     = "SELECT * FROM facturas_ventas where date(fecha_factura) = '$fecha_actual'";
    $orderQuery   = $conexion->query($orderSql);
    $totalRevenue = 0;
    while ($orderResult = $orderQuery->fetch_assoc()) {
        $totalRevenue += $orderResult['monto_factura'];
    }

    echo '' . $id_moneda . '' . number_format($totalRevenue, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Egresos
/*--------------------------------------------------------------*/
function total_egresos()
{
    $id_moneda    = get_row('perfil', 'moneda', 'id_perfil', 1);
    $fecha_actual = date('Y-m-d');
    global $conexion;
    $orderSql    = "SELECT * FROM facturas_compras where date(fecha_factura) = '$fecha_actual'";
    $orderQuery  = $conexion->query($orderSql);
    $totalEgreso = 0;
    while ($orderResult = $orderQuery->fetch_assoc()) {
        $totalEgreso += $orderResult['monto_factura'];
    }

    echo '' . $id_moneda . '' . number_format($totalEgreso, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Inventario Bajo
/*--------------------------------------------------------------*/
function poner_inventario()
{
    global $conexion;
    $lowStockSql   = "SELECT * FROM productos WHERE stock_producto <= 3 AND estado_producto = 1";
    $lowStockQuery = $conexion->query($lowStockSql);

 //   echo '' . $countLowStock . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener las Ultimas Ventas
/*--------------------------------------------------------------*/
function latest_order()
{
    global $conexion;
    $id_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);

    $sql = mysqli_query($conexion, "select * from facturas_ventas where id_cliente >0 order by  id_factura desc limit 0,5");
    while ($rw = mysqli_fetch_array($sql)) {
        $id_factura     = $rw['id_factura'];
        $numero_factura = $rw['numero_factura'];

        $supplier_id       = $rw['id_cliente'];
        $sql_s             = mysqli_query($conexion, "select nombre_cliente from clientes where id_cliente='" . $supplier_id . "'");
        $rw_s              = mysqli_fetch_array($sql_s);
        $supplier_name     = $rw_s['nombre_cliente'];
        $date_added        = $rw['fecha_factura'];
        list($date, $hora) = explode(" ", $date_added);
        list($Y, $m, $d)   = explode("-", $date);
        $fecha             = $d . "-" . $m . "-" . $Y;
        $total             = number_format($rw['monto_factura'], 2);
        ?>
        <tr>
            <td><a href="editar_venta.php?id_factura=<?php echo $id_factura; ?>" data-toggle="tooltip" title="Ver Factura"><label class='badge badge-primary'><?php echo $numero_factura; ?></label></a></td>
            <td><?php echo $fecha; ?></td>
            <td class='text-left'><b><?php echo $id_moneda . '' . $total; ?></b></td>
        </tr>
        <?php

    }
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Ventas del Vendedor
/*--------------------------------------------------------------*/
function venta_users()
{
    $id_moneda    = get_row('perfil', 'moneda', 'id_perfil', 1);
    $fecha_actual = date('Y-m-d');
    $users        = intval($_SESSION['id_users']);
    global $conexion;
    $orderSql   = "SELECT * FROM facturas_ventas where id_users_factura = '$users' and date(fecha_factura) = '$fecha_actual'";
    $orderQuery = $conexion->query($orderSql);
    $countOrder = $orderQuery->num_rows;

    $totalRevenue = 0;
    while ($orderResult = $orderQuery->fetch_assoc()) {
        $totalRevenue += $orderResult['monto_factura'];
    }

    echo '' . $id_moneda . '' . number_format($totalRevenue, 2) . '';
}
/*--------------------------------------------------------------*/
/* Calculo del Descuento
/*--------------------------------------------------------------*/
function rebajas($base, $dto = 0)
{
    $ahorro = ($base * $dto) / 100;
    $final  = $base - $ahorro;
    return $final;
}
/*--------------------------------------------------------------*/
/* Control de Stock
/*--------------------------------------------------------------*/
function guardar_historial($id_producto, $user_id, $fecha, $nota, $reference, $quantity, $tipo)
{
    global $conexion;
    $sql = "INSERT INTO historial_productos (id_historial, id_producto, id_users, fecha_historial, nota_historial, referencia_historial, cantidad_historial, tipo_historial)
  VALUES (NULL, '$id_producto', '$user_id', '$fecha', '$nota', '$reference', '$quantity','$tipo');";
    $query = mysqli_query($conexion, $sql);

}



function eliminar_stock($id_producto, $quantity, $id_sucursal)
{
    global $conexion;
    $update = mysqli_query($conexion, "update stock set cantidad_stock=cantidad_stock-'$quantity' where id_producto_stock='$id_producto' and id_sucursal_stock='$id_sucursal'");
    if ($update) {
        return 1;
    } else {
        return 0;
    }

}
/*--------------------------------------------------------------*/
/* Control de KARDEX
/*--------------------------------------------------------------*/
function guardar_salidas($fecha, $id_producto, $cant_salida, $costo_salida, $total_salida, $cant_saldo, $costo_saldo, $total_saldo, $fecha_added, $users, $tipo)
{
    global $conexion;
    $sql = "INSERT INTO kardex (fecha_kardex, producto_kardex, cant_salida, costo_salida, total_salida, cant_saldo, costo_saldo, total_saldo, added_kardex, users_kardex, tipo_movimiento)
  VALUES ('$fecha','$id_producto','$cant_salida','$costo_salida','$total_salida', '$cant_saldo','$costo_saldo','$total_saldo','$fecha_added','$users','$tipo');";
    $query = mysqli_query($conexion, $sql);
    return $sql;
}
function guardar_entradas($fecha, $id_producto, $cant_entrada, $costo_entrada, $total_entrada, $cant_saldo, $costo_promedio, $total_saldo, $fecha_added, $users, $tipo)
{
    global $conexion;
    $sql = "INSERT INTO kardex (fecha_kardex, producto_kardex, cant_entrada, costo_entrada, total_entrada, cant_saldo, costo_saldo, total_saldo, added_kardex, users_kardex, tipo_movimiento)
  VALUES ('$fecha','$id_producto','$cant_entrada','$costo_entrada','$total_entrada', '$cant_saldo','$costo_promedio','$total_saldo','$fecha_added','$users','$tipo');";
    $query = mysqli_query($conexion, $sql);

}
function formato($valor)
{
    return number_format($valor, 2);
    //return number_format($valor, 2, '.', '.');
}
function iva($sin_iva)
{
    $iva     = get_row('perfil', 'impuesto', 'id_perfil', 1);
    $con_iva = $sin_iva + ($iva * ($sin_iva / 100));
    $con_iva = round($con_iva, 2) - $sin_iva;
    return $con_iva;
}


//para crear y certificar FACT el XML, listaArreglo es el listado de los productos a vender
function certificar($tipoFact,$nitEmisor,$nombreEmisor,$codigoEstablecimiento,$direccionEmisor,$codigoPostal,$departamento, $municipio,$nitCliente, $nombreCliente,$frasesyEscenarios,
$usuario,$requestor,$caja,$listaArreglo, $nombreComercial,$direccion)
{
    date_default_timezone_set('America/Guatemala'); 
    $invoice_date = date('Y-m-d\TH:i:s');   
    $fechaActual = date("Y-m-d");
    $fechaVencimiento = date("Y-m-d",strtotime($fechaActual."+ 30 days"));
    $horaEmision = $invoice_date;
    $nitEmisorEntity = trim($nitEmisor);
    $totalIVA = 0;
    $totalOtro1 = 0;
    $totalOtro2 = 0;
    $ttalOtro3 = 0;
    $granTotal = 0;
$w=new XMLWriter();
$w->openMemory();
$w->startDocument('1.0','UTF-8');
    $w->startElement("dte:GTDocumento");
        $w->writeAttribute("xmlns:ds","http://www.w3.org/2000/09/xmldsig#");
        $w->writeAttribute("xmlns:dte","http://www.sat.gob.gt/dte/fel/0.2.0");
        $w->writeAttribute("xmlns:cfc","http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0");
        $w->writeAttribute("xmlns:cex","http://www.sat.gob.gt/face2/ComplementoExportaciones/0.1.0");
        $w->writeAttribute("xmlns:cno","http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0");
        $w->writeAttribute("xmlns:cfe","http://www.sat.gob.gt/face2/ComplementoFacturaEspecial/0.1.0");
        $w->writeAttribute("Version","0.1");
        $w->startElement("dte:SAT");
            $w->writeAttribute("ClaseDocumento", "dte");
            //$w->text('Wow, it works!');
            $w->startElement("dte:DTE");
                $w->writeAttribute("ID","DatosCertificados");
                    $w->startElement("dte:DatosEmision");
                        $w->writeAttribute("ID","DatosEmision");
                            $w->startElement("dte:DatosGenerales");
                                $w->writeAttribute("Tipo",$tipoFact);
                                $w->writeAttribute("FechaHoraEmision", $horaEmision);
                                $w->writeAttribute("CodigoMoneda","GTQ");
                            $w->endElement(); 
                            $w->startElement("dte:Emisor");
                                $w->writeAttribute("NITEmisor",$nitEmisorEntity);
                                $w->writeAttribute("NombreEmisor",$nombreEmisor);
                                $w->writeAttribute("CodigoEstablecimiento",$codigoEstablecimiento);
                                $w->writeAttribute("NombreComercial",$nombreComercial);
                                $afiliacion_iva = "GEN";
                                if(trim($tipoFact) == "FPEQ"){
                                    $afiliacion_iva = "PEQ";
                                }
                                $w->writeAttribute("AfiliacionIVA",$afiliacion_iva);
                                    $w->startElement("dte:DireccionEmisor");
                                        $w->startElement("dte:Direccion");
                                            $w->text($direccionEmisor);
                                        $w->endElement();
                                        $w->startElement("dte:CodigoPostal");
                                            $w->text($codigoPostal);
                                        $w->endElement();
                                        $w->startElement("dte:Municipio");
                                            $w->text($municipio);
                                        $w->endElement();
                                        $w->startElement("dte:Departamento");
                                        $w->text($departamento);
                                        $w->endElement();
                                        $w->startElement("dte:Pais");
                                        $w->text("GT");
                                        $w->endElement();
                                    $w->endElement();
                            $w->endElement();
                            $w->startElement("dte:Receptor");
                                $w->writeAttribute("IDReceptor",$nitCliente);
                                //echo(strlen("<br>".$nitCliente)." length <br>");
                                if(strlen($nitCliente) >= 12){
                                    $w->writeAttribute("TipoEspecial","CUI");
                                }
                                $w->writeAttribute("NombreReceptor",$nombreCliente);
                                $w->startElement("dte:DireccionReceptor");
                                        $w->startElement("dte:Direccion");
                                        if(strcmp($direccion,"")==0)
                                        {
                                            $w->text("ciudad");
                                        }
                                        else
                                        {
                                            $w->text($direccion);
                                        }
                                            
                                        $w->endElement();
                                        $w->startElement("dte:CodigoPostal");
                                            $w->text($codigoPostal);
                                        $w->endElement();
                                        $w->startElement("dte:Municipio");
                                            $w->text(".");
                                        $w->endElement();
                                        $w->startElement("dte:Departamento");
                                        $w->text(".");
                                        $w->endElement();
                                        $w->startElement("dte:Pais");
                                        $w->text("GT");
                                        $w->endElement();
                                    $w->endElement();

                            $w->endElement();
                            $w->startElement("dte:Frases");
                            for ($x=0;$x<count($frasesyEscenarios); $x+=2) { 
                                $w->startElement("dte:Frase");
                                    $w->writeAttribute("TipoFrase",$frasesyEscenarios[$x]);
                                    $w->writeAttribute("CodigoEscenario",$frasesyEscenarios[$x+1]);
                                $w->endElement();
                            }
                            $w->endElement();
                            $w->startElement("dte:Items");
                                for($i=0; $i<count($listaArreglo); $i++)
                                {
                                    $w->startElement("dte:Item");
                                    $w->writeAttribute("NumeroLinea",$i+1);
                                    $listaItems = $listaArreglo[$i];
                                    $w->writeAttribute("BienOServicio",$listaItems[2]);
                                    
                                    $w->startElement("dte:Cantidad");
                                    $uni=$listaItems[7];
                                    if(strcmp(trim($uni),'')==0)
                                    {
                                        $uni="UNI";
                                    }
                                    $w->text($listaItems[0]);
                                    $w->endElement();
                                    $w->startElement("dte:UnidadMedida");
                                    $w->text($uni);
                                    $w->endElement();
                                    $w->startElement("dte:Descripcion");
                                    $w->text($listaItems[1]);
                                    $w->endElement();
                                    $w->startElement("dte:PrecioUnitario");
                                    $w->text($listaItems[3]);
                                    $w->endElement();
                                    $w->startElement("dte:Precio");
                                    $w->text($listaItems[4]);
                                    $w->endElement();
                                    $w->startElement("dte:Descuento");
                                    $w->text($listaItems[6]);
                                    $w->endElement();
                                    
                                    if(trim($tipoFact) == "FACT"){
                                        $w->startElement("dte:Impuestos");
                                        //nombre del impuesto 0, CodigoUnidadGravable 1,MontoGravable 2, MontoImpuesto 3 
                                        $w->startElement("dte:Impuesto");
                                                $w->startElement("dte:NombreCorto");
                                                $w->text($listaItems[5][0]);
                                                $w->endElement();
                                                $w->startElement("dte:CodigoUnidadGravable");
                                                $w->text($listaItems[5][1]);
                                                $w->endElement();
                                                $w->startElement("dte:MontoGravable");
                                                $w->text($listaItems[5][2]);
                                                $w->endElement();
                                                $w->startElement("dte:MontoImpuesto");
                                                $w->text($listaItems[5][3]);
                                                $w->endElement();
                                                if (strcmp("IVA", $listaItems[5][0]) === 0){
                                                    $totalIVA = $totalIVA+$listaItems[5][3];
                                                }
                                        $w->endElement();    
                                    $w->endElement();
                                    }
                                    $w->startElement("dte:Total");
                                    $total_de_item = $listaItems[4]-$listaItems[6];
                                    $granTotal = $granTotal + $total_de_item;
                                    $w->text($total_de_item);
                                    $w->endElement();

                                    $w->endElement();
                                }
                            $w->endElement();
                            //seccion de totales
                            $w->startElement("dte:Totales");
                                    if($listaItems[5] === NULL)
                                    {                                
                                    }else if(trim($tipoFact) == "FACT"){
                                        $w->startElement("dte:TotalImpuestos");
                                            $w->startElement("dte:TotalImpuesto");
                                                $w->writeAttribute("NombreCorto","IVA");
                                                $w->writeAttribute("TotalMontoImpuesto",$totalIVA);
                                            $w->endElement();
                                            //aqui escribiria el resto de impuestos pero 
                                            //antes verificar que no sean 0 para escribilo
                                        $w->endElement();
                                    }
                                $w->startElement("dte:GranTotal");
                                $w->text($granTotal);
                                $w->endElement();
                            $w->endElement();
                            if($tipoFact == "FCAM")
                            {
                                $w->startElement("dte:Complementos");
                                    $w->startElement("dte:Complemento");
                                    $w->writeAttribute("IDComplemento","AbonosFacturaCambiaria");
                                    $w->writeAttribute("NombreComplemento","RetencionesFacturaEspecial");
                                    $w->writeAttribute("URIComplemento","http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0");
                                        $w->startElement("cfc:AbonosFacturaCambiaria");
                                        $w->writeAttribute("Version","1"); 
                                        $w->writeAttribute("xmlns:cfc","http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0");                                                                                    
                                            $w->startElement("cfc:Abono");
                                                $w->startElement("cfc:NumeroAbono");
                                                $w->text("1");
                                                $w->EndElement();

                                                $w->startElement("cfc:FechaVencimiento");
                                                $w->text($fechaVencimiento."");
                                                $w->EndElement();

                                                $w->startElement("cfc:MontoAbono");
                                                $w->text($granTotal);
                                                $w->EndElement();
                                            $w->EndElement();
                                        $w->EndElement();
                                    $w->EndElement();
                                $w->endElement();
                            }  
                    $w->endElement();
            $w->endElement();
        $w->endElement();
    $w->endElement();
$w->endElement();

    $var = $w->outputMemory(true);
    //echo $var;
    $xml = base64_encode($var);
    try {
        $Requestor= $requestor; 
        $Entity= $nitEmisorEntity; 
        $fecha=date("Y").'-'.date("n").'-'.date("j")."T".date("H").':'.date("i").':'.date("s");                                
        $Data3 = $fecha."-".$codigoEstablecimiento."-".$caja;
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
        CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="UTF-8"?>
        <SOAP-ENV:Envelope xmlns:ws="http://www.fact.com.mx/schema/ws" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
            <SOAP-ENV:Header/>
            <SOAP-ENV:Body>
                <ws:RequestTransaction>
                    <ws:Requestor>'.$Requestor.'</ws:Requestor>
                    <ws:Transaction>SYSTEM_REQUEST</ws:Transaction>
                    <ws:Country>GT</ws:Country>
                    <ws:Entity>'.$Entity.'</ws:Entity>
                    <ws:User>'.$Requestor.'</ws:User>
                    <ws:UserName>ADMINISTRADOR</ws:UserName>
                    <ws:Data1>POST_DOCUMENT_SAT</ws:Data1>
                    <ws:Data2>'.$xml.'</ws:Data2>
                    <ws:Data3>1234578979212745'.$Data3.'</ws:Data3>
                </ws:RequestTransaction>
            </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>
        ',CURLOPT_HTTPHEADER => array('Content-Type: text/xml'),));
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
        $s=json_encode($s);
        $s = json_decode($s);
        $resultado=$s->{'Result'};
        $detalle=$s->{'Description'};
        $tiempo=$s->{'TimeStamp'};
        if(strcmp($resultado,"true")==0)
        {
            $d=$s->{'Identifier'}; 
            $d=json_encode($d);
            $d = json_decode($d);

             $retornar = array(
                'Result' => 'true',
                'serie' => $d->{'Batch'},
                'numero' => $d->{'Serial'},
                'guid' => $d->{'DocumentGUID'},
                'TimeStamp' =>$tiempo,
                'TotalIva' => $totalIVA,
                'Link' => $d->{'InternalID'},
                'HoraEmision' => $horaEmision
            ); 
        }
        else
        {
            $retornar = array(
                'Result' => 'false',
                'Description' => $detalle
            );  
        }
    }
    catch(Exception $e)
    {
        echo 'Error: ' . $e->getMessage();
    }
    return $retornar;
}

function anularFactura($guid, $nitEmisor, $motivoAnulacion, $nitCliente, $fechaAnulacion, $fechaEmision,$requestor, $idFactura, $idSucursal){
    date_default_timezone_set('America/Guatemala'); 
    $invoice_date = date('Y-m-d\TH:i:s');
    
    $fechaActual = date("Y-m-d");

    $w=new XMLWriter();
    $w->openMemory();
    $w->startDocument('1.0','UTF-8');
        $w->startElement("dte:GTAnulacionDocumento");
            $w->writeAttribute("xmlns:dte","http://www.sat.gob.gt/dte/fel/0.1.0");
            $w->writeAttribute("xmlns:ds","http://www.w3.org/2000/09/xmldsig#");
            $w->writeAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
            $w->writeAttribute("Version","0.1");
            $w->writeAttribute("xsi:schemaLocation","http://www.sat.gob.gt/dte/fel/0.1.0 GT_AnulacionDocumento-0.1.0.xsd");
        
            $w->startElement("dte:SAT");
                $w->startElement("dte:AnulacionDTE");
                    $w->writeAttribute("ID","DatosCertificados");
                    $w->startElement("dte:DatosGenerales");
                        $w->writeAttribute("FechaEmisionDocumentoAnular",$fechaEmision);
                        $w->writeAttribute("FechaHoraAnulacion",$invoice_date);
                        $w->writeAttribute("ID","DatosAnulacion");
                        $w->writeAttribute("IDReceptor",$nitCliente);
                        $w->writeAttribute("MotivoAnulacion",$motivoAnulacion);
                        $w->writeAttribute("NITEmisor",$nitEmisor);
                        $w->writeAttribute("NumeroDocumentoAAnular",$guid);
                    $w->endElement();
                $w->endElement();
            $w->endElement();
        $w->endElement();
    $w->endElement();
    $var = $w->outputMemory(true);
 
    
    $codificado64 = base64_encode($var);
  
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
        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:ws="http://www.fact.com.mx/schema/ws">
        <SOAP-ENV:Header/>
        <SOAP-ENV:Body>
            <ws:RequestTransaction>
                <ws:Requestor>'.$requestor.'</ws:Requestor>
                <ws:Transaction>SYSTEM_REQUEST</ws:Transaction>
                <ws:Country>GT</ws:Country>
                <ws:Entity>'.$nitEmisor.'</ws:Entity>
                <ws:User>'.$requestor.'</ws:User>
                <ws:UserName>ADMINISTRADOR</ws:UserName>
                <ws:Data1>VOID_DOCUMENT</ws:Data1>
                <ws:Data2>'.$codificado64.'</ws:Data2>
                <ws:Data3>XML</ws:Data3>
            </ws:RequestTransaction>
        </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>',
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
    $s=json_encode($s);
    $s = json_decode($s);
    $resultado=$s->{'Result'};
    $detalle=$s->{'Description'};
    if(strcmp($resultado,"true")==0)
    {
        actualizar_estado_documento($idFactura, $idSucursal);
    }
    else
    {
        $detalle=base64_encode($detalle);
        echo json_encode(array("resultado"=>"false","detalle"=>$detalle));
    }
}

function actualizar_estado_documento($idFactura, $idSucursal){
    global $conexion;
    $query = mysqli_query($conexion, "select * from detalle_fact_ventas where id_factura = '$idFactura'");

    while ($row = mysqli_fetch_array($query)) {
        $id_producto = $row['id_producto'];
        $cantidad = $row['cantidad'];
        $row['importe_venta'];
        agregar_stock($id_producto, $cantidad, $idSucursal);
    }
    $update = mysqli_query($conexion, "update facturas_ventas set estado_documento= 'anulado', estado_factura = '3' where id_factura = '$idFactura'");
    echo json_encode(array("resultado"=>"true","detalle"=>"Exito"));
}

function agregar_stock($id_producto, $quantity, $id_sucursal)
{
    global $conexion;
    $update = mysqli_query($conexion, "update stock set cantidad_stock=cantidad_stock+'$quantity' where id_producto_stock='$id_producto' and id_sucursal_stock = '$id_sucursal'");
    if ($update) {
        return 1;
    } else {
        return 0;
    }

}


