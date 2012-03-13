<?php if ($response): ?>

<table class="sf-store-meta-box">
    <tr>                
        <td>
            Select Store
        </td>
        <td>
            <select name="jem_sf_store" id="jem_sf_store">
                <?php
                    foreach ($response->Data as $store)
                        echo "<option value='{$store->Id}'>{$store->Name}</option>";
                ?>                
            </select>
            <input class="button-primary" type="submit" name="jem_sf_add_store" 
                id="jem-sf-add-store" value="Add Store" onclick="jemSfAddShortCode(); return false;">
            <div>
                <a target="_SellFire" href="<?php echo JEM_SF_DOMAIN ?>/StoreBuilder/CreateUnnamedStore">
                    Create new Store
                </a>
            </div>
        </td>
    </tr>
</table>

<?php else: ?>

<div>
    Enter your API key in the settings menu to use this plugin. If
    you do not have an API key, you can get one for free by
    registering at <a href="http://www.sellfire.com">SellFire.com</a>
</div>

<?php endif; ?>


