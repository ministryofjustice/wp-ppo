<body <?php body_class(); ?>>
	<a class="skip-main" href="#content">Skip to main content</a>
	<div id="shade-overlay" class=""></div>

	<!--[if lt IE 9]>
	  <div class="alert alert-warning">
	<?php _e( 'You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience, such as Firefox or Chrome.', 'roots' ); ?>
	  </div>
	<![endif]-->

	<?php
	do_action( 'get_header' );
	get_header();
	?>

	<div class="wrap" role="document">
		<div class="content row">
			<main id="content" class="main <?php echo roots_main_class(); ?>" role="main" tabindex="-1">
				<?php include roots_template_path(); ?>
			</main><!-- /.main -->
			<?php if ( roots_display_sidebar() ) : ?>
				<aside class="sidebar <?php echo roots_sidebar_class(); ?>" role="complementary">
					<?php include roots_sidebar_path(); ?>
				</aside><!-- /.sidebar -->
			<?php endif; ?>
		</div><!-- /.content -->
	</div><!-- /.wrap -->

	<?php get_footer(); ?>

</body>
</html>
