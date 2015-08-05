<?php
class Js_Loader {

	/**
	 * Holds the singleton instance of this class
	 * @since 0.0.1
	 * @var Js_Loader
	 */
	static $instance = false;

	static $libraries = array();

	public static function json_source() {
		if (!file_exists(plugin_dir_path( __FILE__ ) .  '/libraries/jsdelivr.json')) {
			file_put_contents(plugin_dir_path( __FILE__ ) . '/libraries/jsdelivr.json', 
				file_get_contents('http://api.jsdelivr.com/v1/jsdelivr/libraries')
			);
		}
		return JSLOADER__PLUGIN_URL . '/libraries/jsdelivr.json';
	}
	
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
				add_action( 'plugins_loaded', array( __CLASS__, 
					'plugin_textdomain' ), 99 );
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

	private static function rglob($pattern, $flags = 0) {
	    $files = glob($pattern, $flags); 
	    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
	        $files = array_merge($files, self::rglob($dir.'/'.basename($pattern), $flags));
	    }
	    return $files;
	}

	/**
	 * Download the scripts and enqueue it
	 */	
	public function load_scripts(){

		//Get the JSON
		$libraries = self::get_remote_libraries();

		//Get the options from database
		$options = get_option( 'jsloader_option', array() );

		//Get zip files if options equals enqueued
		foreach ( $options as $key => $value ) {
			if( 1 == $value ){
				$name = $libraries[$key]['name'];
				$locationname = plugin_dir_path( __FILE__ ) . '/libraries/' . $name;
				$zipfile = $locationname . '.zip';

				//create url to get ZIP from jsdelivr
				$zipurl = 'http://cdn.jsdelivr.net/'
				.	$name . '/latest/' . $name . '.zip';

				//Get file from CDN and write it to libraries folder
				if(!file_exists($locationname)){
					file_put_contents($zipfile, file_get_contents($zipurl));
					if (filesize ( $zipfile ) == 0) {
						unlink($zipfile);
					}
				}

				//extract the ZIP
				$zip = new ZipArchive;
				if ($zip->open($zipfile) === TRUE) {
				    $zip->extractTo($locationname);
				    $zip->close();
					if(file_exists($zipfile)){
						unlink($zipfile);
					}
				}

				//Get all files extracted
				$files = self::rglob(plugin_dir_path( __FILE__ ) . '/libraries/' . $name . '/*', GLOB_NOSORT);

				//Convert it to Plugin URL
				$files = str_replace(plugin_dir_path( __FILE__ ), JSLOADER__PLUGIN_URL, $files);

				//Get the extracted files
				if (is_array($files)){
					foreach ($files as $location) {

						//Enqueue jquery plugin after jquery
						if (pathinfo($location, PATHINFO_EXTENSION) == 'js' && strpos($location,'min.js') == false && strpos($location,'jquery') !== false){
							wp_enqueue_script( basename($location), $location, array('jquery'));
						}

						//Enqueue un-minified scripts only 
						if (pathinfo($location, PATHINFO_EXTENSION) == 'js' && strpos($location,'min.js') == false){
							wp_enqueue_script( basename($location), $location);
						}

						//Enqueue un-minified CSS only
						if (pathinfo($location, PATHINFO_EXTENSION) == 'css' && strpos($location,'min.css') == false){
							wp_enqueue_style( basename($location), $location);
						}
					}
				}
			
			} else {
				// If not enqueued
				if (is_array($files)){
					foreach ($files as $location) {
						
						//Set path for deleting file
						$name = $libraries[$key]['name'];
						$locationdir = plugin_dir_path( __FILE__ ) . '/libraries/' . $name;

						//Delete it!!!!
						if(file_exists($location)){
							unlink($location);
							rmdir($locationdir);
						}
					}
				}
			}
		}
		// exit;
	}

	/**
	 * Get Libraries from JSON
	 */
	public static function get_remote_libraries(){
		$json = json_decode(file_get_contents(self::json_source()), true);
		self::$libraries = $json;
		return self::$libraries;
	}

	/**
	 * Load language files
	 */
	public static function plugin_textdomain() {
		load_plugin_textdomain( 'js_loader', false, 
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } 
	 * by register_activation_hook()
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