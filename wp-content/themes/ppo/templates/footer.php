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

<script>
	new mlPushMenu(document.getElementById('mp-menu'), document.getElementById('trigger'));
	jQuery(document).ready(function($) {
		$(window).scroll(function(e) {
			menuBottom = ($("header .nav-container").height() - $(document).scrollTop());
			$('header .menu-container').css("top",menuBottom+"px");
		});
	});
</script>

<?php wp_footer(); ?>
