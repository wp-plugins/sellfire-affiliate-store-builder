    <?php
        $siteId = jem_sf_getSiteId();    
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
    ?>

    <div id="sfContainer">
    </div>
    <script type="text/javascript">
        pageHeight  = jQuery(document).height();
        new easyXDM.Socket({            
            remote: "<?php echo(JEM_SF_WP_URL) ?>OuterFrame?url=<?php echo $url ?>&pageHeight=" + pageHeight,
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
        
        function setApiKey(key)
        {
            jQuery.get(jem_sf.ajaxurl, {action: 'jem_sf_set_api_key', apiKey: key}, null);
        }
        
    </script>

    <form method="get" action="<?php echo($admin_page) ?>" id="frmSendToEdit">
        
        <input type="hidden" name="jemSfStoreId" value="" id="jemSfStoreId">
        <input type="hidden" name="jemSfStoreName" value="" id="jemSfStoreName">
        <input type="hidden" name="jemSfEditPage" value="1">
        
    </form>