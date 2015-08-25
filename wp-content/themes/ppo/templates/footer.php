<footer class="content-info container" role="contentinfo">
	<div class="row">

		<nav class="footer-social-links">
			<ul>
				<li><a href="#" class="social-icon"><i class="icon-facebook"></i></a></li>
				<li><a href="#" class="social-icon"><i class="icon-twitter"></i></a></li>
				<li><a href="#" class="social-icon"><i class="icon-gplus"></i></a></li>
			</ul>
		</nav>
	
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
