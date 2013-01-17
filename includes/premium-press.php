<?php

/*
 * Returns whether or not premium press import is enabled
 */
function jem_sf_premium_press_import_enabled() {
    return true;
}

/*
 * Gets an ID for a CSV file. Once the CSV file
 * is completely generated, the file can be downloaded
 * from http://www.sellfire.com/premiumpress/{id}.csv
 */
function jem_sf_get_sellfire_csv_id() {
    //in the real version of this file, I will simply call
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
    //in the real version of this file, I will simply call
    //a SellFire web service, passing in the csv ID.
    return 100;
}

?>
