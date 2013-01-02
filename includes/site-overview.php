<div class="wp-content-outer">

<h2>SellFire Affiliate Store Builder</h2>

<?php if ($model->NetworkCount == 0 || $model->MerchantCount == 0) : ?>
    <div class="account-setup"> 
        <strong>Set Up Required</strong><span> - Before you can get started, you'll have to enter in your networks and merchants.</span> 
        <?php if ($model->NetworkCount == 0) : ?>        
            <a href="/WordPress/Networks">Click here to start</a>
        <?php else: ?>
            <a href="/WordPress/Merchants">Click here to start</a>
        <?php endif; ?>
    </div>                 
<?php endif; ?>

<?php  if ($model->UnregisteredAccount) : ?>
    <h2>Set Up</h2>
    <div id="divEmailEntrance">
        <?php if ($model->NetworkCount != 0 && $model->MerchantCount != 0) : ?>        
            <div class="account-setup">
                <strong>Plug In Set Up Recommended</strong> - Enter an email address to make sure
                you never lose access to your account.
            </div>           
        <?php endif; ?>
        <div class="top-space">
            Enter an email address so that you can restore your account in case you lose your registration key.     
        </div>
        <div class="top-space">
            <strong>Email Address: </strong> <input type="text" name="emailAddress" id="txtEmailAddress"/>
            <input type="button" value="Submit" class="secondary" onclick="SF.WordPress.submitEmailAddress(); return false;"/>
            <div class="error" style="display: none" id="divEmailAddressValidation">
                Please enter a valid email address
            </div>
            <div class="error" style="display: none" id="divEmailAddressExists">
                The email address is already associated with a SellFire account. <a href="#here" onclick="jQuery('#divEnterRegistrationKey').dialog('open'); return false;">
                Click here to enter your registration key to associate this site with that account.
                </a>.
            </div>
            <div class="top-space">
                <a href="#here" onclick="jQuery('#divEnterRegistrationKey').dialog('open'); return false;">
                    Already have an account? Click here to enter your registration key                
                </a>    
            </div>
        </div>  
    </div>     
    <div id="divEmailSet" style="display: none">
        Thanks! Your email address has been set.
    </div>    
    <hr />
<?php elseif (!$model->IsPayingCustomer) : ?>

    <h2>Set Up</h2>
    <a href="#here" onclick="jQuery('#divEnterRegistrationKey').dialog('open'); return false;">
        Already have a SellFire account? Click here to enter your registration key               
    </a>       
<?php endif; ?>

<h2>Site Stats</h2>
<div>
The total number of views and clicks your widgets and categories have received in the past 30 days. Stats update
daily.
</div>
<div class="top-space">
    <div class="stat-container">
        <div class="stat-header">
            Views
        </div>
        <div class="stat-value">
            <?php echo $model->TotalViews ?>
        </div>
    </div>

    <div class="stat-container">
        <div class="stat-header">
            Clicks
        </div>
        <div class="stat-value">
            <?php echo $model->TotalClicks ?>
        </div>
    </div>

    <div class="clear"></div>

</div>

<hr />

<h2>Store Contents</h2>
<div>
Categories have their own page on your blog. Widgets don't; they are just embedded directly into your 
blog posts.
</div>

<div class="top-space">
    <div class="stat-container">
        <div class="stat-header">
            Categories
        </div>
        <div class="stat-value clickable"  onclick="document.location = '/WordPress/Categories'; return false;">
            <?php echo $model->CategoryCount ?>
        </div>
        <?php if ($model->CanCreateMoreStores) : ?>        
        <div class="stat-edit">
            <input type="button" value="Add New Category" onclick="document.location = '/WordPress/CreateStore?usage=2'; return false;"/>
        </div>            
        <?php endif; ?>
    </div>

    <div class="stat-container">
        <div class="stat-header">
            Widgets
        </div>
        <div class="stat-value clickable" onclick="document.location = '/WordPress/Widgets'; return false;">
            <?php echo $model->WidgetCount ?>
        </div>
        <?php if ($model->CanCreateMoreStores) : ?>        
        <div class="stat-edit">
            <input type="button" value="Add New Widget" onclick="document.location = '/WordPress/CreateStore?usage=1'; return false;"/>
        </div>   
        <?php endif; ?>        
    </div>
    <div class="clear"></div>
</div>

<hr/>

<h2>Partners</h2>
<div>
How many affiliate networks and merchants are you promoting on this site?
</div>

<div class="top-space">
    <div class="stat-container">
        <div class="stat-header">
            Networks
        </div>
        <div class="stat-value clickable"  onclick="document.location = '/WordPress/Networks'; return false;">
            <?php echo $model->NetworkCount ?>         
        </div>
        <div class="stat-edit">
            <input type="button" value="Edit Networks" onclick="document.location = '/WordPress/Networks'; return false;"/>
        </div>   
    </div>

    <div class="stat-container">
        <div class="stat-header">
            Merchants
        </div>
        <div class="stat-value clickable"  onclick="document.location = '/WordPress/Merchants'; return false;">
            <?php echo $model->MerchantCount ?>
        </div>
        <div class="stat-edit">
            <input type="button" value="Edit Merchants" onclick="document.location = '/WordPress/Merchants'; return false;"/>
        </div>              
    </div>

    <div class="clear"></div>
    
</div>

<hr/>

<h2>Tutorials</h2>
<div>
Tutorial videos to help you use the plug-in
</div>

<div class="top-space">

<ul>
    <li>
        <strong><a href="#" onclick="jQuery('#divOverviewTutorial').dialog('open'); return false;">Complete overview</a></strong>: 
        This is an in-depth overview of all of the features of the SellFire affiliate store builder (14 minutes)                
    </li>
    <li>
        <strong><a href="#" onclick="jQuery('#divAccountSetupTutorial').dialog('open'); return false;">Plugin Setup</a></strong>:  
        A quick overview of setting up your merchant and network information (2.5 minutes)
    </li> 
    <li>
        <strong><a href="#" onclick="jQuery('#divSearchTutorial').dialog('open'); return false;">Search Tutorial</a></strong>: 
        This tutorial focuses on how to use the search panel effectively (5.5 minutes)
    </li> 
    <li>
        <strong><a href="#" onclick="jQuery('#divThemeTutorial').dialog('open'); return false;">Theme Tutorial</a></strong>: 
        This video shows you how to customize your store's style (6 minutes)
    </li> 
</ul>
    
<div id="divOverviewTutorial" class="video-tutorial-dialog">
    <iframe width="800" height="464" src="http://www.youtube.com/embed/HQilT6sVYwo?autoplay=0&hd=1&vq=hd720" frameborder="0" allowfullscreen></iframe>
</div>

<div id="divAccountSetupTutorial" class="video-tutorial-dialog">
    <iframe width="800" height="464" src="http://www.youtube.com/embed/sO7F8aKA598?autoplay=0&hd=1&vq=hd720" frameborder="0" allowfullscreen></iframe>
</div>

<div id="divSearchTutorial" class="video-tutorial-dialog">
    <iframe width="800" height="464" src="http://www.youtube.com/embed/YU2zjQwHhc4?autoplay=0&hd=1&vq=hd720" frameborder="0" allowfullscreen></iframe>
</div>

<div id="divThemeTutorial" class="video-tutorial-dialog">
    <iframe width="800" height="464" src="http://www.youtube.com/embed/RsOMxwEeug4?autoplay=0&hd=1&vq=hd720" frameborder="0" allowfullscreen></iframe>
</div>


</div>

<?php if (!$model->IsPayingCustomer) : ?>
    <hr />
    <h2>Upgrade</h2>    
    <div id="divRegistrationKey">
        <div class="top-space">
            <div>
                You are using the free version of SellFire. <strong>Upgrade now to unlock unlimited websites, categories, widgets, and more great features</strong>.                        
            </div>

            <div class="top-space">
                <input type="button" value="Upgrade Now" onclick="window.open('/SignIn/CreateWordPressAccount'); return false;"/>                
            </div>
            <div class="top-space">
                <a href="#here" onclick="jQuery('#divEnterRegistrationKey').dialog('open'); return false;">
                    Already upgraded? Click here to enter your registration key
                </a>                
            </div>           
        </div>
    </div>
<?php else: ?>
    <hr /> 
    <h2>Registration Key</h2>
    <div>
        <p>You can use the registration key below to activate the SellFire plugin for your other
        sites or blogs.</p>
        <strong>Registration Key: </strong> <span><?php echo $model->ApiKey ?></span>
    </div>  
<?php endif; ?>
    
</div>

@Html.Partial("~/Views/Shared/WordPress/_EnterRegistrationKey.cshtml")

<script type="text/javascript">
    jQuery(document).ready(function ()
    {
        jQuery("#txtEmailAddress").onenter(SF.WordPress.submitEmailAddress);
        jQuery("#txtRegistrationKey").onenter(SF.WordPress.setRegistationKey);
        jQuery("#divEnterRegistrationKey").dialog({ autoOpen: false, modal: true, title: "Enter Registration Key", minWidth: 500, dialogClass: 'wp-dialog' });
        jQuery(".video-tutorial-dialog").dialog({ autoOpen: false, modal: true, title: "Video Tutorial", minWidth: 850, dialogClass: 'wp-dialog' });		
        SF.WordPress.SiteId = '<?php echo $model->WebsiteId ?>';
    });
</script>