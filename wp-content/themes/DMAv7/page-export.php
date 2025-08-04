<html>
<head>
    <style type="text/css">
    body,html {width:15000px}
    body { background:#161715; color:rgba(255,255,255,0.6); padding:20px; font-family:mono; font-size:10px;}
    </style>
</head>
<body>
<?php
// GENRES
// $terms = get_terms('genre');
// if ( !empty( $terms ) && !is_wp_error( $terms ) ){
//     $i = 0;
//     foreach ( $terms as $term ) {
//       //echo $term->name . "<br/>";
//       echo '{
//         "_createdAt":"2018-10-23T21:12:00Z",
//         "_id":"genre-'.$term->slug.'",
//         "_rev":"genre-rev-'.$term->slug.'-'.$i.'",
//         "_type":"genre",
//         "_updatedAt":"2018-10-23T00:00:00Z",
//         "genrename":"'.$term->name.'",
//         "name":"'.$term->name.'",
//         "slug":{"_type":"slug","current":"'.$term->slug.'"},
//         "title":"'.$term->name.'"
//     }<br/>';
//     $i++;
//     }
// }
?>
<?php
// LABELS
// $terms = get_terms('labels');
// if ( !empty( $terms ) && !is_wp_error( $terms ) ){
//     $i = 0;
//     foreach ( $terms as $term ) {
//       //echo $term->name . "<br/>";
//       echo '{
//         "_createdAt":"2018-11-09T19:05:00Z",
//         "_id":"label-'.$term->slug.'",
//         "_rev":"label-rev-'.$term->slug.'-'.$i.'",
//         "_type":"label",
//         "_updatedAt":"2018-11-09T19:05:00Z",
//         "name":"'.$term->name.'",
//         "slug":{"_type":"slug","current":"'.$term->slug.'"}
//     }<br/>';
//     $i++;
//     }
// }
?>
<?php
// ARTISTS
// $terms = get_terms('artiest');
// if ( !empty( $terms ) && !is_wp_error( $terms ) ){
//     $i = 0;
//     foreach ( $terms as $term ) {
//       //echo $term->name . "<br/>";
//       echo '{
//         "_createdAt":"2018-11-09T19:20:00Z",
//         "_id":"artist-'.$term->slug.'",
//         "_rev":"artist-rev-'.$term->slug.'-'.$i.'",
//         "_type":"artist",
//         "_updatedAt":"2018-11-09T19:20:00Z",
//         "name":"'.$term->name.'",
//         "slug":{"_type":"slug","current":"'.$term->slug.'"}
//     }<br/>';
//     $i++;
//     }
// }
?>

<?php 
    // GET ALL ALBUMS

    // Things replaced after export:
    // – (dash in filename ZealArdor)
    // % (replaced with _)
    $the_query = new WP_Query( array('post_type' => 'dma_alba','posts_per_page' => -1)); 
    if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); 

    // DO ALL THE WORKS
    $i = 0;
    // Score
    $var_score = 0;
    if( get_field('rating') == 'none' ) { $var_score = '0';}
    if( get_field('rating') == 'one' ) { $var_score = '1';}
    if( get_field('rating') == 'two' ) { $var_score = '2';}
    if( get_field('rating') == 'three' ) { $var_score = '3';}
    if( get_field('rating') == 'four' ) { $var_score = '4';}
    if( get_field('rating') == 'five' ) { $var_score = '5';}
    if( get_field('rating') == 'six' ) { $var_score = '6';}
    if( get_field('rating') == 'gold' ) { $var_score = '10';}

    // Artists
    $artist_list = get_the_terms( $post->ID, 'artiest', '', ', ' ); 
    $keyi=0;

    // The content
    $notes_content = get_field('notes', $post->ID, false);
    $notes_content = str_replace('"', "'", $notes_content);

    // Err else
    $years_list = get_the_terms( $post->ID, 'jaren', '', ', ' ); 
    $labels_list = get_the_terms( $post->ID, 'labels', '', ', ' ); 
    $genre_list = get_the_terms( $post->ID, 'genre', '', ' ' ); 

    $preview = false;
?>
<?php if($preview) { echo the_title() . '<br><pre>'; } ?>
<?php //if(count($labels_list) > 1) { ?>
{
    "_createdAt":"<?php echo get_post_time('c', $post->ID); ?>",
    "_id":"<?php echo $post->post_name.'-'.$post->ID; ?>",
    "_rev":"<?php echo 'album-rev-' . $post->post_name .'-'.$i; ?>",
    "_type":"album",
    "_updatedAt":"<?php echo get_post_time('c', $post->ID); ?>",
    "albumart":{"_sanityAsset":"image@<?php the_post_thumbnail_url('full');  ?>","_type":"image"},
    "artist":[<?php
            $artistcount=0; $artistlen=count($artist_list); 
            $multiple = ($artistlen > 1 ? true : false);
            foreach ($artist_list as $artist) {
                echo '{"_key":"artistkey-'.$keyi.'-'.$artist->slug.'","_ref":"artist-'.$artist->slug.'","_type":"reference"}';
                if ($multiple && $artistcount != $artistlen - 1) { echo ',';}
                $keyi++; $artistcount++;
            }
        ?>],
    "genre":[<?php
            $genrecount=0; 
            $genrelen=count($genre_list);
            $multiple = ($genrelen > 1 ? true : false);
            if (count($genre_list) >1 ) {
                
            }
            foreach ($genre_list as $genre) {
                echo '{"_key":"genrekey-'.$keyi.'-'.$genre->slug.'","_ref":"genre-'.$genre->slug.'","_type":"reference"}';
                if ($multiple && $genrecount != $genrelen - 1) { echo ',';}
                $keyi++; $genrecount++;
            }
        ?>],
    <?php if($labels_list) { ?>"label":<?php
            $labelcount=0; $labellen=count($labels_list); 
            $multiple = ($labellen > 1 ? true : false);
            $first = true;
            foreach ($labels_list as $label) {
                if ($first) {
                echo '{"_key":"labelkey-'.$keyi.'-'.$label->slug.'","_ref":"label-'.$label->slug.'","_type":"reference"}';
                //if ($multiple && $labelcount != $labellen - 1) { echo ',';}
                $keyi++; $labelcount++;
                $first=false;
                }
            }
        ?>,<?php } ?>
    "overview":[
        {
        "_key":"<?php echo 'overviewkey-'.$post->post_name.'-'.keyi; ?>",
        "_type":"block","children":[
            {
                "_key":"<?php echo 'overviewkey-'.$post->post_name.'-'.keyi; ?>0",
                "_type":"span",
                "marks":[],
                "text":"<?php echo $notes_content; ?>"
            }
        ],
        "markDefs":[],
        "style":"normal"
        }
    ],
    "releaseDate":"<?php
            foreach ($years_list as $year) {
                echo $year->slug;
            }
            ?>-01-01T00:00:00.000Z",
    "score": <?php echo $var_score; ?>,
    "slug":{"_type":"slug","current":"<?php echo $post->post_name ; ?>"},
    "title":"<?php echo $post->post_title ; ?>"
}<br/>
<?php
    $i++;
    if($preview) { echo '</pre>'; } 
?>
<?php //} //iflabellist?>


<?php endwhile; ?>
<?php wp_reset_postdata(); ?>
<?php endif; ?>
</body>
</html>