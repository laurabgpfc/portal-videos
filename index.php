<?php
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<!--meta http-equiv="X-UA-Compatible" content="IE=Edge" /-->
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="" />
		<meta name="author" content="" />
		<!--link rel="shortcut icon" href="favicon.ico" /-->
		<title>Portal Vídeos</title>
		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />
		<link rel="stylesheet" type="text/css" href="js/flowplayer-5.4.4/skin/minimalist.css" />
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="index.php">Portal Vídeos</a>
					</div>
					<div class="navbar-collapse collapse">
						<?php if ( (isset($_COOKIE['MoodleUserSession']))&&(decrypt($_COOKIE['MoodleUserSession'],1)['esAdmin'] == 1) ) { ?>
						<ul class="nav navbar-nav">
							<li><a href="admin.php">Admin</a></li>
						</ul>
						<?php } ?>
						<form name="userSession" class="navbar-form navbar-right" role="form" method="POST" action="forms/login.php">
							<div class="form-error btn btn-danger"></div>
						<?php if(!isset($_COOKIE['MoodleUserSession'])) { ?>
							<div class="form-group">
								<input name="userName" type="text" placeholder="Usuario" class="form-control" />
							</div>
							<div class="form-group">
								<input name="userPass" type="password" placeholder="Contraseña" class="form-control" />
							</div>
							<button type="submit" name="login" class="btn btn-success">Acceder</button>
						<?php //} else if(isset($_COOKIE['MoodleUserFaltaCorreo'])) { ?>
							<!--div class="form-error form-error-show btn btn-warning">Debe facilitar el correo electr&oacute;nico con el que accede a Moodle</div>
							<div class="form-group">
								<input name="email" type="text" placeholder="Correo electr&oacute;nico" class="form-control" />
							</div>
							<button type="submit" name="asociar-correo" class="btn btn-success">Acceder</button>
							<button type="submit" name="logout" class="btn btn-danger">Cerrar sesi&oacute;n</button-->
						<?php } else { ?>
							<!--div class="form-group"-->
								<span class="saludo">Bienvenido, <?php echo decrypt($_COOKIE['MoodleUserSession'],1)['fullname']; ?></span>
							<!--/div-->
							<button type="submit" value="aa" name="logout" class="btn btn-danger">Cerrar sesi&oacute;n</button>
						<?php } ?>
						</form>
					</div><!--/.navbar-collapse -->
				</div>
			</div>
		</header>
		<div id="main">
		<?php
			require_once(_DOCUMENTROOT.'modules/migas-pan.php');
			require_once(_DOCUMENTROOT.'modules/content.php');

			if (isset($_COOKIE['MoodleUserFaltaCorreo'])) {
				include_once(_DOCUMENTROOT.'modules/pedirEmail.php');
			}
		?>
		</div>
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
		<script type="text/javascript" src="js/jquery.form.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/flowplayer-5.4.4/flowplayer.min.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>