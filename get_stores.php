<?php
$pdo = new PDO("mysql:host=localhost;dbname=test", "gothem", "");

$storage_id = $_GET["storage_id"];

$stmt = $pdo->prepare(
    "SELECT s.id, s.nombre FROM sucursales s JOIN bodegas_sucursales ss ON s.id = ss.Sucursales_id WHERE ss.Bodegas_id = ?",
);

$stmt->execute([$storage_id]);
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($stores);
?>
