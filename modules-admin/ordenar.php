<?php
include_once(__DIR__.'/../config.php');


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

if ($_GET['opt'] == 'cursos') {
	$listado = getListaCursos();

} else if ($_GET['opt'] == 'temas') {
	$listado = getListaTemasByCurso($_POST['IDcurso']);

} else if ($_GET['opt'] == 'videos') {
	$listado = getListaVideosByTemaCurso($_POST['IDcurso'], $_POST['IDtema']);

} else if ($_GET['opt'] == 'adjuntos') {
	$listado = getListaAdjuntosByVideoTemaCurso($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo']);

}

$OUT = '';
?>


<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Ordenar elementos</h4>
		</div>
		<div class="modal-body">
			<p>Arrastre los elementos en la posici&oacute;n en la que quiere dejarlos.</p>
			<?php
				$OUT .= '<ol id="lista-sortable" class="default vertical">';
				foreach ($listado as $item) {
					$OUT .= '<li data-tipo="'.$_GET['opt'].'" data-id="'.$item[0].'" data-name="'.$item[1].'">'.$item[1].'</li>';
				}
				$OUT .= '</ol>';

				echo $OUT;
			?>
		</div>
	</div>
</div>
