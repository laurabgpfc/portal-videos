$(window).load(function() {
	loadAjaxForm();
	
	if (getCookie('MoodleUserFaltaCorreo') != '') {
		$('#pedirEmail').modal({
			show: true,
			backdrop: 'static'
		});
	}
	
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');

	firstPlay = 1;
	$('video').on('play', function() {
		if (firstPlay == 1) {
			$.ajax({
				type: 'POST',
				async: true,
				url: 'modules/videoPlayed.php',
				data: sPageURL
			});
			
			firstPlay = 0;
		}
	});

	$('.descargarArchivo').click(function() {
		urlData = sPageURL;
		if ($(this).attr('rel')) {
			urlData = urlData + '&IDadjunto='+$(this).attr('rel');
		} else {
			urlData = urlData + '&IDadjunto=0';
		}

		$.ajax({
			type: 'POST',
			async: true,
			url: 'modules/descargarArchivo.php',
			data: urlData,
			success: function(msg) {
				window.location.reload();
			}
		});
	});

	if ($('select[name="select-categoria"]').length > 0) {
		$('select[name="select-categoria"]').change(function() {
			window.location = '?'+sPageURL+'&cat='+$(this).val();
		});
	}
});

function beforeSubmit(formData, jqForm, options) { 
	var queryString = $.param(formData); 

	console.log('enviando... ('+queryString+')');

	if (queryString.indexOf('logout') != -1) {
		location.reload();
	}

	return true;
} 
 
function submitDone(responseText, statusText, xhr, $form)  { 
	console.log('done!!!');
	
	if (responseText != '') {
		$('.form-error').show();
		loadAjaxForm();
	} else {
		location.reload();
	}
} 

function loadAjaxForm() {
	$('form[name="userSession"]').ajaxForm({
		target: 		'.form-error',
		beforeSubmit: 	beforeSubmit,
		success: 		submitDone
	});
}

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	}
	return "";
}