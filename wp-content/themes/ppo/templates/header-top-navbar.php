<!-- <header class="banner navbar navbar-default navbar-static-top" role="banner">

	<div class="nav-container">
		<a class="brand" href="<?php echo home_url(); ?>/"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/ppo-logo_white.png" alt="<?php bloginfo( 'name' ); ?>"></a>
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
			<button id="trigger" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
	</div>
</header>

<div class="mp-pusher" id="mp-pusher">
	<nav id="mp-menu" class="mp-menu">
		<?php
		if ( has_nav_menu( 'primary_navigation' ) ) :
			wp_nav_menu( array( 'theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-nav', 'depth' => 3, 'walker' => new Mob_Nav_Walker ) );
		endif;
		?>
	</nav>

</div> -->

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

<?php // Social sharing icons ?>
<div class="sharing">
	<?php $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
	<a class="twitter" href="http://twitter.com/share?url=<?= $current_url ?>" target="_blank">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/icons/social/64-twitter.png" title="Twitter" class="share" alt="Tweet about this on Twitter">
	</a>
	<a class="facebook" href="http://www.facebook.com/sharer.php?u=<?= $current_url ?>" target="_blank">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/icons/social/64-facebook.png" title="Facebook" class="share" alt="Share on Facebook">
	</a>
	<a class="google" href="https://plus.google.com/share?url=<?= $current_url ?>" target="_blank">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/icons/social/64-googleplus.png" title="Google+" class="share" alt="Share on Google+">
	</a>
</div>
