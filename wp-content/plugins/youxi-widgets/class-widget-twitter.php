<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Twitter_Widget extends Youxi_WP_Widget {

	private static $ajax_hook_registered = false;

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-twitter-widget', 'description' => __( 'Use this widget to display your twitter feed.', 'youxi' ) );
		$control_opts = array();

		// Initialize WP_Widget
		parent::__construct( 'twitter-widget', __( 'Youxi &raquo; Twitter', 'youxi' ), $widget_opts, $control_opts );

		if( ! self::$ajax_hook_registered ) {

			$ajax_action = apply_filters( 'youxi_widgets_twitter_ajax_action', 'youxi_get_tweets' );

			if( ! has_action( "wp_ajax_{$ajax_action}"  ) ) {
				add_action( "wp_ajax_{$ajax_action}", array( 'Youxi_Twitter_Widget', 'get_tweets' ) );
			}
			if( ! has_action( "wp_ajax_nopriv_{$ajax_action}" ) ) {
				add_action( "wp_ajax_nopriv_{$ajax_action}", array( 'Youxi_Twitter_Widget', 'get_tweets' ) );
			}

			self::$ajax_hook_registered = true;
		}
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title'    => '', 
			'username' => '', 
			'count'    => 1
		));

		$instance = apply_filters( "youxi_widgets_{$this->id_base}_instance", $instance, $this->id );

		echo $before_widget;

		if( isset( $instance['title'] ) && ! empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;

		$this->maybe_load_template( $id, $instance );

		echo $after_widget;
	}

	public function form( $instance ) {

		$vars = wp_parse_args( (array) $instance, array(
			'title'    => '', 
			'username' => '', 
			'count'    => 1
		));

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php _e( 'Username', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Number of Tweets', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" value="<?php echo esc_attr( $count ); ?>">
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( preg_replace( '/\W/', '', $new_instance['username'] ) );
		$instance['count']    = absint( strip_tags( $new_instance['count'] ) );

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}

	public function enqueue() {

		if( parent::enqueue() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if( ! wp_script_is( 'mini-tweets' ) ) {
				wp_enqueue_script( 'mini-tweets', self::frontend_plugins_url( "minitweets/jquery.minitweets{$suffix}.js" ), array( 'jquery' ), '0.1', true );
			}
		}
	}

	public function get_defaults() {

		$widget_name = preg_replace( array( '/[^a-zA-Z0-9]/', '/_?widget_?/' ), '', $this->id_base );
		
		return apply_filters( "youxi_widgets_{$widget_name}_defaults", array(
			'entryPath'  => admin_url( 'admin-ajax.php' ), 
			'userParams' => array( 'action' => apply_filters( 'youxi_widgets_twitter_ajax_action', 'youxi_get_tweets' ) ), 
			'template'   => '<li>' . 
					'<span class="twitter-header">' . 
						'<span class="twitter-avatar">' . 
							'<a href="<%= user_url %>"><img src="<%= avatar_normal %>" alt="<%= user_screen_name %>" title="<%= user_screen_name %>"></a>' . 
						'</span>' . 
						'<span class="twitter-info">' . 
							'<a href="<%= user_url %>" class="twitter-name"><%= user_name %></a>' . 
							'<a href="<%= user_url %>" class="twitter-user">@<%= user_screen_name %></a>' . 
						'</span>' . 
					'</span>' . 
					'<span class="twitter-text"><%= text %></span>' . 
					'<a class="twitter-time" href="<%= tweet_url %>"><%= relative_time %></a>' . 
					'<span class="twitter-intents">' . 
						'<ul>' . 
							'<li><a href="<%= reply_url %>" title="Reply">Reply</a></li>' . 
							'<li><a href="<%= retweet_url %>" title="Retweet">Retweet</a></li>' . 
							'<li><a href="<%= favorite_url %>" title="Favorite">Favorite</a></li>' . 
						'</ul>' . 
					'</span>' . 
				'</li>'
		));
	}

	public static function get_tweets() {
		
		if( isset( $_REQUEST['request'] ) ) {

			if( ! class_exists( 'YTwitterOAuth' ) ) {
				require( YOUXI_WIDGETS_DIR . 'api/twitter/class-twitter-oauth.php' );
			}

			$request = wp_parse_args( $_REQUEST['request'], array(
				'host' => '', 
				'url'  => '', 
				'parameters' => array()
			));

			// Initialize Twitter Feeds Manager
			$keys = apply_filters( 'youxi_widgets_twitter_keys', array(
				'consumer_key' => '', 
				'consumer_secret' => '', 
				'oauth_token' => '', 
				'oauth_token_secret' => ''
			));

			extract( $keys, EXTR_SKIP );

			$twitter = new YTwitterOAuth( $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret );
			$tweets = $twitter->fetch( $request['host'], $request['url'], 'GET', $request['parameters'] );

			$response = array( 'response' => null, 'message' => null );
			
			if( $tweets ) {
				$response['response'] = $tweets;
			} else {
				$response['message'] = $twitter->get_debug_info();
			}

			wp_send_json( $response );
		}

		wp_send_json_error();
	}
}