<?php
/**
 * Includes functions for all admin page templates.
 * functions that add menu pages in the dashboard.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Visualcrossingwfcst_Front {
	
	static $add_script;	
	static $settings;
	var $widget_id;
	var $scripts = array();
	
	/**
	 * Class Construct.
	 *
	 * @since 1.0
	 */	
	public function __construct() {
		
		//Need Ob Started
		add_action('init', array($this,'weather_output_buffer') );
		
		//add shortcode
		add_shortcode( 'weather', array($this,'weather_shortcode_func') );
		
		//Register our scripts
		add_action( 'wp_enqueue_scripts', array($this,'weather_register_scripts'), 999 );		
	}
	
	public function weather_output_buffer(){
		ob_start(); 
	}
	
	public function weather_register_scripts(){
		
		wp_register_style('wfd3-css', VISUALCROSSINGWFCST_ASSETS_URL.'/css/weather-forecast-widget-d3.css');
		wp_register_script('wfd3-js', VISUALCROSSINGWFCST_ASSETS_URL.'js/d3.min.js', array(), false, true);
		
		wp_register_script('wfmd3-js', VISUALCROSSINGWFCST_ASSETS_URL.'js/weather-forecast-widget-d3.js',
										  array(), false, true);
		wp_register_script('wfmsmpl-js', VISUALCROSSINGWFCST_ASSETS_URL.'js/weather-forecast-widget-simple.js', 
		array(), false, true);
		
		wp_enqueue_style('wfd3-css');
		wp_enqueue_script('wfd3-js');
		wp_enqueue_script('wfmd3-js');
		wp_enqueue_script('wfmsmpl-js');
		
		//need single time top of all other inline script.
		wp_add_inline_script('wfmd3-js', 'window.weatherWidgetConfig =  window.weatherWidgetConfig || [];', 'before');
	}
	
	public function weather_shortcode_func($atts){
		
		$default  = array('api'=>'', 'loc'=>'', 'days'=>7, 'mode'=>'simple', 'unit'=>'metric', 'title'=>'', 'stitle'=>'y', 'scond'=>'y');
		$settings = get_option('_visualcrossingwfcst_settings', false);
		if ( is_array($settings) ) {
			$default = array_merge($default, $settings);
		}
		
		
		if( trim($default['api']) == '' ){return '';}
		
		$atts = shortcode_atts( array(
										'loc' => $default['loc'],
										'days' => $default['days'],
										'mode' => $default['mode'],
										'unit' => $default['unit'],
										'title' => $default['title'],
										'showtitle' => $default['stitle'],
										'conditions' => $default['scond'],
									),
								 $atts	
							  );	
		
		$atts['api'] = $default['api'];				  
		self::$add_script  = true;
		$widget_id = uniqid();
		
		$api  = trim($atts['api']);
		$loc  = trim(strtolower($atts['loc']));
		$days = (int)trim($atts['days']);
		if($days <= 0 ){$days=7;}
		
		$mode = trim(strtolower($atts['mode']));
		if( $mode !='simple' && $mode !='d3' ) {
			$mode = 'simple';
		}
		
		$unit = trim(strtolower($atts['unit']));	
		$title = strip_tags(trim($atts['title']));	
		$showtitle = trim(strtolower($atts['showtitle']));
		$conditions = trim(strtolower($atts['conditions']));
		
		if($unit !='us' && $unit !='metric'){
			$unit = 'metric';
		}
		
		
		$include_script = '';
		if( $mode == 'simple' ):
		
			ob_start();
			?>
			window.weatherWidgetConfig.push({
			   selector:".weatherWidget-<?php echo $widget_id; ?>",
			   apiKey:"<?php echo $api; ?>", //lots of usage? Sign up for your personal key
			   location:"<?php echo $loc; ?>", //enter an addres
			   unitGroup:"<?php echo $unit; ?>", //"us" or "metric"
			   forecastDays:<?php echo $days; ?>, //how many days forecast to show
			   title:"<?php echo $title; ?>", //optional title to show in the 
			   showTitle:<?php if($showtitle=='y'){ echo 'true';}else{ echo 'false';} ?>, 
			   showConditions:<?php if($conditions=='y'){ echo 'true';}else{ echo 'false';} ?>
			});
			<?php
			$include_script = ob_get_contents();
			ob_end_clean();
			$this->scripts['simple_'.$widget_id] = $include_script;
			wp_add_inline_script('wfmsmpl-js', $include_script, 'before');
			
		elseif( $mode=='d3' ):
		
			ob_start();
			?>
			window.weatherWidgetConfig.push({
			   selector:".weatherWidget-<?php echo $widget_id; ?>",
			   apiKey:"<?php echo $api; ?>", //lots of usage? Sign up for your personal key
			   location:"<?php echo $loc; ?>", //enter an addres
			   unitGroup:"<?php echo $unit; ?>", //"us" or "metric"
			   forecastDays:<?php echo $days; ?>, //how many days forecast to show
			   title:"<?php echo $title; ?>", //optional title to show in the 
			   showTitle:<?php if($showtitle=='y'){ echo 'true';}else{ echo 'false';} ?>, 
			   showConditions:<?php if($conditions=='y'){ echo 'true';}else{ echo 'false';} ?>
			});
			<?php
			$include_script = ob_get_contents();
			ob_end_clean();
			
			$this->scripts['d3_'.$widget_id] = $include_script;
			wp_add_inline_script('wfmd3-js', $include_script, 'before');
			
		endif;	
						
		return '<div class="weather-forecast-block weatherWidget-'.$widget_id.'"></div>';					  
	}	
}