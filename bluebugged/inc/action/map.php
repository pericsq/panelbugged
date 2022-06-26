<?php	
define('wcode.ro',true);

if(!isset($_POST['x']) || !isset($_POST['y'])) return;

header('Content-Type: image/png');

$im = imagecreatefrompng('../../assets/img/map.png');

$house = imagecreatefromgif('../../assets/img/marker.gif');

$x = ($_POST['x']/3.9 + 768);
$y = -(($_POST['y']/3.9) - 745);

imagecopyresized($im,$house,$x,$y,0,0,35,50,50,50);

ob_start();
$im = imagejpeg($im);

$outputBuffer = ob_get_clean();
$base64 = base64_encode($outputBuffer);

echo json_encode(array('image'=>$base64));
return;
?>