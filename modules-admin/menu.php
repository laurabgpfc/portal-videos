<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');

$OUT = '';

function listaItemsCursos($anoAcademico = "", $crear = 1) {
	$opt = 'cursos';
	$OUT = '';

	$cls = '';
	if ( ($_GET['IDcurso'] == '')&&($_GET['opt'] == $opt) ) {
		$cls = ' active';
	}

	if ($crear == 1) {
		$OUT .= '<li class="firstChild'.$cls.'">';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-plus"></span>';
				$OUT .= '<a href="?opt='.$opt.'">';
					$OUT .= '<span class="txt" title="Crear nuevo curso">Crear nuevo curso</span>';
				$OUT .= '</a>';
				$OUT .= '<a class="order pull-right" title="Ordenar Cursos" href="?opt='.$opt.'"><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
		$OUT .= '</li>';
	}

	$listaCursos = getListaCursos($anoAcademico);
	
	for ($i = 0; $i < sizeof($listaCursos); $i++) {
		$item = $listaCursos[$i];
		$cls = '';
		
		if ( ($item[0] == $_GET['IDcurso']) ) {
			if ( ($_GET['IDtema'] != '')||( ($_GET['IDtema'] == '')&&($_GET['opt'] == 'temas') ) ) {
				$cls .= ' expanded';
			}
			if ( ($_GET['opt'] == $opt)&&($_GET['IDtema'] == '')&&($_GET['IDvideo'] == '')&&($_GET['IDadjunto'] == '') )  {
				$cls .= ' active';
			}
		}

		$OUT .= '<li class="firstChild'.$cls.'">';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-folder-close"></span>';
				$OUT .= '<span class="txt" title="'.$item[1].'">'.$item[1].'</span>';
				$OUT .= '<a class="edit" title="Editar Curso" href="?opt='.$opt.'&IDcurso='.urlencode($item[0]).'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
				$OUT .= '<a class="dup" title="Duplicar Curso" href="?opt='.$opt.'&IDcurso='.urlencode($item[0]).'"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
			$OUT .= '<ul class="submenu">';
				$OUT .= listaItemsTemas($item[0], $crear);
			$OUT .= '</ul>';
		$OUT .= '</li>';
	}
	
	return $OUT;
}

function listaItemsTemas($IDcurso, $crear = 1) {
	$opt = 'temas';
	$OUT = '';

	$cls = '';

	if ( ($_GET['opt'] == $opt)&&($_GET['IDcurso'] == $IDcurso)&&($_GET['IDtema'] == '') ) {
		$cls = ' class="active"';
	}

	if ($crear == 1) {
		$OUT .= '<li'.$cls.'>';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-plus"></span>';
				$OUT .= '<a href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'">';
					$OUT .= '<span class="txt" title="Añadir nuevo tema">Añadir nuevo tema</span>';
				$OUT .= '</a>';
				$OUT .= '<a class="order pull-right" title="Ordenar Temas" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'"><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
		$OUT .= '</li>';
	}

	$listaTemas = getListaTemasByCurso($IDcurso);

	for ($i = 0; $i < sizeof($listaTemas); $i++) {
		$item = $listaTemas[$i];
		$cls = '';

		if ( ($item[0] == $_GET['IDtema']) ) {
			if ( ($_GET['IDvideo'] != '')||( ($_GET['IDvideo'] == '')&&($_GET['opt'] == 'videos') ) ) {
				$cls .= ' expanded';
			}
			if ( ($_GET['opt'] == $opt)&&($_GET['IDvideo'] == '')&&($_GET['IDadjunto'] == '') ) {
				$cls .= ' active';
			}
		}

		$OUT .= '<li class="'.$cls.'">';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-folder-close"></span>';
				$OUT .= '<span class="txt" title="'.$item[1].'">'.$item[1].'</span>';
				$OUT .= '<a class="edit" title="Editar Tema" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($item[0]).'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
				$OUT .= '<a class="dup" title="Duplicar Tema" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($item[0]).'"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
			$OUT .= '<ul class="submenu">';
				$OUT .= listaItemsVideos($IDcurso, $item[0], $crear);
			$OUT .= '</ul>';
		$OUT .= '</li>';
	}

	return $OUT;
}

function listaItemsVideos($IDcurso, $IDtema, $crear = 1) {
	$opt = 'videos';
	$OUT = '';

	$cls = '';
	if ( ($_GET['opt'] == $opt)&&($_GET['IDcurso'] == $IDcurso)&&($_GET['IDtema'] == $IDtema)&&($_GET['IDvideo'] == '') ) {
		$cls = ' class="active"';
	}

	if ($crear == 1) {
		$OUT .= '<li'.$cls.'>';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-plus"></span>';
				$OUT .= '<a href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($IDtema).'">';
					$OUT .= '<span class="txt" title="Añadir nuevo vídeo">Añadir nuevo vídeo</span>';
				$OUT .= '</a>';
				$OUT .= '<a class="order pull-right" title="Ordenar V&iacute;deos" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($IDtema).'"><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
		$OUT .= '</li>';
	}

	$listaVideos = getListaVideosByTemaCurso($IDcurso, $IDtema);

	for ($i = 0; $i < sizeof($listaVideos); $i++) {
		$item = $listaVideos[$i];
		$cls = '';

		if ( ($item[0] == $_GET['IDvideo']) ) {
			if ( ($_GET['IDadjunto'] != '')||( ($_GET['IDadjunto'] == '')&&($_GET['opt'] == 'adjuntos') ) ) {
				$cls .= ' expanded';
			}
			if ( ($_GET['opt'] == $opt)&&($_GET['IDadjunto'] == '') ) {
				$cls .= ' active';
			}
		}

		$OUT .= '<li class="'.$cls.'">';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-folder-close"></span>';
			//	$OUT .= '<span class="glyphicon glyphicon-facetime-video"></span>';
				$OUT .= '<span class="txt" title="'.$item[1].'">'.$item[1].'</span>';
				$OUT .= '<a class="edit" title="Editar V&iacute;deo" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($IDtema).'&IDvideo='.urlencode($item[0]).'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
				$OUT .= '<a class="dup" title="Duplicar V&iacute;deo" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($IDtema).'&IDvideo='.urlencode($item[0]).'"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
			$OUT .= '<ul class="submenu">';
				$OUT .= listaItemsAdjuntos($IDcurso, $IDtema, $item[0], $crear);
			$OUT .= '</ul>';
		$OUT .= '</li>';
	}

	return $OUT;
}


function listaItemsAdjuntos($IDcurso, $IDtema, $IDvideo, $crear = 1) {
	$opt = 'adjuntos';
	$OUT = '';

	$cls = '';
	if ( ($_GET['opt'] == $opt)&&($_GET['IDcurso'] == $IDcurso)&&($_GET['IDtema'] == $IDtema)&&($_GET['IDvideo'] == $IDvideo)&&($_GET['IDadjunto'] == '') ) {
		$cls = ' class="active"';
	}

	if ($crear == 1) {
		$OUT .= '<li'.$cls.'>';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-plus"></span>';
				$OUT .= '<a href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($IDtema).'&IDvideo='.urlencode($IDvideo).'">';
					$OUT .= '<span class="txt" title="Añadir nuevo adjunto">Añadir nuevo adjunto</span>';
				$OUT .= '</a>';
				$OUT .= '<a class="order pull-right" title="Ordenar Adjuntos" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($IDtema).'&IDvideo='.urlencode($IDvideo).'"><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
		$OUT .= '</li>';
	}

	$listaAdjuntos = getListaAdjuntosByVideoTemaCurso($IDcurso, $IDtema, $IDvideo);

	for ($i = 0; $i < sizeof($listaAdjuntos); $i++) {
		$item = $listaAdjuntos[$i];
		$cls = '';

		if ( ($_GET['opt'] == $opt)&&($item[0] == $_GET['IDadjunto']) ) {
			$cls = ' class="active"';
		}

		$OUT .= '<li'.$cls.'>';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-file"></span>';
				$OUT .= '<span class="txt" title="'.$item[1].'">'.$item[1].'</span>';
				$OUT .= '<a class="edit" title="Editar Adjunto" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($IDtema).'&IDvideo='.urlencode($IDvideo).'&IDadjunto='.$item[0].'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
				$OUT .= '<a class="dup" title="Duplicar Adjunto" href="?opt='.$opt.'&IDcurso='.urlencode($IDcurso).'&IDtema='.urlencode($IDtema).'&IDvideo='.urlencode($IDvideo).'&IDadjunto='.$item[0].'"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
		$OUT .= '</li>';
	}

	return $OUT;
}

$menu = array(
	array( 'nombre' => 'Estad&iacute;sticas', 'url' => 'estadisticas' ),
	array( 'nombre' => 'Usuarios', 'url' => 'usuarios' ),
	array( 'nombre' => 'Configuración', 'url' => 'config' ),
	array( 'nombre' => 'gestionCursos', 'url' => 'gestionCursos' )
);

$OUT .= '<ul class="nav nav-sidebar">';

for ($i = 0; $i < sizeof($menu); $i++) {
	$item = $menu[$i];

	if ($item['url'] == 'gestionCursos') {
		if ($i > 0) {
			$OUT .= '</ul>';
		}

		$OUT .= '<div class="anoAcademico">';
			$OUT .= '<div class="titulo"><span class="glyphicon glyphicon-chevron-down"></span> <em>Año Acad&eacute;mico actual</em></div>';

			$OUT .= '<div class="tree mostrar">';
				$OUT .= '<ul class="nav nav-sidebar">';
					$OUT .= listaItemsCursos();
				$OUT .= '</ul>';
			$OUT .= '</div>';
		$OUT .= '</div>';

		$listaAnosAcademicos = getListaAnosAcademicos();

		for ($j = 0; $j < sizeof($listaAnosAcademicos); $j++) {
			$OUT .= '<div class="anoAcademico">';
				$OUT .= '<div class="titulo"><span class="glyphicon glyphicon-chevron-up"></span> <em>'.$listaAnosAcademicos[$j].'</em></div>';
				
				$OUT .= '<div class="tree no-mostrar">';
					$OUT .= '<ul class="nav nav-sidebar">';
						$OUT .= listaItemsCursos($listaAnosAcademicos[$j], 0);
					$OUT .= '</ul>';
				$OUT .= '</div>';
			$OUT .= '</div>';
		}

	} else {
		$OUT .= '<li';
		if ( ( ($_GET['opt'] == '')&&($item['url'] == _ADMINDEF) )||( ($_GET['opt'] == $item['url']) ) ) {
			$OUT .=  ' class="active"';
		}
		$OUT .= '><a href="?opt='.$item['url'].'">'.$item['nombre'].'</a></li>';
	}
}
//$OUT .= '</ul>';

echo $OUT;

?>