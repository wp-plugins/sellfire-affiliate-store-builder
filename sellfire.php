<?php

/*
 * Plugin Name: SellFire Affiliate Store Builder
 * Plugin URI: http://www.sellfiredev.com/Features/AffiliateWordPressPlugin
 * Description: SellFire's store builder allows word press users to easily embed affiliate products into their blog. 
 * Author: Jason MacInnes
 * Author URI: http://www.jasonmacinnes.com
 * License: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 */
register_activation_hook(__FILE__, 'jem_sf_install');

//adds the menu
add_action('admin_menu', 'jem_sf_add_menus');

//registers the settings
add_action( 'admin_init', 'jem_sf_register_settings' );

//registers the cache flush ajax handler
add_action ( 'wp_ajax_jem_sf_flush_cache', 'jem_sf_flush_cache' );

//strips out store tags
//add_filter( 'the_content', 'jem_sf_replace_store_tags' );

//register a shortcode
add_shortcode( 'sellfire', 'jem_sf_sellfire_shortcode' );

//SF Domain
define ( 'JEM_SF_DOMAIN', 'http://www.sellfire.com' );

//SF API Domain
define ( 'JEM_SF_API_DOMAIN', 'https://www.sellfire.com' );

//url of the SF API 
define( 'JEM_SF_API_URL', JEM_SF_API_DOMAIN . '/Api/' );

//define a constant to the JS directory
define( 'JEM_SF_INSERTJS', plugin_dir_url(__FILE__).'js' );

//define a constant to the CSS directory
define( 'JEM_SF_INSERTCSS', plugin_dir_url(__FILE__).'css' );


//include the definition of the post meta box
include( 'includes/post-meta-box.php' );

/*
 * Installs the SellFire plug-in
 */
function jem_sf_install() {
    $jem_sf_options = array(
        'api_key' => ''
    );
    //todo: verify the existence of curl
    update_option( 'jem_sf_options', $jem_sf_options );
}

/*
 * Remove store tags that are embedded into a store's
 * content
 */
function jem_sf_replace_store_tags( $content ) {
    $match_found = 1;    
    $match_pattern = '/<div[^<>]*?data-sf-storeid=["\']([\w]+)["\'][^<]*?[^<>]*?>[^<>]*?<\/div>/i';
        
    while (true)
    {        
        $match_found = preg_match( 
                    $match_pattern, 
                    $content, 
                    $matches);
        
        if (! $match_found)
        {            
            break;
        }
        
        $store_id = $matches[1];
                
        $store_content = jem_sf_get_url_contents( JEM_SF_DOMAIN . '/StoreDisplay/EmbeddedStore?logImpression=false&storeId=' . $store_id);
        $content = preg_replace($match_pattern, jem_sf_preg_escape_back($store_content), $content);
        return $content;
    }
    
    return $content;
}

/*
 * Replaces the sellfire shortcode with store contents
 */
function jem_sf_sellfire_shortcode($attr) {   
    $store_content = get_transient(jem_sf_sellfire_transient_code($attr["id"]));
    if (!$store_content || current_user_can('edit_posts'))
    {
        $response = wp_remote_get( JEM_SF_DOMAIN . '/StoreDisplay/EmbeddedStore?logImpression=false&storeId=' . $attr["id"]);
        if (is_wp_error($response) || wp_remote_retrieve_response_code(&$response) != 200)
        {
            return '';
        }
        $store_content = wp_remote_retrieve_body(&$response);
        set_transient(jem_sf_sellfire_transient_code($attr["id"]), $store_content);
    }
    return $store_content;
}

/*
 * Given a store ID, returns the transient option code 
 * for that store
 */
function jem_sf_sellfire_transient_code($store_id)
{
    return "sellfire-store-" . $store_id;
}

/*
 * Removes characters that trigger a back-reference
 */
function jem_sf_preg_escape_back($string) { 
    // Replace $ with \$ and \ with \\ 
    $string = preg_replace('#(?<!\\\\)(\\$|\\\\)#', '\\\\$1', $string); 
    return $string; 
}

/*
 * Adds the appropriate menu's to the wordpress admin page
 */
function jem_sf_add_menus() {
    add_options_page( 'SellFire Plugin Settings', 'SellFire', 'manage_options', __FILE__, 'jem_sf_settings' );        
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jem_sf_jsscript', JEM_SF_INSERTJS . '/sellfire.js' );
    $protocol = isset( $_SERVER["HTTPS"]) ? "https://" : "http://";
    $params = array(
        "ajaxurl" => admin_url('admin-ajax.php', $protocol));
    
    wp_localize_script('jem_sf_jsscript', 'jem_sf', $params);
    wp_enqueue_style( 'jem_sf_cssscript', JEM_SF_INSERTCSS . '/sellfire.css' );
}
 
/*
 * Register's SellFire settings with WP
 */
function jem_sf_register_settings() {
    //validate setting updates
    register_setting('jem_sf_options', 'jem_sf_options', 'jem_sf_validate_options');
}

/*
 * Draws the settings page
 */
function jem_sf_settings() {
    //add section for settings
    add_settings_section('jem_sf_auth_section', 'Authentication Settings', 'jem_sf_auth_section_text', 'jem_sf_settings');

    //add api key setting
    add_settings_field('jem_sf_api_key', 'Your SellFire API key', 'jem_sf_api_key_input', 'jem_sf_settings', 'jem_sf_auth_section'); 

    include('includes/options-page.php');
}

/*
 * Description of the authentication section
 */
function jem_sf_auth_section_text() {
    echo("To use the SellFire Plugin, you need to enter your API key. This can be found in your <a href='http://www.SellFire.com'>SellFire.com</a> account page. If you don't have a SellFire account, you can register for free.");
}

/*
 * Draws the API key input box
 */
function jem_sf_api_key_input() {
    $options = get_option( 'jem_sf_options' );
    echo "<input id='api_key' style='width: 400px;' name='jem_sf_options[api_key]' type='text' value='{$options['api_key']}'/>";
    echo "<div><a target='_SELLFIRE' href='https://www.sellfire.com/MyAccount/DataApiAccount'>Find my API Key</a></div>";
}

/*
 * Validates the settings that have been posted
 */
function jem_sf_validate_options( $input ) {    
    $api_key = $input['api_key'];
    $url = JEM_SF_API_URL . 'ValidApiKey?apiKey=' . $api_key;
    $params = array("sslverify" => false);
    $response = wp_remote_get($url, $params);
    $content = wp_remote_retrieve_body(&$response);
    if ($content != "true")
    {
        add_settings_error('jem_sf_api_key', 'jem_sf_api_key_error', 'Invalid API key', 'error');
    }
    return $input;
}

/*
 * Makes a call to the SellFire API for the specified operation
 * and posts the JSON decoded values in post_values.
 */
function jem_sf_api_call( $api_operation, $post_values ) {
    
    if ($post_values == null)
    {
        $post_values = array();
    }
    
    //create array of variables to post
    $sf_options = get_option('jem_sf_options');

    $post_values['apiKey'] = $sf_options['api_key'];
    
    $response = wp_remote_post( 
            JEM_SF_API_URL . $api_operation, 
            array ( 'body' => $post_values, "sslverify" => false) );

    return json_decode( wp_remote_retrieve_body(&$response) );
}

/*
 * Removes all of the store data from the cache
 */
function jem_sf_flush_cache ( ) {
    
    $response = jem_sf_api_call( 'ListStoreSummaries', null);
    foreach ($response->Data as $store)
    {
        delete_transient(jem_sf_sellfire_transient_code($store->Id));
    }
}

function jem_sf_get_ajax_url()
{
    $protocol = isset( $_SERVER["HTTPS"]) ? "https://" : "http://";
    return admin_url('admin-ajax.php', $protocol);
}

?>

