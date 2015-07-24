<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$OUT = '';
$numPag = 0;
$cont = 0;
$totalPorPag = 3;

$cursoData = getCursoData($_GET['IDcurso']);
$listaCat = getListaCategoriasByCurso($_GET['IDcurso']);

if ( ($cursoData['publico'] == 0)&&(!isset($_COOKIE['MoodleUserSession'])) ) {
	$OUT .= '<div class="container">';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-12 margin-bottom">';
				$OUT .= '<h1>'.$cursoData['nombre'].': Acceso restringido</h1>';
				$OUT .= '<p>Debes estar logueado para poder ver este curso</p>';
			$OUT .= '</div>';
		$OUT .= '</div>';
	$OUT .= '</div>';

} else if ( ($cursoData['publico'] == 0)&&(isset($_COOKIE['MoodleUserSession']))&&(decrypt($_COOKIE['MoodleUserSession'],1)['esAdmin'] == 0)&&(checkCursoUsuario('IDusuario = '.decrypt($_COOKIE['MoodleUserSession'],1)['IDusuario'].' AND IDcursoMoodle = '.$cursoData['IDcursoMoodle']) == 0) ) {
	$OUT .= '<div class="container">';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-12 margin-bottom">';
				$OUT .= '<h1>'.$cursoData['nombre'].': Acceso restringido</h1>';
				$OUT .= '<p>Para poder ver este curso debes estar matriculado en &eacute;l</p>';
			$OUT .= '</div>';
		$OUT .= '</div>';
	$OUT .= '</div>';

} else if ($cursoData['archivar'] == 1) {
	$OUT .= '<div class="container">';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-12 margin-bottom">';
				$OUT .= '<h1>'.$cursoData['nombre'].'</h1>';
				$OUT .= '<p>Este curso ya ha terminado</p>';
			$OUT .= '</div>';
		$OUT .= '</div>';
	$OUT .= '</div>';

} else if ( ($cursoData['IDcursoMoodle'] == '')||($cursoData['IDcursoMoodle'] == 0) ) {
	$OUT .= '<div class="container">';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-12 margin-bottom">';
				$OUT .= '<h1>'.$cursoData['nombre'].'</h1>';
				$OUT .= '<p>Este curso no se puede mostrar</p>';
			$OUT .= '</div>';
		$OUT .= '</div>';
	$OUT .= '</div>';

} else {
	if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}
	
	$OUT .= '<div class="container">';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-12 margin-bottom">';
				$OUT .= '<h1>'.$cursoData['nombre'].'</h1>';
				$OUT .= '<p>'.$cursoData['descripcion'].'</p>';
			$OUT .= '</div>';
		$OUT .= '</div>';
		
		$mostrar = 1;
		if ( ($cursoData['fechaIni'] != '')&&($cursoData['fechaFin'] != '') ) {
			$date0 = new DateTime();
			$date1 = new DateTime($cursoData['fechaIni']);
			$date2 = new DateTime($cursoData['fechaFin']);

			if ( ($date0 < $date1)||($date0 > $date2) ) {
				$OUT .= '<div class="row">';
					$OUT .= '<div class="col-md-12 margin-bottom">';
						$OUT .= '<p>Este curso ya ha terminado</p>';
					$OUT .= '</div>';
				$OUT .= '</div>';
				$mostrar = 0;
			}
		}
	$OUT .= '</div>';
	
	if ($mostrar == 1) {
		$OUT .= '<div class="container botones-visualizacion margin-bottom">';
			$OUT .= '<div class="row">';
				$OUT .= '<div class="col-md-3 pull-right">';
					$OUT .= '<a href="?opt=1&IDcurso='.urlencode($cursoData['IDcurso']).'"><button class="btn btn-default pull-right';
					if ( ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 1) )||(!isset($_COOKIE['listMode'])) ) {
						$OUT .= ' active';
					}
					$OUT .= '" type="button"><span class="glyphicon glyphicon-th"></span></button></a>';
					$OUT .= '<a href="?opt=2&IDcurso='.urlencode($cursoData['IDcurso']).'"><button class="btn btn-default pull-right';
					if ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 2) ) {
						$OUT .= ' active';
					}
					$OUT .= '" type="button"><span class="glyphicon glyphicon-th-list"></span></button></a>';
				$OUT .= '</div>';
				$OUT .= '<div class="col-md-3 pull-right">';
					
				$OUT .= '</div>';
				$OUT .= '<div class="col-md-6">';
					if (count($listaCat) > 0) {
						$OUT .= '<select class="form-control" name="select-categoria" placeholder="Seleccione qu&eacute; categor&iacute; desea ver">';
							$OUT .= '<option value="ALL"'.( ((!isset($_COOKIE['cat'])) || (isset($_COOKIE['cat']))&&($_COOKIE['cat'] == 'ALL')) ? ' selected' : '' ).'>Ver todas las categor&iacute;as</option>';
							$OUT .= '<option value="noCat"'.( (isset($_COOKIE['cat']))&&($_COOKIE['cat'] == 'noCat') ? ' selected' : '' ).'>Ver v&iacute;deos sin categor&iacute;as</option>';
							foreach ($listaCat as $cat) {
								$OUT .= '<option value="'.$cat[0].'"'.( (isset($_COOKIE['cat']))&&($_COOKIE['cat'] == $cat[0]) ? ' selected' : '' ).'>Categor&iacute;a: '.$cat[1].'</option>';
							}
						$OUT .= '</select>';
					} else {
						if (isset($_COOKIE['cat'])) {
							unset($_COOKIE['cat']);
							setcookie('cat', null, -1, _PORTALROOT);
						}
					}
				$OUT .= '</div>';
			$OUT .= '</div>';
		$OUT .= '</div>';
		
		$OUT .= '<div class="container listado-paginado">';
			$SQLCount = 'SELECT COUNT(*) FROM temas WHERE';
			$SQLCount .= ' ocultar = 0';
			$SQLCount .= ' AND IDcurso = '.decrypt($cursoData['IDcurso']);
			$SQLCount .= ' AND ID IN (SELECT IDtema FROM videos WHERE ocultar = 0 AND ( (fechaCaducidad != "" AND DATE("now") < fechaCaducidad) OR (fechaCaducidad = "") )';
			if ( (isset($_COOKIE['cat']))&&($_COOKIE['cat'] != 'ALL')&&($_COOKIE['cat'] != 'noCat') ) {
				$SQLCount .= ' AND ID IN (SELECT IDvideo FROM categorias WHERE ID = '.(decrypt($_COOKIE['cat'])).')';
			} else if ( (isset($_COOKIE['cat']))&&($_COOKIE['cat'] != 'ALL')&&($_COOKIE['cat'] == 'noCat') ) {
				$SQLCount .= ' AND ID NOT IN (SELECT IDvideo FROM categorias)';
			}
			$SQLCount .= ')';
			
			$totalCursos = $db->querySingle($SQLCount);

			$SQL = 'SELECT * FROM temas WHERE';
			$SQL .= ' ocultar = 0';
			$SQL .= ' AND IDcurso = '.decrypt($cursoData['IDcurso']);
			$SQL .= ' AND ID IN (SELECT IDtema FROM videos WHERE ocultar = 0 AND ( (fechaCaducidad != "" AND DATE("now") < fechaCaducidad) OR (fechaCaducidad = "") )';
			if ( (isset($_COOKIE['cat']))&&($_COOKIE['cat'] != 'ALL')&&($_COOKIE['cat'] != 'noCat') ) {
				$SQL .= ' AND ID IN (SELECT IDvideo FROM categorias WHERE ID = '.(decrypt($_COOKIE['cat'])).')';
			} else if ( (isset($_COOKIE['cat']))&&($_COOKIE['cat'] != 'ALL')&&($_COOKIE['cat'] == 'noCat') ) {
				$SQL .= ' AND ID NOT IN (SELECT IDvideo FROM categorias)';
			}
			$SQL .= ')';
			$SQL .= ( (isset($_GET['last'])) ? ' AND orden < '.$_GET['last'] : '' );
			$SQL .= ' ORDER BY orden DESC, nombre LIMIT '.$totalPorPag;

			$res = $db->query($SQL);
			while ($row = $res->fetchArray()) {
				$OUT .= '<div class="panel panel-primary">';
					$OUT .= '<div class="panel-heading">'.$row['nombre'].'</div>';
					$OUT .= '<div class="panel-body">';
					
					// Listar videos de cada tema:
					$SQLvideos = 'SELECT * FROM videos WHERE ocultar = 0 AND IDtema = '.$row['ID'].' AND IDcurso = '.decrypt($cursoData['IDcurso']);
					if ( (isset($_COOKIE['cat']))&&($_COOKIE['cat'] != 'ALL')&&($_COOKIE['cat'] != 'noCat') ) {
						$SQLvideos .= ' AND ID IN (SELECT IDvideo FROM categorias WHERE ID = '.(decrypt($_COOKIE['cat'])).')';
					} else if ( (isset($_COOKIE['cat']))&&($_COOKIE['cat'] != 'ALL')&&($_COOKIE['cat'] == 'noCat') ) {
						$SQLvideos .= ' AND ID NOT IN (SELECT IDvideo FROM categorias)';
					}
					$SQLvideos .= ' ORDER BY orden DESC, nombre';

					$resVideo = $db->query($SQLvideos);
					while ($rowVideo = $resVideo->fetchArray()) {
						$mostrar = 1;

						// Comprobar que el video, si tiene fecha de caducidad, se encuentre en fechas validas:
						if ($rowVideo['fechaCaducidad'] != '') {
							$mostrar = 0;

							$date1 = new DateTime();
							$date2 = new DateTime($rowVideo['fechaCaducidad']);

							if ($date1 <= $date2) {
								$mostrar = 1;
							}
						}

						if ($mostrar == 1) {
							if ( ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 1) )||(!isset($_COOKIE['listMode'])) ) {
								$OUT .= '<div class="col-sm-6 col-md-3 video-col">';
									$OUT .= '<a class="ver-video" href="?IDcurso='.urlencode($cursoData['IDcurso']).'&IDtema='.urlencode($row['IDencriptado']).'&IDvideo='.urlencode($rowVideo['IDencriptado']).'">';
										$OUT .= '<img src="'._PORTALROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$row['ruta'].'/img/'.$rowVideo['img'].'" />';
										$OUT .= '<span>'.$rowVideo['nombre'].'</span>';
									$OUT .= '</a>';
								$OUT .= '</div>';
							} else if ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 2) ) {
								$OUT .= '<div class="row"><div class="col-md-3">';
									$OUT .= '<a class="ver-video" href="?IDcurso='.urlencode($cursoData['IDcurso']).'&IDtema='.urlencode($row['IDencriptado']).'&IDvideo='.urlencode($rowVideo['IDencriptado']).'">';
										$OUT .= '<img src="'._PORTALROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$row['ruta'].'/img/'.$rowVideo['img'].'" />';
									$OUT .= '</a>';
								$OUT .= '</div>';
								$OUT .= '<div class="col-md-9 caption">';
									$OUT .= '<a href="?IDcurso='.urlencode($cursoData['IDcurso']).'&IDtema='.urlencode($row['IDencriptado']).'&IDvideo='.urlencode($rowVideo['IDencriptado']).'">'.$rowVideo['nombre'].'</a>';
									$OUT .= '<p>'.$rowVideo['descripcion'].'</p>';
								$OUT .= '</div></div>';
							}
						}
					}
					$OUT .= '</div>';
				$OUT .= '</div>';
				$cont++;
			}

			$numPag = ceil($totalCursos / $totalPorPag);
			$last = $totalCursos;

			if ($numPag > 1) {
				$OUT .= '<ul class="pagination">';

				for ($i = 1; $i <= $numPag; $i++) {
					if ($i > 1) {
						$last = $last - $totalPorPag;
					}
					if ( ($i == $_GET['pag'])||( (!isset($_GET['pag']))&&($i == 1)) ) {
						$OUT .= '<li class="active"><a href="#">'.$i.'</a></li>';
					} else {
						$OUT .= '<li><a href="?IDcurso='.urlencode($cursoData['IDcurso']).'&pag='.$i.'&last='.($last+1).'">'.$i.'</a></li>';
					}
				}

				$OUT .= '</ul>';
			}
		$OUT .= '</div>';
	}
}

echo $OUT;
?>