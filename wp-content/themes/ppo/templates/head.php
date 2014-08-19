<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<?php wp_head(); ?>

		<!-- Google fonts -->
		<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Gentium+Book+Basic' rel='stylesheet' type='text/css'>

		<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo( 'name' ); ?> Feed" href="<?php echo esc_url( get_feed_link() ); ?>">

		<![if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/assets/js/respond.js"></script>
		<![endif]-->
	</head>
