<?php
if ($conexion) {
    /*Datos de la empresa*/

    //echo("JOLA");
    //$id_vendedor    = intval($_SESSION['id_users']);
    $id_vendedor    = 1;
    //echo($id_vendedor."----idVendedor");
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    //echo("select * from users where id_users = '".$id_vendedor."'");
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
  
    
    $sql2           = mysqli_query($conexion, "SELECT * FROM perfil where codigoEstablecimiento = '1'");
    //echo("SELECT * FROM perfil where codigoEstablecimiento = '".$id_sucursal."'");
    $row            = mysqli_fetch_array($sql2);

    //$sql           = mysqli_query($conexion, "SELECT * FROM perfil");
    //$rw            = mysqli_fetch_array($sql);
    $moneda        = $row["moneda"];
    $bussines_name = $row["nombre_empresa"];
    $address       = $row["direccion"];
    $city          = $row["ciudad"];
    $state         = $row["estado"];
    $postal_code   = $row["codigo_postal"];
    $phone         = $row["telefono"];
    $email         = $row["email"];
    $logo_url      = $row["logo_url"];

    //echo($logo_url);
/*Fin datos empresa*/
    ?>
    <table cellspacing="0" style="width: 100%;"  border="0">
        <tr>

            <td style="width: 18%;">
                <img style="width: 100%;" src="../<?php echo $logo_url; ?>" alt="Logo"><br>

            </td>
              <td style="width: 12%;"></td>
            <td style="width: 45%; font-size:12px;text-align:center">
                <span style="font-size:14px;font-weight:bold"><?php echo $bussines_name; ?></span>
                <br><?php echo $address . ', ' . $city . ', ' . $state; ?><br>
                Teléfono: <?php echo $phone; ?><br>
                Email: <?php echo $email; ?>

            </td>
            <td style="width: 30%;text-align:right; color:#ff0000">

            </td>

        </tr>
    </table>
    <?php }?>