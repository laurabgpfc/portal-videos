<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'lib/curl.php');

function connect($functionName, $params) {
	$serverURL = _MOODLEURL . '/webservice/rest/server.php';
	$serverURL .= '?wstoken=' . _WSTOKEN;
	$serverURL .= '&wsfunction='.$functionName;
	$serverURL .= '&moodlewsrestformat=json';
	
	$curl = new curl;
	$rsp = $curl->post($serverURL, $params);
	
	return json_decode($rsp);
}



function wsRegistrarUsuarios($IDcurso, $IDcursoMoodle) {
	$configData = getConfigData();
	
	// Obtener los usuarios inscritos al curso:
	$usuariosEnCurso = connect('core_enrol_get_enrolled_users', array( 'courseid' => $IDcursoMoodle ));
	
	foreach ($usuariosEnCurso as $user) {
		$insertar = 0;
		$esAdmin = 0;

		// Comprobar si el rol se puede importar:
		foreach ($user->roles as $rol) {
			if (checkMoodleRol('nombre = "'.$rol->shortname.'" AND importar = 1')) {
				$insertar = 1;
			}
			// Comprobar si el rol es de admin:
			if (is_int(array_search($rol->shortname, array_column($configData['listaMoodleRoles'], 'nombre')))) {
				$esAdmin = $configData['listaMoodleRoles'][array_search($rol->shortname, array_column($configData['listaMoodleRoles'], 'nombre'))]['esAdmin'];
			}
		}
		if ($insertar == 1) {
			registrarUsuarioCurso($IDcurso, $IDcursoMoodle, $user->fullname, $user->email, $esAdmin);
		}
	}
}

?>