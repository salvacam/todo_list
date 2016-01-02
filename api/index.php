<?php

date_default_timezone_set('Europe/Madrid');

require 'src/Nanite.php';
require 'src/functions.php';

Nanite::get('/', function() {
    header("Content-Type: application/json");
    echo listar();
});

Nanite::get('/delete/([a-zA-Z0-9]+)', function($id) {
	header("Content-Type: application/json");
    echo borrar($id);
});

Nanite::post('/new', function() {
	header("Content-Type: application/json");
	$objDatos = json_decode(file_get_contents("php://input"));
    $texto = '';
    if (isset($objDatos->texto)) {
        $texto = $objDatos->texto;
    }
    echo crear($texto);
});

Nanite::post('/update/([0-9]+)', function($id) {
    header("Content-Type: application/json");
    $objDatos = json_decode(file_get_contents("php://input"));
    $texto = '';
    if (isset($objDatos->texto)) {
        $texto = $objDatos->texto;
    }
    $estado = '';
    if (isset($objDatos->estado)) {
        $estado = $objDatos->estado;
    }
    echo modificar($id, $texto, $estado);
});

if (!Nanite::$routeProccessed) {
    header('HTTP/1.1 405 Method Not Allowed');
    header("Content-Type: text/html");
    echo info();
} 
