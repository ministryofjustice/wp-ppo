<div id="loading-spinner"><img src="<?php echo get_template_directory_uri( __FILE__ ) . '/assets/img/ajax-loader.gif'; ?>"></div>

<nav id="sort-filter">
	<?php
// Set up filter/sort controls array
	$doc_filters = array(
		'annual-report' => array(
			'filters' => array(
				// Decade starting from year below (x) up to x + 9 of publish date
				'date' => 'decades' // TODO: Make this automatic based on data
			),
			'sort' => array( 'date', 'size' ),
			'default' => 'date'
		),
		'fii-report' => array(
			'filters' => array(
				'establishment-type' => 'all', // Provides all taxonomy values for the filter
				//'fii-death-date' => 'range',
				'fii-death-type' => 'all'
			),
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

// Output sort controls
	echo "<div class='sorts'>";
	foreach ( $current_sorts as $sort ) {
		$sort_text = str_replace( "-", " ", ucfirst( $sort ) );
		echo "<div class='sort-control " . ($sort == $doc_filters[$doc_type]['default'] ? "desc" : "off") . "' data-sort-field='$sort'>$sort_text</div>";
	}
	echo "</div>";

// Output filter controls
	echo "<div class='filters'>";
	foreach ( $current_filters as $filter => $values ) {
		?>
		<div class="filter-control">
			<div class='filter-header'>
				<?php echo str_replace( array( "-", "Fii" ), array( " ", "FII" ), ucfirst( $filter ) ); ?>
			</div>
			<?php
			echo "<div class='filter-options'>";
			$extras = null;
			$orig_values = $values;
			$values = array();
			if ( !is_array( $orig_values ) ) {
				switch ( $orig_values ) {
					case "all":
						$terms = get_terms( $filter, array( 'hide_empty' => 0 ) );
						foreach ( $terms as $term ) {
							$values[] = array( "label" => $term->name, "option" => $term->term_id );
						}
						break;
					case "decades":
						$extras .= " data-filter-command='decades'";
						$values = array( 1990, 2000, 2010 );
						break;
					case "years":
						$extras .= " data-filter-command='years'";
						for ( $y = 1990; $y <= date( "Y" ); $y++ ) {
							$values[] = "$y";
						}
						break;
					case "range":
						$values = array( "start", "end" );
						// NOTE: http://amsul.ca/pickadate.js/date.htm
						break;
					default:
						break;
				}
			}
			foreach ( $values as $option ) {
				switch ( $orig_values ) {
					case "decades":
						$contents = $option . " - " . ($option + 9);
						break;
					case "years":
						$contents = $option;
						break;
					case "range":
						$contents = "<input type='text' id='$filter-$option'>";
						break;
					default:
						$contents = $option['label'];
						$option = $option['option'];
				}
				echo "<div class='filter-option on' data-filter-type='$filter' data-filter-field='$option'$extras>$contents</div>";
			}
			echo "</div>";
			?>
		</div>
		<?php
	}
	echo "</div>";
	?>
</nav>

<div class="tile-container">

	<?php
	// Modify query to retrive all docs
	global $wp_query;
	$post_per_page = 50;
	$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
	$args = ( array_merge( $wp_query->query, array(
				'posts_per_page' => $post_per_page,
				'paged' => $paged,
				'order' => "DESC",
				'orderby' => 'meta_value',
				'meta_key' => 'document-date'
			) ) );
	echo "<script type='text/javascript'>curPage = $paged;maxPage = " . ceil( $wp_query->found_posts / $post_per_page ) . ";PPOAjax.queryParams='" . http_build_query( $args ) . "'</script>";
	?>

	<div class="live-results"></div>

</div>

<script type="text/javascript">

	$(document).ready(
			function() {
				$contentLoadTriggered = false;
				$(document).scroll(function() {
					if (($(document).scrollTop() + $(window).height()) >= ($(document).height() -
							150) &&
							$contentLoadTriggered == false)
					{
						$contentLoadTriggered = true;
						// Modify queryParams
						PPOAjax.queryParams = PPOAjax.queryParams.replace("paged=" + curPage, "paged=" + (curPage + 1));
						// Increment paged
						curPage++;
						// Load next results
						if (curPage <= maxPage) {
							$("#loading-spinner").show();
							update_tiles(PPOAjax.queryParams);
						}
					}
				});

				// Setup sort controls
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

					/*
					 * queryParameters -> handles the query string parameters
					 * queryString -> the query string without the fist '?' character
					 * re -> the regular expression
					 * m -> holds the string matching the regular expression
					 */
					var queryParameters = {}, queryString = PPOAjax.queryParams,
							re = /([^&=]+)=([^&]*)/g, m;

					// Creates a map with the query string parameters
					while (m = re.exec(queryString)) {
						queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
					}
					
					sortByValue = sortByValue=="date"?"document-date":"";

					queryParameters["order"] = (sortAsc ? "ASC" : "DESC");
					queryParameters["orderby"] = 'meta_value';
					queryParameters["meta_key"] = sortByValue;
					queryParameters["paged"] = 1;
					console.log(queryParameters);

					PPOAjax.queryParams = $.param(queryParameters);
					update_tiles(PPOAjax.queryParams,true);
				});

				// Setup filter controls
				$('#sort-filter').on('click', '.filter-option', function() {
					var filterType = $(this).attr('data-filter-type');
					$(this).toggleClass('on');
//					$container.isotope({
//						filter: function(tile) {
//							showTile = false;
//							filterArray = $('#sort-filter .filter-option.on[data-filter-type="' + filterType + '"]').map(function() {
//								return $(this).attr('data-filter-field');
//							}).get();
//							if (filterType === "date") {
//								var filterValue = $(this).attr('data-' + filterType);
//								showTile = ((jQuery.inArray(filterValue.substring(0, 3) + "0", filterArray)) > -1);
//							} else {
//								var filterValue = $(this).attr('data-' + filterType);
//								showTile = (jQuery.inArray(filterValue, filterArray) > -1);
//							}
//							return showTile;
//						}
//					});
				});

				// Fix scroll position of sort-filter
				//		navBottom = $(".nav-container").position().top + $(".nav-container").outerHeight(true);
				navBottom = 0;
				$(window).on('scroll resize load touchmove', function() {
					if ($(window).width() < 768) {
						sortTop = "90px";
						sortReset = "-80px";
						scrollStart = 120;
					} else {
						sortTop = (navBottom + 90) + "px";
						sortReset = 0;
						scrollStart = 0;
					}
					if ($(window).scrollTop() - scrollStart > navBottom) {
						$("#sort-filter").css("top", sortTop).css("position", "fixed").css("margin", "-20px 0").css("padding","0");
						$(".sorts,.filters").css("margin","20px 35px");
					} else {
						$("#sort-filter").css("top", sortReset).css("position", "absolute").css("margin", "-20px -15px").css("padding","0 15px");
						$(".sorts,.filters").css("margin","20px");
					}
				});

				// Navigation for filters
				$('.filter-header').on('click', function(f) {
					menu = $(this).parent().find(".filter-options");
					if (menu.css('display') == 'none') {
						$('.filters .filter-options').hide();
						menu.show();
					} else {
						menu.hide();
					}
				});
			}
	);

</script>