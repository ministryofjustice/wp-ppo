<?php get_template_part( 'templates/head' ); ?>

<nav id="sort-filter">
<?php
// Set up filter/sort controls array
$doc_filters = array(
	'annual-report' => array(
		'filters' => array(),
		'sort' => array( 'date' )
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
	echo "<div class='sort-control off' data-sort-field='$sort'>$sort_text</div>";
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
		$container.isotope({
			itemSelector: 'article'
		});
	});

</script>