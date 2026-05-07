<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

$host = "mysql-claseapi.alwaysdata.net";
$db   = "claseapi_gestion_articulos";
$user = "claseapi";
$pass = "clase1234";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch(PDOException $e) {

    die(json_encode([
        "conexion_error" => $e->getMessage()
    ]));
}


/* ======================
   TEST SIMPLE
====================== */

$method = $_SERVER['REQUEST_METHOD'];

if($method == "GET") {

    try {

        $sql = "SELECT * FROM articulos";

        $stmt = $pdo->query($sql);

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($datos);

    } catch(PDOException $e) {

        echo json_encode([
            "select_error" => $e->getMessage()
        ]);
    }
}


if($method == "POST") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    try {

        $sql = "
            INSERT INTO articulos
            (
                nombre,
                marca,
                cantidad,
                bodega
            )
            VALUES
            (
                ?, ?, ?, ?
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([

            $data["nombre"],

            $data["marca"],

            intval($data["cantidad"]),

            $data["bodega"]
        ]);

        echo json_encode([
            "success" => true
        ]);

    } catch(PDOException $e) {

        echo json_encode([
            "insert_error" => $e->getMessage()
        ]);
    }
}
?>