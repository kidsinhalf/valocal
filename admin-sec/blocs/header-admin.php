<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="fr" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="fr" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="fr" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if gt IE 7]> <html class="no-js ie oldie" lang="fr" prefix="og: http://ogp.me/ns#"> <![endif]-->
<html class="no-js" lang="fr" prefix="og: http://ogp.me/ns#"> <!--<![endif]-->
<?php 
///home/clients/fdd1a6019f1e8376cbae15369dc54de1/web/applis
$url_base='http://www.valocal.fr/applis/';
if (file_exists('bapt.txt')) $url_base='http://localhost/valocal/valocal/';
?>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Valocable Admin</title>
	<!-- CSS Framework et personnalisé-->
    <link rel="stylesheet" href="<?php echo $url_base;?>css/foundation.css" />
	
	<script src="<?php echo $url_base;?>/js/vendor/modernizr.js"></script>
	
	<script src="../js/vendor/jquery.js"></script>
    <script src="../js/foundation.min.js"></script>
	<!-- CSS Tables Datatable MIT-->
	<link rel="stylesheet" type="text/css" href="<?php echo $url_base;?>js/datatables/media/css/dataTables.foundation.min.css">
	<script type="text/javascript" charset="utf8" src="<?php echo $url_base;?>js/datatables/media/js/jquery.dataTables.min.js"></script>
	
	<!-- Date Picker -->
	<link rel="stylesheet" type="text/css" href="<?php echo $url_base;?>css/foundation-datepicker.css">
	<script src="<?php echo $url_base;?>/js/foundation-datepicker.js"></script>
	<script src="<?php echo $url_base;?>/js/locales/foundation-datepicker.fr.js"></script>

	<link rel="stylesheet" href="<?php echo $url_base;?>css/app.css">
	</head>
<body>