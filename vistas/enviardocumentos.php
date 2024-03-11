<?php

$curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://corpo-sistemas.com/corpoenvio/correo.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
      "receptor":"'.base64_decode($_GET['receptor']).'",
      "emisor":"'.base64_decode($_GET['emisor']).'",
      "nitemisor":"'.base64_decode($_GET['nitemisor']).'",
      "requestor":"'.base64_decode($_GET['requestor']).'",
      "guid":"'.base64_decode($_GET['guid']).'",
      "fecha_emision":"'.base64_decode($_GET['fecha']).'",
      "tipo":"'.base64_decode($_GET['tipo']).'",
      "total":"'.base64_decode($_GET['total']).'",
      "link":"'.base64_decode($_GET['link']).'",
      "correo":"'.base64_decode($_GET['corr']).'",
      "telefono":"'.base64_decode($_GET['tel']).'"
  }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));
  
  $response = curl_exec($curl);
  echo '{
    "receptor":"'.base64_decode($_GET['receptor']).'",
    "emisor":"'.base64_decode($_GET['emisor']).'",
    "nitemisor":"'.base64_decode($_GET['nitemisor']).'",
    "requestor":"'.base64_decode($_GET['requestor']).'",
    "guid":"'.base64_decode($_GET['guid']).'",
    "fecha_emision":"'.base64_decode($_GET['fecha']).'",
    "tipo":"'.base64_decode($_GET['tipo']).'",
    "total":"'.base64_decode($_GET['total']).'",
    "link":"'.base64_decode($_GET['link']).'",
    "correo":"'.base64_decode($_GET['corr']).'",
    "telefono":"'.base64_decode($_GET['tel']).'"
}';
  curl_close($curl);
  echo $response;
