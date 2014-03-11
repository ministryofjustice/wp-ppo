<?php

/**
 * Register sidebars and widgets
 */
function roots_widgets_init() {
	// Sidebars
	register_sidebar( array(
		'name' => __( 'Primary', 'roots' ),
		'id' => 'sidebar-primary',
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer', 'roots' ),
		'id' => 'sidebar-footer',
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );

	// Widgets
	register_widget( 'Roots_Vcard_Widget' );
	register_widget( 'PPO_Related_Docs_Widget' );
	register_widget( 'PPO_Quick_Links_Widget' );
	register_widget( 'PPO_See_Also_Widget' );
	register_widget( 'PPO_Contact_Widget' );
}

add_action( 'widgets_init', 'roots_widgets_init' );

/**
 * Example vCard widget
 */
class Roots_Vcard_Widget extends WP_Widget {

	private $fields = array(
		'title' => 'Title (optional)',
		'street_address' => 'Street Address',
		'locality' => 'City/Locality',
		'region' => 'State/Region',
		'postal_code' => 'Zipcode/Postal Code',
		'tel' => 'Telephone',
		'email' => 'Email'
	);

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_roots_vcard', 'description' => __( 'Use this widget to add a vCard', 'roots' ) );

		$this->WP_Widget( 'widget_roots_vcard', __( 'Roots: vCard', 'roots' ), $widget_ops );
		$this->alt_option_name = 'widget_roots_vcard';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_roots_vcard', 'widget' );

		if ( !is_array( $cache ) ) {
			$cache = array();
		}

		if ( !isset( $args['widget_id'] ) ) {
			$args['widget_id'] = null;
		}

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'vCard', 'roots' ) : $instance['title'], $instance, $this->id_base );

		foreach ( $this->fields as $name => $label ) {
			if ( !isset( $instance[$name] ) ) {
				$instance[$name] = '';
			}
		}

		echo $before_widget;

		if ( $title ) {
			echo $before_title, $title, $after_title;
		}
		?>
		<p class="vcard">
			<a class="fn org url" href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a><br>
			<span class="adr">
				<span class="street-address"><?php echo $instance['street_address']; ?></span><br>
				<span class="locality"><?php echo $instance['locality']; ?></span>,
				<span class="region"><?php echo $instance['region']; ?></span>
				<span class="postal-code"><?php echo $instance['postal_code']; ?></span><br>
			</span>
			<span class="tel"><span class="value"><?php echo $instance['tel']; ?></span></span><br>
			<a class="email" href="mailto:<?php echo $instance['email']; ?>"><?php echo $instance['email']; ?></a>
		</p>
		<?php
		echo $after_widget;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_roots_vcard', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance = array_map( 'strip_tags', $new_instance );

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_roots_vcard'] ) ) {
			delete_option( 'widget_roots_vcard' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_roots_vcard', 'widget' );
	}

	function form( $instance ) {
		foreach ( $this->fields as $name => $label ) {
			${$name} = isset( $instance[$name] ) ? esc_attr( $instance[$name] ) : '';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php _e( "{$label}:", 'roots' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" type="text" value="<?php echo ${$name}; ?>">
			</p>
			<?php
		}
	}

}

class PPO_Related_Docs_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_ppo_related_docs', 'description' => __( 'This widget will show links to docs related to the current page', 'roots' ) );

		$this->WP_Widget( 'widget_ppo_related_docs', __( 'PPO Related Docs', 'roots' ), $widget_ops );
		$this->alt_option_name = 'widget_ppo_related_docs';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_ppo_related_docs', 'widget' );

		if ( !is_array( $cache ) ) {
			$cache = array();
		}

		if ( !isset( $args['widget_id'] ) ) {
			$args['widget_id'] = null;
		}

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$new_entries = get_post_meta( get_the_ID(), 'sidebar-related-docs' );

		if ( count($new_entries) ) {

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Related Documents', 'roots' ) : $instance['title'], $instance, $this->id_base );

			echo $before_widget;

			if ( $title ) {
				echo $before_title, $title, $after_title;
			}

			foreach ( $new_entries[0] as $entry ) {
				$file_url = wp_get_attachment_url( $entry['link'] );
				?>		
				<li>
					<h5><a href="<?php echo $file_url; ?>"><?php echo $entry[title] . " " . get_filesize( $file_url, true ); ?></a></h5>
					<?php echo $entry[content]; ?>
				</li>
				<?php
			}

			echo $after_widget;
		}

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_ppo_related_docs', $cache, 'widget' );
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_ppo_related_docs', 'widget' );
	}

}

class PPO_Quick_Links_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_ppo_quick_links', 'description' => __( 'This widget will show external links related to the current page', 'roots' ) );

		$this->WP_Widget( 'widget_ppo_quick_links', __( 'PPO Quick Links', 'roots' ), $widget_ops );
		$this->alt_option_name = 'widget_ppo_quick_links';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_ppo_quick_links', 'widget' );

		if ( !is_array( $cache ) ) {
			$cache = array();
		}

		if ( !isset( $args['widget_id'] ) ) {
			$args['widget_id'] = null;
		}

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$new_entries = get_post_meta( get_the_ID(), 'sidebar-quick-links' );

		if ( count( $new_entries ) ) {

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Quick Links', 'roots' ) : $instance['title'], $instance, $this->id_base );

			echo $before_widget;

			if ( $title ) {
				echo $before_title, $title, $after_title;
			}

			foreach ( $new_entries[0] as $entry ) {
				?>		
				<li>
					<h5><a href="<?php echo get_permalink( $entry[link] ); ?>"><?php echo $entry[title]; ?></a></h5>
					<?php echo $entry[content]; ?>
				</li>
				<?php
			}

			echo $after_widget;
		}

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_ppo_quick_links', $cache, 'widget' );
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_ppo_quick_links', 'widget' );
	}

}

class PPO_See_Also_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_ppo_see_also', 'description' => __( 'This widget will show other pages of interest related to the current page', 'roots' ) );

		$this->WP_Widget( 'widget_ppo_see_also', __( 'PPO See Also', 'roots' ), $widget_ops );
		$this->alt_option_name = 'widget_ppo_see_also';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_ppo_see_also', 'widget' );

		if ( !is_array( $cache ) ) {
			$cache = array();
		}

		if ( !isset( $args['widget_id'] ) ) {
			$args['widget_id'] = null;
		}

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$new_entries = get_post_meta( get_the_ID(), 'sidebar-see-also' );

		if ( count( $new_entries ) ) {

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'See Also', 'roots' ) : $instance['title'], $instance, $this->id_base );

			echo $before_widget;

			if ( $title ) {
				echo $before_title, $title, $after_title;
			}

			foreach ( $new_entries[0] as $entry ) {
				?>		
				<li>
					<h5><a href="<?php echo get_permalink( $entry[link] ); ?>"><?php echo $entry[title]; ?></a></h5>
					<?php echo $entry[content]; ?>
				</li>
				<?php
			}

			echo $after_widget;
		}

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_ppo_see_also', $cache, 'widget' );
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_ppo_see_also', 'widget' );
	}

}

class PPO_Contact_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_ppo_contact', 'description' => __( 'This widget will show contact details related to the current page', 'roots' ) );

		$this->WP_Widget( 'widget_ppo_contact', __( 'PPO Contact', 'roots' ), $widget_ops );
		$this->alt_option_name = 'widget_ppo_contact';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_ppo_contact', 'widget' );

		if ( !is_array( $cache ) ) {
			$cache = array();
		}

		if ( !isset( $args['widget_id'] ) ) {
			$args['widget_id'] = null;
		}

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$contact_email = get_post_meta( get_the_ID(), 'sidebar-contact' );
		$contact_text = get_post_meta( get_the_ID(), 'sidebar-contact-text' );

		if ( count( $contact_text ) > 0 ) {
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Contact Details', 'roots' ) : $instance['title'], $instance, $this->id_base );

			echo $before_widget;

			if ( $title ) {
				echo $before_title, $title, $after_title;
			}

			if ( strlen( $contact_email[0] ) > 0 ) {
				?>
				<h5><a href="mailto:<?php echo $contact_email[0]; ?>"><?php echo $contact_text[0]; ?></a></h5>
				<?php
			} else {
				?>
				<h5><?php echo $contact_text[0]; ?></h5>
				<?php
			}
			echo $after_widget;
		}

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_ppo_contact', $cache, 'widget' );
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_ppo_contact', 'widget' );
	}

}
