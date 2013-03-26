<?php

/*
 * Returns whether or not coupon press import is enabled
 */
function jem_sf_coupon_press_import_enabled() {
    return strtolower(PREMIUMPRESS_SYSTEM) === "couponpress";
}

/*
 * Returns whether or not shopper press import is enabled
 */
function jem_sf_shopper_press_import_enabled() {
    return strtolower(PREMIUMPRESS_SYSTEM) === "shopperpress";
}

/*
 * Gets an ID for a CSV file. Once the CSV file
 * is completely generated, the file can be downloaded
 * from http://www.sellfire.com/premiumpress/{id}.csv
 */
function jem_sf_get_sellfire_csv_id() {
    //in the final version of this function, I will simply call
    //a SellFire web service, passing in the users SellFire site id, which is a wordpress
    //option. I will return the ID returned by that web service call   
    return 'sample-demo';
}

/*
 * Returns an int indicating the progress of generating the CSV file. 
 * The csv ID is retrieved from jem_sf_get_sellfire_csv_id.
 * 100 means complete. Negative means error.
 */
function jem_sf_get_sellfire_csv_status($csv_id) {
    //in the final version of this fnction, I will simply call
    //a SellFire web service, passing in the csv ID and return
    //the output of that web service
    return 100;
}

function jem_sf_import_coupons($storeId, $pageNumber) 
{    
    $importedCoupons = 0;
    $result = array();
    if ($pageNumber > 25)
    {
        $result['succeed'] = false;
        $result['importedCount'] = 0;
        $result['errorMsg'] = 'Can only import up to 25 pages of your store';
        return $result;
    }
    
    $apiData = array();
    $apiData['storeId'] = $storeId;
    $apiData['pageNumber'] = $pageNumber;
    
    $response = jem_sf_api_call('GetStorePageData', $apiData);
    if (!$response || $response->ResponseCode != 200)
    {
        $result['succeed'] = false;
        $result['importedCount'] = 0;
        if ($response)        
        {
            $result['errorMsg'] = $response->ResponseMessage;                    
        }
        else 
        {
            $result['errorMsg'] = 'Could not connect to SellFire.com';        
        }
        return $result;
    }
    
    foreach ($response->Data->Coupons as $coupon)
    {
        jem_sf_import_coupon_from_ad($coupon, $response->Data->ImportCategory);
        $importedCoupons++;
        if ($importedCoupons == 500)
        {
            break;
        }        
    }

    $result['succeed'] = true;
    $result['importedCount'] = $importedCoupons;
    $result['totalPages'] = $response->Data->TotalPages;
    $result['pageNumber'] = $pageNumber;
    $result['storeId'] = $storeId;
    $result['storeType'] = '2';
    $result['hasMore'] = $pageNumber < 26 && 
    $response->Data->TotalPages > $pageNumber;
    
    $result['errorMsg'] = false;
    return $result;                   
}

function jem_sf_import_coupon_from_ad($ad, $storeName)
{    
    global $PPTImport;
    $ppArray = new stdClass();
    $ppArray->icid = $ad->ExternalId;
    $ppArray->title = html_entity_decode($ad->Name);
    $ppArray->description = html_entity_decode($ad->Description);
    $ppArray->merchant = $ad->MerchantName;
    $ppArray->merchant_logo_url = $ad->MerchantLogoUrl;
    $ppArray->merchant_id = $ad->MerchantId;
    $ppArray->network = $ad->NetworkName;
    $ppArray->category = $storeName;
    $ppArray->voucher_code = html_entity_decode($ad->CouponCode);
    $ppArray->merchant_url = $ad->MerchantHomePageUrl;
    $ppArray->affiliate_url = $ad->Url;
    $startDate = jem_sf_parse_json_date($ad->StartDate, 'array');
    if ($startDate && intval($startDate[0]) > 2012)
    {        
        $ppArray->start_date = implode('-', $startDate);
    }
    else
    {
        $ppArray->start_date = null;
    }    
    $endDate = jem_sf_parse_json_date($ad->EndDate, 'array');
    if ($endDate && intval($endDate[0]) > 2012 && intval($endDate[0]) < 2035)
    {
        $ppArray->expiry_date = implode('-', $endDate);
    }            
    else
    {
        $ppArray->expiry_date = null;
    }
    $type = 'Codes';
    if ($ad->CouponCode == null || $ad->CouponCode == '')
    {
        $type = 'Offer';
    }
    $PPTImport->ICODESADDCOUPON($ppArray, 'setup', $type);
}

function jem_sf_import_shopper_press($storeId, $pageNumber) 
{    
    $importedProducts = 0;
    $result = array();
    if ($pageNumber > 25)
    {
        $result['succeed'] = false;
        $result['importedCount'] = 0;
        $result['errorMsg'] = 'Can only import up to 25 pages of your store';
        return $result;
    }
    
    $apiData = array();
    $apiData['storeId'] = $storeId;
    $apiData['pageNumber'] = $pageNumber;
    
    $response = jem_sf_api_call('GetStorePageData', $apiData);
    if (!$response || $response->ResponseCode != 200)
    {
        $result['succeed'] = false;
        $result['importedCount'] = 0;
        if ($response)        
        {
            $result['errorMsg'] = $response->ResponseMessage;                    
        }
        else 
        {
            $result['errorMsg'] = 'Could not connect to SellFire.com';        
        }
        return $result;
    }
    
    foreach ($response->Data->Products as $product)
    {
        jem_sf_import_shopper_press_product_from_ad($product, $response->Data->ImportCategory);
        $importedProducts++;
        if ($importedProducts == 500)
        {
            break;
        }        
    }

    $result['succeed'] = true;
    $result['importedCount'] = $importedProducts;
    $result['totalPages'] = $response->Data->TotalPages;
    $result['pageNumber'] = $pageNumber;
    $result['storeId'] = $storeId;
    $result['storeType'] = '1';
    $result['hasMore'] = $pageNumber < 26 && 
    $response->Data->TotalPages > $pageNumber;
    
    $result['errorMsg'] = false;
    return $result;                   
}

/*
 * Adds a single product to shopper press
 */
function jem_sf_import_shopper_press_product_from_ad($product, $storeName) {
    $post_data = array(
        
    );
    
    $post_meta = array(
        'SKU' => $product-> MerchantId . '-' . $product->ExternalId,
        'featured' => 'no',
        'allowupload' => 'no',
        'price' => $product->Price/100,
        'qty' => 1,
        'file_type' => 'affiliate',
        'image' => $product->SmallImageUrl,
        'featured_image' => null,
        'url' => $product->FinalUrl,
        'link' => $product->Url,
        'redirect' => 'no',
        'images' => null
    );
    
    if ($product->Price < $product->ListPrice) {
        $post_meta['old_price'] = $product->ListPrice/100;
    }
            
    $existing_post_query = array(
        'numberposts' => 1,
        'meta_key' => 'SKU',
        'meta_query' => array(
            array(
                'key'=>'SKU',
                'value'=> $post_meta['SKU'],
                'compare' => '='
            )
        ),
        'post_type' => 'post');
    
    $existing_product = null;
    $existing_posts = get_posts($existing_post_query);
    $post_id = 0;
    
    if(is_array($existing_posts) && sizeof($existing_posts) > 0) {
        $existing_product = array_shift($existing_posts);
        $post_id = $existing_product->ID;       
    }  
    
    if ($existing_product == null) {     
        //product does not exist, insert the post
        $post_data = array();
        $post_data['post_type'] = 'post';
        $post_data['post_status'] = 'publish';
        $post_data['post_title'] = html_entity_decode($product->Name);
        $post_data['post_excerpt'] = '';
        $post_data['post_content'] = html_entity_decode($product->ShortDescription);
        $post_id = wp_insert_post($post_data, true);        
    }
        
    $tax = 'category';
    $term_paths = explode('|', $storeName);
    $new_post_terms = array();
    foreach($term_paths as $term_path) {
        $term_names = explode('/', $term_path);        
        $term_ids = array();

        for($depth = 0; $depth < count($term_names); $depth++) {                   
            $term_parent = ($depth > 0) ? $term_ids[($depth - 1)] : '';
            $term = term_exists($term_names[$depth], $tax, $term_parent);

            //if term does not exist, try to insert it.
            if( $term === false || $term === 0 || $term === null) {
                $insert_term_args = ($depth > 0) ? array('parent' => $term_ids[($depth - 1)]) : array();
                $term = wp_insert_term($term_names[$depth], $tax, $insert_term_args);
                delete_option("{$tax}_children");
            }

            if(is_array($term)) {
                $term_ids[$depth] = intval($term['term_id']);
            }
        }

        //if we got a term at the end of the path, save the id so we can associate
        if(array_key_exists(count($term_names) - 1, $term_ids)) {
            $new_post_terms[$tax][] = $term_ids[(count($term_names) - 1)];
        }
    }    
    //set post terms on inserted post
    foreach($new_post_terms as $tax => $term_ids) {
        wp_set_object_terms($post_id, $term_ids, $tax);
    }    
    
    //set post_meta on inserted post
    foreach($post_meta as $meta_key => $meta_value) {
        add_post_meta($post_id, $meta_key, $meta_value, true) or
            update_post_meta($post_id, $meta_key, $meta_value);
    }  
}

/*
 * Ajax action that handles exporting to couponpress/shopperpress
 */
function jem_sf_store_export_pp()
{       
    $pageNumber = $_GET['pageNumber'];
    $storeId = $_GET['storeId'];
    $storeType = $_GET['storeType'];
    $result = false;
    if ($storeType == '2')
    {
        $result = jem_sf_import_coupons($storeId, $pageNumber);
    }   
    else
    {
        $result = jem_sf_import_shopper_press($storeId, $pageNumber);
    }
    
    die(json_encode($result));
}

function jem_sf_parse_json_date($date, $type = 'date') {
    
    // Match the time stamp (microtime) and the timezone offset (may be + or -)
    if (preg_match( '/\/Date\((\d+)([+-]\d{4})\)/', $date, $matches))
    {
        // convert to seconds from microseconds
        $date = date( 'Y-m-d', $matches[1]/1000 ); 
    }
    else
    {
        return false;
    }
    
    switch($type)
    {    
        case 'date':
            return $date; // returns '05-04-2012'
            break;

        case 'array':
            return explode('-', $date); // return array('05', '04', '2012')
            break;

        case 'string':
            return $matches[1] . $matches[2]; // returns 1336197600000-0600
            break;
    }    
}  

?>
