<?php
if ($conexion) {
    /*Datos de la empresa*/
    $id_vendedor    = intval($_SESSION['id_users']);
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
  
    
    $sql           = mysqli_query($conexion, "SELECT * FROM perfil where id_perfil = '".$id_sucursal."'");
    $rw            = mysqli_fetch_array($sql);
    $moneda        = $rw["moneda"];
    $bussines_name = $rw["nombre_empresa"];
    $address       = $rw["direccion"];
    $city          = $rw["ciudad"];
    $state         = $rw["estado"];
    $nombreComer   =$rw["giro_empresa"];
    $postal_code   = $rw["codigo_postal"];
    $phone         = $rw["telefono"];
    $email         = $rw["email"];
    $logo_url      = $rw["logo_url"];
    $nitEmisor      = $rw["fiscal_empresa"];


    date_default_timezone_set('America/Guatemala'); 
    $fechaEmision = date('d-m-Y H:i:s');

/*Fin datos empresa*/
    ?>
    <table cellspacing="0" style="width: 100%;"  border="0">
        <tr>

            <td style="width: 15%;">
                <img style="width: 100%;" src="../<?php echo $logo_url; ?>" alt="Logo"><br>

            </td>          
              <td style="width: 8%;"></td>
            <td style="width: 45%; font-size:12px;text-align:center">
            <span style="font-size:14px;font-weight:bold"><?php echo $nombreComer; ?></span><br>
                <span style="font-size:11px;font-weight:bold"><?php echo $bussines_name; ?></span>
                <br><?php echo $address . ', ' . $city . ', ' . $state; ?><br>
                <?php echo $nitEmisor; ?><br>
                Teléfono: <?php echo $phone; ?><br>
                Email: <?php echo $email; ?>

            </td>
            <td style="width: 48%; font-size:9px; text-align:center; color:#ff0000">
                
               <?php 
               
               
               if($estadoFactura == 1 || $tipoFact == "FCAM")
                    {?>
                        Serie: <?php echo $batch; ?><br>
                        NO.: <?php echo $numero_factura; ?><br>
                        Fecha Emisión: <?php echo $fechaEmision; ?><br>
                   <?php }    
                    else{
                        ?>
                        Fecha Emisión: <?php echo $fechaEmision; ?><br>
                    <?php } ?>
            
            </td>
        </tr>
        
        <tr>
            <td style="width: 18%;"></td>
            <td style="width: 12%;"></td>

            <td style="width: 45%; font-size:12px;text-align:center">
                <?php 
                    if($estadoFactura == 1 || $tipoFact == "FCAM")
                    {
                        echo("DOCUMENTO TRIBUTARIO ELECTRÓNICO");
                    }    
                    else{
                        echo("DOCUMENTO DE ENVÍO");
                    }
                ?>
                
            </td>
        </tr>
        <tr>
            <td style="width: 18%;"></td>
            <td style="width: 12%;"></td>

            <td style="width: 45%; font-size:12px;text-align:center">
               <?php 

                    if($estadoFactura == 1)
                    {
                        if($tipoFact == "FACT")
                        {
                            echo("FACTURA");
                        }else if($tipoFact == "FCAM")
                        {
                            echo("FACTURA CAMBIARIA");
                        } 
                    }
                      
               
               ?>
            </td>
        </tr>
    </table>
    <?php }?>