<?php
  $nit=$_GET['nit'];
  include "../db.php";
  include "../php_conexion.php";
  $sql    = mysqli_query($conexion, "select * from clientes where fiscal_cliente='".$nit."'");
  $vv="";
  while ($row = mysqli_fetch_array($sql)) 
    {
      $vv= json_encode(array("resultado"=>"true","nombre"=>$row["nombre_cliente"],
      "correos"=>$row["email_cliente"],"direccion"=>$row["direccion_cliente"],"telefono"=>$row["telefono_cliente"]));
      echo trim($vv);
    }
  if(strcmp($vv,"")==0)
  { 
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://corpo-sistemas.com/corpoconnect/corpo/consultanit.php?nit='.$nit,
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
    echo $response;
  }
?>
