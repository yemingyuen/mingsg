<?php
global $edd_register_redirect;

edd_print_errors(); ?>

<form id="edd_register_form" class="edd_form" action="" method="post">
	
	<?php do_action( 'edd_register_form_fields_top' ); ?>

	<fieldset>

		<legend><?php esc_html_e( 'Register New Account', 'helium' ); ?></legend>

		<?php do_action( 'edd_register_form_fields_before' ); ?>

		<div class="form-group">
			<label for="edd-user-login" class="control-label"><?php esc_html_e( 'Username', 'helium' ); ?></label>
			<input id="edd-user-login" class="required edd-input form-control" type="text" name="edd_user_login" title="<?php esc_attr_e( 'Username', 'helium' ); ?>" />
		</div>

		<div class="form-group">
			<label for="edd-user-email" class="control-label"><?php esc_html_e( 'Email', 'helium' ); ?></label>
			<input id="edd-user-email" class="required edd-input form-control" type="email" name="edd_user_email" title="<?php esc_attr_e( 'Email Address', 'helium' ); ?>" />
		</div>

		<div class="form-group">
			<label for="edd-user-pass" class="control-label"><?php esc_html_e( 'Password', 'helium' ); ?></label>
			<input id="edd-user-pass" class="password required edd-input form-control" type="password" name="edd_user_pass" />
		</div>

		<div class="form-group">
			<label for="edd-user-pass2" class="control-label"><?php esc_html_e( 'Confirm Password', 'helium' ); ?></label>
			<input id="edd-user-pass2" class="password required edd-input form-control" type="password" name="edd_user_pass2" />
		</div>

		<?php do_action( 'edd_register_form_fields_before_submit' ); ?>

		<input type="hidden" name="edd_honeypot" value="" />
		<input type="hidden" name="edd_action" value="user_register" />
		<input type="hidden" name="edd_redirect" value="<?php echo esc_url( $edd_register_redirect ); ?>"/>
		<input class="btn btn-primary" name="edd_register_submit" type="submit" value="<?php esc_attr_e( 'Register', 'helium' ); ?>" />

		<?php do_action( 'edd_register_form_fields_after' ); ?>

	</fieldset>

	<?php do_action( 'edd_register_form_fields_bottom' ); ?>
</form>
