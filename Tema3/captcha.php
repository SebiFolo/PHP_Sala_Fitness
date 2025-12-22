<?php
session_start();

$caractere = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; 
$cod = substr(str_shuffle($caractere), 0, 5);

$_SESSION['cod_captcha'] = $cod;

$imagine = imagecreatetruecolor(120, 40);

$fundal = imagecolorallocate($imagine, 240, 240, 240); 
$text_color = imagecolorallocate($imagine, 50, 50, 50); 
$line_color = imagecolorallocate($imagine, 200, 200, 200); 

imagefill($imagine, 0, 0, $fundal);

for($i=0; $i<5; $i++) {
    imageline($imagine, rand(0, 120), rand(0, 40), rand(0, 120), rand(0, 40), $line_color);
}

imagestring($imagine, 5, 35, 12, $cod, $text_color);

header('Content-type: image/png');
imagepng($imagine);
imagedestroy($imagine);
?>