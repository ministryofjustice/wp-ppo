<?php
/*
Template Name: FAQ Page
*/
get_template_part( 'templates/page', 'header' );

the_content();

echo "<section id='faq-accordion'>";

$qandas = get_post_meta( get_the_ID(), 'faq-entries' );

if ( $qandas[0] ) {
	foreach ( $qandas[0] as $qanda ) {
		?>		
		<h5 class="question"><?php echo $qanda[title]; ?></a></h5>
		<section class="answer"><?php echo wpautop( $qanda[answer] ); ?></section>
			<?php
		}
	}

	echo "</section>";
	?>

<script type="text/javascript">
	$(document).ready(function() {
		$("#faq-accordion").accordion({header: "h5", heightStyle: "content", active: "false", collapsible: "true"});
	});
</script>