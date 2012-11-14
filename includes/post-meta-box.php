<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

add_action('add_meta_boxes', 'jem_sf_create_metabox');

/*
 * Registers the meta box with WP
 */
function jem_sf_create_metabox() {
    add_meta_box('sellfire-meta', 'SellFire Affiliate Plugin', 
            'jem_sf_meta_render', 'post', 'normal', 'high');    
    
    add_meta_box('sellfire-meta', 'SellFire Affiliate Plugin', 
            'jem_sf_meta_render', 'page', 'normal', 'high');
}

/*
 * Renders the meta box
 */
function jem_sf_meta_render() {
    $options = get_option( 'jem_sf_options' );   
    $response = null;
    if ($options['api_key']){
        $response = jem_sf_api_call( 'GetWordPressPostData', null );
    }    
    include ( 'post-meta-html.php' );
}