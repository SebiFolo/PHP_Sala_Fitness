<?php
require 'db.php'; 

$sql = "SELECT t_users.nume, COUNT(t_classes.id) as nr_clase 
        FROM t_users 
        LEFT JOIN t_classes ON t_users.id = t_classes.antrenor_id 
        WHERE t_users.rol_id = 2 
        GROUP BY t_users.id";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$latime = 500;
$inaltime = 300;
$img = imagecreate($latime, $inaltime);

$alb = imagecolorallocate($img, 255, 255, 255); 
$negru = imagecolorallocate($img, 0, 0, 0);     
$verde = imagecolorallocate($img, 40, 167, 69); 
$gri = imagecolorallocate($img, 200, 200, 200); 

imagefilledrectangle($img, 0, 0, $latime, $inaltime, $alb);

imageline($img, 40, 10, 40, $inaltime - 30, $negru);       
imageline($img, 40, $inaltime - 30, $latime - 10, $inaltime - 30, $negru); 

$max_val = 0;
foreach ($data as $row) {
    if ($row['nr_clase'] > $max_val) $max_val = $row['nr_clase'];
}
if ($max_val == 0) $max_val = 1; 

$nr_antrenori = count($data);
if ($nr_antrenori > 0) {
    $latime_bara = ($latime - 60) / $nr_antrenori - 20; 
    $x = 60; 

    foreach ($data as $row) {
        $valoare = $row['nr_clase'];
        $nume = $row['nume'];

        $inaltime_bara = ($valoare / $max_val) * ($inaltime - 60);

        imagefilledrectangle(
            $img, 
            $x, ($inaltime - 30 - $inaltime_bara), 
            $x + $latime_bara, ($inaltime - 31), 
            $verde
        );

        imagestring($img, 5, $x + ($latime_bara/2) - 5, ($inaltime - 30 - $inaltime_bara - 15), $valoare, $negru);

        imagestring($img, 3, $x, $inaltime - 25, substr($nume, 0, 8), $negru);

        $x += $latime_bara + 20; 
    }
} else {
    imagestring($img, 5, 150, 150, "Nu exista date!", $negru);
}

header("Content-Type: image/png");
imagepng($img);

imagedestroy($img);
?>