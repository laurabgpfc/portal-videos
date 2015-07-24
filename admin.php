<?php
include_once('config.php');
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Administración</title>

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />
		<link rel="stylesheet" type="text/css" href="js/bootstrap-datepicker-1.4.0-dist/css/bootstrap-datepicker.min.css">
		<link rel="stylesheet" type="text/css" href="js/fileinput/fileinput.css" />

		<!-- Custom styles for this template -->
		<link rel="stylesheet" type="text/css" href="css/dashboard.css">

		<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
		<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
		<!--script src="../../assets/js/ie-emulation-modes-warning.js"></script-->

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="/portal-videos/">Portal v&iacute;deos</a>
					</div>
					<div class="navbar-collapse collapse">
						<form name="userSession" class="navbar-form navbar-right userSession" role="form" method="POST" action="forms/login.php">
						<?php if(!isset($_COOKIE['MoodleUserSession'])) { ?>
							<div class="form-error btn btn-danger"></div>
							<div class="form-group">
								<input name="userName" type="text" placeholder="Usuario" class="form-control" />
							</div>
							<div class="form-group">
								<input name="userPass" type="password" placeholder="Contraseña" class="form-control" />
							</div>
							<button type="submit" name="login" class="btn btn-success">Acceder</button>
						<?php } else { ?>
							<div class="form-group">
								<span class="saludo">Bienvenido, <?php echo decrypt($_COOKIE['MoodleUserSession'],1)['fullname']; ?></span>
							</div>
							<button type="submit" value="aa" name="logout" class="btn btn-danger">Cerrar sesi&oacute;n</button>
						<?php } ?>
						</form>
					</div><!--/.navbar-collapse -->
				</div>
			</nav>
		</header>
		<?php
			if ( (!isset($_COOKIE['MoodleUserSession'])) || ( (isset($_COOKIE['MoodleUserSession']))&&(decrypt($_COOKIE['MoodleUserSession'],1)['esAdmin'] == 0) ) ) {
				$OUT = '<div class="container">';
					$OUT .= '<div class="row">';
						$OUT .= '<div class="col-md-12 margin-bottom">';
							$OUT .= '<h1>Acceso restringido</h1>';
							$OUT .= '<p>Esta intentando acceder a una pagina protegida. Debe ser usuario administrador para poder acceder.</p>';
						$OUT .= '</div>';
					$OUT .= '</div>';
				$OUT .= '</div>';
				echo $OUT;
			} else {
				require_once(_DOCUMENTROOT.'modules-admin/content.php');
			}
			
			if (isset($_COOKIE['MoodleUserFaltaCorreo'])) {
				include_once(_DOCUMENTROOT.'modules/pedirEmail.php');
			}
		?>
		<div class="modal fade" id="modalContent" tabindex="-1" role="dialog" ></div>
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/jquery.form.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.min.js"></script>
		<script type="text/javascript" src="js/bootstrap-datepicker-1.4.0-dist/locales/bootstrap-datepicker.es.min.js"></script>
		<script type="text/javascript" src="js/highcharts-4.1.6/highcharts.js"></script>
		<script type="text/javascript" src="js/highcharts-4.1.6/modules/drilldown.js"></script>
		<script type="text/javascript" src="js/fileinput/fileinput.min.js"></script>
		<script type="text/javascript" src="js/jquery-sortable.js"></script>
		<!--script type="text/javascript" src="js/highcharts-4.1.6/modules/exporting.js"></script-->
		<script type="text/javascript" src="js/charts.js"></script>
		<script type="text/javascript" src="js/admin.js"></script>
	</body>
</html>