<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'lib/curl.php');
include_once(_DOCUMENTROOT.'lib/simple_html_dom.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');

function login($userName, $userPass) {
	$errorText = '';

	$serverURL = _MOODLEURL . '/login/index.php';
	
	$curl = new curl;
	$rsp = $curl->post($serverURL, 'username='.$userName.'&password='.$userPass);
	
	$html = str_get_html($rsp);

	foreach($html->find('span') as $element) {
		if ($element->class == 'error') {
			$errorText .= $element->plaintext.'<br />';
		}
	}

	if ($errorText == '') {
		$cookie_name = 'MoodleUserSession';
		$cookie_value = time();
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), _PORTALROOT); // 86400 = 1 day

		return '';
	} else {
		return $errorText;
	}
}

function logout() {
	if (isset($_COOKIE['MoodleUserSession'])) {
		unset($_COOKIE['MoodleUserSession']);
		setcookie('MoodleUserSession', null, -1, _PORTALROOT);
	}
}

?>