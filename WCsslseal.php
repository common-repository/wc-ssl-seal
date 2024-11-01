<?php   
/* 
Plugin Name: WooCommerce SSL Seal - WP Fix It
Version: 3.2
Plugin URI: https://www.wpfixit.com
Description: This is the easiest way to display a SSL seal or any custom image of your choice on your WooCommerce checkout page. Great way to show that your checkout process is secure.
Author: WP Fix It
Author URI: https://www.wpfixit.com
License: GPLv2 or later
*/
/**
* Add SSL Seal to checkout page
*/

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function woocommerce_seal_needed_notice() {
		$message = sprintf(
		/* translators: Placeholders: %1$s and %2$s are <strong> tags. %3$s and %4$s are <a> tags */
			esc_html__( '%1$sWooCommerce SSL Seal %2$s requires WooCommerce to function. Please %3$sinstall WooCommerce%4$s.', 'woocommerce_seal' ),
			'<strong>',
			'</strong>',
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'&nbsp;&raquo;</a>'
		);
		echo sprintf( '<div class="error"><p>%s</p></div>', $message );
}
	add_action( 'admin_notices', 'woocommerce_seal_needed_notice' );
	return;
}
//Load up styling for plugin needs
function wc_ssl_seal_css() {
    wp_enqueue_style( 'myCSS', plugins_url( 'wcseal.css', __FILE__ ) );
}
add_action('admin_print_styles', 'wc_ssl_seal_css'); 
//Add Media Uploader
 function wc_ssl_seal_uploader_enqueue() {
    wp_enqueue_media();
    wp_register_script( 'wc_ssl_seal-uploader-js', plugins_url( 'wcsslseal.js' , __FILE__ ), array('jquery') );
    wp_enqueue_script( 'wc_ssl_seal-uploader-js' );
  }
  add_action('admin_enqueue_scripts', 'wc_ssl_seal_uploader_enqueue');
//Add SSL Seal Tab in WC Settings
class WC_SSL_Seal_Tab {
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_ssl-seal', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_ssl-seal', __CLASS__ . '::update_settings' );
    }
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['ssl-seal'] = __( 'SSL Seal', 'wc_settings_ssl_seal_tab' );
        return $settings_tabs;
    }
    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }
    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }
    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {
       //$settings_sslseal = $settings;
       
       // Preview SSL Seal Image
		$settings_sslseal[] = array( 
		'name' => __( '', 'text-domain' ), 
		'type' => 'title', 
		'desc' => __( '<div id="seal-logo"><a href="https://www.sslshopper.com/ssl-checker.html#hostname='. get_option( 'wcsealdomain' ) . '" TARGET="_blank"><img style="width: 180px; margin-right: 33px; float:left; height: auto;" alt="This Site is Secure" src="'.get_option( 'wcsealdomain_image' ) .'"></a></div>', 'text-domain' ), 
		'id' => 'wcsslsealpreviewimage' );
		
		// Preview SSL Seal Embed
		$settings_sslseal[] = array( 
		'name' => __( '', 'text-domain' ), 
		'type' => 'title', 
		'desc' => __( '<p>'. get_option( 'wcsealdomain_embed' ) . '</p>', 'text-domain' ), 
		'id' => 'wcsslsealpreviewembed' );
		
		// Add Title to the Settings
		$settings_sslseal[] = array( 
		'name' => __( 'WC SSL Seal Settings', 'text-domain' ), 
		'type' => 'title', 
		'desc' => __( 'The following options are used to configure WC SSL Seal brought to you by <a href="https://www.wpfixit.com/" target="_blank"><strong> WP Fix It</strong></a>', 'text-domain' ), 
		'id' => 'wcsslseal' );
		
		// Add first checkbox option
		$settings_sslseal[] = array(
			'name'     => __( 'Secured Domain' ),
			'desc_tip' => __( 'Enter SSL Domain', 'text-domain' ),
			'id'       => 'wcsealdomain',
			'type'     => 'text',
			'css'      => 'min-width:300px;',
			'desc'     => __( '<br>Example: DOMAIN.COM (no http:// or https://)', 'text-domain' ),
		);
		// Add second text field option
		$settings_sslseal[] = array(
			'name'     => __( 'SSL Custom Image', 'text-domain' ),
			'desc_tip' => __( 'Upload Your Custom SSL Seal Image', 'text-domain' ),
			'id'       => 'wcsealdomain_image',
			'type'     => 'text',
			'css'      => 'min-width:500px;',
			'desc'     => __( '', 'text-domain' ),
		);
		
		// Add third embed field option
		$settings_sslseal[] = array(
			'name'     => __( 'SSL Embed Code', 'text-domain' ),
			'desc_tip' => __( 'This can be used for your SSL certificate embed code.', 'text-domain' ),
			'id'       => 'wcsealdomain_embed',
			'type'     => 'textarea',
			'css'      => 'min-width:500px;',
			'desc'     => __( '' ),
		);
	
		
		$settings_sslseal[] = array( 'type' => 'sectionend', 'id' => 'wcsslseal' );
		return $settings_sslseal;
		
        return apply_filters( 'wc_ssl_seal_tab_settings', $settings );
    }
}
function ssl_seal_admin_css() {
echo '<style>input#wcsealdomain_image {margin-right: 23px}</style>';
if(empty(get_option('wcsealdomain_image')))
echo '<style>div#seal-logo{display:none}</style>';
}
add_action('admin_head', 'ssl_seal_admin_css');
WC_SSL_Seal_Tab::init();
//Add SSL Seal to Checkout Page
function wcss_add_seal_to_checkout() {    if(!empty(get_option('wcsealdomain_image')))
    echo '<div class="alignright">
    <a href="https://www.sslshopper.com/ssl-checker.html#hostname='. get_option( 'wcsealdomain' ) . '" TARGET="_blank"><img style="width:100%; height: auto; max-width:150px;" alt="This Site is Secure" src="'.get_option( 'wcsealdomain_image' ) .'"></a>
    </div>';
    
    echo '<div class="alignright">'. get_option( 'wcsealdomain_embed' ) . '</div>';
}
add_action( 'woocommerce_review_order_before_payment', 'wcss_add_seal_to_checkout' );/* Activate the plugin and do something. */
register_activation_hook( __FILE__, 'woocommerce_seal_welcome_message' );
function woocommerce_seal_welcome_message() {
set_transient( 'woocommerce_seal_welcome_message_notice', true, 5 );
}
add_action( 'admin_notices', 'woocommerce_seal_welcome_message_notice' );
function woocommerce_seal_welcome_message_notice(){
/* Check transient, if available display notice */
if( get_transient( 'woocommerce_seal_welcome_message_notice' ) ){
?>
<div class="updated notice is-dismissible">
	<style>div#message {display: none}</style>
<p>&#127881; <strong>WP Fix It - WooCommerce SSL Seal</strong> has been activated and you now can show shoppers your checkout is secure.
<br>
<br><a href="<?php echo get_admin_url(null, 'admin.php?page=wc-settings&tab=ssl-seal') ?>"><b>CLICK HERE</b></a> to setup your WooCommerce SSL seal.</p>
</div>
<?php
/* Delete transient, only display this notice once. */
delete_transient( 'woocommerce_seal_welcome_message_notice' );
}
}
/* Activate the plugin and do something. */
function woocommerce_seal_plugin_action_links( $links ) {
$links = array_merge( array(
'<a href="' . esc_url( admin_url( '/admin.php?page=wc-settings&tab=ssl-seal' ) ) . '">' . __( '<b>Settings</b>', 'textdomain' ) . '</a>'
), $links );
$links = array_merge( array(
'<a href="https://www.wpfixit.com/" target="_blank">' . __( '<span id="p-icon" class="dashicons dashicons-awards"></span> <span class="ticket-link" >GET HELP</span>', 'textdomain' ) . '</a>'
), $links );
return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woocommerce_seal_plugin_action_links' );