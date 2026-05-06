<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

/* =========================
   CONEXIÓN BASE DE DATOS
========================= */

$host    = "mysql-claseapi.alwaysdata.net";
$db      = "claseapi_gestion_articulos";
$user    = "claseapi";
$pass    = "clase1234";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {

    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "error" => "Error de conexión",
        "detalle" => $e->getMessage()
    ]);

    exit;
}


/* =========================
   OBTENER TODOS
========================= */

function obtenerTodos($pdo) {

    $stmt = $pdo->query("
        SELECT id, nombre, marca, cantidad, bodega
        FROM articulos
        ORDER BY id DESC
    ");

    echo json_encode($stmt->fetchAll());
}


/* =========================
   OBTENER POR ID
========================= */

function obtenerPorId($pdo, $id) {

    $stmt = $pdo->prepare("
        SELECT id, nombre, marca, cantidad, bodega
        FROM articulos
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $articulo = $stmt->fetch();

    if ($articulo) {

        echo json_encode($articulo);

    } else {

        http_response_code(404);

        echo json_encode([
            "error" => "Artículo no encontrado"
        ]);
    }
}


/* =========================
   CREAR
========================= */

function crear($pdo, $data) {

    if (
        !isset(
            $data['nombre'],
            $data['marca'],
            $data['cantidad'],
            $data['bodega']
        )
    ) {

        http_response_code(400);

        echo json_encode([
            "error" => "Datos incompletos"
        ]);

        return;
    }

    $stmt = $pdo->prepare("
        INSERT INTO articulos
        (nombre, marca, cantidad, bodega)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['nombre'],
        $data['marca'],
        $data['cantidad'],
        $data['bodega']
    ]);

    http_response_code(201);

    echo json_encode([
        "message" => "Artículo creado correctamente",
        "id" => $pdo->lastInsertId()
    ]);
}


/* =========================
   ACTUALIZAR
========================= */

function actualizar($pdo, $id, $data) {

    if (
        !isset(
            $data['nombre'],
            $data['marca'],
            $data['cantidad'],
            $data['bodega']
        )
    ) {

        http_response_code(400);

        echo json_encode([
            "error" => "Datos incompletos"
        ]);

        return;
    }

    $stmt = $pdo->prepare("
        UPDATE articulos
        SET
            nombre = ?,
            marca = ?,
            cantidad = ?,
            bodega = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $data['nombre'],
        $data['marca'],
        $data['cantidad'],
        $data['bodega'],
        $id
    ]);

    echo json_encode([
        "message" => "Artículo actualizado correctamente"
    ]);
}


/* =========================
   ELIMINAR
========================= */

function eliminar($pdo, $id) {

    $stmt = $pdo->prepare("
        DELETE FROM articulos
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    echo json_encode([
        "message" => "Artículo eliminado correctamente"
    ]);
}


/* =========================
   ROUTER API REST
========================= */

$method = $_SERVER['REQUEST_METHOD'];

$id = $_GET['id'] ?? null;

switch ($method) {

    case "GET":

        if ($id) {
            obtenerPorId($pdo, $id);
        } else {
            obtenerTodos($pdo);
        }

        break;


    case "POST":

        $data = json_decode(file_get_contents("php://input"), true);

        crear($pdo, $data);

        break;


    case "PUT":

        if (!$id) {

            http_response_code(400);

            echo json_encode([
                "error" => "ID requerido"
            ]);

            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        actualizar($pdo, $id, $data);

        break;


    case "DELETE":

        if (!$id) {

            http_response_code(400);

            echo json_encode([
                "error" => "ID requerido"
            ]);

            exit;
        }

        eliminar($pdo, $id);

        break;


    default:

        http_response_code(405);

        echo json_encode([
            "error" => "Método no permitido"
        ]);
}
?>