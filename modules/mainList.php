<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$OUT = '';
$found = 0;

$OUT .= '<div class="container">';
	$OUT .= '<div class="row">';
		$OUT .= '<div class="col-md-12 margin-bottom">';
			$OUT .= '<h1>Portal v&iacute;deos</h1>';
			$OUT .= '<p>Bienvenido al portal de v&iacute;deos. Aqu&iacute; podr&aacute; ver los cursos en los que est&aacute; matriculado.</p>';
		$OUT .= '</div>';

		// Si el usuario está bloqueado, pero tiene iniciada una sesión, no mostrar nada:
		if ( (isset($_COOKIE['MoodleUserSession']))&&(checkUsuarioBloqueado($MoodleUserSession['IDusuario']) == 1) ) {
			$OUT .= '<center><strong>Este usuario ha sido bloqueado. No puede visualizar ning&uacute;n contenido ni p&uacute;blico ni privado.<br/><br/></strong></center>';
		} else {
			$SQL = 'SELECT * FROM cursos WHERE';
			$SQL .= ' (publico = 1'; // Mostrar los cursos publicos

			// Si es un usuario administrador:
			if ( (isset($_COOKIE['MoodleUserSession']))&&($MoodleUserSession['esAdmin'] == 1) ) {
				// Mostrar los cursos que tengan asociado uno de Moodle
				$SQL .= ' OR (IDcursoMoodle > 0)';
			// Si hay un usuario conectado, y tiene asociado el correo, y no está bloqueado:
			} else if ( (isset($_COOKIE['MoodleUserSession']))&&(!isset($_COOKIE['MoodleUserFaltaCorreo']))&&(checkUsuarioBloqueado($MoodleUserSession['IDusuario']) == 0) ) {
				// Mostrar los cursos a los que tiene acceso
				$SQL .= ' OR IDcursoMoodle IN (SELECT IDcursoMoodle FROM cursosUsuarios WHERE IDusuario = '.$MoodleUserSession['IDusuario'].')';
			}
			$SQL .= ')';

			// Y el curso, si tiene fecha de inicio y fin, se encuentra en fechas
			$SQL .= ' AND ( (fechaIni != "" AND fechaFin != "" AND DATE("now") BETWEEN fechaIni AND fechaFin) OR (fechaIni = "" AND fechaFin = "") )';
			$SQL .= ' AND ocultar = 0'; // Y que no esten ocultos
			$SQL .= ' AND archivar = 0'; // Y que no esten archivados
			$SQL .= ' AND ID IN (SELECT IDcurso FROM videos WHERE ocultar = 0 AND ( (fechaCaducidad != "" AND DATE("now") < fechaCaducidad) OR (fechaCaducidad = "") ))'; // Y que tengan videos
			$SQL .= ' ORDER BY orden DESC, nombre'; // Ordenados por orden descendente y nombre


			// Listar los cursos
			$res = $db->query($SQL);
			while ($row = $res->fetchArray()) {
				$found = 1;
				if (is_int(array_search($row['ubicacion'], array_column($listaDirs, 'ID')))) {
					$dir = $listaDirs[array_search($row['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
					$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
				}

				$OUT .= '<div class="col-md-12">';
					$OUT .= '<div class="panel panel-primary">';
						$OUT .= '<div class="panel-heading">Novedades en: <a href="?IDcurso='.urlencode($row['IDencriptado']).'"><b>'.$row['nombre'].'</b></a></div>';
						$OUT .= '<div class="panel-body">';
							$OUT .= '<div class="row">';
								
								$SQLVideos = 'SELECT a.IDencriptado AS IDvideo, a.nombre AS nombreVideo, a.img, a.descripcion AS descVideo, a.ruta AS rutaVideo, b.IDencriptado AS IDtema, b.nombre AS nombreTema, b.ruta AS rutaTema FROM videos a, temas b WHERE';
								$SQLVideos .= ' a.ocultar = 0 AND b.ocultar = 0';
								$SQLVideos .= ' AND ( (a.fechaCaducidad != "" AND DATE("now") < a.fechaCaducidad) OR (a.fechaCaducidad = "") )';
								$SQLVideos .= ' AND a.IDtema = b.ID AND a.IDcurso = '.$row['ID'];
								$SQLVideos .= ' ORDER BY b.orden DESC, b.nombre, a.orden DESC, a.nombre LIMIT 4';

								// Listar videos del tema:
								$resVideo = $db->query($SQLVideos);
								while ($rowVideo = $resVideo->fetchArray()) {
									$OUT .= '<div class="col-sm-6 col-md-3">';
										$OUT .= '<a class="ver-video" href="?IDcurso='.urlencode($row['IDencriptado']).'&IDtema='.urlencode($rowVideo['IDtema']).'&IDvideo='.urlencode($rowVideo['IDvideo']).'">';
											//$OUT .= '<span class="glyphicon glyphicon-play play-video"></span>';
											$OUT .= '<img src="'._DIRCURSOS.$dir.$row['ruta'].'/'.$rowVideo['rutaTema'].'/img/'.$rowVideo['img'].'" />';
											$OUT .= '<p>'.$rowVideo['nombreTema'].': '.$rowVideo['nombreVideo'].'</p>';
										$OUT .= '</a>';
									$OUT .= '</div>';
								}
							$OUT .= '</div>';
							$OUT .= '<p><a href="?IDcurso='.urlencode($row['IDencriptado']).'" class="btn btn-default pull-right" role="button">Ver curso completo</a></p>';
						$OUT .= '</div>';
					$OUT .= '</div>';
				$OUT .= '</div>';
			}

			if ($found == 0) {
				$OUT .= '<div class="col-md-12">';
					if ( (isset($_COOKIE['MoodleUserSession']))&&($MoodleUserSession['IDusuario'] == 0) ) {
						$OUT .= '<strong>No hay cursos registrados en los que est&eacute;s matriculado.</strong>';
					} else {
						$OUT .= '<p>En estos momentos no hay cursos publicados.</p>';
					}
				$OUT .= '</div>';
			}
		}
		
	$OUT .= '</div>';
$OUT .= '</div>';

echo $OUT;

?>