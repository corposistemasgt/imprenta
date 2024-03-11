<?php
    include 'is_logged.php';
    require_once "../db.php";
    require_once "../php_conexion.php";
    try
    {
        $tipo=$_GET['tipo'];
        $id=$_GET['id'];
        switch ($tipo) {
            case 1:
                $sql = mysqli_query($conexion, "select nombre_producto as n from productos where id_producto=".$_GET['v1']);
                while ($row = mysqli_fetch_array($sql)) {
                    $n=$row["n"];    
                } 
                echo base64_encode(json_encode(array("t"=>1,"n"=>$n,"c"=>$_GET['v3'],"p"=>$_GET['v2'],"d"=>"0","i"=>$_GET['v1'])));
                break;
            case 2:
                $sql = mysqli_query($conexion, "select nombre_producto as n,precio_tmp as p,productos.id_producto as i from tmp_ventas,productos where productos.id_producto=tmp_ventas.id_producto and 
                session_id='".$id."' and codigo_producto='".$_GET['v1']."'");
                while ($row = mysqli_fetch_array($sql)) {
                    $n=$row["n"];  
                    $p=$row["p"];   
                    $i=$row["i"];   
                    
                }  
                echo base64_encode(json_encode(array("t"=>1,"n"=>$n,"c"=>$_GET['v3'],"p"=>$p,"d"=>"0","i"=>$i))); 
            break;
            case 3:
                $sql = mysqli_query($conexion, "select id_producto as n from tmp_ventas where id_tmp=".$_GET['v1']);      
                while ($row = mysqli_fetch_array($sql)) {
                    $i=$row["n"];                     
                }                
                echo base64_encode(json_encode(array("t"=>2,"n"=>"","c"=>"","p"=>"","d"=>"","i"=>$i))); 
            break;
            case 4:
                $sql = mysqli_query($conexion, "select nombre_producto as n,cantidad_tmp as c,precio_tmp as p,desc_tmp as d,productos.id_producto as i from 
                tmp_ventas,productos where productos.id_producto=tmp_ventas.id_producto and 
                session_id='".$id."' and id_tmp='".$_GET['v1']."'");
                while ($row = mysqli_fetch_array($sql)) {
                    $n=$row["n"];  
                    $p=$row["p"]; 
                    $c=$row["c"]; 
                    $d=$row["d"];   
                    $i=$row["i"];   
                    
                } 
                echo base64_encode(json_encode(array("t"=>3,"n"=>$n,"c"=>$c,"p"=>$p,"d"=>$d,"i"=>$i))); 
            break;
            case 5:
                $sql = mysqli_query($conexion, "select nombre_producto as n,cantidad_tmp as c,precio_tmp as p,desc_tmp as d,productos.id_producto as i from 
                tmp_ventas,productos where productos.id_producto=tmp_ventas.id_producto and 
                session_id='".$id."' and id_tmp='".$_GET['v1']."'");
                while ($row = mysqli_fetch_array($sql)) {
                    $n=$row["n"];  
                    $p=$row["p"]; 
                    $c=$row["c"]; 
                    $d=$row["d"];   
                    $i=$row["i"];   
                } 
                echo base64_encode(json_encode(array("t"=>3,"n"=>$n,"c"=>$c,"p"=>$p,"d"=>$d,"i"=>$i))); 
            break;
            case 6:   
                
                echo base64_encode(json_encode(array("t"=>4))); 
            break;
        }
        http_response_code(200);
    }
    catch(Exception $e)
    {
        http_response_code(400);
    }
?>