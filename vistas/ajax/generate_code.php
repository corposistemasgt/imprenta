<?php 
if(isset($_POST) && !empty($_POST)) {
    //echo("entra a generate_code.php");
    include('../phpqrcode/qrlib.php'); 
    $codesDir = "codes/";   
    $codeFile = date('d-m-Y-h-i-s').'.png';
    //$codeFile = '123.png';
    QRcode::png($_POST['formData'], $codesDir.$codeFile,"H", 5); 
    //echo("<br>--".$codesDir.$codeFile."--");
    echo '<center><img class="img-thumbnail" src="../ajax/'.$codesDir.$codeFile.'" /></center>';
} else {
    echo("va al else");
    //header('location:./');
}
?>