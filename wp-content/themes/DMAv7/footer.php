<div id="menubar">
	<div class="container pure-g">
		<div class="logo pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-lg-1-4 pure-u-xl-1-5">
			<a href="<?php bloginfo('wpurl'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/assets/img/logo.svg" class="logo"></a>
		</div>
		<div id="mobileOpener"><i class="fas fa-caret-up"></i></div>

		<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-lg-1-4 pure-u-xl-1-5">
			<form role="form" class="searchbox">
				<div class="form-group">
					<input class="form-control" id="filter-artists" type="search" placeholder="Search artists..." />
				</div>
				<div id="list-artists" class="list-group">
					<?php 
					$taxonomy = 'artiest';
					$terms = get_terms( $taxonomy, '' );
					if ($terms) { foreach($terms as $term) {
					    echo '' . '<a class="list-group-item" href="' . esc_attr(get_term_link($term, $taxonomy)) . '" title="' . sprintf( __( "View all posts in %s" ), $term->name ) . '" ' . '><span>' . $term->name.'</span><b class="artistcount">' . $term->count . '</b></a>';
					}}  ?>
				</div>
			</form>
		</div>


		<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-lg-1-4 pure-u-xl-1-5">
			<form role="form" class="searchbox">
				<div class="form-group">
					<input class="form-control" id="filter-genres" type="search" placeholder="Search genres..." />
				</div>
				<div id="list-genres" class="list-group">
					<?php 
					$taxonomy = 'genre';
					$terms = get_terms( $taxonomy, '' );
					if ($terms) { foreach($terms as $term) {
					    echo '' . '<a class="list-group-item" href="' . esc_attr(get_term_link($term, $taxonomy)) . '" title="' . sprintf( __( "View all posts in %s" ), $term->name ) . '" ' . '><span>' . $term->name.'</span><b class="artistcount">' . $term->count . '</b></a>';
					}}  ?>
				</div>
			</form>
		</div>

		<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-lg-1-4 pure-u-xl-1-5">
			<form role="form" class="searchbox">
				<div class="form-group">
					<input class="form-control" id="filter-labels" type="search" placeholder="Search labels..." />
				</div>
				<div id="list-labels" class="list-group">
					<?php 
					$taxonomy = 'labels';
					$terms = get_terms( $taxonomy, '' );
					if ($terms) { foreach($terms as $term) {
					    echo '' . '<a class="list-group-item" href="' . esc_attr(get_term_link($term, $taxonomy)) . '" title="' . sprintf( __( "View all posts in %s" ), $term->name ) . '" ' . '><span>' . $term->name.'</span><b class="artistcount">' . $term->count . '</b></a>';
					}}  ?>
				</div>
			</form>
		</div>

		<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-lg-1-4 pure-u-xl-1-5">
			<form role="form" class="searchbox">
				<div class="form-group">
					<input class="form-control" id="filter-jaren" type="search" placeholder="Search years..." />
				</div>
				<div id="list-jaren" class="list-group">
					<?php 
					$taxonomy = 'jaren';
					$terms = get_terms( $taxonomy, '' );
					if ($terms) { foreach($terms as $term) {
					    echo '' . '<a class="list-group-item" href="' . esc_attr(get_term_link($term, $taxonomy)) . '" title="' . sprintf( __( "View all posts in %s" ), $term->name ) . '" ' . '><span>' . $term->name.'</span><b class="artistcount">' . $term->count . '</b></a>';
					}}  ?>
				</div>
			</form>
		</div>


	</div>
</div>
<?php wp_footer(); ?>
</body>
</html>