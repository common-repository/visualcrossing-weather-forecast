<?php
/**
 * Includes functions for all admin page templates.
 * functions that add menu pages in the dashboard.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Visualcrossingwfcst_Admin {
	
	/**
	 * Class Construct.
	 *
	 * @since 1.0
	 */	
	public function __construct() {

		//Admin Menu
		add_action( 'admin_menu', array($this, 'visualcrossingwfcst_admin_menu'), 9 );

		
		//Add content link
		add_filter( "plugin_action_links", array($this, 'visualcrossingwfcst_add_context_link'), 10, 2 );
	}
	
	public function visualcrossingwfcst_add_context_link( $links, $file ){
		
		if($file == plugin_basename(VISUALCROSSINGWFCST_PLUGIN_FILE) && function_exists('admin_url') ) {
		$settings_link = '<a href="'.admin_url('options-general.php?page=visualcrossingwfcst').'">'.esc_html__('Settings', 'visualcrossingwfcst-text-domain').'</a>';
		array_unshift( $links, $settings_link );
		}
		return $links;
	}
	
	
	public function visualcrossingwfcst_admin_menu(){
	
		$label = esc_html__('Weather Forecast Settings', 'visualcrossingwfcst-text-domain');
				
		add_options_page(
			$label, // page_title
			$label, // menu_title
			'manage_options', // capability
			'visualcrossingwfcst', // menu_slug
			array( $this, 'visualcrossingwfcst_admin_settings_page' ) // function
		);
		
		
	}
	public function visualcrossingwfcst_admin_settings_page(){
		
		$message = '';
		if (isset($_POST['visualcrossingwfcst_settings'])) {
			
			if( wp_verify_nonce( $_POST['visualcrossingwfcst_settings'], "visualcrossingwfcst-api-setting" ) ){
				
				$data_array = (array)($_POST['visualcrossingwfcst']);
				$data = array();
				
				//Sanitize User Input
				$data['api'] = sanitize_text_field($data_array['api']);
				$data['loc'] = sanitize_text_field($data_array['loc']);
				$data['mode'] = sanitize_text_field($data_array['mode']);
				$data['days'] = intval($data_array['days']);
				$data['title'] = sanitize_text_field($data_array['title']);
				$data['unit'] = sanitize_text_field($data_array['unit']);
				$data['stitle'] = sanitize_text_field($data_array['stitle']);
				$data['scond'] = sanitize_text_field($data_array['scond']);
				
				//Validate Data
				if( $data['days'] <= 0 ){
					$data['days'] = 7;	
				}
				if($data['mode'] !='simple' && $data['mode'] !='d3'){
					$data['mode'] = 'simple';	
				}
				if($data['unit'] !='us' && $data['unit'] !='metric'){
					$data['unit'] = 'metric';	
				}
				if($data['stitle'] !='y' && $data['stitle'] !='n'){
					$data['stitle'] = 'y';	
				}
				if($data['scond'] !='y' && $data['scond'] !='n'){
					$data['scond'] = 'y';	
				}
				
				//Only save if API Key provided
				if(''!=trim($data['api'])){
					
					$ack = update_option('_visualcrossingwfcst_settings', $data);
					$message = '<div id="message" class="updated notice notice-success"><p>'.esc_html__('Settings Updated.', 'visualcrossingwfcst-text-domain').'</p></div>';
				
				}
				else{
					$message = '<div id="message" class="notice notice-error"><p>'.esc_html__('API Key must not be blank.', 'visualcrossingwfcst-text-domain').'</p></div>';
				}
			
				
			}else{ //Nonce validate failed
				
				$message = '<div id="message" class="notice notice-error"><p>'.esc_html__('Error Saving Settings.', 'visualcrossingwfcst-text-domain').'</p></div>';
			}
		}
		
		$label = esc_html__('Weather Forecast Settings', 'visualcrossingwfcst-text-domain');
		
		$default = array('api'=>'', 'loc'=>'', 'days'=>7, 'mode'=>'simple', 'title'=>'', 'unit'=>'metric', 'stitle'=>'y', 'scond'=>'y');
		$settings = get_option('_visualcrossingwfcst_settings', false);
		
		if(is_array($settings)){
			$default = array_merge($default, $settings);
		}
	?>	
    <style type="text/css">
	#visualcrossingwfcst-wrap .flex-container {
		display: flex;
	}
	#visualcrossingwfcst-wrap .flex-child {
		flex: 1;
	}  
	#visualcrossingwfcst-wrap .flex-child:first-child {
		margin-right: 10px;
	} 
	#visualcrossingwfcst-wrap  .info-block{
		border: 1px solid #ccc;
	}
	#visualcrossingwfcst-wrap h2.the-head-label img{vertical-align: text-top;}
	#visualcrossingwfcst-wrap .onthe-border-label{
		text-align:center;
	}
	#visualcrossingwfcst-wrap .onthe-border-label::after {
		content: '';
		display: block;
		flex: 1;
		height: 1px;
		background-color: #ccc;
		margin-left: 5px;
		margin-right: 5px;
	}
	#visualcrossingwfcst-wrap .dashicons{
		font-size: 16px;
		margin-top: 3px;
		color: #228B22;
		opacity: 0.5;
	}
	#visualcrossingwfcst-wrap .pleft{
		padding-left:15px;	
	}
	#visualcrossingwfcst-wrap code{background-color: #EEE8AA;}
	#visualcrossingwfcst-wrap table{width: 100%;}
	#visualcrossingwfcst-wrap .regular-text, #visualcrossingwfcst-wrap .regular-select{
		width: 100%;
		max-width: 360px;
	}
	#visualcrossingwfcst-wrap .submit{
		margin-left: 10px;
	}
	#visualcrossingwfcst-wrap .example{
		margin-bottom: 5px;
		display: inline-block;
		width:100%;
	}
	#visualcrossingwfcst-wrap .example.ex1{margin-top:5px;}
	#visualcrossingwfcst-wrap td.ib{display:inline-block; margin-right:25px;}
	#visualcrossingwfcst-wrap .note-icon{font-size:20px;vertical-align: sub;opacity: 0.7;}
	#visualcrossingwfcst-wrap .note-head{border-bottom: 1px solid #ccc;padding-bottom: 3px;}
	
	#visualcrossingwfcst-wrap .hide{display:none !important;}
	</style>
	<div class="wrap" id="visualcrossingwfcst-wrap">
        <h2 class="the-head-label"><img src="<?php echo VISUALCROSSINGWFCST_ASSETS_URL; ?>/images/icon-34.png" title="visualcrossing.com"> <?php echo $label; ?> <small>(visualcrossing.com)</small></h2>
        <hr/>
        <p></p>
        <?php echo $message; ?>
        <div class="flex-container">
		<div class="flex-child left-block settings-block">
        <form method="post" action="options-general.php?page=visualcrossingwfcst">
              
      <table>
      <tr valign="top">
      <th scope="row"></th>
      <td>
      <h3><?php _e('API Key', 'visualcrossingwfcst-text-domain'); ?></h3>
      <input type="text" id="visualcrossingwfcst_api" class="regular-text" name="visualcrossingwfcst[api]" 
      value="<?php echo $default['api']; ?>" placeholder="<?php _e('Put your API key here.', 'visualcrossingwfcst-text-domain'); ?>" required="required"/>
      </td>
      </tr>
      
      <tr valign="top">
      <th scope="row"></th>
      <td>
      <h3><?php _e('Location', 'visualcrossingwfcst-text-domain'); ?></h3>
      <input type="text" id="visualcrossingwfcst_loc" class="regular-text" name="visualcrossingwfcst[loc]" 
      value="<?php echo $default['loc']; ?>" placeholder="<?php _e('ex. London, UK', 'visualcrossingwfcst-text-domain'); ?>" required="required"/>
      </td>
      </tr>
      
      <tr valign="top">
      <th scope="row"></th>
      <td>
      <h3><?php _e('Mode', 'visualcrossingwfcst-text-domain'); ?></h3>
      <select name="visualcrossingwfcst[mode]" id="display-mode-select" class="regular-select">
      <option <?php if($default['mode']=='simple'){ echo 'selected="selected"'; }?> value="simple">Simple</option>
      <option <?php if($default['mode']=='d3'){ echo 'selected="selected"'; }?> value="d3">D3</option>
      </select>
      </td>
      </tr>
      
      <tr valign="top" class="mode-simple <?php if($default['mode']=='d3'){ echo 'hide'; }?>">
      <th scope="row"></th>
      <td>
      <h3><?php _e('Forecast Days', 'visualcrossingwfcst-text-domain'); ?></h3>
      <input type="number" id="visualcrossingwfcst_fd" class="regular-text" name="visualcrossingwfcst[days]" 
      value="<?php echo $default['days']; ?>"/>
      </td>
      </tr>
      
      <tr valign="top" class="mode-simple <?php if($default['mode']=='d3'){ echo 'hide'; }?>">
      <th scope="row"></th>
      <td>
      <h3><?php _e('Title', 'visualcrossingwfcst-text-domain'); ?></h3>
      <input type="text" id="visualcrossingwfcst_title" class="regular-text" name="visualcrossingwfcst[title]" 
      value="<?php echo $default['title']; ?>"/>
      </td>
      </tr>
      
      <tr valign="top">
      <th scope="row"></th>
      <td class="ib">
      <h3><?php _e('Unit Group', 'visualcrossingwfcst-text-domain'); ?></h3>
      <select name="visualcrossingwfcst[unit]">
      <option <?php if($default['unit']=='metric'){ echo 'selected="selected"'; }?> value="metric">Metric</option>
      <option <?php if($default['unit']=='us'){ echo 'selected="selected"'; }?> value="us">US</option>
      </select>
      </td>
      <td class="ib">
      <h3><?php _e('Show Title', 'visualcrossingwfcst-text-domain'); ?></h3>
      <select name="visualcrossingwfcst[stitle]">
      <option <?php if($default['stitle']=='y'){ echo 'selected="selected"'; }?> value="y">Yes</option>
      <option <?php if($default['stitle']=='n'){ echo 'selected="selected"'; }?> value="n">No</option>
      </select>
      </td>
      <td class="ib">
      <h3><?php _e('Show Conditions', 'visualcrossingwfcst-text-domain'); ?></h3>
      <select name="visualcrossingwfcst[scond]">
      <option <?php if($default['scond']=='y'){ echo 'selected="selected"'; }?> value="y">Yes</option>
      <option <?php if($default['scond']=='n'){ echo 'selected="selected"'; }?> value="n">No</option>
      </select>
      </td>
      </tr>
      
      
      
      </table>
      <p class="submit">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Settings">
      </p>
      
      <input type="hidden" name="visualcrossingwfcst_settings" value="<?php echo wp_create_nonce('visualcrossingwfcst-api-setting'); ?>"/>
      </form>
        </div>
        <div class="flex-child right-block info-block">
        
        <div class="onthe-border-label"> 
        <i class="dashicons dashicons-info"></i> 
		<?php echo esc_html__('Shortcode Information', 'visualcrossingwfcst-text-domain' );?> 
        </div>
        
        <p class="pleft"><?php echo esc_html__('Copy this shortcode and put in the page or post to display Weather Forecast.', 'visualcrossingwfcst-text-domain'); ?></p>
        <h4 class="pleft">Shortcode: <code>[weather]</code></h4>
        
        <div class="pleft">
        <span class="note-icon">&#9728;</span>
		<span class="note-head"><?php echo esc_html__('You can override default settings by using shortcode parameters', 'visualcrossingwfcst-text-domain'); ?></span> 
        <ul>
        <li><strong>loc:</strong> Location for Weather Forecast.</li>
        <li><strong>days:</strong> 3</li>
        <li><strong>mode:</strong> "Simple" or "D3"</li>
        <li><strong>title:</strong> Set Title.</li>
        <li><strong>showtitle:</strong> "Yes" or "No".</li>
        <li><strong>unit:</strong> "US" or "Metric".</li>
        <li><strong>conditions:</strong> "Yes" or "No".</li>
        </ul>
        
		<span class="example ex1">Ex.<strong><code>[weather loc="London, UK" days="4" mode="D3" title="London,UK Weather" unit="Metric" showtitle="Yes" conditions="No"]</code></strong></span>
                
        </div>
        
        <br/>
        <div class="pleft">
        <small><strong>Q. </strong><?php echo esc_html__('How to insert Shortcode in WordPress ?','visualcrossingwfcst-text-domain'); ?> <br/><strong>A. </strong><a href="https://www.youtube.com/watch?v=DvGZAsCnmpY" target="_blank"><?php echo esc_html__('Check This Video Guide','visualcrossingwfcst-text-domain'); ?></a></small>
        </div>
        
        </div>
        </div>
    </div>
    <script type="text/javascript">	
jQuery(function($) {
'use strict';
$('#display-mode-select').on('change', function() {
  if(this.value=='d3'){
	$('tr.mode-simple').addClass('hide');  
  }else{
	$('tr.mode-simple').removeClass('hide');   
  }
});
});
	</script>				
	<?php	
	}
}