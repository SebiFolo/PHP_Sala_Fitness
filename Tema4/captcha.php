<?php
session_start();

$string = md5(microtime());
$cod_generat = substr($string, 0, 5);

$_SESSION['captcha'] = $cod_generat;

$latime = 100;
$inaltime = 40;
$imagine = imagecreate($latime, $inaltime);

$fundal = imagecolorallocate($imagine, 255, 255, 255);
$text_color = imagecolorallocate($imagine, 0, 0, 0);
$line_color = imagecolorallocate($imagine, 200, 200, 200);

for($i=0; $i<5; $i++) {
    imageline($imagine, 0, rand()%$inaltime, $latime, rand()%$inaltime, $line_color);
}

imagestring($imagine, 5, 25, 12, $cod_generat, $text_color);

header("Content-type: image/png");
imagepng($imagine);
imagedestroy($imagine);
?>