<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

class Youxi_Entries {

	public function get_related( $limit = -1, $post_id = null ) {

		$post = get_post( $post_id );
		$posts = array();

		if( is_a( $post, 'WP_Post' ) ) {

			$tax_query = array();

			foreach( get_object_taxonomies( $post->post_type ) as $taxonomy ) {

				if( 'post_format' !== $taxonomy && ( $terms = get_the_terms( $post->ID, $taxonomy ) ) ) {

					$tax_query[] = array(
						'taxonomy' => $taxonomy, 
						'field'    => 'id', 
						'terms'    => array_values( wp_list_pluck( $terms, 'term_id' ) )
					);

				}

			}

			if( ! empty( $tax_query ) ) {

				$tax_query['relation'] = 'OR';

				$posts = get_posts(array(
					'post_type'        => $post->post_type, 
					'tax_query'        => $tax_query, 
					'posts_per_page'   => $limit, 
					'post__not_in'     => array( $post->ID ), 
					'orderby'          => 'rand', 
					'no_found_rows'    => true, 
					'cache_results'    => false, 
					'suppress_filters' => false
				));
			}
		}

		return $posts;
	}

	public function get_excerpt( $excerpt_length = '' ) {

		$post = get_post();
		if( empty( $post ) ) {
			return '';
		}

		$text = $post->post_excerpt;
		$raw_excerpt = $text;

		if( '' == $text ) {
			
			$text = get_the_content('');

			$text = strip_shortcodes( $text );

			/** This filter is documented in wp-includes/post-template.php */
			$text = apply_filters( 'the_content', $text );
			$text = str_replace(']]>', ']]&gt;', $text);

			if( empty( $excerpt_length ) ) {
				$excerpt_length = apply_filters( 'excerpt_length', 55 );
			}

			/**
			 * Filter the string in the "more" link displayed after a trimmed excerpt.
			 *
			 * @since 2.9.0
			 *
			 * @param string $more_string The string shown within the more link.
			 */
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
			$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
		}

		/**
		 * Filter the trimmed excerpt string.
		 *
		 * @since 2.8.0
		 *
		 * @param string $text        The trimmed text.
		 * @param string $raw_excerpt The text prior to trimming.
		 */
		return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
	}

}
