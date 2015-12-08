<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Admin Notice
 *
 * This class is a helper class for displaying multiple admin notices in a single admin notice box.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Admin_Notice' ) ) {

	class Youxi_Admin_Notice {

		private $errors = array();

		private $warnings = array();

		private static $instance;

		private function __construct() {

			add_action( 'admin_notices', array( $this, 'display_errors' ) );
			add_action( 'admin_notices', array( $this, 'display_warnings' ) );

			add_action( 'youxi_plugins_after_admin_notices', array( $this, 'display_footer_message' ) );
		}

		public static function instance() {

			if( ! self::$instance ) {
				self::$instance = new Youxi_Admin_Notice();
			}

			return self::$instance;
		}

		public function add_error( $plugin, $message ) {
			if( ! empty( $message ) ) {
				if( ! isset( $this->errors[ $plugin ] ) ) {
					$this->errors[ $plugin ] = array();
				}
				$this->errors[ $plugin ][] = $message;
			}
		}

		public function add_warning( $plugin, $message ) {
			if( ! empty( $message ) ) {
				if( ! isset( $this->warnings[ $plugin ] ) ) {
					$this->warnings[ $plugin ] = array();
				}
				$this->warnings[ $plugin ][] = $message;
			}
		}

		public function display_errors() {
			if( ! empty( $this->errors ) ) {

				echo '<div class="error">';

					echo '<ul>';

					foreach( $this->errors as $file => $errors ):

						$data = get_plugin_data( $file );

						echo "<li><strong>{$data['Name']}</strong>";

							echo '<ul>';

								echo '<li>&raquo; ' . join( '</li><li>&raquo; ', $errors ) . '</li>';

							echo '</ul>';

						echo '</li>';

					endforeach;

					echo '</ul>';

					echo '<h3>' . sprintf( __( 'Lost? Open a support thread at Youxi Themes <a href="%s" target="_blank">support forum</a>.', 'youxi' ), 'http://support.youxithemes.com' ). '</h3>';

				echo '</div>';
			}
		}

		public function display_warnings() {
			if( ! empty( $this->warnings ) ) {

				echo '<div class="updated">';

					echo '<ul>';

					foreach( $this->warnings as $file => $warnings ):

						$data = get_plugin_data( $file );

						echo "<li><strong>{$data['Name']}</strong>";

							echo '<ul>';

								echo '<li>&raquo; ' . join( '</li><li>&raquo; ', $warnings ) . '</li>';

							echo '</ul>';

						echo '</li>';

					endforeach;

					echo '</ul>';

				echo '</div>';
			}
		}

	}

}