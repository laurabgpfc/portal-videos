<h1 class="page-header">Cursos</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'forms/admin-cursos.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');

if (!isset($_POST['IDcurso'])) {
	$_POST['IDcurso'] = ( ($_GET['IDcurso'] != '') ? $_GET['IDcurso'] : '' );
}

$listaCursosMoodle = connect('core_course_get_courses', '');

// Si se ha eliminado el curso, borrar sus datos:
if ($error == 'danger') {
	$_POST['IDcurso'] = '';
	$_POST['nombreCurso'] = '';
	$_POST['descripcion'] = '';
	$_POST['rutaCurso'] = '';
	$_POST['ubicacion'] = '';
	$_POST['orden'] = '';
	$_POST['ocultar'] = '';
	$_POST['IDcursoMoodle'] = '';
	$_POST['fechaIni'] = '';
	$_POST['fechaFin'] = '';
	$_POST['publico'] = '';
	$_POST['archivar'] = '';

// Si estamos viendo un curso, mostrar sus datos:
} else if ($_POST['IDcurso'] != '') {
	$cursoData = getCursoData($_POST['IDcurso']);
	$_POST['nombreCurso'] = $cursoData['nombre'];
	$_POST['descripcion'] = $cursoData['descripcion'];
	$_POST['rutaCurso'] = $cursoData['ruta'];
	$_POST['ubicacion'] = $cursoData['ubicacion'];
	$_POST['orden'] = $cursoData['orden'];
	$_POST['ocultar'] = $cursoData['ocultar'];
	$_POST['IDcursoMoodle'] = $cursoData['IDcursoMoodle'];
	$_POST['fechaIni'] = $cursoData['fechaIni'];
	$_POST['fechaFin'] = $cursoData['fechaFin'];
	$_POST['publico'] = $cursoData['publico'];
	$_POST['archivar'] = $cursoData['archivar'];
}

$_POST['orden'] = ( $_POST['orden']=='' ? getNextOrdenCurso() : $_POST['orden'] );

$OUT = '';

if ($msgError != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form role="form" method="POST" action="'._PORTALROOT.'modules-admin/cursos.php">';
	if ($_POST['IDcurso'] != '') {
		$OUT .= '<div class="form-group">';
			$OUT .= '<label>URL del curso:</label> http://'.$_SERVER['SERVER_NAME']._PORTALROOT.'?IDcurso='.$cursoData['IDcurso'];
		$OUT .= '</div>';
	}
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="nombreCurso">* Nombre del curso:</label>';
		$OUT .= '<input required type="text" name="nombreCurso" class="form-control" id="nombreCurso" placeholder="Nombre del curso" value="'.$_POST['nombreCurso'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="IDcursoMoodle">Seleccione el curso de Moodle asociado:</label>';
		$OUT .= '<select class="form-control" name="IDcursoMoodle" id="IDcursoMoodle" >';
			$OUT .= '<option value="">Seleccione un curso</option>';
			if (sizeof($listaCursosMoodle) > 0) {
				foreach ($listaCursosMoodle as $c) {
					if ($c->categoryid > 0) {
						$OUT .= '<option value="'.$c->id.'"';
						if ($_POST['IDcursoMoodle'] == $c->id) {
							$OUT .= ' selected';
						}
						$OUT .= '>'.$c->fullname.'</option>';
					}
				}
			}
		$OUT .= '</select>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="ubicacion">* Seleccione una ubicaci&oacute;n:</label>';
		$OUT .= '<select required class="form-control" name="ubicacion" id="ubicacion" >';
			$OUT .= '<option value="">Seleccione una ubicaci&oacute;n </option>';
			if (sizeof($listaDirs) > 0) {
				foreach ($listaDirs as $ub) {
					$OUT .= '<option value="'.$ub['ID'].'"';
					if ($_POST['ubicacion'] == $ub['ID']) {
						$OUT .= ' selected';
					}
					$OUT .= '>'.$ub['ruta'].'</option>';
				}
			}
		$OUT .= '</select>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="fechaIni">Fechas en las que mostrar el curso:</label>';
		$OUT .= '<div class="input-daterange input-group" id="datepicker">';
			$OUT .= '<span class="input-group-addon">desde</span>';
			$OUT .= '<input type="text" class="input-sm form-control" name="fechaIni" value="'.$_POST['fechaIni'].'" />';
			$OUT .= '<span class="input-group-addon">hasta</span>';
			$OUT .= '<input type="text" class="input-sm form-control" name="fechaFin" value="'.$_POST['fechaFin'].'" />';
		$OUT .= '</div>';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="publico" type="checkbox"';
		if ($_POST['publico'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Curso público (visible para usuarios no conectados)</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="ocultar" type="checkbox"';
		if ($_POST['ocultar'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Curso oculto, no se mostrar&aacute; a ning&uacute;n usuario</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="orden">* Posici&oacute;n en la que se mostrar&aacute; el curso:</label>';
		$OUT .= '<input required type="number" name="orden" class="form-control" id="orden" placeholder="Posici&oacute;n en la que se mostrar&aacute; el curso" value="'.$_POST['orden'].'" min="1" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="descripcion">Descripción del curso:</label>';
		$OUT .= '<textarea class="form-control" name="descripcion" rows="3">'.$_POST['descripcion'].'</textarea>';
	$OUT .= '</div>';

	if ($_POST['IDcursoMoodle'] != '') {
		$listaUsuarios = getUsuariosByCurso($_POST['IDcurso']);

		$OUT .= '<div class="form-group">';
			$OUT .= '<label>Listado de usuarios inscritos a este curso:</label>';
			$OUT .= '<div class="table-responsive">';
				$OUT .= '<table class="table">';
					$OUT .= '<thead>';
						$OUT .= '<tr>';
							$OUT .= '<th>Nombre completo</th>';
							$OUT .= '<th>Email</th>';
						$OUT .= '</tr>';
					$OUT .= '</thead>';
					$OUT .= '<tbody>';
						foreach ($listaUsuarios as $usuario) {
							$OUT .= '<tr>';
								$OUT .= '<td'.( $usuario['esAdmin']==1 ? ' title="Usuario Administrador"' : ' title="Estudiante"' ).'><span class="glyphicon glyphicon-'.( $usuario['esAdmin']==1 ? 'user' : 'education' ).'"></span> '.$usuario['fullname'].'</td>';
								$OUT .= '<td>'.$usuario['email'].'</td>';
							$OUT .= '</tr>';
						}
					$OUT .= '</tbody>';
				$OUT .= '</table>';
			$OUT .= '</div>';
		$OUT .= '</div>';
	}

	if ($_POST['IDcurso'] != '') {
		if ($_POST['archivar'] == 0) {
			$OUT .= '<button type="submit" class="btn btn-default">Guardar</button>';
			$OUT .= '<button type="button" rel="opt=cursos&IDcurso='.$_POST['IDcurso'].'" id="archivar-curso" class="btn btn-primary">Archivar</button>';
		} else {
			$OUT .= '<button type="submit" name="archivar-curso" value="recuperar" class="btn btn-primary">Recuperar curso archivado</button>';
		}
	} else {
		$OUT .= '<button type="submit" class="btn btn-default">Guardar</button>';
	}
	if ($_POST['IDcurso'] != '') {
		$OUT .= '<button type="submit" value="del" name="formDel" class="btn btn-danger">Eliminar</button>';
	}
	$OUT .= '<input type="hidden" value="cursos" name="form" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcurso'].'" name="IDcurso" />';
	$OUT .= '<input type="hidden" value="'.$_POST['rutaCurso'].'" name="rutaCursoORI" />';
	$OUT .= '<input type="hidden" value="'.$_POST['ubicacion'].'" name="ubicacionORI" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcursoMoodle'].'" name="IDcursoMoodleORI" />';
$OUT .= '</form>';

print($OUT);
?>
	</div>
</div>