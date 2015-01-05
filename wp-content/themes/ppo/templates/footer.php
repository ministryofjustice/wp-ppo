<footer class="content-info container" role="contentinfo">
	<div class="row">

		<div class="footer-search"><?php get_search_form( true ); ?></div>
	
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

<script>
	new mlPushMenu(document.getElementById('mp-menu'), document.getElementById('trigger'));
</script>

<?php wp_footer(); ?>
