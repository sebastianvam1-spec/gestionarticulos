<?php

header("Content-Type: application/json");

$conexion = new mysqli(

    "mysql-claseapi.alwaysdata.net",

    "claseapi",

    "clase1234",

    "claseapi_gestion_articulos"
);


if ($conexion->connect_error) {

    die(json_encode([
        "error" => $conexion->connect_error
    ]));
}


$method = $_SERVER['REQUEST_METHOD'];


/* =========================
   GET
========================= */

if ($method == "GET") {

    if(isset($_GET["id"])) {

        $id = $_GET["id"];

        $sql = "
            SELECT *
            FROM articulos
            WHERE id = $id
        ";

        $resultado =
            $conexion->query($sql);

        echo json_encode(
            $resultado->fetch_assoc()
        );

    } else {

        $sql =
            "SELECT * FROM articulos";

        $resultado =
            $conexion->query($sql);

        $datos = [];

        while($fila =
            $resultado->fetch_assoc()) {

            $datos[] = $fila;
        }

        echo json_encode($datos);
    }
}


/* =========================
   POST
========================= */

if ($method == "POST") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $nombre = $data["nombre"];

    $marca = $data["marca"];

    $cantidad =
        intval($data["cantidad"]);

    $bodega = $data["bodega"];


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
            '$nombre',
            '$marca',
            $cantidad,
            '$bodega'
        )
    ";

    if($conexion->query($sql)) {

        echo json_encode([
            "success" => true
        ]);

    } else {

        echo json_encode([
            "error" => $conexion->error
        ]);
    }
}


/* =========================
   PUT
========================= */

if ($method == "PUT") {

    $id = $_GET["id"];

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $nombre = $data["nombre"];

    $marca = $data["marca"];

    $cantidad =
        intval($data["cantidad"]);

    $bodega = $data["bodega"];


    $sql = "
        UPDATE articulos

        SET

        nombre='$nombre',

        marca='$marca',

        cantidad=$cantidad,

        bodega='$bodega'

        WHERE id=$id
    ";

    if($conexion->query($sql)) {

        echo json_encode([
            "success" => true
        ]);

    } else {

        echo json_encode([
            "error" => $conexion->error
        ]);
    }
}


/* =========================
   DELETE
========================= */

if ($method == "DELETE") {

    $id = $_GET["id"];

    $sql = "
        DELETE FROM articulos
        WHERE id = $id
    ";

    if($conexion->query($sql)) {

        echo json_encode([
            "success" => true
        ]);

    } else {

        echo json_encode([
            "error" => $conexion->error
        ]);
    }
}
?>