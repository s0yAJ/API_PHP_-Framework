<?php

declare(strict_types=1);

require_once("vendor/autoload.php");
//require 'flight/autoload.php';
//require_once 'flight/Flight.php';

Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=pokemons', 'root', ''));

/**
 * return al pokemons
 */
Flight::route('GET /', function () {
    $db = Flight::db();

    $query = $db->prepare("SELECT * FROM pokemons_t");
    $query->execute();

    $data = $query->fetchAll();

    foreach ($data as $info) {
        $array[] = [
            "ID" => $info['ID'],
            "Nombre" => $info['Nombre'],
            "Color" => $info['Color'],
            "Nivel" => $info['Nivel'],
            "Tipo" => $info['Tipo'],
        ];
    }

    Flight::json([
        "Total row" => $query->rowCount(),
        "row" => $array
    ]);
});

/**
 * return the info of pokemos by ID
 *  @param ID
 */
Flight::route('GET /Pokemons/@id', function ($id) {
    $db = Flight::db();

    $query = $db->prepare("SELECT * FROM pokemons_t WHERE ID = :id");
    $query->execute([":id" => $id]);

    $data = $query->fetch();

    $array[] = [
        "ID" => $data['ID'],
        "Nombre" => $data['Nombre'],
        "Color" => $data['Color'],
        "Nivel" => $data['Nivel'],
        "Tipo" => $data['Tipo'],
    ];

    Flight::json([
        "Total row" => $query->rowCount(),
        "row" => $array
    ]);
});

/**
 * insert information in our database
 *  @param Nombre
 *  @param Color
 *  @param Tipo
 *  @param Nivel
 */
Flight::route('POST /Pokemons', function () {
    $db = Flight::db();

    $nombre = Flight::request()->data->nombre;
    $nivel = Flight::request()->data->nivel;
    $tipo = Flight::request()->data->tipo;
    $color = Flight::request()->data->color;

    $query = $db->prepare("INSERT INTO pokemons_t (Nombre, Color, Tipo, Nivel) VALUES (:Nombre, :Color, :Tipo, :Nivel)");

    $array = [
        "Error" => "Algo anda mal, verifica que estes hacien las cosas bien; ",
        "status" => "Error"
    ];

    if ($query->execute([":Nombre" => $nombre, ":Color" => $color, ":Tipo" => $tipo, ":Nivel" => $nivel])) {
        $array = [
            "data" => [
                "ID" => $db->lastInsertId(),
                "Nombre" => $nombre,
                "Nivel" => $nivel,
                "Color" => $color,
                "Tipo" => $tipo
            ],
            "status" => "success"
        ];
    }

    Flight::json($array);
});

/**
 * UPDATE the information in our database
 *  @param ID
 *  @param Nombre
 *  @param Color
 *  @param Tipo
 *  @param Nivel
 */
Flight::route('PUT /Pokemons', function () {
    $db = Flight::db();

    $id = Flight::request()->data->id;
    $nombre = Flight::request()->data->nombre;
    $nivel = Flight::request()->data->nivel;
    $tipo = Flight::request()->data->tipo;
    $color = Flight::request()->data->color;

    $query = $db->prepare("UPDATE pokemons_t SET Nombre = :Nombre, Color = :Color, Tipo = :Tipo, Nivel = :Nivel WHERE ID = :id");

    $array = [
        "Error" => "Algo anda mal, verifica que estes hacien las cosas bien; ",
        "status" => "Error"
    ];

    if ($query->execute([":Nombre" => $nombre, ":Color" => $color, ":Tipo" => $tipo, ":Nivel" => $nivel, ":id" => $id])) {
        $array = [
            "data" => [
                "ID" => $id,
                "Nombre" => $nombre,
                "Nivel" => $nivel,
                "Color" => $color,
                "Tipo" => $tipo
            ],
            "status" => "success",
            "chance" => ""
        ];
    }

    Flight::json($array);
});

/**
 * DELETE Pokedatos in our database
 * @param ID 
 */
Flight::route('DELETE /Pokemons', function () {
    $db = Flight::db();

    $ID = Flight::request()->data->ID;

    $query = $db->prepare("DELETE FROM pokemons_t WHERE ID = :id");

    $array = [
        "Error" => "Algo anda mal, verifica que estes hacien las cosas bien; ",
        "status" => "Error"
    ];

    if ($query->execute([":id" => $ID])) {
        $array = [
            "Data" => [ 
                "ID" => $ID,
                "Info" => 'was DELETE'
            ],
            "Status" => "success"
        ];  
    }

    Flight::json($array);
});

Flight::start();
