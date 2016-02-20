<?php
 
 /**
 * Api para todo_list
 * 
 * PHP version 5
 * 
 * @category MyCategory
 * @package  MyPackage
 * @author   Salvador Camacho <salvacams@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://salvacam.tk/todo_list/api/index.php 
 */
 
// define la zona horaria
date_default_timezone_set('Europe/Madrid');

// requerimientos de archivos
require 'src/Nanite.php';
require 'src/functions.php';


Nanite::get(
    '/', function () {        
        header("Content-Type: application/json");
        echo listar();
    }
);


Nanite::get(
    '/delete/([a-zA-Z0-9]+)', function ($id) {
        header("Content-Type: application/json");
        if ($id == 'All') {
            echo borrarAll();
        } else if ($id == 'Active' || $id == 'Completed') {
            echo borrarEstado($id);
        } else {
            echo borrar($id);
        }
    }
);


Nanite::post(
    '/new', function () {
        header("Content-Type: application/json");
        $objDatos = json_decode(file_get_contents("php://input"));
        $texto = '';
        if (isset($objDatos->texto)) {
            $texto = $objDatos->texto;
        }
        echo crear($texto);
    }
);


Nanite::post(
    '/update/([0-9]+)', function ($id) {
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
    }
);


if (!Nanite::$routeProccessed) {
    header('HTTP/1.1 405 Method Not Allowed');
    header("Content-Type: text/html");
    echo info();
}
