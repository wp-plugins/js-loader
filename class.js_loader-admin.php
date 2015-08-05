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

		add_action( 'update_option_jsloader_option', array( $this, 'jsloader_option_save_callback' ), 10, 2 );
	}

	function jsloader_option_save_callback( $old_value, $new_value ){
	    // var_dump($new_value);exit;
	}

	/**
	 * Enqueue styles
	 */
	public function admin_styles() {
		global $wp_scripts;
		$screen = get_current_screen();

		wp_enqueue_style( 'js_loader_admin_styles', 
			plugins_url( 'assets/css/admin.css', __FILE__ ) );
	}

	/**
	 * Enqueue scripts
	 */
	public function admin_scripts() {
		global $wp_scripts;
		$screen = get_current_screen();

		wp_enqueue_script( 'js_loader_admin_scripts', 
			plugins_url( 'assets/js/admin.js', __FILE__ ) );
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
		<div class="wrap jsl-wrapper">
			<form method="post" action="options.php">
	    		<?php
	    			self::plugin_filter();
	                // This prints out all hidden setting fields
	                settings_fields( 'jsloader_option-group' );   
	                do_settings_sections( 'js_loader_settings' );
	                submit_button(); 
	            ?>
	    	</form>
		</div>
		
	<?php
	}

	public function plugin_filter() {
		echo 
			'<button id="activated_plugin" class="button button-primary">
				Show Activated Plugin
			</button>'
		;
		echo '<input id="plugin_filter" type="text" 
				placeholder="Filter Plugin Name">'
		;
	}

	public function page_init(){
		register_setting(
            'jsloader_option-group', // Option group
            'jsloader_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

		add_settings_section( 
			'jsloader_settings_section', 
			esc_html__( 'JS Loader Settings', 'js_loader' ), 
			'__return_false', 
			'js_loader_settings'  //Page slug
		);

		$show_files = 0;

		foreach ( Js_Loader::get_remote_libraries() as $key => $library ) {
			$details = '<h4>' . $library['name'] . '</h4>';
			$details .= '<div class="details">';
			foreach ($library as $detail_name => $detail_value) {
				if ( is_array($detail_value) || $detail_name == '$loki' || $detail_value == '') {
					continue;
				} elseif ($detail_name == 'homepage' || $detail_name == 'github') {
					$details .= '<div class="detail_name">' . $detail_name . '</div>';
					$details .= '<div class="detail_value"><a href="' . $detail_value . '" target="_blank">' . $detail_value . '</a></div>';					
				}else {
					$details .= '<div class="detail_name">' . $detail_name . '</div>';
					$details .= '<div class="detail_value">' . $detail_value . '</div>';
				}
			}
			if ($show_files){
				if (is_array($library['assets'][0]['files'])){
					foreach ($library['assets'][0]['files'] as $fileindex => $url) {
							$details .= '<div class="detail_name">file ' . ($fileindex + 1) . '</div>';
							$details .= '<div class="detail_value">' . $url . '</div>';
					}
				}
			}
			$details .= $library['files'];
			$details .= '</div>';
			add_settings_field(
	            'jsloader_' . $key,
				
				$details, 
	          
	            array( $this, 'field_callback' ), 
	            
	            'js_loader_settings',
	           
	            'jsloader_settings_section',
	           
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
				<label title='enable'>
					<input type='radio' name='jsloader_option[$field]' value='1' 
						" . checked( $checked, 1, false ) .">
					Enqueue
				</label>
				<label title='disable'>
					<input type='radio' name='jsloader_option[$field]' value='0'
						" . checked( $checked, 0, false ) .">
					Dequeue
				</label>
            </fieldset>"
        );
    }
}
Js_Loader_Admin::init();