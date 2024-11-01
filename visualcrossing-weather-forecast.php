<?php
/**
 * Plugin Name: Visualcrossing Weather Forecast
 * Plugin URI:  https://www.visualcrossing.com
 * Description: Display Weather Forecast using visualcrossing.com Weather API.
 * Version:     1.0.1
 * Author:      srhelwig
 * Author URI:  mailto:contact@visualcrossing.com?subject=Visualcrossing Weather Forecast WordPress plugin
 * Copyright:   2020 visualcrossing.com
 *
 * Text Domain: visualcrossingwfcst-text-domain
 * Domain Path: /languages/
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Visualcrossingwfcst_Start{

	protected static $instance;
	
	static $requirements_error = array();
	
	var $admin_instance = false;
	var $front_instance = false;
		
	/**
	 * Class Construct.
	 *
	 * @since 1.0
	 */	
	public function __construct() {
		
		//Define Defaults
		$this->define_defaults();
		
		//Load plugin languages
		add_action( 'plugins_loaded', array( $this, 'plugin_text_domain') );
		
		//Load plugin requirements so can be accessed/checked
		add_action( 'plugins_loaded', array( $this, 'requirements_load'), 10 );
			
		//On plugin activation | Dont activate if requirements not met
		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
		
		// Admin end: We need to inform user if requirements error
		add_action( 'admin_notices',  array( $this, 'admin_requirement_check' ) );
		
		//Initiate Plugin		
		add_action( 'plugins_loaded', array( $this, 'plugin_init'), 15 );
	}
	
	
	
	/**
	 * Define Constants and other defaults
	 *
	 * @param  bool $network_wide is a multisite network activation
	 *
	 * @since  1.0
	 */
	protected function define_defaults(){
		
		// Plugin Name.
		if ( ! defined( 'VISUALCROSSINGWFCST_PLG_NAME' ) ) {
			define( 'VISUALCROSSINGWFCST_PLG_NAME', 'Visualcrossing Weather Forecast' );
		}
		

		if ( ! defined( 'VISUALCROSSINGWFCST_PLUGIN_FILE' ) ) {
			define( 'VISUALCROSSINGWFCST_PLUGIN_FILE', __FILE__);
		}			
		// Plugin Folder Path.
		if ( ! defined( 'VISUALCROSSINGWFCST_PLUGIN_DIR' ) ) {
			define( 'VISUALCROSSINGWFCST_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
		// Plugin Folder URL.
		if ( ! defined( 'VISUALCROSSINGWFCST_PLUGIN_URL' ) ) {
			define( 'VISUALCROSSINGWFCST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
		// Plugin Assets Folder URL.
		if ( ! defined( 'VISUALCROSSINGWFCST_ASSETS_URL' ) ) {
			define( 'VISUALCROSSINGWFCST_ASSETS_URL', VISUALCROSSINGWFCST_PLUGIN_URL.'assets/' );
		}
		// Plugin Base Name
		if ( ! defined( 'VISUALCROSSINGWFCST_PLUGIN_BASENAME' ) ) {
			define( 'VISUALCROSSINGWFCST_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}
	
		// Plugin Required Constants
		if ( ! defined( 'VISUALCROSSINGWFCST_PHP_VERSION_REQUIRED' ) ) {
			define( 'VISUALCROSSINGWFCST_PHP_VERSION_REQUIRED', '5.6.0' );
		}
		if ( ! defined( 'VISUALCROSSINGWFCST_WP_VERSION_REQUIRED' ) ) {
			define( 'VISUALCROSSINGWFCST_WP_VERSION_REQUIRED', '4.4' );
		}
		
	}
	
	/**
	 * Check dependency
	 *
	 * @param  bool $network_wide is a multisite network activation
	 *
	 * @since  1.0
	 */
	public function plugin_activation() {
		
		$requirement_error = self::requirements_check();
		
		if($requirement_error !== false){
			
			self::deactivate_plugin();
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
			wp_die(implode("\r\n",$requirement_error));
		}
	}
	
	/**
	 * Check dependency & display error in admin area.
	 *
	 * @since  1.0
	 */
	public function admin_requirement_check() {
		
		$requirement_error = self::$requirements_error;
		
		$class = 'notice notice-error';
	
		if(!empty($requirement_error)){
			foreach($requirement_error as $notice){
				printf( '<div class="%1$s"><p><strong>%2$s:</strong> %3$s</p></div>', $class, VISUALCROSSINGWFCST_PLG_NAME, $notice);
			}
		}
		
	}
		
	/**
	 * load plugin files
	 *
	 * @since  1.0
	 */
	public function plugin_init() {
		
		//Dont initiate plugin if requirement error
		$requirement_error = self::$requirements_error;
		if( $requirement_error !== false ){
			return;	
		}
			
		// Load admin
		if ( is_admin() ) {

			require_once trailingslashit( VISUALCROSSINGWFCST_PLUGIN_DIR ) . 'admin/main.php';
			
			//create admin instance
			$this->admin_instance = new Visualcrossingwfcst_Admin();
		}
		
		// Load Front 
		if ( ! is_admin() ) {
			
			require_once trailingslashit( VISUALCROSSINGWFCST_PLUGIN_DIR ) . 'front/main.php';
			
			//create front instance
			$this->front_instance = new Visualcrossingwfcst_Front();
		}
	}
	
	/**
	 * load plugin text Domain
	 *
	 * @since  1.0
	 */
	public function plugin_text_domain() {
		load_plugin_textdomain( 'visualcrossingwfcst-text-domain', false, basename( dirname(__FILE__) ) . '/languages' );
    }
	
	/**
	 * Deractivate the plugin
	 *
	 * @since  1.0
	 */
	public static function deactivate_plugin() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	
	/**
	* Check the plugin requirements 
	* Return error message array | false if no error
	*
	* @since  1.0
	*/
	public static function requirements_check() {
	
		$requirement_error = array();
		//PHP Version check
		if( version_compare(PHP_VERSION, VISUALCROSSINGWFCST_PHP_VERSION_REQUIRED, '<') ){
			$msg = sprintf( 'Minimum PHP version required is %1$s but you are running %2$s.', VISUALCROSSINGWFCST_PHP_VERSION_REQUIRED, PHP_VERSION );
			$requirement_error['php_version'] =  esc_html__( $msg, 'visualcrossingwfcst-text-domain');
		}
		//WP Version check
		if( version_compare(get_bloginfo('version'), VISUALCROSSINGWFCST_WP_VERSION_REQUIRED, '<') ){
			$msg = sprintf( 'Minimum WordPress version required is %1$s but you are running %2$s.', VISUALCROSSINGWFCST_WP_VERSION_REQUIRED, get_bloginfo('version') );	
			$requirement_error['wp_version'] =  esc_html__( $msg, 'visualcrossingwfcst-text-domain');
		}
		
		
		if( empty($requirement_error) ) {
            return false;
        }
		else{
            return $requirement_error;
        }
	}
	
	
	public function requirements_load(){
		
		self::$requirements_error = self::requirements_check();
	}
	
	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'visualcrossingwfcst-text-domain' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'visualcrossingwfcst-text-domain' ), '1.0' );
	}


	/**
	 * Returns the class instance.
	 *
	 * @since  1.0
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

// ok! ready to go.
function visualcrossingwfcst() { 
	return Visualcrossingwfcst_Start::instance();
}
visualcrossingwfcst();