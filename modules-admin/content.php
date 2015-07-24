<?php

$opt = _ADMINDEF;

if ($_GET['opt'] != '') {
	$opt = $_GET['opt'];
}

include_once(__DIR__.'/../config.php');
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-4 col-md-3 sidebar">
			<?php include_once(_DOCUMENTROOT.'modules-admin/menu.php'); ?>
		</div>
		<div class="col-sm-8 col-sm-offset-4 col-md-9 col-md-offset-3 main">
			<?php
				if (!file_exists(_DOCUMENTROOT.'modules-admin/'.$opt.'.php')) {
					include_once(_DOCUMENTROOT.'modules/error.php');
				} else {
					include_once(_DOCUMENTROOT.'modules-admin/'.$opt.'.php');
				}
			?>
		</div>
	</div>
</div>


