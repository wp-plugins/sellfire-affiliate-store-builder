<?php if ($response): ?>

<div class="sf-title">Normal Store</div>

<table class="sf-store-meta-box">
  <tr>
    <td style="vertical-align: top; padding-right: 10px">
      Select Store
    </td>
    <td>
      <select name="jem_sf_store" id="jem_sf_store">
        <?php foreach ($response->Data->Stores as $store) echo "<option value='{$store->Id}'>{$store->Name}</option>";?>
      </select>
      <input class="button-primary" type="submit" name="jem_sf_add_store"
          id="jem-sf-add-store" value="Add Store" onclick="jemSfAddShortCode(); return false;" />
        <div>
          <a target="_SellFire" href="<?php echo admin_url('admin.php?page=jem_sf_sellfire_create_store&usage=3') ?>">
            Create new Store
          </a>
        </div>
      </td>
  </tr>
</table>

<div class="sf-title" style="padding-top: 10px;">Quick Store</div>

Quick store is a way to define a product display in just a few seconds.
<br/>
<br/>

<input class="button-primary" type="submit" name="jem_sf_add_quick_store_dialog"
    id="jem-sf-add-store" value="Add Quick Store" onclick="jemSfShowQuickStoreDialog(); return false;" />


  <div id="divSfQuickStoreDialog">
    <div class="sf-title">SellFire Quick Store</div>
    <div class="quick-store-element product amazon coupon">
      <div class="input-label">Store Type:</div>
      <div>
        <select name="sfQuickStoreType" id="sltSfQuickStoreType" onchange="return jemQuickStoreTypeChange();">
          <option value="product">Products</option>
          <?php
                    if ($response->Data->AmazonMode)
                    {
                        echo "<option value='amazon'>Amazon Products</option>";
                    }
                ?>
          <option value="coupon">Coupons</option>
        </select>
        <div class="input-description">
          What type of store
        </div>
      </div>
    </div>
    <div class="quick-store-element product amazon">
      <div class="input-label">Store Theme:</div>
      <div>
        <select name="sfQuickStoreProductTheme" id="sltSfQuickStoreProductTheme">
          <?php
                foreach ($response->Data->ProductThemes as $theme)
                    echo "<option value='{$theme->Id}'>{$theme->Name}</option>";
            ?>
        </select>
        <div class="input-description">
          How should your store be styled?
        </div>
      </div>
    </div>
    <div class="quick-store-element coupon">
      <div class="input-label">Store Theme:</div>
      <div>
        <select name="sfQuickStoreCouponTheme" id="sltSfQuickStoreCouponTheme">
          <?php
                foreach ($response->Data->CouponThemes as $theme)
                    echo "<option value='{$theme->Id}'>{$theme->Name}</option>";
            ?>
        </select>
        <div class="input-description">
          How should your store be styled?
        </div>
      </div>
    </div>

    <div class="quick-store-element coupon amazon">
      <div class="input-label">Keyword:</div>
      <div>
        <input type="text" name="sfQuickStoreKeyword" id="txtSfQuickStoreKeyword"/>
      </div>
    </div>

    <div class="quick-store-element amazon">
      <div class="input-label">Amazon Site:</div>
      <div>
        <select name="sfQuickStoreAmazonSite" id="sltSfQuickStoreAmazonSite">
          <option value="com">.com</option>
          <option value="com">.ca</option>
          <option value="de">.de</option>
          <option value="es">.es</option>
          <option value="fr">.fr</option>
          <option value="co.uk">.co.uk</option>
        </select>
        <div class="input-description">
          Select which Amazon site you would like to search
        </div>
      </div>
    </div>

    <div class="quick-store-element amazon">
      <div class="input-label">Search Category:</div>
      <div>
        <select name="sfQuickStoreSearchCategory" id="sltSfQuickStoreSearchCategory">
          <option value="All">All</option>
          <option value="Apparel">Apparel</option>
          <option value="Appliances">Appliances</option>
          <option value="ArtsAndCrafts">Arts and Crafts</option>
          <option value="Automotive">Automotive</option>
          <option value="Baby">Baby</option>
          <option value="Beauty">Beauty</option>
          <option value="Blended">Blended</option>
          <option value="Books">Books</option>
          <option value="Classical">Classical</option>
          <option value="Digitial Music">Digital Music</option>
          <option value="DVD">DVD</option>
          <option value="Electronics">Electronics</option>
          <option value="GourmetFood">Gourmet Food</option>
          <option value="Grocery">Grocery</option>
          <option value="HealthPersonalCare">Health and Personal</option>
          <option value="HomeGarden">Home and Garden</option>
          <option value="Industrial">Industrial</option>
          <option value="Jewelry">Jewelry</option>
          <option value="Kitchen">Kitchen</option>
          <option value="Magazines">Magazines</option>
          <option value="Miscellaneous">Miscellaneous</option>
          <option value="MobileApps">Mobile Apps</option>
          <option value="MP3Downloads">MP3 Downloads</option>
          <option value="Music">Music</option>
          <option value="MusicTracks">Music Tracks</option>
          <option value="MusicalInstruments">Musical Instruments</option>
          <option value="OfficeProducts">Office Products</option>
          <option value="OutdoorLiving">Outdoor Living</option>
          <option value="PCHardware">PC Hardware</option>
          <option value="PetSupplies">Pet Supplies</option>
          <option value="Photo">Photo</option>
          <option value="Shoes">Shoes</option>
          <option value="Software">Software</option>
          <option value="SportingGoods">Sporting Goods</option>
          <option value="Tools">Tools</option>
          <option value="Toys">Toys</option>
          <option value="VHS">VHS</option>
          <option value="Video">Video</option>
          <option value="VideoGames">Video Games</option>
          <option value="Watches">Watches</option>
          <option value="Wireless">Wireless</option>
          <option value="WirelessAccessories">Wireless Accessories</option>
        </select>
        <div class="input-description">
          Select the most specific search category. Selecting 'All' will limit
          results to 50. Also, you can not filter by price when selecting 'All'
        </div>
      </div>
    </div>

    <div class="quick-store-element product">
      <div class="input-label">Name Contains:</div>
      <div>
        <input type="text" name="sfQuickStoreName" id="txtSfQuickStoreName"/>
          <div class="input-description">
            What words have to appear in the product's name? (Separate distinct terms with a comma - Max of 5)
          </div>
      </div>
    </div>
    <div class="quick-store-element product">
      <div class="input-label">Name does not contain:</div>
      <div>
        <input type="text" name="sfQuickStoreName" id="txtSfQuickStoreNameNeg"/>
          <div class="input-description">
            What words <strong>CANNOT</strong> appear in the product's name?
          </div>
        </div>
    </div>
    <div class="quick-store-element product">
      <tr>
        <div class="input-label">Category Contains:</div>
        <div>
          <input type="text" name="sfQuickStoreCat" id="txtSfQuickStoreCat"/>
            <div class="input-description">
              What words have to appear in the product's category name?
            </div>
          </div>
      </div>
    <div class="quick-store-element product">
      <div class="input-label">Category does not contain:</div>
      <div>
        <input type="text" name="sfQuickStoreCatNeg" id="txtSfQuickStoreCatNeg"/>
          <div class="input-description">
            What words <strong>CANNOT</strong> appear in the product's category name?
          </div>
        </div>
    </div>
    <div class="quick-store-element product coupon">
      <div class="input-label">Merchant name contains:</div>
      <div>
        <input type="text" name="sfQuickStoreMerchant" id="txtSfQuickStoreMerchant"/>
          <div class="input-description">
            What words have to appear in the name of the merchant selling
            the product?
          </div>
        </div>
    </div>
    <div class="quick-store-element product amazon">
      <div class="input-label">Min. Price:</div>
      <div>
        <input type="text" name="sfQuickStorePrice" id="txtSfQuickStorePrice"/>
          <div class="input-description">
            What's the minimum price for the product?
          </div>
        </div>
    </div>
    <div class="quick-store-element product amazon">
      <div class="input-label">Max Price:</div>
      <div>
        <input type="text" name="sfQuickStoreMaxPrice" id="txtSfQuickStoreMaxPrice"/>
          <div class="input-description">
            What's the maximum price for the product?
          </div>
        </div>
    </div>
    <div class="quick-store-element product">
      <div class="input-label">Minimum Discount Percent:</div>
      <div>
        <input type="text" name="sfQuickStoreDiscount" id="txtSfQuickStoreDiscount"/>
          <div class="input-description">
            What's the minimum discount percentage for the product?
          </div>
        </div>
    </div>

    <div class="button-container">
      <input class="button-secondary" type="submit" name="jem_sf_close_quick_store"
             id="jem-sf-add-store" value="Cancel" onclick="jQuery('#divSfQuickStoreDialog').dialog('close'); return false;"/>
        &nbsp;&nbsp;
        <input class="button-primary" type="submit" name="jem_sf_add_quick_store"
               id="jem-sf-add-store" value="Add Quick Store" onclick="jemSfAddQuickShortCode(); return false;"/>
        </div>
  </div>

  <?php else: ?>

  <div>
    The SellFire Plug-In hasn't been configured yet.
    <a target="_SellFire" href=""
      <?php echo admin_url('admin.php?page=jem_sf_sellfire_networks') ?>">
      Click here to configure it.
    </a>
  </div>

  <?php endif; ?>