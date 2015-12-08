<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Social_Widget extends Youxi_WP_Widget {

	private $icons = array(
		
	);

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-social-widget', 'description' => __( 'Use this widget to link your social profiles using a set of retina ready icons.', 'youxi' ) );
		$control_opts = array( 'width' => '400px' );

		// Initialize WP_Widget
		parent::__construct( 'social-widget', __( 'Youxi &raquo; Social', 'youxi' ), $widget_opts, $control_opts );
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'items' => array()
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
			'title' => '', 
			'items' => array()
		));

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<div class="youxi-repeater" data-tmpl="<?php echo $this->id ?>">
			<script id="tmpl-<?php echo $this->id ?>" type="text/html">
			<?php echo $this->get_template() ?>
			</script>
			<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>"><?php _e( 'Items', 'youxi' ); ?>:</label>
			<div class="youxi-repeater-items-wrap">
			<?php if( is_array( $items ) ) : ?>
				<?php foreach( $items as $index => $marker ): 
					$marker = wp_parse_args( $marker, array( 'url' => '', 'icon' => '' ) );
				?>
				<?php echo $this->get_template( $index, $marker ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
			<button type="button" class="button button-small button-repeater-add"><?php echo _e( 'Add Item', 'youxi' ) ?></button>
		</div>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {

		$valid_icons = $this->get_icons();

		foreach( $new_instance['items'] as &$item ) {
			$item['url']    = esc_url_raw( $item['url'] );
			$item['title']  = strip_tags( $item['title'] );
			$item['icon']   = array_key_exists( $item['icon'], $valid_icons ) ? $item['icon'] : '';
			$item['newtab'] = (bool) $item['newtab'];
		}

		$instance = array(
			'title' => strip_tags( $new_instance['title'] ), 
			'items' => array_values( $new_instance['items'] )
		);

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}

	public function enqueue() {

		if( parent::enqueue() ) {

			if( ! wp_style_is( 'youxi-social-icons', 'registered' ) ) {
				wp_register_style( 'youxi-social-icons', YOUXI_WIDGETS_URL . "frontend/assets/social/social.css", array(), false, 'screen' );
			}

			wp_enqueue_style( 'youxi-social-icons' );
		}
	}

	public function get_icons() {
		return apply_filters( 'youxi_widgets_recognized_social_icons', array(
			'500px' => '500px', 
			'about-me' => 'About.me', 
			'addthis' => 'AddThis', 
			'amazon' => 'Amazon', 
			'aol' => 'AOL', 
			'app-store-2' => 'App Store 2', 
			'app-store' => 'App Store', 
			'apple' => 'Apple', 
			'bebo' => 'Bebo', 
			'behance' => 'Behance', 
			'bing' => 'Bing', 
			'blip' => 'Blip', 
			'blogger' => 'Blogger', 
			'caroflot' => 'Coroflot', 
			'daytum' => 'DAYTUM', 
			'delicious' => 'Delicious', 
			'design-bump' => 'Designbump', 
			'design-float' => 'Design Float', 
			'deviantart' => 'DeviantArt', 
			'digg' => 'Digg', 
			'dopplr' => 'DOPPLR', 
			'dribbble' => 'Dribbble', 
			'drupal' => 'Drupal', 
			'ebay' => 'eBay', 
			'email' => 'Email', 
			'ember-app' => 'Ember', 
			'etsy' => 'Etsy', 
			'facebook' => 'Facebook', 
			'feedburner' => 'FeedBurner', 
			'flickr' => 'Flickr', 
			'foodspotting' => 'Foodspotting', 
			'forrst' => 'Forrst', 
			'foursquare' => 'Foursquare', 
			'friendfeed' => 'Friendfeed', 
			'friendster' => 'Friendster', 
			'gdgt' => 'GDGT', 
			'github' => 'GitHub', 
			'google-buzz' => 'Google Buzz', 
			'google-plus-2' => 'Google Plus 2', 
			'google-plus-3' => 'Google Plus 3', 
			'google-plus' => 'Google Plus', 
			'google-talk' => 'Google Talk', 
			'google' => 'Google', 
			'gowalla-2' => 'Gowalla 2', 
			'gowalla' => 'Gowalla', 
			'grooveshark' => 'Grooveshark', 
			'heart' => 'Heart', 
			'hyves' => 'Hyves', 
			'icondock' => 'IconDock', 
			'icq' => 'ICQ', 
			'identi-ca' => 'Identi.ca', 
			'imessage' => 'iMessage', 
			'instagram' => 'Instagram', 
			'itunes' => 'iTunes', 
			'last-fm' => 'Last.fm', 
			'linkedin' => 'Linkedin', 
			'meetup' => 'Meetup', 
			'metacafe' => 'MetaCafe', 
			'microsoft' => 'Microsoft', 
			'mister-wong' => 'Mister Wong', 
			'mixx' => 'Mixx', 
			'mobileme' => 'Mobileme', 
			'msn' => 'MSN', 
			'myspace' => 'MySpace', 
			'netvibes' => 'Netvibes', 
			'newsvine' => 'Newsvine', 
			'paypal' => 'PayPal', 
			'photobucket' => 'PhotoBucket', 
			'picasa' => 'Picasa', 
			'pinterest' => 'Pinterest', 
			'podcast' => 'Podcast', 
			'posterous' => 'Posterous', 
			'qik' => 'QIK', 
			'quora' => 'Quora', 
			'reddit' => 'Reddit', 
			'rss' => 'RSS', 
			'scribd' => 'Scribd', 
			'share-this' => 'ShareThis', 
			'skype' => 'Skype', 
			'slash-dot' => 'Slashdot', 
			'slideshare' => 'Slideshare', 
			'smugmug' => 'SmugMug', 
			'soundcloud' => 'SoundCloud', 
			'spotify' => 'Spotify', 
			'squidoo' => 'SQUIDOO', 
			'stackoverflow' => 'StackOverflow', 
			'star' => 'Star', 
			'stumbleupon-2' => 'StumbleUpon 2', 
			'stumbleupon' => 'StumbleUpon', 
			'technorati' => 'Technorati', 
			'tumblr' => 'Tumblr', 
			'twitter-2' => 'Twitter 2', 
			'twitter-3' => 'Twitter 3', 
			'twitter' => 'Twitter', 
			'viddler' => 'viddler', 
			'vimeo' => 'Vimeo', 
			'virb' => 'VIRB', 
			'w3c' => 'W3C', 
			'wikipedia' => 'Wikipedia', 
			'wordpress-2' => 'WordPress 2', 
			'wordpress' => 'WordPress', 
			'xing' => 'XING', 
			'yahoo-buzz' => 'Yahoo! Buzz', 
			'yahoo' => 'Yahoo!', 
			'yelp' => 'Yelp', 
			'youtube' => 'YouTube'
		));
	}

	protected function get_template( $index = '{{ data.index }}', $values = array() ) {
		$values = wp_parse_args( $values, array(
			'url' => '', 
			'title' => '', 
			'icon' => '', 
			'newtab' => false
		));

		ob_start(); ?>
		<table class="widefat youxi-repeater-item">
			<tr>
				<td colspan="2">
					<p>
						<strong><?php _e( 'Item', 'youxi' ) ?></strong>
						<span style="float: right;">
							<a href="#" class="button-repeater-remove">&times;</a>
						</span>
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "items-$index-url" ) ) ?>"><?php _e( 'URL', 'youxi' ) ?>:</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "items-$index-url" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "items][$index][url" ) ) ?>" type="text" value="<?php echo esc_attr( $values['url'] ) ?>">
					</p>
				</td>
				<td>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "items-$index-title" ) ) ?>"><?php _e( 'Title', 'youxi' ) ?>:</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "items-$index-title" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "items][$index][title" ) ) ?>" type="text" value="<?php echo esc_attr( $values['title'] ) ?>">
					</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "items-$index-icon" ) ) ?>"><?php _e( 'Icon', 'youxi' ) ?>:</label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( "items-$index-icon" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "items][$index][icon" ) ) ?>">
							<?php foreach( $this->get_icons() as $key => $icon ): ?>
							<option value="<?php echo esc_attr( $key ) ?>" <?php selected( $values['icon'], $key ) ?>><?php echo $icon ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<p>
						<input id="<?php echo esc_attr( $this->get_field_id( "items-$index-newtab" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "items][$index][newtab" ) ) ?>" type="checkbox" <?php checked( (bool) $values['newtab'], true ) ?>>
						<label for="<?php echo esc_attr( $this->get_field_id( "items-$index-newtab" ) ) ?>"><?php _e( 'Open link in a new window/tab.', 'youxi' ); ?></label>
					</p>
				</td>
			</tr>
		</table>
		<?php return ob_get_clean();
	}
}