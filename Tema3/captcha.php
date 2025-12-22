<?php
session_start();

// 1. Generam un cod aleatoriu din 5 caractere
$caractere = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Fara 0, 1, I, O (sa nu se confunde)
$cod = substr(str_shuffle($caractere), 0, 5);

// 2. Salvam codul in sesiune ca sa il verificam mai tarziu
$_SESSION['cod_captcha'] = $cod;

// 3. Cream imaginea (Latime 120px, Inaltime 40px)
$imagine = imagecreatetruecolor(120, 40);

// 4. Definim culorile
$fundal = imagecolorallocate($imagine, 240, 240, 240); // Gri deschis
$text_color = imagecolorallocate($imagine, 50, 50, 50); // Gri inchis
$line_color = imagecolorallocate($imagine, 200, 200, 200); // Linii discrete

// Umplem fundalul
imagefill($imagine, 0, 0, $fundal);

// 5. Adaugam "zgomot" (linii aleatorii) pentru securitate
for($i=0; $i<5; $i++) {
    imageline($imagine, rand(0, 120), rand(0, 40), rand(0, 120), rand(0, 40), $line_color);
}

// 6. Scriem codul pe imagine (Centrat)
// Folosim fontul default de sistem (marimea 5)
imagestring($imagine, 5, 35, 12, $cod, $text_color);

// 7. Trimitem imaginea catre browser
header('Content-type: image/png');
imagepng($imagine);
imagedestroy($imagine);
?>