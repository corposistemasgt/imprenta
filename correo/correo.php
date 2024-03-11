<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'Exception.php';
    require 'PHPMailer.php';
    require 'SMTP.php';
    if($_SERVER['REQUEST_METHOD']=='POST')
    {
      $data = json_decode(file_get_contents('php://input'), true);
      if(is_array($data)!=true)
      {
        echo json_encode(array("resultado"=>"false","detalles"=>"El Json debe ir en el Body"));;
      }
      else
      {
       
        $receptor=$data['receptor'];
        $emisor=$data['emisor'];
        $nitemisor=$data['nitemisor'];
        $guid=$data['guid'];
        $fecha=$data['fecha_emision'];
        $tipo=$data['tipo'];
        $total=$data['total'];
        $link=$data['link'];
        $correo=$data['correo'];
        $requestor=$data['requestor'];
        if(strcmp($receptor,'')==0 || strcmp($emisor,'')==0 || strcmp($nitemisor,'')==0 || strcmp($guid,'')==0 || 
        strcmp($fecha,'')==0 || strcmp($tipo,'')==0 || strcmp($total,'')==0 || strcmp($link,'')==0 || 
        strcmp($correo,'')==0 ||strcmp($requestor,'')==0)
        {
          $campos="Faltan los siguientes campos: ";
          if(isset($_POST ['receptor'])==false)
          {
              $campos.=" receptor,";
          }
          if(isset($_POST ['emisor'])==false)
          {
              $campos.=" emisor,";
          }
          if(isset($_POST ['nitemisor'])==false)
          {
              $campos.=" nitemisor,";
          }
          if(isset($_POST ['guid'])==false)
          {
              $campos.=" guid,";
          }
          if(isset($_POST ['fecha_emision'])==false)
          {
              $campos.=" fecha_emision,";
          }
          if(isset($_POST ['tipo'])==false)
          {
              $campos.=" tipo,";
          }
          if(isset($_POST ['total'])==false)
          {
              $campos.=" total,";
          }
          if(isset($_POST ['link'])==false)
          {
              $campos.=" link,";
          }
          if(isset($_POST ['correo'])==false)
          {
              $campos.=" correo,";
          }
          if(isset($_POST ['requestor'])==false)
          {
              $campos.=" requestor,";
          }
          $campos=trim($campos, ',');
          echo json_encode(array("resultado"=>"false","detalles"=>$campos)); 
        }
        else
        {
          date_default_timezone_set('America/Guatemala'); 
          try{
            $mail = new PHPMailer(true);       
            $mail->isSMTP();                                        
            $mail->Host = 'mail.smtp2go.com';                 
            $mail->SMTPAuth = true;                             
            $mail->Username = 'postmaster@corposistemasgt.com';             
            $mail->Password = 'Corpo2021';                       
            $mail->Port = 2525;        
            $mail->setFrom('postmaster@corposistemasgt.com', 'Corposistemas');
            $aKeyword = explode(",",strtolower(trim($correo)));  
            for($i = 0; $i < count($aKeyword); $i++) 
            {
              if(!empty($aKeyword[$i])) 
              {
                  $mail->addAddress(''.$aKeyword[$i], $receptor); 
                 // echo $aKeyword[$i];
              }
            } 
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
            CURLOPT_POSTFIELDS =>'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://www.fact.com.mx/schema/ws">
            <SOAP-ENV:Header/>
            <SOAP-ENV:Body>
                <ws:RequestTransaction>
                    <ws:Requestor>'.$requestor.'</ws:Requestor>
                    <ws:Transaction>GET_DOCUMENT</ws:Transaction>
                    <ws:Country>GT</ws:Country>
                    <ws:Entity>800000001026</ws:Entity>
                    <ws:User>'.$requestor.'</ws:User>
                    <ws:UserName>ADMINISTRADOR</ws:UserName>
                    <ws:Data1>'.$guid.'</ws:Data1>
                    <ws:Data2></ws:Data2>
                    <ws:Data3>XML PDF</ws:Data3>
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
            $d=$s->{'Response'}; 
            $d=json_encode($d);
            $d = json_decode($d);
            if(strcmp($d->{'Result'},'true')==0)
            {
              $d=$s->{'ResponseData'}; 
              $d=json_encode($d);
              $d = json_decode($d);
              $xml=$d->{'ResponseData1'};
              $pdf=$d->{'ResponseData3'};
              $resx=base64_decode($xml);
              $resp=base64_decode($pdf);
  
  
              $fe = date('dhms');
              $archivo = fopen($fe.'pp.xml', "w+b");  
              if( $archivo == false )
                echo "Error al crear el archivo";
              else
                  fwrite($archivo,$resx);
              fclose($archivo);
          
              $archivo = fopen($fe.'pp.pdf', "w+b");   
              if( $archivo == false )
                echo "Error al crear el archivo2";
              else
                  fwrite($archivo,$resp);
              fclose($archivo);
              $mail->addAttachment($fe.'pp.pdf');  
              $mail->addAttachment($fe.'pp.xml'); 
              $mail->isHTML(true);                                  
              $mail->Subject = 'Notificacion de Envio de Documento Tributario Electronico';
              $mail->Body='<!DOCTYPE html
              PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office"
              style="font-family:arial, "helvetica neue", helvetica, sans-serif">
            
            <head>
              <meta charset="UTF-8">
              <meta content="width=device-width, initial-scale=1" name="viewport">
              <meta name="x-apple-disable-message-reformatting">
              <meta http-equiv="X-UA-Compatible" content="IE=edge">
              <meta content="telephone=no" name="format-detection">
              <title>Nuevo mensaje</title>
              <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i" rel="stylesheet">
              <style type="text/css">
                #outlook a {
                  padding: 0;
                }
                .es-button {
                  mso-style-priority: 100 !important;
                  text-decoration: none !important;
                }
                a[x-apple-data-detectors] {
                  color: inherit !important;
                  text-decoration: none !important;
                  font-size: inherit !important;
                  font-family: inherit !important;
                  font-weight: inherit !important;
                  line-height: inherit !important;
                }
                .es-desk-hidden {
                  display: none;
                  float: left;
                  overflow: hidden;
                  width: 0;
                  max-height: 0;
                  line-height: 0;
                  mso-hide: all;
                }
                [data-ogsb] .es-button {
                  border-width: 0 !important;
                  padding: 10px 20px 10px 20px !important;
                }
                @media only screen and () {
                  p,
                  ul li,
                  ol li,
                  a {
                    line-height: 150% !important
                  }
                  h1,
                  h2,
                  h3,
                  h1 a,
                  h2 a,
                  h3 a {
                    line-height: 120%
                  }
                  h1 {
                    font-size: 30px !important;
                    text-align: left
                  }
                  h2 {
                    font-size: 24px !important;
                    text-align: left
                  }
            
                  h3 {
                    font-size: 20px !important;
                    text-align: left
                  }
            
                  .es-header-body h1 a,
                  .es-content-body h1 a,
                  .es-footer-body h1 a {
                    font-size: 30px !important;
                    text-align: left
                  }
            
                  .es-header-body h2 a,
                  .es-content-body h2 a,
                  .es-footer-body h2 a {
                    font-size: 24px !important;
                    text-align: left
                  }
            
                  .es-header-body h3 a,
                  .es-content-body h3 a,
                  .es-footer-body h3 a {
                    font-size: 20px !important;
                    text-align: left
                  }
            
                  .es-menu td a {
                    font-size: 14px !important
                  }
            
                  .es-header-body p,
                  .es-header-body ul li,
                  .es-header-body ol li,
                  .es-header-body a {
                    font-size: 14px !important
                  }
            
                  .es-content-body p,
                  .es-content-body ul li,
                  .es-content-body ol li,
                  .es-content-body a {
                    font-size: 14px !important
                  }
            
                  .es-footer-body p,
                  .es-footer-body ul li,
                  .es-footer-body ol li,
                  .es-footer-body a {
                    font-size: 14px !important
                  }
            
                  .es-infoblock p,
                  .es-infoblock ul li,
                  .es-infoblock ol li,
                  .es-infoblock a {
                    font-size: 12px !important
                  }
            
                  *[class="gmail-fix"] {
                    display: none !important
                  }
            
                  .es-m-txt-c,
                  .es-m-txt-c h1,
                  .es-m-txt-c h2,
                  .es-m-txt-c h3 {
                    text-align: center !important
                  }
            
                  .es-m-txt-r,
                  .es-m-txt-r h1,
                  .es-m-txt-r h2,
                  .es-m-txt-r h3 {
                    text-align: right !important
                  }
            
                  .es-m-txt-l,
                  .es-m-txt-l h1,
                  .es-m-txt-l h2,
                  .es-m-txt-l h3 {
                    text-align: left !important
                  }
            
                  .es-m-txt-r img,
                  .es-m-txt-c img,
                  .es-m-txt-l img {
                    display: inline !important
                  }
            
                  .es-button-border {
                    display: inline-block !important
                  }
            
                  a.es-button,
                  button.es-button {
                    font-size: 18px !important;
                    display: inline-block !important
                  }
            
                  .es-adaptive table,
                  .es-left,
                  .es-right {
                    width: 100% !important
                  }
            
                  .es-content table,
                  .es-header table,
                  .es-footer table,
                  .es-content,
                  .es-footer,
                  .es-header {
                    width: 100% !important;
                    
                  }
            
                  .es-adapt-td {
                    display: block !important;
                    width: 100% !important
                  }
            
                  .adapt-img {
                    width: 100% !important;
                    height: auto !important
                  }
            
                  .es-m-p0 {
                    padding: 0px !important
                  }
            
                  .es-m-p0r {
                    padding-right: 0px !important
                  }
            
                  .es-m-p0l {
                    padding-left: 0px !important
                  }
            
                  .es-m-p0t {
                    padding-top: 0px !important
                  }
            
                  .es-m-p0b {
                    padding-bottom: 0 !important
                  }
            
                  .es-m-p20b {
                    padding-bottom: 20px !important
                  }
            
                  .es-mobile-hidden,
                  .es-hidden {
                    display: none !important
                  }
            
                  tr.es-desk-hidden,
                  td.es-desk-hidden,
                  table.es-desk-hidden {
                    width: auto !important;
                    overflow: visible !important;
                    float: none !important;
                    max-height: inherit !important;
                    line-height: inherit !important
                  }
            
                  tr.es-desk-hidden {
                    display: table-row !important
                  }
            
                  table.es-desk-hidden {
                    display: table !important
                  }
            
                  td.es-desk-menu-hidden {
                    display: table-cell !important
                  }
            
                  .es-menu td {
                    width: 1% !important
                  }
            
                  table.es-table-not-adapt,
                  .esd-block-html table {
                    width: auto !important
                  }
            
                  table.es-social {
                    display: inline-block !important
                  }
            
                  table.es-social td {
                    display: inline-block !important
                  }
            
                  .es-desk-hidden {
                    display: table-row !important;
                    width: auto !important;
                    overflow: visible !important;
                    max-height: inherit !important
                  }
            
                  .h-auto {
                    height: auto !important
                  }
                }
              </style>
            </head>
            
            <body
              style="width:100%;font-family:arial, "helvetica neue", helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
              <div class="es-wrapper-color" style="background-color:#F6F6F6">
                <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0"
                  style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top">
                  <tr>
                    <td valign="top" style="padding:0;Margin:0">
                      <table class="es-header" cellspacing="0" cellpadding="0" align="center"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
                        <tr>
                          <td align="center" style="padding:0;Margin:0">
                            <table class="es-header-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                              style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
                                  <table cellpadding="0" cellspacing="0" width="100%"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                      <td align="center" valign="top" style="padding:0;Margin:0;">
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                          style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                          <tr>
                                            <td align="center" style="padding:0;Margin:0;font-size:0px"><img class="adapt-img"
                                                src="https://app.corposistemasgt.com/invoice/logos/a.png" alt
                                                style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                                width="150"></td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <table cellpadding="0" cellspacing="0" class="es-content" align="center"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                        <tr>
                          <td align="center" style="padding:0;Margin:0">
                            <table class="es-content-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                              style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
                                  <table width="100%" cellspacing="0" cellpadding="0"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                      <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                        <table width="100%" cellspacing="0" cellpadding="0"
                                          style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                          <tr>
                                            <td align="center" style="padding:0;Margin:0">
                                              <p
                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;color:#ff0000;font-size:14px">
                                                <span style="font-size:20px">
            
                                                  <font style="vertical-align:inherit">CLIENTE
                                                    ESTIMADO, </font>
                                                  </font>
                                                  <br/>
                                                </span>&nbsp;<span style="font-size:30px;">'.$receptor.'</span>
                                              </p>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <table cellpadding="0" cellspacing="0" class="es-content" align="center"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                        <tr>
                          <td align="center" style="padding:0;Margin:0">
                            <table class="es-content-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                              style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
                                  <table width="100%" cellspacing="0" cellpadding="0"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                      <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                        <table width="100%" cellspacing="0" cellpadding="0"
                                          style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                          <tr>
                                            <td align="center" style="padding:30px;padding-bottom:10px;Margin:0">
                                              <h2
                                                style="Margin:0;line-height:29px;mso-line-height-rule:exactly;font-family:"open sans", "helvetica neue", helvetica, arial, sans-serif;font-size:24px;font-style:normal;font-weight:normal;color:#333333;text-align:justify">
                                                <font style="vertical-align:inherit">
            
                                                  <span style="color:#8c8c8c">
            
                                                    <font style="vertical-align:inherit">'.$emisor.' </font>
                                                  </span>
            
                                                  <span style="color:#bbbd4b">
            
                                                    <font style="vertical-align:inherit">
                                                      (NIT: '.$nitemisor.')
                                                    </font>
            
                                                  </span>
                                                  <span style="color:#8c8c8c">
            
                                                    <font style="vertical-align:inherit">
                                                      &nbsp;le envia un 
                                                    </font>
            
                                                  </span>
            
                                                  <span style="color:#FF0000">
            
                                                    <font style="vertical-align:inherit">
                                                      documento electr&oacute;nico
                                                    </font>
                                                  </span>
                                                  <span style="color:#8c8c8c">
                                                    <font style="vertical-align:inherit">
                                                    adjunto.&nbsp;
                                                    </font>
                                                  </span>
                                              </h2>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <table class="es-content" cellspacing="0" cellpadding="0" align="center"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                        <tr>
                          <td align="center" style="padding:0;Margin:0">
                            <table class="es-content-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                              style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                <td align="left" style="padding:0;Margin:0;padding-top:15px;padding-right:20px;padding-left:40px">
                                  <table width="100%" cellspacing="0" cellpadding="0"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                      <td valign="top" align="center" style="padding:0;Margin:0;width:540px">
                                        <table width="100%" cellspacing="0" cellpadding="0"
                                          style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                          <tr>
                                            <td align="center" style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:54px;color:#8c8c8c;font-size:20px"">
                                              <font>Detalle del documento</font>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td align="left"
                                              style="Margin:0;padding-top:20px;padding-bottom:20px;padding-right:20px;padding-left:40px">
                                              <p
                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:54px;color:#8c8c8c;font-size:30px">
                                                <strong>
            
                                                  <font style="vertical-align:inherit">
                                                    NUMERO:
                                                  </font>
            
                                                </strong><br>
                                                  <font style="vertical-align:inherit">'.$guid.'</font>
                                              </p>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td align="left"
                                              style="Margin:0;padding-top:20px;padding-bottom:20px;padding-right:20px;padding-left:40px">
                                              <p
                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:54px;color:#8c8c8c;font-size:30px">
                                                <strong>
            
                                                  <font style="vertical-align:inherit">
                                                    FECHA:
                                                  </font>
            
                                                </strong><br>
                                                <font style="vertical-align:inherit">
                                                  <font style="vertical-align:inherit">'.$fecha.'</font>
                                                </font>
                                              </p>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td align="left"
                                              style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:40px;padding-right:40px">
                                              <p
                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:54px;color:#8c8c8c;font-size:30px">
                                                <strong>
            
                                                  <font style="vertical-align:inherit">
                                                    TIPO: </font>
            
                                                  </font>
                                                </strong><br>
            
                                                <font style="vertical-align:inherit">'.$tipo.' </font>
            
                                              </p>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td align="left"
                                              style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:40px;padding-right:40px">
                                              <p
                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:54px;color:#8c8c8c;font-size:30px">
                                                <strong>
            
                                                  <font style="vertical-align:inherit">
                                                    TOTAL: </font>
            
                                                  </font>
                                                </strong><br>
            
                                                <font style="vertical-align:inherit">'.number_format($total, 2).' GTQ
                                                </font>
            
                                              </p>
                                            </td>
                                          </tr>
                                          <tr>
                                          <td align="center"
                                          style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:40px;padding-right:40px">
                                         
                                            
                                            <span style="color:#8c8c8c">
          
                                              <font style="vertical-align:inherit">
                                                Para descargar tu factura:
                                              </font>
                                            </span>
                                            <a href="'.$link.'">
                                              <button  style="background-color: #ff0f1e;color: white;border-radius: 40px;padding:  15; border: none; height:30px;font-weight:bold;";> Click Aqui! </button>                                    
                                            </a>
                                           </td>
                                        </tr>
          
          
          
          
                                          <tr>
                                            <td align="center" style="padding:20px;Margin:0;font-size:0">
                                              <table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr>
                                                  <td
                                                    style="padding:0;Margin:0;border-bottom:3px dashed #ffd042;background:unset;height:1px;width:100%;margin:0px">
                                                  </td>
                                                </tr>
                                              </table>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td align="center" style="padding:0;Margin:0">
                                              <p
                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, "helvetica neue", helvetica, sans-serif;line-height:27px;color:#8c8c8c;font-size:18px">
            
                                                <font style="vertical-align:inherit">MAS QUE
                                                  FACTURAS </font>
                                                <br>
            
                                                <font style="vertical-align:inherit">ELECTRONICAS
                                                </font>
                                                <br>
                                              </p>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <table cellpadding="0" cellspacing="0" class="es-content" align="center"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                        <tr>
                          <td align="center" style="padding:0;Margin:0">
                            <table class="es-content-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                              style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                <td align="left" bgcolor="#ff0f1e"
                                  style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;background-color:#ff0f1e">
                                  <table width="100%" cellspacing="0" cellpadding="0"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                      <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                        <table width="100%" cellspacing="0" cellpadding="0"
                                          style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                          <tr>
                                            <td class="h-auto" align="center" valign="middle" height="217"
                                              style="padding:0;Margin:0">
                                              <h2
                                                style="Margin:0;line-height:31px;mso-line-height-rule:exactly;font-family:"open sans", "helvetica neue", helvetica, arial, sans-serif;font-size:31px;font-style:normal;font-weight:normal;color:#ffffff">
            
                                                <font style="vertical-align:inherit">
                                                  INFORMACION
                                                </font>
            
                                              </h2>
                                              <p
                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:"open sans", "helvetica neue", helvetica, arial, sans-serif;line-height:31px;color:#ffffff;font-size:31px">
                                                <span style="font-size:20px;line-height:20px">
            
                                                  <font style="vertical-align:inherit">
                                                    DE CONTACTO:
                                                  </font>
            
                                                </span>
            
                                          
                                                <br><br><span style="font-size:20px">
            
                                                  <font style="vertical-align:inherit">
                                                    <img src="https://app.corposistemasgt.com/invoice/logos/telefono.png" height="25px" align="center"/>:
                                                    7951-1815
                                                  </font>
            
                                                  <br>
            
                                                  <a style="text-decoration: none; color: #fff" href="https://facebook.com/corpofel" target="_blank"><font style="vertical-align:inherit">
                                                    <img src="https://app.corposistemasgt.com/invoice/logos/facebook.png" height="30px" align="center"/>:
                                                    Corposistemas
                                                    Guatemala
                                                  </font></a>
                                                  <br>
            
                                                  <a style="text-decoration: none; color: #fff" href="mailto:soporte@corposistemasgt.com"><font style="vertical-align:inherit">
                                                    <img src="https://app.corposistemasgt.com/invoice/logos/correo.png" height="28px" align="center" />:&nbsp;
                                                  </font>
            
                                                  <font style="vertical-align:inherit">
                                                    soporte@corposistemasgt.com
                                                  </font></a>
                                                  <br><br>
                                              </p>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
            </body>
            </html>';
              $mail->send();
              echo json_encode(array("resultado"=>"true","detalles"=>"Correo enviado correctamente"));
              unlink($fe.'pp.xml');
              unlink($fe.'pp.pdf');
            }
            else
            {
              echo json_encode(array("resultado"=>"false","detalles"=>"No se ecnuentra el Documento"));
            }
          }
          catch (Exception $e) 
          {
            echo json_encode(array("resultado"=>"false","detalles"=>"Error de Envio".$mail->ErrorInfo));
          }
          
        }
      }
    }
    else
    {
        echo json_encode(array("resultado"=>"false","detalles"=>"Metodo Incorrecto usa un POST"));
    }
  ?>