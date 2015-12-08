<?php

if( ! defined( 'CLOSURE_COMPILER_URL' ) ) {
	define( 'CLOSURE_COMPILER_URL', 'http://closure-compiler.appspot.com/compile' );
}

if( ! class_exists( 'Youxi_JS' ) ):

class Youxi_JS {

	/**
	 * Helper function to quote a variable into JS
	 * Taken from Yii PHP Framework
	 */
	public static function quote( $js ) {
		return strtr( $js, array(
			"\t" => '\t', 
			"\n" => '\n', 
			"\r" => '\r', 
			'"'  => '\"', 
			'\'' => '\\\'', 
			'\\' => '\\\\', 
			'</' => '<\/'
		));
	}

	/**
	 * Helper function to encode a PHP variable into JS
	 * Taken from Yii PHP Framework
	 */
	public static function encode( $value, $safe = false ) {

		if( is_string( $value ) ) {

			if( strpos( $value, 'js:' ) === 0 && $safe === false ) {
				return substr( $value, 3 );
			} else {
				return "'" . self::quote( $value ) . "'";
			}
		} elseif( is_null( $value ) ) {
			return 'null';
		} elseif( is_bool( $value ) ) {
			return $value ? 'true' : 'false';
		} elseif( is_integer( $value ) ) {
			return "$value";
		} elseif( is_float( $value ) ) {
			if( $value === -INF ) {
				return 'Number.NEGATIVE_INFINITY';
			} elseif( $value === INF ) {
				return 'Number.POSITIVE_INFINITY';
			} else {
				return str_replace( ',', '.', floatval( $value ) );
			}
		} elseif( is_object( $value ) ) {
			return self::encode( get_object_vars( $value ), $safe );
		} elseif( is_array( $value ) ) {
			$es=array();
			if( ( $n = count( $value ) ) > 0 && array_keys( $value ) !== range( 0, $n - 1 ) ) {
				foreach( $value as $k => $v ) {
					$es[] = "'" . self::quote( $k ) . "':" . self::encode( $v, $safe );
				}
				return '{' . implode( ',', $es ) . '}';
			} else {
				foreach( $value as $v ) {
					$es[] = self::encode( $v, $safe );
				}
				return '[' . implode( ',', $es ) . ']';
			}
		}

		return '';
	}

	/**
	 * Minify JavaScript using Google's Closure Compiler
	 */
	public static function minify( $js_code, $args = array() ) {

		$js_hash  = md5( $js_code );
		$js_cache = get_option( '_youxi_minjs_cache', array() );

		/* Check first if we have the JS string in cache */
		if( is_array( $js_cache ) && isset( $js_cache[ $js_hash ] ) && is_string( $js_cache[ $js_hash ] ) ) {
			return $js_cache[ $js_hash ];
		}

		// Default request data
		$request_data = array(
			'output_info' => array( 'compiled_code', 'warnings', 'errors', 'statistics' ), 
			'output_format' => 'json'
		);
		$request_data = array_merge( $request_data, $args, compact( 'js_code' ) );

		// Process the request body manually to make same named parameters possible
		$body = http_build_query( $request_data, null, '&' );
		$body = preg_replace( '/output_info%5B\d+%5D=/', 'output_info=', $body );

		// Initiate request
		$response = wp_remote_post( CLOSURE_COMPILER_URL, array(
			'sslverify' => false, 
			'timeout' => 10, 
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' )
			), 
			'body' => $body
		));

		// Check if the request was successful
		if( ! is_wp_error( $response ) && 200 == wp_remote_retrieve_response_code( $response ) ) {

			$response_body = wp_remote_retrieve_body( $response );
			$response_obj  = json_decode( $response_body, true );

			// Check for errors
			if( is_null( $response_obj ) || ! is_array( $response_obj ) || 
				! isset( $response_obj['compiledCode'] ) || 
				isset( $response_obj['errors'], $response_obj['serverErrors'] ) ) {

				return $js_code;
			}

			// Everything OK, let's first cache the JS code
			$js_code = $js_cache[ $js_hash ] = $response_obj['compiledCode'];
			if( ! add_option( '_youxi_minjs_cache', $js_cache, '', 'no' ) ) {
				update_option( '_youxi_minjs_cache', $js_cache );
			}
		}

		return $js_code;
	}

}
endif;
