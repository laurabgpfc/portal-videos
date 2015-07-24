<h1 class="page-header">Archivos adjuntos</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'forms/admin-adjuntos.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

if (!isset($_POST['IDcurso'])) {
	$_POST['IDcurso'] = ( ($_GET['IDcurso'] != '') ? $_GET['IDcurso'] : '' );
}
if (!isset($_POST['IDtema'])) {
	$_POST['IDtema'] = ( ($_GET['IDtema'] != '') ? $_GET['IDtema'] : '' );
}
if (!isset($_POST['IDvideo'])) {
	$_POST['IDvideo'] = ( ($_GET['IDvideo'] != '') ? $_GET['IDvideo'] : '' );
}
if (!isset($_POST['IDadjunto'])) {
	$_POST['IDadjunto'] = ( ($_GET['IDadjunto'] != '') ? $_GET['IDadjunto'] : '' );
}

$dir = '';
$listaVideos = getListaVideosDisponibles($_POST['IDvideo']);

// Si se ha eliminado el adjunto, borrar sus datos:
if ($error == 'danger') {
	$_POST['IDadjunto'] = '';
	$_POST['nombreAdjunto'] = '';
	$_POST['descripcion'] = '';
	$_POST['rutaAdjunto'] = '';
	$_POST['fechaCaducidad'] = '';
	$_POST['orden'] = '';
	$_POST['ocultar'] = '';

// Si estamos viendo un curso, pero no se ha enviado el formulario, mostrar sus datos:
} else if ( ($_POST['IDcurso'] != '')&&($_POST['IDtema'] != '')&&($_POST['IDvideo'] != '')&&($_POST['IDadjunto'] != '') ) {
	$cursoData = getCursoData($_POST['IDcurso']);
	$temaData = getTemaData($_POST['IDcurso'], $_POST['IDtema']);
	$videoData = getVideoData($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo']);
	$adjuntoData = getAdjuntoData($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], $_POST['IDadjunto']);

	if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}

	$_POST['nombreAdjunto'] = $adjuntoData['nombre'];
	$_POST['descripcion'] = $adjuntoData['descripcion'];
	$_POST['rutaAdjunto'] = $adjuntoData['ruta'];
	$_POST['fechaCaducidad'] = $adjuntoData['fechaCaducidad'];
	$_POST['orden'] = $adjuntoData['orden'];
	$_POST['ocultar'] = $adjuntoData['ocultar'];

}

$_POST['orden'] = ( $_POST['orden']=='' ? getNextOrdenAdjunto($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo']) : $_POST['orden'] );

$OUT = '';

if ($msgError != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form name="adjuntos" role="form" method="POST" action="'._PORTALROOT.'modules-admin/adjuntos.php" enctype="multipart/form-data">';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="nombreAdjunto">* Título del archivo adjunto:</label>';
		$OUT .= '<input required type="text" name="nombreAdjunto" class="form-control" id="nombreAdjunto" placeholder="Título del archivo adjunto" value="'.$_POST['nombreAdjunto'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="ocultar" type="checkbox"';
		if ($_POST['ocultar'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Adjunto oculto, no se mostrar&aacute; a ning&uacute;n usuario</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="orden">* Posici&oacute;n en la que se mostrar&aacute; el archivo adjunto:</label>';
		$OUT .= '<input required type="number" name="orden" class="form-control" id="orden" placeholder="Posici&oacute;n en la que se mostrar&aacute; el archivo adjunto" value="'.$_POST['orden'].'" min="1" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="descripcion">Descripción del archivo adjunto:</label>';
		$OUT .= '<textarea class="form-control" name="descripcion" rows="3">'.$_POST['descripcion'].'</textarea>';
	$OUT .= '</div>';
	if (sizeof($listaVideos) > 0) {
		$OUT .= '<div class="form-group">';
			$OUT .= '<label for="cambiar-adjunto">Seleccione el v&iacute;deo donde quiere cambiar el archivo adjunto:</label>';
			$OUT .= '<select class="form-control" name="cambiar-adjunto" id="cambiar-adjunto" >';
				$OUT .= '<option value="">Seleccione un v&iacute;deo</option>';
				foreach ($listaVideos as $video) {
					$OUT .= '<option value="'.$video['IDcurso'].'/-/'.$video['IDtema'].'/-/'.$video['IDvideo'].'">'.$video['nombre'].'</option>';
				}
			$OUT .= '</select>';
		$OUT .= '</div>';
	}
	$OUT .= '<div class="form-group input-group input-daterange">';
		$OUT .= '<label for="fechaCaducidad">Fecha en la que dejar de mostrar el adjunto:</label>';
		$OUT .= '<input type="text" class="form-control" name="fechaCaducidad" value="'.$_POST['fechaCaducidad'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		if (_ALLOWFILEUPLOAD == 1) {
			$OUT .= '<label for="rutaAdjunto" class="control-label">* Archivo:</label>';
			$OUT .= '<input id="rutaAdjunto" name="rutaAdjunto" type="file" accept="*" placeholder="Nombre del archivo de v&iacute;deo" data-preview-file-type="other" class="file-loading" value="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/docs/'.$_POST['rutaAdjunto'].'" />';
		} else {
			$OUT .= '<label for="rutaAdjunto">* Nombre del archivo:</label>'.( ($_POST['IDadjunto'] != '') ? ' <input class="checkbox-con-margen" type="checkbox" name="renombrarAdjunto" /> Marcar para renombrar' : '' );
			$OUT .= '<input required type="text" name="rutaAdjunto" class="form-control" id="rutaAdjunto-noUpload" placeholder="Nombre del archivo" value="'.$_POST['rutaAdjunto'].'" />';
		}
	$OUT .= '</div>';
	if ($cursoData['archivar'] == 0) {
		$OUT .= '<button type="submit" class="btn btn-default">Guardar</button>';
	}
	if ($_POST['IDadjunto'] != '') {
		$OUT .= '<button type="submit" value="del" name="formDel" class="btn btn-danger">Eliminar</button>';
	}
	$OUT .= '<input type="hidden" value="adjuntos" name="form" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcurso'].'" name="IDcurso" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDtema'].'" name="IDtema" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDvideo'].'" name="IDvideo" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDadjunto'].'" name="IDadjunto" />';
	$OUT .= '<input type="hidden" value="'.$_POST['rutaAdjunto'].'" name="rutaAdjuntoORI" />';
$OUT .= '</form>';

print($OUT);
?>
	</div>
</div>
