<nav id="sort-filter">
	<?php
// Set up filter/sort controls array
	$doc_filters = array(
		'annual-report' => array(
			'filters' => array(),
			'sort' => array( 'date', 'size' ),
			'default' => 'date'
		)
	);

// Get document type slug
	$value = get_query_var( $wp_query->query_vars['taxonomy'] );
	$doc_type_object = get_term_by( 'slug', $value, $wp_query->query_vars['taxonomy'] );
	$doc_type = $doc_type_object->slug;

// Setup filter and sort arrays
	$current_filters = $doc_filters[$doc_type]['filters'];
	$current_sorts = $doc_filters[$doc_type]['sort'];

// Output filter controls
	echo "<div class='filters'>";
	foreach ( $current_filters as $filter ) {
		echo ucfirst( $filter );
	}
	echo "</div>";

// Outout sort controls
	echo "<div class='sorts'>";
	foreach ( $current_sorts as $sort ) {
		$sort_text = ucfirst( $sort );
		echo "<div class='sort-control " . ($sort == $doc_filters[$doc_type]['default'] ? "asc" : "off") . "' data-sort-field='$sort'>$sort_text</div>";
	}
	echo "</div>";
	?>
</nav>

<div class="tile-container">

	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'templates/content-tile', get_post_format() ); ?>
	<?php endwhile; ?>

</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {
		var $container = $(".tile-container");
		// Activate Isotop
		$container.isotope({
			itemSelector: 'article',
			getSortData: {
				date: '[data-doc-date]',
				size: '[data-size] parseInt'
			}
		});

		// Setup sort filters
		$('#sort-filter').on('click', '.sort-control', function() {
			var sortByValue = $(this).attr('data-sort-field');
			var sortAsc;
			if ($(this).hasClass("asc")) {
				$("#sort-filter .sort-control").removeClass("asc").removeClass("desc");
				$(this).addClass("desc");
				sortAsc = false;
			} else if ($(this).hasClass("desc")) {
				$("#sort-filter .sort-control").removeClass("asc").removeClass("desc");
				$(this).addClass("asc");
				sortAsc = true;
			} else {
				$("#sort-filter .sort-control").removeClass("asc").removeClass("desc");
				$(this).removeClass("off");
				$(this).addClass("asc");
				sortAsc = true;
			}
			$container.isotope({
				sortBy: sortByValue,
				sortAscending: sortAsc
			});
		});

		// Fix scroll position of sort-filter
		navBottom = $(".nav-container").position().top + $(".nav-container").outerHeight(true);
		$(window).scroll(function() {
			if ($(window).width() < 768) {
				sortTop = "20px";
				sortReset = "-80px";
				scrollStart = 130;
			} else {
				sortTop = (navBottom + 20) + "px";
				sortReset = 0;
				scrollStart = 60;
			}
			if ($(window).scrollTop() - scrollStart > navBottom) {
				$("#sort-filter").css("top", sortTop).css("position", "fixed").css("margin", "-20px 0");
			} else {
				$("#sort-filter").css("top", sortReset).css("position", "absolute").css("margin", "-20px -15px");
			}
		});
	});

</script>