<?php
    try
    {  
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://corpo-sistemas.com/corpoconnect/verificar_caja.php?pass='.$_POST['pass'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',));
        $response = curl_exec($curl);
        $res='';
        curl_close($curl);
        $response='['.$response.']';
        $array = json_decode($response, true);
        foreach ($array as $value) 
        {
            $res=$value['resultado'];
        }
        if (strcmp($res,'true')==0) 
        {
            http_response_code(200);
            echo "Abriendo caja";
        }
        else
        {

            http_response_code(400);
            echo "La contraseña no coincide";
        }
      
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al abrir caja".$e;
    }
?>