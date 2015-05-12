<?php
class Js_Loader {

	/**
	 * Holds the singleton instance of this class
	 * @since 0.0.1
	 * @var Js_Loader
	 */
	static $instance = false;

	static $libraries = array();
	
	/**
	 * Return the Js_Loader object
	 *
	 * @return  object
	 */

	public static function init() {
		if ( ! self::$instance ) {
			if ( did_action( 'plugins_loaded' ) ){
				self::plugin_textdomain();
			} else {
				add_action( 'plugins_loaded', array( __CLASS__, 'plugin_textdomain' ), 99 );
			}

			self::$instance = new Js_Loader;
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct(){
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	public function load_scripts(){
		$libraries = self::get_libraries();
		$options = get_option( 'jsloader_option', array() );

		foreach ( $options as $key => $value ) {
			if( 1 == $value ){
				wp_enqueue_script( $key, $libraries[$key]['js_file'], $libraries[$key]['dependency_js'], false, $libraries[$key]['in_footer']);
				if( !empty( $libraries[$key]['css_file'] ) ){
					wp_enqueue_style( $key, $libraries[$key]['css_file'], $libraries[$key]['dependency_css'], false );
				}
			}
		}
	}

	public static function get_libraries(){
		self::$libraries = array(
			'wow' => array(
				'name'				=> 'WOW',
				'description'		=> 'jQuery plugin for animation on scroll',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/wow.min.js',
				'css_file'			=> JSLOADER__PLUGIN_URL . '/assets/css/animate.css',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> '',
			),
			'owl.carousel' => array(
				'name'				=> 'Owl Carousel',
				'description'		=> 'jQuery plugin for creating carousel and sliders',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/owl.carousel.min.js',
				'css_file'			=> JSLOADER__PLUGIN_URL . '/assets/css/owl.carousel.css',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> '',
			),
			'jquery.smoothState' => array(
				'name'				=> 'jQuery SmoothState',
				'description'		=> 'jQuery plugin for smooth page transitions',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/jquery.smoothState.js',
				'css_file'			=> '',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> array( '' ),
			),
			'jquery.nicescroll' => array(
				'name'				=> 'jQuery NiceScroll',
				'description'		=> 'jQuery plugin for a nice scroll effect',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/jquery.nicescroll.min.js',
				'css_file'			=> '',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> array( '' ),
			),
			'fullpage' => array(
				'name'				=> 'Full Page',
				'description'		=> 'jQuery plugin for a nice full page scroll',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/jquery.fullPage.js',
				'css_file'			=> '',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> array( '' ),
			),
			'cycle2' => array(
				'name'				=> 'Cycle2 Slider',
				'description'		=> 'jQuery plugin for creating sliders',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/jquery.cycle2.min.js',
				'css_file'			=> '',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> array( '' ),
			),
			'stellar' => array(
				'name'				=> 'Stellar JS',
				'description'		=> 'jQuery plugin for parallax!',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/jquery.stellar.js',
				'css_file'			=> '',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> array( '' ),
			),
			'angular' => array(
				'name'				=> 'Angular JS',
				'description'		=> 'JavaScript framework for creating web applications',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/angular.min.js',
				'css_file'			=> '',
				'dependency_js'		=> array( '' ),
				'dependency_css'	=> array( '' ),
			),
			'okzoom' => array(
				'name'				=> 'OK Zoom',
				'description'		=> 'JavaScript plugin for a nice zoom hover effect',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/okzoom.js',
				'css_file'			=> '',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> array( '' ),
			),
			'okvideo' => array(
				'name'				=> 'OK Video',
				'description'		=> 'JavaScript plugin for embedding youtube video as background',
				'in_footer'			=> true,
				'js_file'			=> JSLOADER__PLUGIN_URL . '/assets/js/okvideo.js',
				'css_file'			=> '',
				'dependency_js'		=> array( 'jquery' ),
				'dependency_css'	=> array( '' ),
			),
		);

		return self::$libraries;
	}

	/**
	 * Load language files
	 */
	public static function plugin_textdomain() {
		load_plugin_textdomain( 'js_loader', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation( $network_wide ) {

	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
	}

}