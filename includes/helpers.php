<?php

/**
 * Lists all the terms of a given taxonomy
 *
 * Adds the taxonomy title and a list of the terms associated with that taxonomy
 * used in custom post type templates.
 */
function impress_agents_list_terms($taxonomy) {
	$the_tax_object = get_taxonomy($taxonomy);
	$terms = get_terms($taxonomy);
	$term_list = '';

	$count = count($terms); $i=0;
	if ($count > 0) {
	    foreach ($terms as $term) {
	        $i++;
	    	$term_list .= '<li><a href="' . site_url($taxonomy . '/' . $term->slug) . '" title="' . sprintf(__('View all post filed under %s', 'gbd'), $term->name) . '">' . $term->name . ' (' . $term->count . ')</a></li>';
	    }
		echo '<div class="' . $taxonomy . ' term-list-container">';
		echo '<h3 class="taxonomy-name">' . $the_tax_object->label . '</h3>';
		echo "<ul class=\"term-list\">{$term_list}</ul>";
		echo '</div> <!-- .' . $taxonomy . ' .term-list-container -->';
	}
}


/**
 * Returns true if the queried taxonomy is a taxonomy of the given post type
 */
function impress_agents_is_taxonomy_of($post_type) {
	$taxonomies = get_object_taxonomies($post_type);
	$queried_tax = get_query_var('taxonomy');

	if ( in_array($queried_tax, $taxonomies) ) {
		return true;
	}

	return false;
}

/**
 * Display navigation to next/previous employee when applicable.
 *
 * @since 0.1.0
 */
function impress_agents_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	?>
	<nav class="navigation employee-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Agents navigation', 'impress_agents' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '%link', __( '<span class="meta-nav">Published In</span>%title', 'impress_agents' ) );
			else :
				previous_post_link( '%link', __( '<span class="meta-nav">Previous Agent</span>%title', 'impress_agents' ) );
				next_post_link( '%link', __( '<span class="meta-nav">Next Agent</span>%title', 'impress_agents' ) );
			endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}


/**
 * Display navigation to next/previous set of employees when applicable.
 *
 * @since 0.1.0
 */
function impress_agents_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => __( '&larr; Previous', 'impress_agents' ),
		'next_text' => __( 'Next &rarr;', 'impress_agents' ),
	) );

	if ( $links ) :

	?>
	<nav class="navigation archive-employee-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Agents navigation', 'impress_agents' ); ?></h1>
		<div class="pagination loop-pagination">
			<?php echo $links; ?>
		</div><!-- .pagination -->
	</nav><!-- .navigation -->
	<?php
	endif;
}

/**
 * Return registered image sizes.
 *
 * Return a two-dimensional array of just the additionally registered image sizes, with width, height and crop sub-keys.
 *
 * @since 1.0.1
 *
 * @global array $_wp_additional_image_sizes Additionally registered image sizes.
 *
 * @return array Two-dimensional, with width, height and crop sub-keys.
 */
function impress_agents_get_additional_image_sizes() {

	global $_wp_additional_image_sizes;

	if ( $_wp_additional_image_sizes )
		return $_wp_additional_image_sizes;

	return array();

}