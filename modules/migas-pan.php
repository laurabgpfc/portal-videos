<?php

include_once(__DIR__.'/../config.php');

$OUT = '';

if (isset($_GET['IDcurso'])) {
	$cursoData = getCursoData($_GET['IDcurso']);

	$OUT .= '<div class="container margin-bottom">';
		$OUT .= '<ol class="breadcrumb">';
			$OUT .= '<li><a href="'._PORTALROOT.'">Inicio</a></li>';
			if (!isset($_GET['IDvideo'])) {
				$OUT .= '<li class="active">'.$cursoData['nombre'].'</li>';
			} else {
				$temaData = getTemaData($_GET['IDcurso'], $_GET['IDtema']);
				$videoData = getVideoData($_GET['IDcurso'], $_GET['IDtema'], $_GET['IDvideo']);
				$OUT .= '<li><a href="'._PORTALROOT.'?IDcurso='.urlencode($cursoData['IDcurso']).'">'.$cursoData['nombre'].'</a></li>';
				$OUT .= '<li class="active">'.$temaData['nombre'].'</li>';
				$OUT .= '<li class="active">'.$videoData['nombre'].'</li>';
			}
		$OUT .= '</ol>';
	$OUT .= '</div>';
}
echo $OUT;

?>
