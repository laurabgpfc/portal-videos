<h1 class="page-header">Temas</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'forms/admin-temas.php');

if (!isset($_POST['IDcurso'])) {
	$_POST['IDcurso'] = ( ($_GET['IDcurso'] != '') ? $_GET['IDcurso'] : '' );
}
if (!isset($_POST['IDtema'])) {
	$_POST['IDtema'] = ( ($_GET['IDtema'] != '') ? $_GET['IDtema'] : '' );
}

$listaCursos = getListaCursosDisponibles($_POST['IDcurso']);

// Si se ha eliminado el tema, borrar sus datos:
if ($error == 'danger') {
	$_POST['IDtema'] = '';
	$_POST['nombreTema'] = '';
	$_POST['rutaTema'] = '';
	$_POST['descripcion'] = '';
	$_POST['orden'] = '';
	$_POST['ocultar'] = '';

// Si estamos viendo un curso, mostrar sus datos:
} else if ( ($_POST['IDcurso'] != '')&&($_POST['IDtema'] != '') ) {
	$cursoData = getCursoData($_POST['IDcurso']);
	$temaData = getTemaData($_POST['IDcurso'], $_POST['IDtema']);
	$_POST['nombreTema'] = $temaData['nombre'];
	$_POST['rutaTema'] = $temaData['ruta'];
	$_POST['descripcion'] = $temaData['descripcion'];
	$_POST['orden'] = $temaData['orden'];
	$_POST['ocultar'] = $temaData['ocultar'];
}

$_POST['orden'] = ( $_POST['orden']=='' ? getNextOrdenTema($_POST['IDcurso']) : $_POST['orden'] );

$OUT = '';

if ($msgError != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form role="form" method="POST" action="'._PORTALROOT.'modules-admin/temas.php">';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="nombreTema">* Nombre del tema:</label>';
		$OUT .= '<input required type="text" name="nombreTema" class="form-control" id="nombreTema" placeholder="Nombre del tema" value="'.$_POST['nombreTema'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="ocultar" type="checkbox"';
		if ($_POST['ocultar'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Tema oculto, no se mostrar&aacute; a ning&uacute;n usuario</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="orden">* Posici&oacute;n en la que se mostrar&aacute; el tema:</label>';
		$OUT .= '<input required type="number" name="orden" class="form-control" id="orden" placeholder="Posici&oacute;n en la que se mostrar&aacute; el tema" value="'.$_POST['orden'].'" min="1" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="descripcion">Descripción del tema:</label>';
		$OUT .= '<textarea class="form-control" name="descripcion" rows="3">'.$_POST['descripcion'].'</textarea>';
	$OUT .= '</div>';
	if (sizeof($listaCursos) > 0) {
		$OUT .= '<div class="form-group">';
			$OUT .= '<label for="cambiar-tema">Seleccione el curso donde quiere cambiar el tema:</label>';
			$OUT .= '<select class="form-control" name="cambiar-tema" id="cambiar-teman" >';
				$OUT .= '<option value="">Seleccione un curso</option>';
				foreach ($listaCursos as $curso) {
					$OUT .= '<option value="'.$curso['ID'].'">'.$curso['nombre'].'</option>';
				}
			$OUT .= '</select>';
		$OUT .= '</div>';
	}
	$OUT .= '<button type="submit" class="btn btn-default">Guardar</button>';
	if ($_POST['IDtema'] != '') {
		$OUT .= '<button type="submit" value="del" name="formDel" class="btn btn-danger">Eliminar</button>';
	}
	$OUT .= '<input type="hidden" value="temas" name="form" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcurso'].'" name="IDcurso" />';
	$OUT .= '<input type="hidden" value="'.$_POST['rutaTema'].'" name="rutaTemaORI" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDtema'].'" name="IDtema" />';
$OUT .= '</form>';

print($OUT);
?>
	</div>
</div>