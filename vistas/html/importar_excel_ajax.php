<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

    if(is_array($_FILES['archivoexcel']) && count($_FILES['archivoexcel']) > 0)
    {
        /* Connect To Database*/
        require_once "../db.php"; //Contiene las variables de configuracion para conectar a la base de datos
        require_once "../php_conexion.php"; //Contiene funcion que conecta a la base de datos
        //FIN
        /** Se agrega la libreria PHPExcel */
        require_once '../excel/lib/PHPExcel/PHPExcel.php';

        $tmpfname = $_FILES['archivoexcel']['tmp_name'];
        
      // Crear el objeto PHPExcel_Reader_Excel2007
    $leerExcel = PHPExcel_IOFactory::createReader('Excel2007');
    $excelobj = $leerExcel->load($tmpfname);

    // Seleccionar la hoja
    $hoja = $excelobj->getSheet(0);
    $filas = $hoja->getHighestRow();


        echo "<table id = 'tabla_detalle' class ='table' style='width:100%';
                table-layout:fixed>
                <thead>
                    <tr bgColor = 'black' style ='color:#FFF'>
                        <td>Categoria</td>
                        <td>Codigo</td>
                        <td>Producto</td>
                        <td>Descripción</td>
                        <td>IVA</td>
                        <td>Costo</td>
                        <td>Precio1</td>
                        <td>Precio2</td>
                        <td>Precio3</td>
                        <td>Precio4</td>
                        <td>Cantidad Inv.</td>
                        <td>Cant. Mínima</td>
                        <td>es Genérico</td>
                        <td>F. Vencimiento</td>
                        <td>Proveedor</td>
                    </tr>
                </thead>
                <tbody id='tbody_tabla_detalle'>";
        
        for($i = 2; $i <= $filas; $i++)
        {
            $categoria = $hoja ->getCell('A'.$i)->getValue();
            $codigo = $hoja ->getCell('B'.$i)->getValue();
            $producto = $hoja ->getCell('C'.$i)->getValue();
            $descripcion = $hoja ->getCell('D'.$i)->getValue();
            $iva = $hoja ->getCell('E'.$i)->getValue();
            $costo = $hoja ->getCell('F'.$i)->getValue();
            $precio1 = $hoja ->getCell('G'.$i)->getValue();
            $precio2 = $hoja ->getCell('H'.$i)->getValue();
            $precio3 = $hoja ->getCell('I'.$i)->getValue();
            $precio4 = $hoja ->getCell('J'.$i)->getValue();
            $cantInventario = $hoja ->getCell('K'.$i)->getValue();
            $cantMin = $hoja ->getCell('L'.$i)->getValue();
            $generico = $hoja ->getCell('M'.$i)->getValue();
            $vence = $hoja ->getCell('N'.$i)->getValue();
            $prove = $hoja ->getCell('O'.$i)->getValue();
            /*if(empty($codigo) || empty($categoria) || 
            empty($producto) || isset($precio1) || empty($descripcion) ||
            isset($iva) || isset($costo) || isset($precio2) || 
            isset($precio3) || isset($precio4) || isset($cantInventario) || empty($cantMin) || empty($generico))
            {
                //echo("continue '$codigo' cod / '$categoria' cat / '$producto' prod / '$precio1' p1/ '$descripcion desc<br>");
                //echo("'$iva' / $costo / $precio2 / $precio3 p3 / $cantInventario inv / $cantMin min / $generico gen");
                continue;
            }*/
            echo " <tr>";
            echo "<td>".$categoria." </td>";
            echo "<td>".$codigo." </td>";
            echo "<td>".$producto." </td>";
            echo "<td>".$descripcion." </td>";
            echo "<td>".$iva." </td>";
            echo "<td>".$costo." </td>";
            echo "<td>".$precio1." </td>";
            echo "<td>".$precio2." </td>";
            echo "<td>".$precio3." </td>";
            echo "<td>".$precio4." </td>";
            echo "<td>".$cantInventario." </td>";
            echo "<td>".$cantMin." </td>";
            echo "<td>".$generico." </td>";
            echo "<td>".$vence." </td>";
            echo "<td>".$prove." </td>";
            echo "</tr>";

        }

        echo"</tbody> </table>";
    }

?>