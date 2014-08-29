<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<?php wp_head(); ?>

		<!-- Google fonts -->
		<link href='<?php echo get_template_directory_uri(); ?>/assets/css/Roboto.css' rel='stylesheet' type='text/css'>
		<link href='<?php echo get_template_directory_uri(); ?>/assets/css/GentiumBookBasic.css' rel='stylesheet' type='text/css'>

		<!--<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo( 'name' ); ?> Feed" href="<?php echo esc_url( get_feed_link() ); ?>">-->

		<!--[if IE 7]>
			<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/font-awesome-ie7.min.css">
		<![endif]-->

		<!--[if IE 8]>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/EventHelpers.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/cssQuery-p.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/sylvester.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/cssSandpaper.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/IE9.js"></script>
		<![endif]-->

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/respond.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/json2.js"></script>
			<script src="<?php echo get_template_directory_uri(); ?>/assets/js/html5shiv.min.js"></script>
		<![endif]-->
	</head>
