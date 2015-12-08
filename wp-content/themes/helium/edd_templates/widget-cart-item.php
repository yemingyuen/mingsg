<li class="edd-cart-item" data-cart-quantity="<?php echo esc_attr( edd_get_cart_quantity() ); ?>">
	<span class="edd-cart-item-title">{item_title}</span>
	<span class="edd-cart-item-separator">-</span><span class="edd-cart-item-price">&nbsp;{item_amount}&nbsp;</span><span class="edd-cart-item-separator">-</span>
	<a href="{remove_url}" data-cart-item="{cart_item_id}" data-download-id="{item_id}" data-action="edd_remove_from_cart" class="edd-remove-from-cart"><?php esc_html_e( 'remove', 'helium' ); ?></a>
</li>
