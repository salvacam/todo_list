<?php

/**
 * Funciones para el trabajo con el modelo
 * 
 * PHP version 5
 * 
 * @category MyCategory
 * @package  MyPackage
 * @author   Salvador Camacho <salvacams@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://salvacam.tk/todo_list/api/src/functions.php 
 */
 
 // requerimientos de archivos
require 'idiorm.php';

// parametros de conexíon con la base de datos
$host = 'localhost';
$database = 'todo_list';
$user = 'user';
$pass = 'pass';

// nombre de la tabla donde se encuentran las tareas
const TABLA = 'todo_task';

// configuración del orm
ORM::configure('mysql:host='.$host.';dbname='.$database);
ORM::configure('username', $user);
ORM::configure('password', $pass);  


/**
 * Muestra todas las tareas
 * 
 * @return json
 */
function listar() 
{

    $lista = ORM::for_table(TABLA)->find_many();
    $salida = [];
    foreach ($lista as $task) {
        $id = $task->id;
        $texto = $task->texto;
        $estado = $task->estado;
        $salida[$id] = array('id'=>$id, 'texto'=>$texto, 'estado'=>$estado);
        //$salida[] = array('id'=>$id, 'texto'=>$texto, 'estado'=>$estado);
    }
    if (count($salida) == 0) {
        return json_encode(-1);
    }
    return json_encode($salida);
}


/**
 * Borra todas las tareas
 * 
 * @return json
 */
function borrarAll() 
{
    
    try {
        $lista = ORM::for_table(TABLA)
        ->find_many();
        foreach ($lista as $task) {
            $task->delete();
        }

        $salida = listar();
        return json_encode($salida);
    } catch (Exception $e) {
        return json_encode(0);
    }
}


/**
 * Borra todas las tareas según el estado
 * 
 * @param string $remove Estado de las tareas a borrar
 * 
 * @return json
 */
function borrarEstado($remove) 
{

    if ($remove == 'Active' || $remove == 'Completed') {
        if ($remove == 'Active') {
            $estado = 0;
        } else if ($remove == 'Completed') {
            $estado = 1;
        }
        
        try {
            $lista = ORM::for_table(TABLA)
            ->where('estado', $estado)
            ->find_many();
        
            foreach ($lista as $task) {
                $task->delete();
            }

            $salida = listar();
            return json_encode($salida);
        } catch (Exception $e) {
            return json_encode(0);
        }
    }
    
    return json_encode(0);
}


/**
 * Borra una tarea
 * 
 * @param int $remove Identificador de la tarea
 * 
 * @return json
 */
function borrar($remove)
{
    
    try {
        ORM::for_table(TABLA)
        ->where('id', $remove)
        ->find_one()
        ->delete();

        $salida = listar();
        return json_encode($salida);
    } catch (Exception $e) {
        return json_encode(0);
    }
}


/**
 * Crea una tarea
 * 
 * @param string $texto nombre de la tarea
 * 
 * @return json
 */
function crear($texto) 
{
    $salida = 0;
    try {
        $task = ORM::for_table(TABLA)->create();
        $task->texto = $texto;
        $task->estado = 0;
        $task->save();

        $salida = listar();
    } catch (Exception $e) {
    }
    return $salida;
}


/**
 * Modifica una tarea
 * 
 * @param int    $identificador Identificador de la tarea
 * @param string $texto         Nuevo texto de la tarea
 * @param int    $estado        estado de la tarea
 * 
 * @return json
 */
function modificar($identificador, $texto, $estado)
{
    
    $salida = 0;
    try {
        $task = ORM::for_table(TABLA)->where('id', $identificador)->find_one();
        $task->texto = $texto;
        $task->estado = $estado;
        $task->save();

        $salida = listar();

    } catch (Exception $e) {
    }
    return $salida;
}

/**
 * Devuelve la forma de uso de la api
 * 
 * @return string
 */
function info()
{
    return 'GET / : List all task<br/>GET /delete/{id | all | active |'.
    ' commpleted} : Remove task(s)<br/>'.
    'POST /new Request (texto): Create task<br/>'.
    'POST /update/{id} Request (texto, estado): Update task<br/>';
}
