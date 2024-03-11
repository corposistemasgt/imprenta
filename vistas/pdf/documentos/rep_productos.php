<?php
/* Connect To Database*/
require_once "../../db.php";
require_once "../../php_conexion.php";
#require_once "../libraries/inventory.php"; //Contiene funcion que controla stock en el inventario
//Inicia Control de Permisos
include "../../permisos.php";
//Archivo de funciones PHP
require_once "../../funciones.php";
//Ontengo variables pasadas por GET
//$user_id = $_SESSION['id_users'];
$id_categoria = intval($_REQUEST['categoria']);
//$tables       = "productos left join stock on productos.id_producto = stock.id_producto_stock,  lineas";
//$campos       = "*";
//$sWhere       = "lineas.id_linea=productos.id_linea_producto";

//$consultaSQLNueva = "SELECT * FROM productos inner join lineas on productos.id_linea_producto = lineas.id_linea order by nombre_producto asc ";
$consulta2 = "SELECT * FROM productos left join lineas on productos.id_linea_producto = lineas.id_linea left join stock on productos.id_producto = stock.id_producto_stock left join perfil on id_perfil =  id_sucursal_stock"; 
if ($id_categoria > 0) {
    $consulta2 .= " Where productos.id_linea_producto = '" . $id_categoria . "'";
}
$consulta2 .= " order by productos.id_producto";
//echo($consulta2." laconsulta en documentos rep_productos");
$query = mysqli_query($conexion, $consulta2);
// get the HTML
ob_start();
include dirname(__FILE__) . '/res/rep_productos_html.php';
$content = ob_get_clean();

// convert to PDF
require_once dirname(__FILE__) . '/../html2pdf.class.php';
try
{
    $html2pdf = new HTML2PDF('L', 'A4', 'es', true, 'UTF-8', 3);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    ob_end_clean();
    $html2pdf->Output('productos.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
