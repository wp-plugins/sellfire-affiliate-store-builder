<script type="text/javascript" src="<? echo JEM_API_DOMAIN ?>/Scripts/SimpleProfileOverview.js?version=6.2sf"></script>

<script type="text/javascript">
    jQuery(document).ready(function ()
    {
        jQuery("#divEnterRegistrationKey").dialog({ autoOpen: false, modal: true, title: "Enter Registration Key", minWidth: 500, dialogClass: 'wp-dialog' });
        SF.WordPress.SiteId = '@(Model.CustomerWebsite.ExternalWebsiteId)';
        jQuery(".video-tutorial-dialog").dialog({ autoOpen: false, modal: true, title: "Video Tutorial", minWidth: 850, dialogClass: 'wp-dialog' });		
    });
</script>

<?php if (Model.Profile.EnabledNetworks.Count == 0) : ?>
    <div class="account-setup">
        <strong>Plugin Set Up - Step 1 of 2</strong> -  Please enter an affiliate ID for at least 1 network.
        All links use your affiliate IDs. You keep 100% of your commmissions. 
        <?php if (!Model.CustomerData.Features.IsPayingCustomer) : ?>
            <span>
                If you already have an account, 
                <a href="#here" onclick="jQuery('#divEnterRegistrationKey').dialog('open'); return false;">
                click here to enter your registration key
                </a>
            </span>        
        <?php endif; ?>
    </div>       
<?php endif; ?>


<h2>What networks do you work with?</h2>

<div class="promo-text">    
    <div class="top-space">In order to use SellFire with an affiliate network, you need to link your 
    affiliate network accounts.
    </div>    
    <ul>
        <li>You must add an ID for <strong>at least</strong> 1 network</li>
        <li>SellFire includes your IDs in your affiliate links to ensure you get commissions for every sale</li>
        <li><strong>Need help?</strong>&nbsp;<a href="#" onclick="jQuery('#divAccountSetupTutorial').dialog('open'); return false;">View our acount setup tutorial</a></li>
    </ul>
</div>  

<div id="divAccountSetupTutorial" class="video-tutorial-dialog">
    <iframe width="800" height="464" src="http://www.youtube.com/embed/sO7F8aKA598?autoplay=0&hd=1&vq=hd720" frameborder="0" allowfullscreen></iframe>
</div>

<?php if (Model.AmazonErrorOccurred)  : ?>
    <div class="account-setup">
        <strong>Error Occurred</strong> - Could not validate your Amazon Information. Please
        double check your keys and try again.
    </div>    
<?php elseif (Model.ShowUpdateMessage) : ?>
    <div class="account-setup">
        <strong>Your Affiliate IDs have been saved</strong>
    </div>
<?php endif; ?>

<div>
    <form method="post" action="UpdateProfile" id="frmProfile">
        <div>
            @Html.Partial("~/Views/Shared/MyAccount/_AffiliateIdForm.cshtml", Model)
        </div>
        
        <div class="top-space">
            <input type="submit" value="Submit" />
        </div>        
    </form>    
</div>

@Html.Partial("~/Views/Shared/WordPress/_EnterRegistrationKey.cshtml")

<div style="height: 40px">
&nbsp;
</div>