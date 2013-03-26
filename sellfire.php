<?php

/*
 * Plugin Name: SellFire Affiliate Store Builder
 * Plugin URI: http://www.sellfire.com/Features/AffiliateWordPressPlugin
 * Description: SellFire's store builder allows word press users to easily embed affiliate products,coupons, and deals into their blog.
 * Author: Jason MacInnes
 * Version: 3.0
 * Author URI: http://www.jasonmacinnes.com
 * License: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 */
register_activation_hook(__FILE__, 'jem_sf_install');

register_deactivation_hook(__FILE__, 'jem_sf_deactivate');

//functions for importing to premium press
include( 'includes/premium-press.php' );

//functions for importing to woo commerce
include( 'includes/woo-commerce-import.php' );

//adds the menu
add_action('admin_menu', 'jem_sf_add_menus');

//registers the cache flush ajax handler
add_action ( 'wp_ajax_jem_sf_flush_cache', 'jem_sf_flush_cache' );

//registers the set api key
add_action ( 'wp_ajax_jem_sf_set_api_key', 'jem_sf_set_api_key' );

//registers an action to export to PP
add_action ( 'wp_ajax_jem_sf_store_export_pp', 'jem_sf_store_export_pp' );

//registers an action to import to woocomerce
add_action ( 'wp_ajax_jem_sf_import_woocommerce_action', 'jem_sf_import_woocommerce_action' );

//registers the redirect that looks up a page for a store and redirects to the
//edit page
add_action( 'admin_init', 'jem_sf_redirect' );

add_action( 'wp_enqueue_scripts', 'jem_sf_add_blog_scripts');

add_action( 'admin_enqueue_scripts', 'jem_sf_add_scripts' );

add_action( 'template_redirect', 'jem_sf_redirect_to_product_page' );

add_action( 'template_redirect', 'jem_sf_set_product_page_variable');

add_filter('query_vars', 'jem_sf_add_query_var');

add_filter('init', 'jem_sf_add_rules');

add_filter('the_title', 'jem_sf_get_product_page_title', 10, 2);

add_filter('wp_title', 'jem_sf_add_product_page_title_tag', 10, 3);

add_filter("wp_list_pages_excludes", "jem_sf_filter_product_page_from_list");

add_filter('the_permalink', 'jem_sf_filter_permalink');

//disable canonical URL tag for Yoast
add_filter ('wpseo_canonical', 'jem_sf_remove_wpseo_canonical');

//set the product page title correctly for Yoast SEO users
add_filter ('wpseo_title', 'jem_sf_get_product_page_title');

//register a shortcode

//main sellfire store short code
add_shortcode( 'sellfire', 'jem_sf_sellfire_shortcode' );

//short code for a quick store
add_shortcode( 'sellFireQuick', 'jem_sf_sellfire_quick_shortcode' );

//short code for a product page
add_shortcode( 'sellfirepp', 'jem_sf_sellfire_product_page_shortcode' );

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
define('JEM_SF_VERSION', '2.9');

//include the definition of the post meta box
include( 'includes/post-meta-box.php' );

$jemSfShortCodeSequence = 1;

global $jem_sf_product_page;

$jem_sf_product_page = null;

/*
 * Installs the SellFire plug-in
 */
function jem_sf_install() {
    jem_sf_initialize_options();
    jem_sf_add_rules();
    flush_rewrite_rules();       
}

function jem_sf_deactivate() {    
    flush_rewrite_rules();
}


/*turns off Yoast on the SellFire product page*/
function jem_sf_remove_wpseo_canonical ($canonical) {
     if (jem_sf_is_sellfire_product_page()) {
         $canonical = false;
     }
     return $canonical;
}

/*
 * Sets up the SellFire options to their initial values
 * and retrieves their API key if they don't have one.
 */
function jem_sf_initialize_options() {
    
    $options = get_option( 'jem_sf_options' );    
    
    jem_sf_getSiteId($options);   
            
    if ($options['direct_echo'] == null || $options['direct_echo'] == '') {
        $options['direct_echo'] = false;
    }
        
    update_option('jem_sf_options', $options);    
}

function jem_sf_initialize_product_page_default(&$options) {
    
    if (!$options['pp_button_text'] && !$options['pp_xsell_header'])
    {
        $options['pp_xsell_header'] = 'You might also like...';
        $options['pp_merchant_header']= 'Available from...';
        $options['pp_button_text']= 'Learn more';
        $options['pp_xsell_max'] = 10;
        $options['pp_xsell_cols'] = 2;
        $options['pp_xsell_img'] = 150;
        $options['pp_image_width'] = 250;     
        $options['pp_call_to_action_img'] = '';     
        $options['pp_new_window'] = true;    
        return true;
    }    
    return false;
}

function jem_sf_add_rules() {    

    $options = get_option( 'jem_sf_options' );    
    if ($options['product_page_id'])
    {
        //this perma link could have additional items added to
        //it via the custom post structure (like adding .htm)
        //we need to find a way of removing that
        $permalink = jem_sf_get_product_page_permalink($options['product_page_id']); 
        
        if (substr($permalink, 0, 1) == '/')
        {
           $permalink = substr($permalink, 1);
        }

        //trim trailing /
        if (substr($permalink, -1) == '/')
        {
           $permalink = substr_replace($permalink ,"",-1);
        }
        
        $redirect_url = 'index.php?pagename=' . $permalink . '&sfpid=$matches[1]&sfProdName=$matches[2]';            
        add_rewrite_tag('%sfpid%','([^&]+)');
        add_rewrite_tag('%sfProdName%','([^&]+)');
        add_rewrite_rule($permalink . '/([^/]*)/([^/]*)/?', $redirect_url, 'top');
    }
    
    if ($_GET['pp_activate'])
    {
        flush_rewrite_rules();
    }
}

function jem_sf_get_product_page_permalink($product_page_id) {
    $permalink = get_permalink($product_page_id);
    return str_replace(parse_url(home_url(), PHP_URL_PATH), "", parse_url($permalink, PHP_URL_PATH));
}

function jem_sf_is_sellfire_product_page()
{
    global $wp_query;
    $options = get_option( 'jem_sf_options' );    
    return $options['product_page_id'] && $options['product_page_id'] == $wp_query->post->ID;    
}

function jem_sf_redirect_to_product_page() {
    if (stripos($_SERVER['REQUEST_URI'], 'sf-product-lookup'))
    {        
        $lookup_id = get_query_var( 'sfpid' );
        $product_name = get_query_var( 'sfProdName' );
        if (!$lookup_id || !$product_name) {
            $re = null;
            preg_match('/sf-product-lookup\/(.*)\/(.*)/', $_SERVER['REQUEST_URI'], $re);
            if ($re && count($re) > 2)
            {
                $lookup_id = $re[1];
                $product_name = $re[2];
            }
        }
        
        if (!$lookup_id || !$product_name) {
            wp_redirect(get_home_url(), 301);
            die();
            return;
        }
        
        $post_values = array();
        $post_values['identifier'] = $lookup_id;
        $post_values['name'] = $product_name;
        $product_page_id = jem_sf_api_call('GetProductPageId', $post_values)->Data;
        if ($product_page_id == 'invalid')
        {
            wp_redirect(get_home_url(), 301);
        }
        else
        {
            $options = get_option('jem_sf_options');
            $permalink = jem_sf_get_product_page_permalink($options['product_page_id']);
            
            if (substr($permalink, -1) != '/')
            {
                $permalink = $permalink . '/';
            }                   
            $redirect_url = get_home_url(null, $permalink . $product_page_id . '/' . $product_name);
            wp_redirect($redirect_url, 301);
        }                        
        die();
    }
    //echo 'left lookup';            
    //die();
}

function jem_sf_filter_permalink($permalink) {  
    $product_page_id = get_query_var( 'sfpid' );
    $product_name = get_query_var( 'sfProdName' );
    if ($product_page_id) 
    {        
        $options = get_option( 'jem_sf_options' );    
        $product_page_link = jem_sf_get_product_page_permalink($options['product_page_id']);
        $replace_url = $product_page_link . $product_page_id . '/' . $product_name;
        $permalink = str_replace($permalink, $product_page_link, $replace_url);
    }
    return $permalink;    
}
/*
 * Sets the product page being viewed. Done early so that
 * we can set the title of the page and the H1 based 
 * on its content.
 */
function jem_sf_set_product_page_variable() {
    
    $product_page_id = get_query_var( 'sfpid' );
    $post_values = array();
    global $jem_sf_product_page;
    if ($product_page_id)
    {
        $post_values['productPageId'] = $product_page_id;
        $result = jem_sf_api_call('GetProductPageData', $post_values);
        $jem_sf_product_page = $result->Data;            
    }
    else
    {
        $jem_sf_product_page = null;            
    }    
}

/*
 * Registers query arguments used in the URL rewriting for SellFire
 * product pages
 */
function jem_sf_add_query_var($vars) {
    $vars[] = 'sfplid';
    $vars[] = 'sfpid';
    $vars[] = 'sfProdName';
    return $vars;
}

/*
 * Changes the name of the product page's H1 tag
 */
function jem_sf_get_product_page_title() {    
    global $jem_sf_product_page;    
    $num_args = func_num_args();
    if (num_args < 2)
    {
        if ($num_args > 0 && $jem_sf_product_page != null) {
            $title = func_get_arg(0);
            if ($title && stristr($title, 'sellfire p')) {
            	return html_entity_decode($jem_sf_product_page->MainProduct->Name);
	    }
	    return $title;
        }
        else if (func_num_args() > 0){
            return func_get_arg(0);
        }
        return "";
    }
    
    $title = func_get_arg(0);    
    $id = func_get_arg(1);
    
    $options = get_option('jem_sf_options');
    $product_page_id = $options['product_page_id'];
    
    if ($jem_sf_product_page == null || $id != $product_page_id) {        
        return $title;
    }   

    return $jem_sf_product_page->MainProduct->Name;    
}

/*
 * Sets the page title to contain the name of the product
 */
function jem_sf_add_product_page_title_tag() {    
    
    global $jem_sf_product_page;
    if (func_num_args() < 3)
    {
        if ($jem_sf_product_page != null) {
            return html_entity_decode($jem_sf_product_page->MainProduct->Name);
        }   
        else if (func_num_args() > 0) {
            return func_get_arg(0);
        }            
        return "";
    }    
    
    $title = func_get_arg(0);    
    $sep = func_get_arg(1);
    $seplocation = func_get_arg(2);
    
    if ($jem_sf_product_page == null) {
        return $title;
    }
        
    // account for $seplocation
    $left_sep = ( $seplocation != 'right' ? ' ' . $sep . ' ' : '' );
    $right_sep = ( $seplocation != 'right' ? '' : ' ' . $sep . ' ' );   
    
    
    return $left_sep . html_entity_decode($jem_sf_product_page->MainProduct->Name) . $right_sep;
}

/*
 * Removes the SellFire product page from appearing in the list generated 
 * by wp_list_pages
 */
function jem_sf_filter_product_page_from_list($excluded_ids) {
    $options = get_option('jem_sf_options');
    $product_page_id = $options['product_page_id'];
    if ($product_page_id == null || $product_page_id == '')
    {
        return $excluded_ids;
    }
    
    $excluded_ids[] = $options['product_page_id'];
    return $excluded_ids;
}

function jem_sf_get_and_validate_product_page($options) {
    
    $product_page_id = $options['product_page_id'];
    
    if ($product_page_id != null && $product_page_id != '')
    {
        $post = get_post($product_page_id);        
        if (!$post || $post->post_status != 'publish')
        {
            $product_page_id = null;
        }
    }
    
    return $product_page_id;
}

/*
 * Creates a page that will serve as the product page
 * for this blog. This page should not be deleted
 */
function jem_sf_create_product_page(&$options) {
    
    $product_page_id = $options['product_page_id'];
    
    if ($product_page_id != null && $product_page_id != '')
    {
        $post = get_post($product_page_id);        
        if (!$post || $post->post_status != 'publish')
        {
            $product_page_id = null;
        }
    }
    
    if ($product_page_id == null || $product_page_id == '')
    {
        $user = wp_get_current_user();                          
        $post = array();
        $post['post_author'] = $user->ID;
        $post['post_content'] = '[sellfirepp]';
        $post['post_name'] = 'product';
        $post['post_status'] = 'publish';
        $post['post_title'] = 'SellFire Product Page - Do Not Delete';
        $post['post_type'] = 'page';                    
        $product_page_id = wp_insert_post($post);
    }    
    
    $options['product_page_id'] = $product_page_id;
    return $product_page_id;
}

/*
 * Replaces the sellfire shortcode with store contents
 */
function jem_sf_sellfire_shortcode($attr) {        
    $store_content = get_transient(jem_sf_sellfire_transient_code($attr["id"]));
    $options = get_option( 'jem_sf_options' );
    if (!$store_content || current_user_can('edit_posts'))
    {        
        $product_page_root = '';
        if ($options['product_page_id'])
        {
            $product_page_root = get_home_url(null, 'sf-product-lookup');
        }        
        $sf_url = JEM_SF_DOMAIN . '/StoreDisplay/EmbeddedStore?ppRoot=' . urlencode($product_page_root) . '&wpMode=true&logImpression=false&storeId=' . $attr["id"] . '&wpPluginVersion=' . JEM_SF_VERSION;
        $response = wp_remote_get($sf_url, jem_sf_get_default_http_args());
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200)
        {
            return '';
        }
        $store_content = wp_remote_retrieve_body($response);
        set_transient(jem_sf_sellfire_transient_code($attr["id"]), $store_content, 300);
    }             
    return jem_sf_output_store_for_shortcode($store_content, $attr["id"], $options['direct_echo']);
}

function jem_sf_get_default_http_args() {
    return array(
        "timeout" => 15,
        "redirection" => 5,
        "httpversion" => "1.0",
        "blocking" => true,
        "headers" => array(),
        "body" => null,
        "cookies" => array()
    );
}


/*
 * Outputs store content. Handles with to directly echo it or use it 
 * as a normal short code
 */
function jem_sf_output_store_for_shortcode($store_content, $store_id, $direct_echo) {
    
    //$store_content = preg_replace('/\&[a-zA-Z]+\;/', "cruelworld", $store_content);
    
    if ($direct_echo || $_GET['sfecho'] == '1')
    {
        echo "<div id='divSfStoreTemp" . $store_id . "' style='display: none' class='sf-temp-store-holder'/>" . $store_content . "</div>";        
        return "<div id='divSfStore" . $store_id . "'></div>";
    }
    else        
    {       
        return $store_content;
    }
}

/*
 * Replaces the sellfire quick store shortcode with store contents
 */
function jem_sf_sellfire_quick_shortcode($attr) {
    global $post, $jemSfShortCodeSequence;

    $postId = 1;
    if ($post->ID){
        $postId = $post->ID;
    }
    $transientCode =jem_sf_sellfire_quick_transient_code($postId, $jemSfShortCodeSequence);
    $store_content = get_transient($transientCode);

    if (!$store_content || current_user_can("edit_posts"))
    {
        $options = get_option( 'jem_sf_options' );
        $product_page_root = get_home_url();
        $url = JEM_SF_DOMAIN . '/StoreDisplay/EmbeddedQuickStore?ppRoot=' .  urlencode($product_page_root) . '&wpMode=true&postId=' . $postId . '&qsSequence=' . $jemSfShortCodeSequence . '&siteId=' . $options['site_id'];

        foreach ($attr as $key => $value)
        {
            $url .= '&' . urlencode($key) . "=" . urlencode($value);
        }
        $response = wp_remote_get($url, jem_sf_get_default_http_args());
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200)
        {
            return '';
        }

        $store_content = wp_remote_retrieve_body($response);
        set_transient($transientCode, $store_content, 300);
    }
    $jemSfShortCodeSequence++;
    $options = get_option( 'jem_sf_options' );
    return jem_sf_output_store_for_shortcode($store_content, $attr["id"], $options['direct_echo']);    
}

/*
 * Replaces the product page short code with the content from
 * a product page identifier given in the URL
 */
function jem_sf_sellfire_product_page_shortcode($attr) {    
    global $jem_sf_product_page;    
    if ($jem_sf_product_page == null)
    {
        return "";
    }
    include('includes/product-page.php');
    $productPageText = jem_sf_renderProductPage($jem_sf_product_page);    
    $options = get_option( 'jem_sf_options' );
    $direct_echo = $options['direct_echo'];
    return jem_sf_output_store_for_shortcode($productPageText, 'SfProductPage', $direct_echo);    
}

/*
 * Given a store ID, returns the transient option code
 * for that store
 */
function jem_sf_sellfire_transient_code($store_id)
{
    return "sf-st-" . $store_id;
}


/*
 * Given a post ID and sequence, returns the quick store option
 * for that store
 */
function jem_sf_sellfire_quick_transient_code($post_id, $sequence)
{
    return "sf-qs-" . $post_id . "-" . $sequence;
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
    add_submenu_page ('jem_sf_sellfire', 'Settings', 'Settings', 'manage_options', 'jem_sf_sellfire_settings', 'jem_sf_settings' );
    add_submenu_page ('jem_sf_sellfire', 'Product Page Settings', 'Product Pages', 'manage_options', 'jem_sf_sellfire_product_pages', 'jem_sf_product_pages' );    
    add_submenu_page (null, 'Create Store', 'Create Store', 'manage_options', 'jem_sf_sellfire_create_store', 'jem_sf_create_store' );
}

function jem_sf_add_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-dialog' );
    wp_enqueue_script( 'jem_sf_jsscript', JEM_SF_INSERTJS . '/sellfire.js?sfversion=' . JEM_SF_VERSION );
    wp_enqueue_script( 'jem_sf_jseasyXDM', JEM_SF_INSERTJS . '/easyXDM/easyXDM.min.js' );

    $protocol = isset( $_SERVER["HTTPS"]) ? "https://" : "http://";

    $params = array("ajaxurl" => admin_url('admin-ajax.php', $protocol));

    wp_localize_script('jem_sf_jsscript', 'jem_sf', $params);
    wp_enqueue_style (  'wp-jquery-ui-dialog');
    wp_enqueue_style( 'jem_sf_cssscript', JEM_SF_INSERTCSS . '/sellfire.css?sfversion=' . JEM_SF_VERSION );
}

function jem_sf_add_blog_scripts() {    
    if (get_query_var( 'sfpid' )) 
    {        
        wp_enqueue_style( 'jem_sf_productcssscript', JEM_SF_INSERTCSS . '/sellfire-product-page.css?sfversion=' . JEM_SF_VERSION );
    }
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jem_sf_blog_jsscript', JEM_SF_INSERTJS . '/sellfire-blog.js?sfversion=' . JEM_SF_VERSION );
}


function jem_sf_product_pages() {
    include('includes/product-page-settings.php');
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
    include('includes/general-settings.php');
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
function jem_sf_getSiteId(&$options) {
    //delete_option('jem_sf_options');
    //jem_sf_initialize_options();
    if ($options == null)
    {
        $options = get_option( 'jem_sf_options' );
    }           
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
        $params = array('sslverify' => false, 'body' => $post_values, 'timeout' => 10, 'method' => post, 'blocking' => true, 'httpversion' => 1.0, 'redirection' => 5);
        $response = wp_remote_post($url, $params);
        $result = json_decode( wp_remote_retrieve_body($response) );
        if ($result->ErrorMessage)
        {
            return array(
                "Success"=>false, 
                "ErrorMessage"=>$result->ErrorMessage);            
        }
        $options['api_key'] = $result->ApiKey;
        $options['site_id'] = $result->SiteId;
        $siteId = $result->SiteId;
        update_option('jem_sf_options', $options);
    }

    return array("Success"=>true, "ErrorMessage"=>false);
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
function jem_sf_validate_api_key( $api_key ) {
    $url = JEM_SF_API_URL . 'ValidApiKey';
    $post_values = array();
    $post_values['apiKey'] = $api_key;
    $params = array('sslverify' => false, 'body' => $post_values);
    $response = wp_remote_post($url, $params);
    $content = wp_remote_retrieve_body($response);
    return $content == "true";        
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
    $post_values['siteId'] = $sf_options['site_id'];
        
    $response = wp_remote_post(
            JEM_SF_API_URL . $api_operation,
            array ( 'body' => $post_values, "sslverify" => false, 'timeout' => 20) );
    
    return json_decode( wp_remote_retrieve_body($response) );
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