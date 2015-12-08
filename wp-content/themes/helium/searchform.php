<form method="get" role="form" class="search-form" action="<?php echo esc_url( home_url( '/' ) ) ?>">
	<input id="search-query" type="text" class="form-control" placeholder="<?php esc_attr_e( 'To search type &amp; hit enter', 'helium' ) ?>" name="s" value="<?php the_search_query() ?>">
	<span class="help-block"><?php esc_html_e( 'Type a keyword and hit enter to start searching. Press Esc to cancel.', 'helium' ) ?></span>
</form>