<footer class="content-info container" role="contentinfo">
	<div class="row">
		<div>
			<?php dynamic_sidebar( 'sidebar-footer' ); ?>
			<?php
			if ( has_nav_menu( 'footer-navigation' ) ) :
				wp_nav_menu( array( 'theme_location' => 'footer-navigation', 'menu_class' => 'nav nav-pills' ) );
			endif;
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
