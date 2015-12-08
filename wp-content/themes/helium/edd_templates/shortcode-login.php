<?php
global $edd_login_redirect;
if ( ! is_user_logged_in() ) :

	// Show any error messages after form submission
	edd_print_errors(); ?>

	<form id="edd_login_form" class="edd_form" action="" method="post">

		<fieldset>

			<legend><?php esc_html_e( 'Log into Your Account', 'helium' ); ?></legend>

			<?php do_action( 'edd_login_fields_before' ); ?>

			<div class="form-group">
				<label class="control-label" for="edd_user_Login"><?php esc_html_e( 'Username', 'helium' ); ?></label>
				<input name="edd_user_login" id="edd_user_login" class="required edd-input form-control" type="text" title="<?php esc_attr_e( 'Username', 'helium' ); ?>">
			</div>

			<div class="form-group">
				<label class="control-label" for="edd_user_pass"><?php esc_html_e( 'Password', 'helium' ); ?></label>
				<input name="edd_user_pass" id="edd_user_pass" class="password required edd-input form-control" type="password">
			</div>

			<div class="form-group">
				<input type="hidden" name="edd_redirect" value="<?php echo esc_url( $edd_login_redirect ); ?>">
				<input type="hidden" name="edd_login_nonce" value="<?php echo wp_create_nonce( 'edd-login-nonce' ); ?>">
				<input type="hidden" name="edd_action" value="user_login">
				<input id="edd_login_submit" type="submit" class="edd_submit btn btn-primary" value="<?php esc_attr_e( 'Log In', 'helium' ); ?>">
			</div>

			<div class="edd-lost-password">
				<a href="<?php echo wp_lostpassword_url(); ?>" title="<?php esc_attr_e( 'Lost Password', 'helium' ); ?>">
					<?php esc_html_e( 'Lost Password?', 'helium' ); ?>
				</a>
			</div>

			<?php do_action( 'edd_login_fields_after' ); ?>

		</fieldset>

	</form>

<?php else : ?>
	<div class="alert alert-info edd-logged-in">
		<?php esc_html_e( 'You are already logged in', 'helium' ); ?>
	</div>
<?php endif; ?>