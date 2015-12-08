<?php
/**
 * This template is used to display the profile editor with [edd_profile_editor]
 */
global $current_user;

if ( is_user_logged_in() ):
	$user_id      = get_current_user_id();
	$first_name   = get_user_meta( $user_id, 'first_name', true );
	$last_name    = get_user_meta( $user_id, 'last_name', true );
	$display_name = $current_user->display_name;
	$address      = edd_get_customer_address( $user_id );

	if ( edd_is_cart_saved() ): ?>
		<?php $restore_url = add_query_arg( array( 'edd_action' => 'restore_cart', 'edd_cart_token' => edd_get_cart_token() ), edd_get_checkout_uri() ); ?>
		<div class="alert alert-success edd_success">
			<strong><?php esc_html_e( 'Saved cart', 'helium'); ?>:</strong> <?php printf( wp_kses( __( 'You have a saved cart, <a href="%s">click here</a> to restore it.', 'helium' ), array( 'a' => array( 'href' => array() ) ) ), $restore_url ); ?>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['updated'] ) && $_GET['updated'] == true && ! edd_get_errors() ): ?>
		<div class="alert alert-success edd_success">
			<strong><?php esc_html_e( 'Success', 'helium'); ?>:</strong> <?php esc_html_e( 'Your profile has been edited successfully.', 'helium' ); ?>
		</div>
	<?php endif; ?>

	<?php edd_print_errors(); ?>

	<form id="edd_profile_editor_form" class="edd_form" action="<?php echo esc_url( edd_get_current_page_url() ); ?>" method="post">

		<fieldset>
			<span id="edd_profile_name_label"><legend><?php esc_html_e( 'Change your Name', 'helium' ); ?></legend></span>

			<div class="form-group">
				<label for="edd_first_name" class="control-label"><?php esc_html_e( 'First Name', 'helium' ); ?></label>
				<input name="edd_first_name" id="edd_first_name" class="text edd-input form-control" type="text" value="<?php echo esc_attr( $first_name ); ?>" >
			</div>

			<div class="form-group">
				<label for="edd_last_name" class="control-label"><?php esc_html_e( 'Last Name', 'helium' ); ?></label>
				<input name="edd_last_name" id="edd_last_name" class="text edd-input form-control" type="text" value="<?php echo esc_attr( $last_name ); ?>" >
			</div>

			<div class="form-group">
				<label for="edd_display_name" class="control-label"><?php esc_html_e( 'Display Name', 'helium' ); ?></label>
				<select name="edd_display_name" id="edd_display_name" class="select edd-select form-control">
					<?php if ( ! empty( $current_user->first_name ) ): ?>
					<option <?php selected( $display_name, $current_user->first_name ); ?> value="<?php echo esc_attr( $current_user->first_name ); ?>"><?php echo esc_html( $current_user->first_name ); ?></option>
					<?php endif; ?>
					<option <?php selected( $display_name, $current_user->user_nicename ); ?> value="<?php echo esc_attr( $current_user->user_nicename ); ?>"><?php echo esc_html( $current_user->user_nicename ); ?></option>
					<?php if ( ! empty( $current_user->last_name ) ): ?>
					<option <?php selected( $display_name, $current_user->last_name ); ?> value="<?php echo esc_attr( $current_user->last_name ); ?>"><?php echo esc_html( $current_user->last_name ); ?></option>
					<?php endif; ?>
					<?php if ( ! empty( $current_user->first_name ) && ! empty( $current_user->last_name ) ): ?>
					<option <?php selected( $display_name, $current_user->first_name . ' ' . $current_user->last_name ); ?> value="<?php echo esc_attr( $current_user->first_name . ' ' . $current_user->last_name ); ?>"><?php echo esc_html( $current_user->first_name . ' ' . $current_user->last_name ); ?></option>
					<option <?php selected( $display_name, $current_user->last_name . ' ' . $current_user->first_name ); ?> value="<?php echo esc_attr( $current_user->last_name . ' ' . $current_user->first_name ); ?>"><?php echo esc_html( $current_user->last_name . ' ' . $current_user->first_name ); ?></option>
					<?php endif; ?>
				</select>
			</div>

			<div class="form-group">
				<label for="edd_email" class="control-label"><?php esc_html_e( 'Email Address', 'helium' ); ?></label>
				<input name="edd_email" id="edd_email" class="text edd-input required form-control" type="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" >
			</div>

			<span id="edd_profile_billing_address_label"><legend><?php esc_html_e( 'Change your Billing Address', 'helium' ); ?></legend></span>

			<div class="form-group">
				<label for="edd_address_line1" class="control-label"><?php esc_html_e( 'Line 1', 'helium' ); ?></label>
				<input name="edd_address_line1" id="edd_address_line1" class="text edd-input form-control" type="text" value="<?php echo esc_attr( $address['line1'] ); ?>" >
			</div>

			<div class="form-group">
				<label for="edd_address_line2" class="control-label"><?php esc_html_e( 'Line 2', 'helium' ); ?></label>
				<input name="edd_address_line2" id="edd_address_line2" class="text edd-input form-control" type="text" value="<?php echo esc_attr( $address['line2'] ); ?>" >
			</div>

			<div class="form-group">
				<label for="edd_address_city" class="control-label"><?php esc_html_e( 'City', 'helium' ); ?></label>
				<input name="edd_address_city" id="edd_address_city" class="text edd-input form-control" type="text" value="<?php echo esc_attr( $address['city'] ); ?>" >
			</div>

			<div class="form-group">
				<label for="edd_address_zip" class="control-label"><?php esc_html_e( 'Zip / Postal Code', 'helium' ); ?></label>
				<input name="edd_address_zip" id="edd_address_zip" class="text edd-input form-control" type="text" value="<?php echo esc_attr( $address['zip'] ); ?>" >
			</div>

			<div class="form-group">
				<label for="edd_address_country" class="control-label"><?php esc_html_e( 'Country', 'helium' ); ?></label>
				<select name="edd_address_country" id="edd_address_country" class="select edd-select form-control">
					<?php foreach( edd_get_country_list() as $key => $country ) : ?>
					<option value="<?php echo $key; ?>"<?php selected( $address['country'], $key ); ?>><?php echo esc_html( $country ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="form-group">
				<label for="edd_address_state" class="control-label"><?php esc_html_e( 'State / Province', 'helium' ); ?></label>
				<input name="edd_address_state" id="edd_address_state" class="text edd-input form-control" type="text" value="<?php echo esc_attr( $address['state'] ); ?>" >
			</div>

			<span id="edd_profile_password_label"><legend><?php esc_html_e( 'Change your Password', 'helium' ); ?></legend></span>

			<div class="form-group">
				<label for="edd_user_pass" class="control-label"><?php esc_html_e( 'New Password', 'helium' ); ?></label>
				<input name="edd_new_user_pass1" id="edd_new_user_pass1" class="password edd-input form-control" type="password">
			</div>

			<div class="form-group">
				<label for="edd_user_pass" class="control-label"><?php esc_html_e( 'Re-enter Password', 'helium' ); ?></label>
				<input name="edd_new_user_pass2" id="edd_new_user_pass2" class="password edd-input form-control" type="password">
			</div>

			<p class="edd_password_change_notice help-block"><?php esc_html_e( 'Please note after changing your password, you must log back in.', 'helium' ); ?></p>

			<input type="hidden" name="edd_profile_editor_nonce" value="<?php echo wp_create_nonce( 'edd-profile-editor-nonce' ); ?>">
			<input type="hidden" name="edd_action" value="edit_user_profile" >
			<input type="hidden" name="edd_redirect" value="<?php echo esc_url( edd_get_current_page_url() ); ?>" >
			<input name="edd_profile_editor_submit" id="edd_profile_editor_submit" type="submit" class="edd_submit btn btn-primary" value="<?php esc_attr_e( 'Save Changes', 'helium' ); ?>">

		</fieldset>

	</form><!-- #edd_profile_editor_form -->
	<?php
else:
	esc_html_e( 'You need to login to edit your profile.', 'helium' );
	echo edd_login_form();
endif;
