<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');

global $dbAn;

$OUT = '';

if ($_POST['chartName'] == 'totalVisualizaciones') {
	$totalAnonimos = $dbAn->querySingle('SELECT COUNT(*) FROM analytics WHERE IDusuario = 0');
	$totalRegistrados = $dbAn->querySingle('SELECT COUNT(*) FROM analytics WHERE IDusuario != 0');

	$OUT .= '[';
		$OUT .= '[ "Anonimos", '.$totalAnonimos.' ],';
		$OUT .= '[ "Registrados", '.$totalRegistrados.' ]';
	$OUT .= ']';

} else if ($_POST['chartName'] == 'totalDescargas') {
	$totalAnonimos = $dbAn->querySingle('SELECT COUNT(*) FROM descargas WHERE IDusuario = 0');
	$totalRegistrados = $dbAn->querySingle('SELECT COUNT(*) FROM descargas WHERE IDusuario != 0');

	$totalVideosAnonimos = $dbAn->querySingle('SELECT COUNT(*) FROM descargas WHERE IDadjunto = 0 AND IDusuario = 0');
	$totalAdjuntosAnonimos = $dbAn->querySingle('SELECT COUNT(*) FROM descargas WHERE IDadjunto != 0 AND IDusuario = 0');
	$totalVideosRegistrados = $dbAn->querySingle('SELECT COUNT(*) FROM descargas WHERE IDadjunto = 0 AND IDusuario != 0');
	$totalAdjuntosRegistrados = $dbAn->querySingle('SELECT COUNT(*) FROM descargas WHERE IDadjunto != 0 AND IDusuario != 0');

	$OUT .= '{"series":[';
		$OUT .= '{"name":"Anonimos","y":'.$totalAnonimos.',"drilldown":"anon"},';
		$OUT .= '{"name":"Registrados","y":'.$totalRegistrados.',"drilldown":"reg"}';
	$OUT .= '],';
	$OUT .= '"drilldown":{';
		$OUT .= '"series":[{';
			$OUT .= '"id": "anon",';
			$OUT .= '"data": [';
				$OUT .= '{"name":"Videos","y":'.$totalVideosAnonimos.'},';
				$OUT .= '{"name":"Adjuntos","y":'.$totalAdjuntosAnonimos.'}';
			$OUT .= ']';
		$OUT .= '},{';
			$OUT .= '"id": "reg",';
			$OUT .= '"data": [';
				$OUT .= '{"name":"Videos","y":'.$totalVideosRegistrados.'},';
				$OUT .= '{"name":"Adjuntos","y":'.$totalAdjuntosRegistrados.'}';
			$OUT .= ']';
		$OUT .= '}]';
	$OUT .= '}}';


} else if ( ($_POST['chartName'] == 'videosMasVistos')||($_GET['chartName'] == 'videosMasVistos') ) {
	$categories = array();
	$data = array();

	$res = $dbAn->query('SELECT IDcurso, IDtema, IDvideo, COUNT(*) AS total FROM analytics GROUP BY IDcurso, IDtema, IDvideo ORDER BY 4 DESC LIMIT 5');
	while ($row = $res->fetchArray()) {
		$cursoData = getCursoData($row['IDcurso']);
		$temaData = getTemaData($row['IDcurso'], $row['IDtema']);
		$videoData = getVideoData($row['IDcurso'], $row['IDtema'], $row['IDvideo']);

		array_push($categories, $cursoData['nombre'].' / '.$temaData['nombre'].' / '.$videoData['nombre']);
		array_push($data, $row['total']);
	}

	$OUT .= '{';
		$OUT .= '"categories": ["'.implode('","', $categories).'"],';
		$OUT .= '"info": [{"name":"Total reproducciones","data":['.implode(',',$data).']}]';
	$OUT .= '}';

} else if ( ($_POST['chartName'] == 'videosMasDescargados')||($_GET['chartName'] == 'videosMasDescargados') ) {
	$categories = array();
	$data = array();

	$res = $dbAn->query('SELECT IDcurso, IDtema, IDvideo, COUNT(*) AS total FROM descargas WHERE IDadjunto = 0 GROUP BY IDcurso, IDtema, IDvideo ORDER BY 4 DESC LIMIT 5');
	while ($row = $res->fetchArray()) {
		$cursoData = getCursoData($row['IDcurso']);
		$temaData = getTemaData($row['IDcurso'], $row['IDtema']);
		$videoData = getVideoData($row['IDcurso'], $row['IDtema'], $row['IDvideo']);

		array_push($categories, $cursoData['nombre'].' / '.$temaData['nombre'].' / '.$videoData['nombre']);
		array_push($data, $row['total']);
	}

	$OUT .= '{';
		$OUT .= '"categories": ["'.implode('","', $categories).'"],';
		$OUT .= '"info": [{"name":"Total descargas","data":['.implode(',',$data).']}]';
	$OUT .= '}';

} else if ( ($_POST['chartName'] == 'adjuntosMasDescargados')||($_GET['chartName'] == 'adjuntosMasDescargados') ) {
	$categories = array();
	$data = array();

	$res = $dbAn->query('SELECT IDcurso, IDtema, IDvideo, IDadjunto, COUNT(*) AS total FROM descargas WHERE IDadjunto != 0 GROUP BY IDcurso, IDtema, IDvideo ORDER BY 4 DESC LIMIT 5');
	while ($row = $res->fetchArray()) {
		$cursoData = getCursoData($row['IDcurso']);
		$temaData = getTemaData($row['IDcurso'], $row['IDtema']);
		$videoData = getVideoData($row['IDcurso'], $row['IDtema'], $row['IDvideo']);
		$adjuntoData = getAdjuntoData($row['IDcurso'], $row['IDtema'], $row['IDvideo'], $row['IDadjunto']);

		array_push($categories, $cursoData['nombre'].' / '.$temaData['nombre'].' / '.$videoData['nombre'].' / '.$adjuntoData['nombre']);
		array_push($data, $row['total']);
	}

	$OUT .= '{';
		$OUT .= '"categories": ["'.implode('","', $categories).'"],';
		$OUT .= '"info": [{"name":"Total descargas","data":['.implode(',',$data).']}]';
	$OUT .= '}';
}

echo $OUT;

?>