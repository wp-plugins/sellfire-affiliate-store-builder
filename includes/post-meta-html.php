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
                <a target="_SellFire" href="<?php echo admin_url('admin.php?page=jem_sf_sellfire_create_store&usage=3') ?>">
                    Create new Store
                </a>
            </div>
        </td>
    </tr>
</table>

<?php else: ?>

<div>
    The SellFire Plug-In hasn't been configured yet. 
    <a target="_SellFire" href="<?php echo admin_url('admin.php?page=jem_sf_sellfire_networks') ?>">
        Click here to configure it.
    </a>    
</div>

<?php endif; ?>