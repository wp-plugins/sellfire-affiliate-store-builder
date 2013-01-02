<h2>General Settings</h2>
<?php 
    
    $options = get_option('jem_sf_options');
    $invalid_api_key = false;
    $api_key =  $options['api_key'];
    if ($_GET['gs_submitted'])
    {        
        if ($_GET['direct_echo'] == '1')
        {
            $options['direct_echo'] = true;
        }
        else
        {
            $options['direct_echo'] = false;
        }
               
        $create_account = false;
        if ($_GET['api_key'] != $options['api_key'])
        {
            $api_key =  $_GET['api_key'];
            if (jem_sf_validate_api_key($_GET['api_key']))
            {
                $options['site_id'] = '';
                $options['api_key']= $_GET['api_key'];
                $create_account = true;
            }
            else
            {
                $invalid_api_key = true;
            }            
        }
        
        update_option('jem_sf_options', $options);
        jem_sf_getSiteId();
    }
    
    $check_echo = "";
    if ($options['direct_echo'])
    {
        $check_echo = "checked";
    }    
    
?>

        <p>
            These settings control how the SellFire plugin behaves.
        </p>
        
        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']  ?>">
            <table class="product-page-setting"  cellpadding="0" cellspacing="0">
                <tr class="alt">
                    <td class="sf-setting-label">Plug-in Compatibility Mode:</td>
                    <td>
                        <input type="checkbox" name="direct_echo" value="1" <?php echo $check_echo ?>>
                        <div>
                            Enabling this setting maximizes compatibility with WordPress themes.
                            You should disable this setting if you want the output of the SellFire plugin
                            to be processed by other WordPress plugins or by your WordPress theme.
                        </div>
                    </td>                        
                    </td>
                </tr>                  
                <tr>
                    <td class="sf-setting-label">API Key:</td>
                    <td>
                        <input type="text" size="50" name="api_key" value="<?php echo $api_key ?>">
                        <?php if ($invalid_api_key) echo "<div class='error'>Invalid API Key</div>"; ?>
                        <div>                            
                            The API key determines what SellFire account your plugin is linked to. If 
                            you want to change the account the plugin is linked to, enter your new key here.
                            
                            <p>
                            You can find your API key by logging into your SellFire account and 
                            clicking the "WordPress key" link on the left.                                
                            </p>                            
                        </div>
                    </td>                        
                    </td>
                </tr>                                                                     
            </table>    
            <input type="hidden" name="page" value="jem_sf_sellfire_settings"/>
            <input type="hidden" name="gs_submitted" value="1"/>            
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </form>
        