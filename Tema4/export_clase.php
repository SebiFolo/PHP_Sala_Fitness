<?php
require 'db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Orar_Fitness.xls");

$sql = "SELECT t_classes.nume_clasa, t_users.nume as antrenor, t_classes.data_ora 
        FROM t_classes 
        LEFT JOIN t_users ON t_classes.antrenor_id = t_users.id";
$stmt = $pdo->query($sql);

echo "<table border='1'>";
echo "<tr><th>Clasa</th><th>Antrenor</th><th>Data</th></tr>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row['nume_clasa'] . "</td>";
    echo "<td>" . $row['antrenor'] . "</td>";
    echo "<td>" . $row['data_ora'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>