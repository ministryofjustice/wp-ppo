<header class="banner navbar navbar-static-top" role="banner">
	<div class="nav-container">
		<a class="brand" href="<?php echo home_url(); ?>/">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/img/ppo-logo.svg" alt="<?php bloginfo( 'name' ); ?>">
		</a>
		<nav class="collapse navbar-collapse" role="navigation">
			<?php
			if ( has_nav_menu( 'primary_navigation' ) ) :
				wp_nav_menu( array( 'theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-nav', 'depth' => 3 ) );
			endif;
			?>
		</nav>
	</div>
	<div class="container">
		<div class="navbar-header">
			<button id="trigger" type="button" class="navbar-toggle">
				<span class="sr-only">Toggle navigation</span>
				<i class="icon-menu"></i>
			</button>
		</div>
	</div>
</header>
<div class="mp-pusher" id="mp-pusher">
	<nav id="mp-menu" class="mp-menu">
		<?php
		if ( has_nav_menu( 'primary_navigation' ) ) :
			wp_nav_menu( array( 'theme_location' => 'primary_navigation', 'menu_class' => false, 'depth' => 3, 'walker' => new Mob_Nav_Walker ) );
		endif;
		?>
	</nav>

</div>
