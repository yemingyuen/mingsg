<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

require_once( dirname( __FILE__ ) . '/class-pagination.php' );
require_once( dirname( __FILE__ ) . '/class-templates.php' );
require_once( dirname( __FILE__ ) . '/class-entries.php' );
require_once( dirname( __FILE__ ) . '/class-option.php' );

final class Youxi_Framework {

	protected static $_instance = null;

	public $pagination = null;

	public $templates = null;

	public $entries = null;

	public $option = null;

	public static function instance() {
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		$this->pagination = new Youxi_Pagination();
		$this->templates  = new Youxi_Templates();
		$this->entries    = new Youxi_Entries();
		$this->option     = new Youxi_Option();
	}
}

function Youxi() {
	return Youxi_Framework::instance();
}