<?php

require 'idiorm.php';


$host = 'localhost';
$database = 'todo_list';
$user = 'user';
$pass = 'pass';

const TABLA = 'todo_task';

ORM::configure('mysql:host='.$host.';dbname='.$database);
ORM::configure('username', $user);
ORM::configure('password', $pass);  

function listar() {
	
	$lista = ORM::for_table(TABLA)->find_many();
	$salida = [];
	foreach ($lista as $task) {
	    $id = $task->id;
	    $texto = $task->texto;
	    $estado = $task->estado;
	    $salida[$id] = array('id'=>$id, 'texto'=>$texto, 'estado'=>$estado);
	    //$salida[] = array('id'=>$id, 'texto'=>$texto, 'estado'=>$estado);
	}
	if ( count($salida) == 0 ) {
		return json_encode(-1);
	}	
	return json_encode($salida);
}


function borrarAll() {
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


function borrarEstado($remove) {	
	if ( $remove == 'Active' || $remove == 'Completed') {	
		if ( $remove == 'Active' ) {
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
}

function borrar($remove) {
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

function crear($texto) {
	$salida = 0;
	try {
		$task = ORM::for_table(TABLA)->create();
		$task->texto = $texto;
		$task->estado = 0;
		$task->save();
		
		//$salida = 1;		

		$salida = listar();
	} catch (Exception $e) {		
	}	
	return $salida;
}

function modificar($identificador, $texto, $estado) {
	$salida = 0;
	try {
		$task = ORM::for_table(TABLA)->where('id', $identificador)->find_one();
		$task->texto = $texto;
		$task->estado = $estado;
		$task->save();

		//$salida = 1;

		$salida = listar();

	} catch (Exception $e) {		
	}	
	return $salida;
}

function info(){
	return 'GET / : List all task<br/>GET /delete/{id | all | active | commpleted} : Remove task(s)<br/>'.
	'POST /new Request (texto): Create task<br/>'.
	'POST /update/{id} Request (texto, estado): Update task<br/>';
}
