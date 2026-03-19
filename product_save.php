<?php
$pdo = new PDO("mysql:host=localhost;dbname=test", "gothem", "");

$code = $_POST["code"];
$name = $_POST["name"];
$storage = $_POST["storage"];
$store = $_POST["store"];
$currency = $_POST["currency"];
$price = $_POST["price"];
$materials = implode(",", $_POST["materials"]);
$description = $_POST["description"];

try {
    $stmt = $pdo->prepare(
        "INSERT INTO productos (codigo,nombre,precio,material,bodega,sucursal,moneda,descripcion) VALUES(?,?,?,?,?,?,?,?)",
    );
    $stmt->execute([
        $code,
        $name,
        $price,
        $materials,
        $storage,
        $store,
        $currency,
        $description,
    ]);
    echo "ok";
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        echo "duplicate";
    } else {
        echo "error";
    }
}
?>
