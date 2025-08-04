<?php get_header();
	echo '<div id="container"><div class="pure-g albumdeck">';
			$paged = ( get_query_var('page') ) ? get_query_var( 'page' ) : 1;

			if ($paged==1){
				// If the first one, take 2 columns for intro

				echo '
					<div class="pure-u-1 pure-u-lg-1-2" id="intro"> 
						<div>
						<h1>Music<br>Tasting<br>Notes</h1>
						<h9>Is what these are.</h9>
						<p>Here I keep notes on \'all\' the albums I listen, as a personal reference to remember me about all the awesome music out there. Most notes will be very positive, because I don\'t tend to listen to music I don\'t like.</p>
						<p>This is also my online playground to play around with data-sets like these, tinckering with the design, database and code of the website, continually improving it. This means this site gets a facelift like every other day. And shit might not work, don\'t tell me, I probably already know.</p>
						<p>The site is called \'De Maandagavond\' (The Mondayevening) and is a remnant of old days of yore, when I would meet up with friends every monday evening to mostly discuss music. That hobby shortly evolved to a local and online radioshow on this domain, and after that crashed and burned, I kept it alive, still dedicated to the music.
						</p>
						</div>
					</div>
				';
			
				$args = array (
					'post_type' => 'dma_alba', 
					'posts_per_page' => 14,
					'paged' => $paged
				);

			} else {

				$args = array (
					'post_type' => 'dma_alba', 
					'posts_per_page' => 16,
					'paged' => $paged
				);

			}

			//$i++;
			$loop = new WP_Query( $args );

		    if ( $loop->have_posts() ) { while ( $loop->have_posts() ) {
	            $loop->the_post();
				get_template_part( 'album' );
			} }
		    wp_reset_postdata();
	echo '</div></div>';

	get_footer();