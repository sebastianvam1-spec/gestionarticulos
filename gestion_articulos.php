<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");


/* =====================================
   CONEXIÓN A LA BASE DE DATOS
===================================== */

$host = "mysql-claseapi.alwaysdata.net";

$db = "api_inventarios";

$user = "claseapi";

$pass = "clase1234";

$charset = "utf8mb4";


$dsn =
"mysql:host=$host;dbname=$db;charset=$charset";


$options = [

    PDO::ATTR_ERRMODE =>
        PDO::ERRMODE_EXCEPTION,

    PDO::ATTR_DEFAULT_FETCH_MODE =>
        PDO::FETCH_ASSOC,

    PDO::ATTR_EMULATE_PREPARES =>
        false,
];


try {

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        $options
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([

        "error" =>
            "Error de conexión",

        "detalle" =>
            $e->getMessage()
    ]);

    exit;
}


/* =====================================
   OBTENER TODOS
===================================== */

function obtenerTodos($pdo)
{
    try {

        $stmt = $pdo->query("
            SELECT
                id,
                nombre,
                marca,
                cantidad,
                bodega
            FROM articulos
            ORDER BY id DESC
        ");

        echo json_encode(
            $stmt->fetchAll()
        );

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([

            "error" =>
                "Error al obtener artículos",

            "detalle" =>
                $e->getMessage()
        ]);
    }
}


/* =====================================
   OBTENER POR ID
===================================== */

function obtenerPorId($pdo, $id)
{
    try {

        $stmt = $pdo->prepare("
            SELECT
                id,
                nombre,
                marca,
                cantidad,
                bodega
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
                "error" =>
                    "Artículo no encontrado"
            ]);
        }

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([

            "error" =>
                "Error al buscar artículo",

            "detalle" =>
                $e->getMessage()
        ]);
    }
}


/* =====================================
   CREAR
===================================== */

function crear($pdo, $data)
{
    try {

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
                "error" =>
                    "Datos incompletos"
            ]);

            return;
        }


        $nombre =
            trim($data['nombre']);

        $marca =
            trim($data['marca']);

        $cantidad =
            intval($data['cantidad']);

        $bodega =
            trim($data['bodega']);


        $stmt = $pdo->prepare("
            INSERT INTO articulos
            (
                nombre,
                marca,
                cantidad,
                bodega
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([

            $nombre,
            $marca,
            $cantidad,
            $bodega
        ]);


        http_response_code(201);

        echo json_encode([

            "message" =>
                "Artículo creado correctamente",

            "id" =>
                $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([

            "error" =>
                "Error al crear artículo",

            "detalle" =>
                $e->getMessage()
        ]);
    }
}


/* =====================================
   ACTUALIZAR
===================================== */

function actualizar($pdo, $id, $data)
{
    try {

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
                "error" =>
                    "Datos incompletos"
            ]);

            return;
        }


        $nombre =
            trim($data['nombre']);

        $marca =
            trim($data['marca']);

        $cantidad =
            intval($data['cantidad']);

        $bodega =
            trim($data['bodega']);


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

            $nombre,
            $marca,
            $cantidad,
            $bodega,
            $id
        ]);


        echo json_encode([

            "message" =>
                "Artículo actualizado correctamente"
        ]);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([

            "error" =>
                "Error al actualizar artículo",

            "detalle" =>
                $e->getMessage()
        ]);
    }
}


/* =====================================
   ELIMINAR
===================================== */

function eliminar($pdo, $id)
{
    try {

        $stmt = $pdo->prepare("
            DELETE FROM articulos
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        echo json_encode([

            "message" =>
                "Artículo eliminado correctamente"
        ]);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([

            "error" =>
                "Error al eliminar artículo",

            "detalle" =>
                $e->getMessage()
        ]);
    }
}


/* =====================================
   ROUTER API REST
===================================== */

$method =
    $_SERVER['REQUEST_METHOD'];

$id =
    $_GET['id'] ?? null;


switch ($method) {


    case "GET":

        if ($id) {

            obtenerPorId($pdo, $id);

        } else {

            obtenerTodos($pdo);
        }

        break;



    case "POST":

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        crear($pdo, $data);

        break;



    case "PUT":

        if (!$id) {

            http_response_code(400);

            echo json_encode([
                "error" =>
                    "ID requerido"
            ]);

            exit;
        }

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        actualizar($pdo, $id, $data);

        break;



    case "DELETE":

        if (!$id) {

            http_response_code(400);

            echo json_encode([
                "error" =>
                    "ID requerido"
            ]);

            exit;
        }

        eliminar($pdo, $id);

        break;



    default:

        http_response_code(405);

        echo json_encode([

            "error" =>
                "Método no permitido"
        ]);
}
?>