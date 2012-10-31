<?php

/*
 * Plugin Name: SellFire Affiliate Store Builder
 * Plugin URI: http://www.sellfire.com/Features/AffiliateWordPressPlugin
 * Description: SellFire's store builder allows word press users to easily embed affiliate products,coupons, and deals into their blog. 
 * Author: Jason MacInnes
 * Version: 2.2
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

//registers the set api key
add_action ( 'wp_ajax_jem_sf_set_api_key', 'jem_sf_set_api_key' );

//registers the redirect that looks up a page for a store and redirects to the 
//edit page
add_action( 'admin_init', 'jem_sf_redirect' );

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

//url of the SF WordPress Controller 
define( 'JEM_SF_WP_URL', JEM_SF_DOMAIN . '/WordPress/' );

//define a constant to the JS directory
define( 'JEM_SF_INSERTJS', plugin_dir_url(__FILE__).'js' );

//define a constant to the CSS directory
define( 'JEM_SF_INSERTCSS', plugin_dir_url(__FILE__).'css' );

//define a version number to communicate with the mother ship
define('JEM_SF_VERSION', '2.2');

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
    add_menu_page( 'SellFire Affiliate Store Builder Plugin', 'SellFire', 'manage_options', 'jem_sf_sellfire', 'jem_sf_site_overview', plugins_url('/images/sf-icon.jpg', __FILE__));        
    add_submenu_page ('jem_sf_sellfire', 'SellFire Plugin', 'Overview', 'manage_options', 'jem_sf_sellfire', 'jem_sf_site_overview' );
    add_submenu_page ('jem_sf_sellfire', 'Networks', 'Networks', 'manage_options', 'jem_sf_sellfire_networks', 'jem_sf_networks' );
    add_submenu_page ('jem_sf_sellfire', 'Merchants', 'Merchants', 'manage_options', 'jem_sf_sellfire_merchants', 'jem_sf_merchants' );
    add_submenu_page ('jem_sf_sellfire', 'Store Categories', 'Categories', 'manage_options', 'jem_sf_sellfire_categories', 'jem_sf_categories' );
    add_submenu_page ('jem_sf_sellfire', 'Store Widgets', 'Widgets', 'manage_options', 'jem_sf_sellfire_widgets', 'jem_sf_widgets' );
    add_submenu_page ('jem_sf_sellfire', 'Store Themes', 'Themes', 'manage_options', 'jem_sf_sellfire_theme', 'jem_sf_store_theme' );    
    add_submenu_page (null, 'Create Store', 'Create Store', 'manage_options', 'jem_sf_sellfire_create_store', 'jem_sf_create_store' );    
        
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jem_sf_jsscript', JEM_SF_INSERTJS . '/sellfire.js?sfversion=2.0' );
    wp_enqueue_script( 'jem_sf_jseasyXDM', JEM_SF_INSERTJS . '/easyXDM/easyXDM.min.js' );
    $protocol = isset( $_SERVER["HTTPS"]) ? "https://" : "http://";
    $params = array(
        "ajaxurl" => admin_url('admin-ajax.php', $protocol));
    
    wp_localize_script('jem_sf_jsscript', 'jem_sf', $params);
    wp_enqueue_style( 'jem_sf_cssscript', JEM_SF_INSERTCSS . '/sellfire.css?sfversion=2.0' );
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
function jem_sf_store_theme() {
    $url  = '/WordPress/Themes';
    include('includes/options-page.php');
}

/*
 * Draws the settings page
 */
function jem_sf_settings() {
    $url  = '/WordPress/Merchants';
    include('includes/options-page.php');
}

/*
 * Draws the categories page
 */
function jem_sf_categories() {
    $url  = '/WordPress/Categories';
    include('includes/options-page.php');
}

/*
 * Draws the widgets page
 */
function jem_sf_widgets() {
    $url  = '/WordPress/Widgets';
    include('includes/options-page.php');
}

/*
 * Draws the widgets page
 */
function jem_sf_create_store() {
    
    $url  = '/WordPress/CreateStore?usage=' . $_GET['usage'];
    include('includes/options-page.php');
}

/*
 * Draws the merchants page
 */
function jem_sf_merchants() {  
    $url = '/WordPress/Merchants';
    include('includes/options-page.php');
}

/*
 * Draws the merchants page
 */
function jem_sf_site_overview() {  
    $url = '/WordPress/SiteOverview';
    include('includes/options-page.php');
}

/*
 * Creates a new SellFire account if needed
 */
function jem_sf_getSiteId() {
    //delete_option('jem_sf_options');
    
    $options = get_option( 'jem_sf_options' );    
    $siteId = null;
    if ($options == null) {
        $options = array();        
    } else {
        $siteId = $options['site_id'];        
    }    
        
    /*
    $options['site_id']=null;
    $options['api_key']=null;
    $siteId = null;      
     */            
      
    if ($siteId == null || $siteId == '') {
        
        $site_url = get_home_url();    
        $site_name = get_bloginfo( 'name' );
        $url = JEM_SF_WP_URL . 'CreateAccount';
        $post_values = array();
        $post_values['siteUrl'] = urlencode($site_url);
        $post_values['siteName'] = urlencode($site_name);
        $post_values['apiKey'] = $options['api_key'] == null ? '' : $options['api_key'];
        $params = array('sslverify' => false, 'body' => $post_values);    
        $response = wp_remote_post($url, $params);    
        $result = json_decode( wp_remote_retrieve_body(&$response) );
        $options['api_key'] = $result->ApiKey;
        $options['site_id'] = $result->SiteId;
        $siteId = $result->SiteId;
        update_option('jem_sf_options', $options);
    }
    
    return $options;
}

/*
 * Draws the networks page
 */
function jem_sf_networks() {
    $url = '/WordPress/Networks';
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
    $site_url = get_home_url();    
    $site_name = get_bloginfo( 'name' );
    $url = JEM_SF_API_URL . 'ValidApiKey';
    $post_values = array();
    $post_values['apiKey'] = $api_key;
    $post_values['siteUrl'] = $site_url;
    $post_values['siteName'] = $site_name;
    $params = array('sslverify' => false, 'body' => $post_values);    
    $response = wp_remote_post($url, $params);
    $content = wp_remote_retrieve_body(&$response);
    if ($content != "true")
    {
        add_settings_error('jem_sf_api_key', 'jem_sf_api_key_error', 'Invalid API key', 'error');
    }
    return $input;
}

/*
 * Redirects the user to the edit page after they are done editing there
 * store
 */
function jem_sf_redirect()
{
    $send_to_edit = $_GET['jemSfEditPage'];
    $store_id = $_GET['jemSfStoreId'];
    $store_name = $_GET['jemSfStoreName'];
    
    if ($send_to_edit)
    {
        $post_id = get_option('jem_sf_' . $store_id);
        
        //check that the post still exists
        if ($post_id)
        {
            $post = get_post($post_id);           
            if (!$post || $post->post_status == 'trash')
            {
                $post_id = false;
            }            
        }
        
        if (!$post_id)
        {
            $post_id = jem_sf_create_post($store_id, $store_name);
            update_option('jem_sf_' . $store_id, $post_id);
        }
        wp_redirect(get_edit_post_link($post_id, ''));  
        exit();
    }    
}

function jem_sf_set_api_key()
{
    $api_key = $_GET['apiKey'];
    $site_id = $_GET['siteId'];
    $option = get_option('jem_sf_options');
    if ($api_key)
    {
        $option = get_option('jem_sf_options');
        $option['api_key'] = $api_key;
        $option['site_id'] = $site_id;
        update_option('jem_sf_options', $option);
        die('true');
    }
    die('false');
}

function jem_sf_create_post( $store_id, $store_name)
{
    $user = wp_get_current_user();
    $post = array();
    $post['post_author'] = $user->ID;
    $post['post_content'] = '[sellfire id="' . $store_id . '" name="' . $store_name . '"]';
    $post['post_status'] = 'draft';
    $post['post_title'] = $store_name;
    $post['post_type'] = 'page';
    
    return wp_insert_post($post);
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