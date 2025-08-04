<?php 
	get_header();

	echo '<div id="container"><div class="pure-g albumdeck">';
 
 	// Get the current slug, whatever it might be
	$slug = $wp_query->get_queried_object()->slug;
	$name = $wp_query->get_queried_object()->name;
	$desc = term_description();
		
	/* For tags */	  $query_tag     = array( 'post_type' => 'dma_alba', 'tag' => $slug, 'posts_per_page' => 9999999);
	/* For genres */  $query_genres  = array( 'post_type' => 'dma_alba', 'genre' => $slug, 'posts_per_page' => 9999999);
	/* For artists */ $query_artists = array( 'post_type' => 'dma_alba', 'artiest' => $slug, 'posts_per_page' => 9999999);
	/* For jaren */   $query_jaren   = array( 'post_type' => 'dma_alba', 'jaren' => $slug, 'posts_per_page' => 9999999);
	/* For labels */  $query_label   = array( 'post_type' => 'dma_alba', 'labels' => $slug, 'posts_per_page' => 9999999);
	/* For else */    $query_dma	 = array( 'post_type' => 'dma_alba', 'paged' => $paged, 'posts_per_page' => 32);
	
	// create a new instance of WP_Query
	if ( is_tag() ) {
		$loop = new WP_Query( $query_tag );
		$type = 'Tag';
	} elseif ( is_tax('artiest') ) {
		$loop = new WP_Query( $query_artists );
		$type = 'Artist';
	} elseif ( is_tax('genre') ) {
		$loop = new WP_Query( $query_genres );
		$type = 'Genre';
	} elseif ( is_tax('labels') ) {
		$loop = new WP_Query( $query_label );
		$type = 'Label';
	} elseif ( is_tax('jaren') ) {
		$loop = new WP_Query( $query_jaren );
		$type = 'Year';
	} else {
		$loop = new WP_Query( $query_dma );
		$type = '';
	}

	echo '
		<div class="pure-u-1 pure-u-md-1-2" id="intro"> 
			<a href="/" class="tohome"><i class="fas fa-angle-left"></i></a>
			<div>
			<span class="type">'.$type.'</span>
			<h1>'.$name.'</h1>
			'.$desc.'
			</div>
		</div>
	';

		    if ( $loop->have_posts() ) { while ( $loop->have_posts() ) {
	            $loop->the_post();
				get_template_part( 'album' );
			} }
		    wp_reset_postdata();

	echo '</div></div>';

	get_footer();