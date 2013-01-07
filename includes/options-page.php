    <?php
        $jemOptions = jem_sf_getSiteId();    
        $siteId = $jemOptions['site_id'];
        $apiKey = $jemOptions['api_key'];
        if (strrpos($url, '?'))
        {
            $url = $url . '&sfSiteId=' . $siteId;
        }
        else
        {
            $url = $url . '?sfSiteId=' . $siteId;
        }
        $url = urlencode($url);        
        $admin_page = get_admin_url();
        
        if ($siteId == null || $siteId == '')
        {
            $curl_worked = false;
            try
            {
                $response = wp_remote_get('http://www.google.com' );
                if( !is_wp_error( $response ) ) 
                {
                    $curl_worked = true;
                }
            }
            catch (Exception $e)
            {
                $curl_worked = false;
            }
            
            if ($curl_worked)
            {
            ?>
                <h2>Could not connect to SellFire</h2>
                <p>
                    Snap! There seems to be a problem. The plug-in tried to create
                    a new SellFire account for you, but it couldn't connect to SellFire.com. 
                    Refresh the page to try again. If the problem persists, please 
                    e-mail <a href="mailto:support@sellfire.com">support@sellfire.com</a>.
                </p>                            
            <?php
            }
            else
            {
                ?>
                
                <h2>Web Hosting Does Not Support this Plug-in</h2>
                <p>
                    Snap! There is a problem. The plug-in cannot communicate
                    with SellFire.com because your web server does not seem to
                    be allowing external communications. Please contact your
                    web hosting provider and ask them to enable the PHP CURL
                    command for your web server. If you have any questions
                    please email <a href="mailto:support@sellfire.com">support@sellfire.com</a>.
                </p>                 
                
                <?php
            }
            return;
        }
    ?>

    <div id="sfContainer">
    </div>
    <script type="text/javascript">
        pageHeight  = jQuery(document).height();
        new easyXDM.Socket({            
            remote: "<?php echo(JEM_SF_WP_URL) ?>OuterFrame?RRFilter=disabled&version=<?php echo JEM_SF_VERSION ?>&url=<?php echo $url ?>&pageHeight=" + pageHeight,
            container: document.getElementById("sfContainer"),
            onMessage: function (message, origin)
            {      
                message = jQuery.parseJSON(message);
                if (message.messageType == 'PageHeight')
                {
                    setFrameHeight(message.data);
                }
                else if (message.messageType == 'CompleteStore')
                {
                    redirectToPage(message.data);
                }                      
                else if (message.messageType == 'ApiKey')
                {
                    setApiKey(message.data);
                }                
            }                                  
        });
        
        function setFrameHeight(height)
        {
            var jElement = jQuery("iframe");
            var frameHeight = height;
            var sfMinHeight = window.document.body.scrollHeight - jElement.offset().top - 20;
            if (frameHeight < sfMinHeight)
            {
                frameHeight = sfMinHeight;
            }
            jElement.height(frameHeight);
            //this.container.getElementsByTagName("iframe")[0].style.height = frameHeight + "px";
            jElement.css("visibility", "visible"); 
            window.scrollTo(0, 0);
        }
        
        function redirectToPage(data)
        {
            jQuery("#jemSfStoreId").val(data.storeId);
            jQuery("#jemSfStoreName").val(data.storeName);
            jQuery("#frmSendToEdit").submit();            
        }
        
        function setApiKey(keyData)
        {
            jQuery.get(
                jem_sf.ajaxurl, 
                {action: 'jem_sf_set_api_key', apiKey: keyData.ApiKey, siteId: keyData.SiteId}, 
                function(){ document.location.reload(true) });
        }
        
    </script>

    <form method="get" action="<?php echo($admin_page) ?>" id="frmSendToEdit">
        
        <input type="hidden" name="jemSfStoreId" value="" id="jemSfStoreId">
        <input type="hidden" name="jemSfStoreName" value="" id="jemSfStoreName">
        <input type="hidden" name="jemSfEditPage" value="1">
        
    </form>
    
    <!---    
    API KEY: <?php echo $apiKey ?>
    SITE ID: <?php echo $siteId ?>
    -->

    