<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Shortcode Animation Class
 *
 * This class handles shortcode animations
 *
 * @package   Youxi Shortcode
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2014, Mairel Theafila
 */
final class Youxi_Shortcode_Animation {

	/**
	 * The array containing shortcode targets
	 *
	 * @access private
	 * @var array
	 */
	private $shortcodes = array();

	/**
	 * The array containing CSS animation names
	 *
	 * @access private
	 * @var array
	 */
	private $animations = array();

	/**
	 * The singleton instance
	 *
	 * @access private
	 * @var array
	 */
	private static $instance;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->animations = apply_filters( 'youxi_shortcode_animation_names', array() );
		$this->shortcodes = apply_filters( 'youxi_shortcode_animation_targets', array() );
	}

	/**
	 * Public method to get the singleton instance
	 *
	 * @return Youxi_Shortcode_Manager
	 */
	public static function get() {
		if( ! is_a( self::$instance, 'Youxi_Shortcode_Animation' ) ) {
			self::$instance = new Youxi_Shortcode_Animation();
		}

		return self::$instance;
	}

	/**
	 * Initialize animations by filtering targetted shortcode fieldsets and atts
	 */
	public function init() {
		foreach( array_keys( $this->shortcodes ) as $tag ) {
			add_filter( "youxi_shortcode_{$tag}_fieldsets", array( $this, 'shortcode_fieldsets' ) );
			add_filter( "youxi_shortcode_{$tag}_atts", array( $this, 'shortcode_atts' ), 10, 2 );
			add_filter( "youxi_shortcode_pre_render_{$tag}_atts", array( $this, 'parse' ), 10, 2 );
		}
	}

	/**
	 * Modify shortcode fieldsets
	 */
	public function shortcode_fieldsets( $fieldsets ) {

		if( ! isset( $fieldsets['auto'] ) && class_exists( 'Youxi_Form' ) ) {
			$fieldsets['auto'] = Youxi_Form::auto_fieldset();
		}

		$animation_fieldset = apply_filters( 'youxi_shortcode_animation_fieldset', 'animation' );
		if( ! isset( $fieldsets[ $animation_fieldset ] ) ) {
			$fieldsets[ $animation_fieldset ] = apply_filters( 'youxi_shortcode_animation_fieldset', array(
				'id' => 'animation', 
				'title' => __( 'Animation', 'youxi' )
			));
		}

		return $fieldsets;
	}

	/**
	 * Modify shortcode atts
	 */
	public function shortcode_atts( $atts, $tag ) {

		$fieldsets = array();
		if( isset( $this->shortcodes[ $tag ] ) && is_array( $this->shortcodes[ $tag ] ) ) {

			/* Create animation fields */
			foreach( $this->shortcodes[ $tag ] as $key => $title ) {

				$fieldsets[ $key ] = array(
					'title' => $title, 
					'fields' => array(
						'name' => array(
							'type' => 'select', 
							'label' => __( 'Animation', 'youxi' ), 
							'description' => __( 'Choose here the animation.', 'youxi' ), 
							'choices' => array_combine( $this->animations, $this->animations ), 
							'std' => 'flipInX'
						), 
						'duration' => array(
							'type' => 'uispinner', 
							'label' => __( 'Duration', 'youxi' ), 
							'description' => __( 'Specify the duration of the animation (-1 means unspecified).', 'youxi' ), 
							'widgetopts' => array(
								'min' => -1, 
								'step' => 10
							), 
							'std' => -1
						), 
						'delay' => array(
							'type' => 'uispinner', 
							'label' => __( 'Delay', 'youxi' ), 
							'description' => __( 'Specify the delay before the animation begins (-1 means unspecified).', 'youxi' ), 
							'widgetopts' => array(
								'min' => -1, 
								'step' => 10
							), 
							'std' => -1
						)
					)
				);
			}

			/* Get the animation attribute name */
			$atts_name = $this->atts_name();

			/* Create the attribute */
			$_atts = array();
			$_atts[ $atts_name ] = array(
				'type' => 'togglable_fieldsets', 
				'label' => __( 'Animate', 'youxi' ), 
				'description' => __( 'Configure here the animation properties.', 'youxi' ), 
				'fieldset' => apply_filters( 'youxi_shortcode_animation_fieldset', 'animation' ), 
				'fieldsets' => $fieldsets, 
				'serialize' => 'js:function( data ) {
					return _.map( data, function( props, key ) {
						var p, a = [];
						for( p in props ) {
							if( "name" == p || parseInt( props[ p ] ) >= 0 ) {
								a.push( p + ":" + props[ p ] );
							}
						}
						return key + ( a.length ? "(" + a.join( "," ) + ")" : "" );
					}).join( "," );
				}', 
				'deserialize' => 'js:function( data ) {
					var m, val = $.trim( data ), 
						regex = /(' . join( '|', array_keys( $this->shortcodes[ $tag ] ) ) . ')(?:\((.+?)\))?,?/g;

					data = {};
					while( m = regex.exec( val ) ) {
						data[ m[1] ] = m[2] ? _.object( _.map( m[2].split( "," ), function( pair ) {
							return pair.split( ":" );
						})) : {};
					}
					return data;
				}'
			);

			return array_merge( $atts, $_atts );
		}

		return $atts;
	}

	/**
	 * Create an array with empty string for animation targets
	 */
	public function defaults( $tag ) {
		if( isset( $this->shortcodes[ $tag ] ) ) {
			return array_map( '__return_empty_string', $this->shortcodes[ $tag ] );
		}
		return array();
	}

	/**
	 * Get the animation shortcode attribute name
	 */
	public function atts_name() {
		return apply_filters( 'youxi_shortcode_animation_atts_name', 'animate' );
	}

	/**
	 * Parse a shortcode attribute value.
	 * Attributes has the form of target(name:value,...)
	 */
	public function parse( $atts, $tag ) {

		if( isset( $atts[ $this->atts_name() ] ) ) {

			$shortcodes = $this->shortcodes[ $tag ];
			$result     = $this->defaults( $tag );
			$value      = $atts[ $this->atts_name() ];

			if( is_string( $value ) && is_array( $shortcodes ) && ! empty( $shortcodes ) ) {

				$regexp = implode( '|', array_map( 'preg_quote', array_keys( $shortcodes ) ) );

				if( preg_match_all( "/($regexp)(?:\((.+?)\))?,?/", $value, $matches, PREG_SET_ORDER ) ) {

					foreach( $matches as $match ) {

						if( isset( $match[1], $match[2] ) ) {

							$props = array();
							$output = '';

							foreach( explode( ',', $match[2] ) as $p ) {
								$p = explode( ':', $p );

								if( isset( $p[0], $p[1] ) ) {
									$props[ $p[0] ] = $p[1];
								}
							}

							extract( wp_parse_args( $props, array(
								'name' => 'flipInX', 
								'duration' => -1, 
								'delay' => -1
							)), EXTR_SKIP );

							/* Make sure it's a valid animation name */
							if( in_array( $name, $this->animations ) ) {

								$result[ $match[1] ] .= ' data-animation-name="' . esc_attr( $name ) . '"';

								if( $duration >= 0 ) {
									$result[ $match[1] ] .= ' data-animation-duration="' . esc_attr( $duration ) . '"';
								}
								if( $delay >= 0 ) {
									$result[ $match[1] ] .= ' data-animation-delay="' . esc_attr( $delay ) . '"';
								}
							}
						}
					}
				}
			}

			$atts[ $this->atts_name() ] = $result;
		}

		return $atts;
	}
}
