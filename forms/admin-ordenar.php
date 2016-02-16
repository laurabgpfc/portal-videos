<?php

include_once(__DIR__.'/../config.php');

//foreach ($_POST as $key => $value)
//	print "Field ".$key." is ".$value."<br>";

if ($_POST['nuevoOrden'] != '') {
	$orden = 1;
	$listaOrdenada = json_decode($_POST['nuevoOrden']);
	
	foreach ($listaOrdenada as $lista) {
		foreach ($lista as $item) {
			if ($item->tipo == 'cursos') {
				updateCursoOrden($item->id, $orden);

			} else if ($item->tipo == 'temas') {
				updateTemaOrden($item->id, $orden);

			} else if ($item->tipo == 'videos') {
				updateVideoOrden($item->id, $orden);

			} else if ($item->tipo == 'adjuntos') {
				updateAdjuntoOrden($item->id, $orden);
				
			}
			$orden += 1;
		}
	}
}

?>