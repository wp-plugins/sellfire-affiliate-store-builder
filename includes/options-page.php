
<div class="wrap">
    <?php screen_icon() ?>
    <h2>SellFire Affiliate Plugin</h2>
    <p>
        The SellFire affiliate plug-in allows you to quickly embed affiliate
        stores into your blog.
    </p>
    <form action="options.php" method="post">
        <?php
        settings_fields('jem_sf_options');
        do_settings_sections('jem_sf_settings');
        ?>
        <br/>
        <input name="Submit" type="submit" value="Save Changes" class="button-primary"/>        
    </form>
</div>