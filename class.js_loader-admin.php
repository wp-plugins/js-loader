<?php

class Js_Loader_Admin {
	/**
	 * @var Js_Loader_Admin
	 **/
	private static $instance = null;

	static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Js_Loader_Admin;
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Enqueue styles
	 */
	public function admin_styles() {
		global $wp_scripts;
		$screen = get_current_screen();

		wp_enqueue_style( 'js_loader_admin_styles', plugins_url( 'assets/css/admin.css', __FILE__ ) );
	}

	/**
	 * Enqueue styles
	 */
	public function admin_scripts() {
		global $wp_scripts;
		$screen = get_current_screen();

		wp_enqueue_script( 'js_loader_admin_scripts', plugins_url( 'assets/js/admin.js', __FILE__ ) );
	}

	public function plugin_menu(){
		add_options_page( 
			'JS Loader Settings', 
			'JS Loader', 
			'manage_options', 
			'js_loader', 
			array( $this, 'plugin_page' ) 
		);
	}

	public function plugin_page(){ ?>
		<div class="wrap">
			<h2><?php esc_html_e( 'JS Loader Settings', 'js_loader' ); ?></h2>
			<form method="post" action="options.php">
	    		<?php
	                // This prints out all hidden setting fields
	                settings_fields( 'jsloader_option-group' );   
	                do_settings_sections( 'js_loader_settings' );
	                submit_button(); 
	            ?>
	    	</form>
		</div>
		
	<?php
	}

	public function page_init(){
		register_setting(
            'jsloader_option-group', // Option group
            'jsloader_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

		add_settings_section( 
			'jsloader_settings_section', 
			esc_html__( 'My Custom Settings', 'js_loader' ), 
			'__return_false', 
			'js_loader_settings'  //Page slug
		);

		foreach ( Js_Loader::get_libraries() as $key => $library ) {
			add_settings_field(
	            'jsloader_' . $key, // ID
	            '<h2>' . $library[ 'name' ] . '</h2>' . '<br><em>' . $library[ 'description'] . '</em>', // Description, // Title
	            array( $this, 'field_callback' ), // Callback
	            'js_loader_settings', //Page slug
	            'jsloader_settings_section', // Section
	            $key
	        );
		}

	}

	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ){
    	
    	foreach ($input as $key => $value) {
    		$input[ $key ] = absint( $value );
    	}

    	return $input;
    }

	/** 
     * Get the settings option array and print one of its values
     */
    public function field_callback( $field ){
        
        $option = get_option( 'jsloader_option', array() );
        $checked = isset( $option[$field] )? $option[$field] : 0;
        
		printf(
            "<fieldset>
				<label title='enable'><input type='radio' name='jsloader_option[$field]' value='1' ".checked( $checked, 1, false ).">Enable</label>
				<label title='disable'><input type='radio' name='jsloader_option[$field]' value='0' ".checked( $checked, 0, false ).">Disable</label>
            </fieldset>"
        );
    }
}
Js_Loader_Admin::init();