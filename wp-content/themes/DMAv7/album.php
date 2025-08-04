<?php 
// Album data
$artist_list = get_the_term_list( $post->ID, 'artiest', '', ', ' ); 
$jaren_list = get_the_term_list( $post->ID, 'jaren', '', ', ' ); 
$labels_list = get_the_term_list( $post->ID, 'labels', '', ', ' ); 

if (is_single()) {
	$genre_list = get_the_term_list( $post->ID, 'genre', '', '' ); 
} else {
	$genre_list = get_the_term_list( $post->ID, 'genre', '', ' ' ); 
}

//$artist_list = strip_tags( $artist_list );
//$jaren_list = strip_tags( $jaren_list );
//$labels_list = strip_tags( $labels_list );
?>

<album id="<?php the_ID(); ?>" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
<album-content>
<img-container>
<?php if (get_field('spotify_uri')) { ?>
	<media-btn>
		<frame-base>https://embed.spotify.com/?uri=</frame-base>
		<frame-src><?php echo the_field('spotify_uri'); ?></frame-src>
		<iframe src="" width="100%" height="80" frameborder="0" allowtransparency="true"></iframe>
	</media-btn>
	<media-btn-bg>
		<i class="fas fa-spinner loader"></i>
	</media-btn-bg>
<?php } ?>
<img src="<?php bloginfo('template_directory'); ?>/assets/img/load.png" data-src="<?php the_post_thumbnail_url( 'full' ); ?>" class="coverimg" height="300" width="300"/>
</img-container>

<album-meta>
	<album-title><?php the_title(); ?></album-title>
	<album-artist><?php echo $artist_list ?></album-artist>
	<!--<album-rating>
		<?php if( get_field('rating') == 'none' ) { echo '<i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';} ?>
		<?php if( get_field('rating') == 'one' ) { echo '<i class="fa fa-star" aria-hidden="true"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';} ?>
		<?php if( get_field('rating') == 'two' ) { echo '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';} ?>
		<?php if( get_field('rating') == 'three' ) { echo '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';} ?>
		<?php if( get_field('rating') == 'four' ) { echo '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="far fa-star"></i><i class="far fa-star"></i>';} ?>
		<?php if( get_field('rating') == 'five' ) { echo '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="far fa-star"></i>';} ?>
		<?php if( get_field('rating') == 'six' ) { echo '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>';} ?>
		<?php if( get_field('rating') == 'gold' ) { echo '<span class="gold"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></span>';} ?>
	</album-rating>-->
	<album-notes><?php the_field('notes'); ?></album-notes>
</album-meta>
<album-tags>
	<a class="tagBtn" tag="genres" albumId="<?php the_ID(); ?>"><i class="fas fa-tags"></i></a>
	<a class="tagBtn" tag="release" albumId="<?php the_ID(); ?>"><i class="fas fa-dot-circle"></i></a>
	<a class="tagBtn" tag="spotify" albumId="<?php the_ID(); ?>"><i class="fab fa-spotify"></i></a>
</album-tags>
	<tag id="tag-<?php the_ID(); ?>-genres" class="genres">
		<?php echo $genre_list ?>
	</tag>
	<tag id="tag-<?php the_ID(); ?>-release" class="release">
		<ul>
			<li><label>Label</label> <?php echo $labels_list ?></li>
			<li><label>Year</label> <?php echo $jaren_list ?></li>
		</ul>
	</tag>
</album-content>
</album>