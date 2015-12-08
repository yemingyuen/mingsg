<?php
// Retrieve all purchases for the current user
$purchases = edd_get_users_purchases( get_current_user_id(), 20, true, 'any' );
if ( $purchases ) : ?>
	<table id="edd_user_history" class="table table-striped table-bordered">
		<thead>
			<tr class="edd_purchase_row">
				<?php do_action('edd_purchase_history_header_before'); ?>
				<th class="edd_purchase_id"><?php esc_html_e('ID', 'helium'); ?></th>
				<th class="edd_purchase_date"><?php esc_html_e('Date', 'helium'); ?></th>
				<th class="edd_purchase_amount"><?php esc_html_e('Amount', 'helium'); ?></th>
				<th class="edd_purchase_details"><?php esc_html_e('Details', 'helium'); ?></th>
				<?php do_action('edd_purchase_history_header_after'); ?>
			</tr>
		</thead>
		<?php foreach ( $purchases as $post ) : setup_postdata( $post ); ?>
			<?php $purchase_data = edd_get_payment_meta( $post->ID ); ?>
			<tr class="edd_purchase_row">
				<?php do_action( 'edd_purchase_history_row_start', $post->ID, $purchase_data ); ?>
				<td class="edd_purchase_id">#<?php echo edd_get_payment_number( $post->ID ); ?></td>
				<td class="edd_purchase_date"><?php echo date_i18n( get_option('date_format'), strtotime( get_post_field( 'post_date', $post->ID ) ) ); ?></td>
				<td class="edd_purchase_amount">
					<span class="edd_purchase_amount"><?php echo edd_currency_filter( edd_format_amount( edd_get_payment_amount( $post->ID ) ) ); ?></span>
				</td>
				<td class="edd_purchase_details">
					<?php if( $post->post_status != 'publish' ) : ?>
					<span class="edd_purchase_status <?php echo $post->post_status; ?>"><?php echo edd_get_payment_status( $post, true ); ?></span>
					<a href="<?php echo add_query_arg( 'payment_key', edd_get_payment_key( $post->ID ), edd_get_success_page_uri() ); ?>">&raquo;</a>
					<?php else: ?>
					<a href="<?php echo add_query_arg( 'payment_key', edd_get_payment_key( $post->ID ), edd_get_success_page_uri() ); ?>"><?php esc_html_e( 'View Details and Downloads', 'helium' ); ?></a>
					<?php endif; ?>
				</td>
				<?php do_action( 'edd_purchase_history_row_end', $post->ID, $purchase_data ); ?>
			</tr>
		<?php endforeach; ?>
	</table>
	<div id="edd_purchase_history_pagination" class="edd_pagination navigation">
		<?php
		$big = 999999;
		echo paginate_links( array(
			'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'  => '?paged=%#%',
			'current' => max( 1, get_query_var( 'paged' ) ),
			'total'   => ceil( edd_count_purchases_of_customer() / 20 ) // 20 items per page
		) );
		?>
	</div>
	<?php wp_reset_postdata(); ?>
<?php else : ?>
	<div class="alert alert-info edd-no-purchases">
		<?php esc_html_e('You have not made any purchases', 'helium'); ?>
	</div>
<?php endif;