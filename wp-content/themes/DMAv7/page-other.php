<?php get_header();

	echo '<div id="container"><div class="pure-g albumdeck">';
			$paged = ( get_query_var('page') ) ? get_query_var( 'page' ) : 1;

			if ($paged==1){
				// If the first one, take 2 columns for intro

				echo '
					<div class="pure-u-1-2" id="intro"> 
						<div>
						<h1>Other<br>Tasting<br>Notes</h1>
						<h9>Is what these are.</h9>
						</div>
					</div>
				';
			
				$args = array (
					'post_type' => array('dma_films', 'dma_games'),
					'posts_per_page' => 13,
					'paged' => $paged
				);

			} else {

				$args = array (
					'post_type' => array('dma_films', 'dma_games'),
					'posts_per_page' => 15,
					'paged' => $paged
				);

			}

			$i++;
			$loop = new WP_Query( $args );

		    if ( $loop->have_posts() ) { while ( $loop->have_posts() ) {
	            $loop->the_post();
				get_template_part( 'album' );
			} }
		    wp_reset_postdata();
	echo '</div></div>';

	get_footer();