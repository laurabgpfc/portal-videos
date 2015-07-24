$(window).load(function() {
	if ($('.nav-sidebar').length > 0) {
		loadMenu();
	}
	loadAjaxForm();
	loadLoginForm();

	if (getCookie('MoodleUserFaltaCorreo') != '') {
		$('#pedirEmail').modal({
			show: true,
			backdrop: 'static'
		});
	}
});

function loadMenu() {
	$('.nav-sidebar a').click(function() {
		$('.nav-sidebar li').removeClass('active');
		$(this).closest('li').addClass('active');
		template = getUrlParameter('opt', $(this).attr('href'));

		url = 'modules-admin/'+template+'.php?opt='+template;

		if ($(this).attr('href').indexOf('IDcurso') != -1) {
			url = url + '&IDcurso='+getUrlParameter('IDcurso', $(this).attr('href'));
		}
		if ($(this).attr('href').indexOf('IDtema') != -1) {
			url = url + '&IDtema='+getUrlParameter('IDtema', $(this).attr('href'));
		}
		if ($(this).attr('href').indexOf('IDvideo') != -1) {
			url = url + '&IDvideo='+getUrlParameter('IDvideo', $(this).attr('href'));
		}
		if ($(this).attr('href').indexOf('IDadjunto') != -1) {
			url = url + '&IDadjunto='+getUrlParameter('IDadjunto', $(this).attr('href'));
		}

		$('.main').html('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
		setTimeout("$('.main').load(url, function () { loadAjaxForm(); });",500);

		return false;
	});
	
	$('div.anoAcademico').children('.titulo').click(function() {
		if ($(this).siblings('.tree').hasClass('mostrar')) {
			$(this).siblings('.tree').removeClass('mostrar');
			$(this).children('span').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
			altura = $(this).siblings('.tree').height();
			$(this).siblings('.tree').animate({ 'height':'0px' },500,function() {
				$(this).addClass('no-mostrar');
				$(this).height(altura);
			});
		} else {
			$(this).children('span').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
			altura = $(this).siblings('.tree').height();
			$(this).siblings('.tree').height(0);
			$(this).siblings('.tree').removeClass('no-mostrar').addClass('mostrar');
			$(this).siblings('.tree').animate({ 'height':altura+'px' },500);
		}
	});

	$('a.dup').unbind().click(function() {
		$('#modalContent').html('').load('modules-admin/duplicar.php?'+$(this).attr('href').split('?')[1], function() {
			// Cargar funcionalidad ajax para todos los formularios:
			$('form[name="duplicarContenido"]').unbind().ajaxForm({
				target: 		'#modalContent .form-error',
				beforeSubmit: 	function(formData, jqForm, options) { 
					$('#modalContent').find('.modal-body').append('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
					$('#modalContent').find('form').hide();
					return true;
				},
				success: 		function(responseText, statusText, xhr, $form)  { 
					if (responseText.indexOf('?opt=') != -1) {
						$('#modalContent').modal('hide');
						template = getUrlParameter('opt', responseText);
						url = 'modules-admin/'+template+'.php'+responseText;
						
						console.log(url);
						console.log('modules-admin/menu.php'+responseText);
						$('.sidebar').html('').load('modules-admin/menu.php'+responseText, function() {
							loadMenu();
						});
						$('.main').html('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
						setTimeout("$('.main').load(url, function () { loadAjaxForm(); });",500);
					} else if (responseText == '') {
						$('#modalContent').modal('hide');
					} else {
						$('#modalContent').find('.form-error').show();
						$('#loading-content').detach();
						$('#modalContent').find('form').show();
					}
				}
			});
			$('#modalContent').modal({
				show: true
			});
		});

		return false;
	});

	$('a.order').unbind().click(function() {
		url = '?'+$(this).attr('href').split('?')[1];

		template = getUrlParameter('opt', url);
		urlReload = 'modules-admin/'+template+'.php'+url;
		
		$('#modalContent').html('').load('modules-admin/ordenar.php'+url, function() {
			var group = $('ol#lista-sortable').sortable({
				onDrop: function ($item, container, _super) {
					var data = group.sortable('serialize').get();
					var jsonString = JSON.stringify(data, null, ' ');

					$.ajax({
						type: 'POST',
						url: 'forms/admin-ordenar.php',
						data: 'nuevoOrden='+jsonString,
						success: function(msg) {
							console.log(msg);
						}
					});

					console.log(data);
					
					_super($item, container);
				}
			});
			$('#modalContent').modal({
				show: true
			});
			$('#modalContent').on('hide.bs.modal', function (e) {
				$('.sidebar').html('').load('modules-admin/menu.php'+url, function() {
					loadMenu();
				});
				$('.main').html('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
				setTimeout("$('.main').load(urlReload, function () { loadAjaxForm(); });",500);
			})
		});

		return false;
	});

    // Ocultar todos los li
    $('.tree li').hide();
    // Mostrar solo los de primer nivel:
    $('.tree li.firstChild').show();

    $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
    
    // Al entrar, si estamos viendo algún elemento en concreto, expandir lo que sea necesario:
    $('.tree ul > li.parent_li').each(function() {
    	if ($(this).hasClass('expanded') == true) {
	        var children = $(this).find(' > ul > li');
            children.show();
            $(this).children('div.item').attr('title', 'Collapse this branch').find('.glyphicon-folder-close').addClass('glyphicon-folder-open').removeClass('glyphicon-folder-close');
    	}
    });

    // Al hacer click en un elemento, expandir su contenido:
    $('.tree li.parent_li > div.item').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find('.glyphicon-folder-open').addClass('glyphicon-folder-close').removeClass('glyphicon-folder-open');
        } else {
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find('.glyphicon-folder-close').addClass('glyphicon-folder-open').removeClass('glyphicon-folder-close');
        }
        e.stopPropagation();
    });
}

function getUrlParameter(sParam, fullURL) {
	if (fullURL == '') {
		fullURL = window.location.search.substring(1);
	}
	if ( (fullURL.indexOf('?') != -1)&&(fullURL.split('?').length > 0) ) {
		sPageURL = fullURL.split('?')[1];
	} else {
		sPageURL = fullURL;
	}
	var sURLVariables = sPageURL.split('&');
	for (var i = 0; i < sURLVariables.length; i++)  {
		var sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] == sParam)  {
			return sParameterName[1];
		}
	}
}       

function loadAjaxForm() {
	loadCharts();

	$('#loading-content').detach();

	$('#archivar-curso').unbind().click(function() {
		$('#modalContent').html('').load('modules-admin/archivarCurso.php?'+$(this).attr('rel'), function() {
			// Cargar funcionalidad ajax para todos los formularios:
			$('form[name="archivarCurso"]').unbind().ajaxForm({
				target: 		'#modalContent .form-error',
				beforeSubmit: 	function(formData, jqForm, options) { 
					$('#modalContent').find('.modal-body').append('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
					$('#modalContent').find('form').hide();
					return true;
				},
				success: 		function(responseText, statusText, xhr, $form)  { 
					if (responseText == '') {
						$('#modalContent').modal('hide');
						url = 'modules-admin/cursos.php?opt=cursos';
						
						console.log(url);
						console.log('modules-admin/menu.php?opt=cursos');
						$('.sidebar').html('').load('modules-admin/menu.php?opt=cursos', function() {
							loadMenu();
						});
						$('.main').html('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
						setTimeout("$('.main').load(url, function () { loadAjaxForm(); });",500);
					} else {
						$('#modalContent').find('.form-error').show();
						$('#loading-content').detach();
						$('#modalContent').find('form').show();
					}
					$('#loading-content').detach();
					$('#modalContent').find('form').show();
				}
			});
			$('#modalContent').modal({
				show: true
			});
		});

		return false;
	});

	if ($('.listaCategorias').length > 0) {
		$('.btn-add-cat').click(function() {
			if ($('input[name="categoria-nueva"]').val() != '') {
				catList = $('input[name="categoria-nueva"]').val();
				catList = catList.replace(/, /g,',');
				catList = catList.split(',');

				$('input[name="categoria-nueva"]').val('');

				for (i=0; i < catList.length; i++) {
					nuevaCat = '<div class="input-group categoria">';
						nuevaCat += '<input type="text" name="categorias[]" class="form-control" value="'+catList[i]+'" />';
						nuevaCat += '<span class="input-group-btn"><button class="btn btn-danger btn-remove-cat" type="button"><span class="glyphicon glyphicon-remove"></span></button></span>';
					nuevaCat += '</div>';

					$('.listaCategorias').append(nuevaCat);
				}
			}
		});

		$('.btn-remove-cat').click(function() {
			$(this).parent().parent().detach();
		});

		$('input[name="categoria-nueva"]').on('keyup keypress', function(e) {
			var code = e.keyCode || e.which;
			if (code == 13) { 
				$('.btn-add-cat').click();
				e.preventDefault();
				return false;
			}
		});
	}

	$('.input-group.input-daterange').datepicker({
		weekStart: 1,
		format: 'yyyy-mm-dd',
		language: 'es'
	});
	
	$('form[name="config"] .add-ub').click(function() {
		newUb = '<div class="row"><div class="col-md-2"></div><div class="col-md-10">';
		newUb = newUb + '<input type="text" class="form-control" name="ubicacion-new[]" id="ubicacion" value="" /></div></div>';
		$('.listaUbicaciones').append(newUb);
	});

	$('form[name="config"] .add-ext').click(function() {
		newUb = '<div class="row"><div class="col-md-2"></div><div class="col-md-10">';
		newUb = newUb + '<input type="text" class="form-control" name="extension-new[]" id="extension" value="" /></div></div>';
		$('.listaExtensiones').append(newUb);
	});

	$('form[name="usuarios"] button[name="list-user"]').click(function() {
		IDusuario = $(this).attr('value');

		if ($('tr.list-user-'+IDusuario).is(':visible')) {
			$('tr.list-user-'+IDusuario).hide();
		} else {
			$('tr.no-mostrar').hide();
			$('tr.list-user-'+IDusuario).show();
		}
	});

	$('form[name="usuarios"] button[name="save-cursos-user"]').click(function() {
		IDusuario = $(this).attr('value');

		$('tr.no-mostrar').each(function() {
			if ($(this).is(':visible') == false) {
				$(this).detach();
			}
		});
	});
	
	if ($('#img').length > 0) {
		rutaImg = $('#img').attr('value');
		previewImg = '';
		if (rutaImg.split('/')[rutaImg.split('/').length-1] != '') {
			previewImg = '<img src="'+rutaImg+'" class="file-preview-image" />';
		}

		$('#img').fileinput({
			initialPreview: previewImg,
			previewFileType: 'image',
			browseLabel: 'Buscar imagen',
			browseIcon: '<i class="glyphicon glyphicon-picture"></i> ',
			showUpload: false,
			showRemove: false
		});
	}

	if ($('#rutaVideo').length > 0) {
		rutaVideo = $('#rutaVideo').attr('value');
		previewVideo = '';

		if (rutaVideo.split('/')[rutaVideo.split('/').length-1] != '') {
			previewVideo = '<video width="213px" height="160px" controls=""><source src="'+rutaVideo+'" type="video/mp4" /><div class="file-preview-other"><i class="glyphicon glyphicon-file"></i></div></video>';
			previewVideo = previewVideo + '<div class="file-thumbnail-footer"><div class="file-caption-name">'+rutaVideo.split('/')[rutaVideo.split('/').length-1]+'</div></div>';
		}

		$('#rutaVideo').fileinput({
			initialPreview: previewVideo,
			previewFileType: 'video',
			browseLabel: 'Buscar v&iacute;deo',
			browseIcon: '<i class="glyphicon glyphicon-facetime-video"></i>  ',
			showUpload: false,
			showRemove: false
		});

		$('#rutaVideo').on('fileloaded', function(event, file, previewId, index, reader) {
			if ( (document.getElementsByName('IDvideo').length > 0)&&(document.getElementsByName('IDvideo')[0] != '') ) {
				$('span.obtenerCaptura').show();
			}
		});
	}

	if ($('#rutaAdjunto').length > 0) {
		rutaAdjunto = $('#rutaAdjunto').attr('value');
		previewAdjunto = '';

		if (rutaAdjunto.split('/')[rutaAdjunto.split('/').length-1] != '') {
			previewAdjunto = '<div class="file-preview-other"><a href="'+rutaAdjunto+'" target="_blank"><i class="glyphicon glyphicon-file"></i> '+rutaAdjunto.split('/')[rutaAdjunto.split('/').length-1]+'</a></div>';
		//	previewAdjunto = '<div class="file-preview-text"><h2><i class="glyphicon glyphicon-file"></i></h2>'+rutaAdjunto.split('/')[rutaAdjunto.split('/').length-1]+'</div>';
		}

		$('#rutaAdjunto').fileinput({
			initialPreview: previewAdjunto,
			browseLabel: 'Buscar archivo',
			browseIcon: '<i class="glyphicon glyphicon-file"></i> ',
			showUpload: false,
			showRemove: false
		});
	}

	encriptarChange = 0;
	$('input[name="_ENCRIPTAR"]').change(function() {
		encriptarChange = 0;
		if ( ($(this).is(':checked'))&&($('input[name="_ENCRIPTARORI"]').val() == '') ) {
			encriptarChange = 1;
		} else if ( (!$(this).is(':checked'))&&($('input[name="_ENCRIPTARORI"]').val() == 'on') ) {
			encriptarChange = 1;
		}
	});

	$('.btn-cancel').click(function() {
		window.location.reload();
	});

	// Cargar funcionalidad ajax para todos los formularios:
	$('form:not(.userSession)').unbind().ajaxForm({
		target: 		'.main',
		beforeSubmit: 	beforeSubmit,
		success: 		submitDone
	});
}

function beforeSubmit(formData, jqForm, options) { 
	var queryString = $.param(formData); 

	console.log('enviando... ('+queryString+')');

	// Si se ha pulsado "eliminar", pedir confirmacion:
	if (queryString.indexOf('formDel') != -1) {
		if (confirm('¿Desea eliminar este elemento?')) {
			$('.main').children('.row').append('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
			$('.main').find('form').hide();

			return true;
		}

	// Si se ha pulsado "autorizar acceso usuario", pedir confirmacion:
	} else if (queryString.indexOf('unblock-access-user') != -1) {
		if (confirm('¿Desea desbloquear el acceso de este usuario a sus cursos?')) {
			$('.main').children('.row').append('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
			$('.main').find('form').hide();

			return true;
		}

	// Si se ha pulsado "desautorizar acceso usuario", pedir confirmacion:
	} else if (queryString.indexOf('block-access-user') != -1) {
		if (confirm('¿Desea bloquear el acceso de este usuario a sus cursos?')) {
			$('.main').children('.row').append('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
			$('.main').find('form').hide();

			return true;
		}

	// Si se ha pulsado "desautorizar acceso usuario", pedir confirmacion:
	} else if (queryString.indexOf('del-user') != -1) {
		if (confirm('¿Desea eliminar este usuario?')) {
			$('.main').children('.row').append('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
			$('.main').find('form').hide();

			return true;
		}

	// Si se ha cambiado el valor de _ENCRIPTAR, mostrar alerta:
	} else if (encriptarChange == 1) {
		if (confirm('¿Seguro que desea cambiar la configuracion de encriptacion de identificadores?')) {
			$('.main').children('.row').append('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
			$('.main').find('form').hide();

			return true;
		}

	} else {
		$('.main').children('.row').append('<button id="loading-content" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Cargando...</button>');
		$('.main').find('form').hide();

		return true;
	}
	
	return false;
} 
 
function submitDone(responseText, statusText, xhr, $form)  { 
	console.log('done!!!');

	$('#loading-content').detach();
	$('.main').find('form').show();
	
	opt = document.getElementsByName('form')[0].value;
	IDcurso = '';
	if (document.getElementsByName('IDcurso').length > 0) {
		IDcurso = document.getElementsByName('IDcurso')[0].value;
	}
	IDtema = '';
	if (document.getElementsByName('IDtema').length > 0) {
		IDtema = document.getElementsByName('IDtema')[0].value;
	}
	IDvideo = '';
	if (document.getElementsByName('IDvideo').length > 0) {
		IDvideo = document.getElementsByName('IDvideo')[0].value;
	}
	IDadjunto = '';
	if (document.getElementsByName('IDadjunto').length > 0) {
		IDadjunto = document.getElementsByName('IDadjunto')[0].value;
	}
	
	// Si ha ido todo bien, recargar el menu:
	if ( (responseText.indexOf('alert-success') != -1)||(responseText.indexOf('alert-danger') != -1) ) {
		console.log('modules-admin/menu.php?opt='+opt+'&IDcurso='+encodeURIComponent(IDcurso)+'&IDtema='+encodeURIComponent(IDtema)+'&IDvideo='+encodeURIComponent(IDvideo)+'&IDadjunto='+IDadjunto);
		$('.sidebar').html('').load('modules-admin/menu.php?opt='+opt+'&IDcurso='+encodeURIComponent(IDcurso)+'&IDtema='+encodeURIComponent(IDtema)+'&IDvideo='+encodeURIComponent(IDvideo)+'&IDadjunto='+IDadjunto, function() {
			loadMenu();
		});
	}

	loadAjaxForm();
} 

function beforeLoginSubmit(formData, jqForm, options) { 
	var queryString = $.param(formData); 

	console.log('enviando... ('+queryString+')');

	if (queryString.indexOf('logout') != -1) {
		location.reload();
	}

	return true;
} 
 
function submitLoginDone(responseText, statusText, xhr, $form)  { 
	console.log('done!!!');
	
	if (responseText != '') {
		$('.form-error').show();
		loadAjaxForm();
	} else {
		location.reload();
	}
} 

function loadLoginForm() {
	$('form[name="userSession"]').ajaxForm({
		target: 		'.form-error',
		beforeSubmit: 	beforeLoginSubmit,
		success: 		submitLoginDone
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