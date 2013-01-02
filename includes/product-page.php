<?php
    $options = get_option('jem_sf_options');
    $pp = $jem_sf_product_page;
    $cross_sell_columns = $options['pp_xsell_cols'];
    $max_cross_sells = $options['pp_xsell_max'];
    $xsell_header = $options['pp_xsell_header']; 
    $xsell_img = $options['pp_xsell_img'];
    $returned_cross_sells = sizeof($pp->CrossSells);
    if ($returned_cross_sells < $max_cross_sells)
    {
        $max_cross_sells = $returned_cross_sells;
    }
    $button_text = $options['pp_button_text'];   
    $merchant_header = $options['pp_merchant_header'];
    $image_width = $options['pp_image_width'];
?>

<table cellspacing="0" cellpadding="0" border="0" style="border: none; vertical-align: top; padding-top: 10px; padding-bottom: 10px" class="sfpp-product-overview">
    <tr>
        <td style="padding-right: 20px; border: none;">
            <a href="<?php echo $pp->MainProduct->Url ?>" style="border: none">
                <img src="<?php echo $pp->PrimaryImage ?>" alt="<?php echo $pp->MainProduct->Name ?>" width="<?php echo $image_width ?>px">
            </a>
        </td>
        <td style="vertical-align: top; border: none;">
            <?php echo $pp->MainProduct->ShortDescription ?>
            <div style="padding-top: 10px; text-align: right;">
                <h3 class="sfpp-main-price"><?php echo $pp->MainProduct->FormattedPrice ?></h3>
                <div style="padding: 10px 0 10px 0">
                    <a href="<?php echo $pp->MainProduct->Url ?>" id="sfpp-button">
                        <?php echo $button_text ?>
                    </a>                    
                </div>
            </div>        
            <div style="text-align: right; margin-left: auto; padding-top: 10px;">
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style ">
                <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                <a class="addthis_button_tweet"></a>
                <a class="addthis_button_pinterest_pinit"></a>
                <a class="addthis_counter addthis_pill_style"></a>
                </div>
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-50ca584d7d3a9ff2"></script>
                <!-- AddThis Button END -->
            </div>            
        </td>
    </tr>
</table>

<hr/>

<h2 class="sfpp-header"><?php echo $merchant_header ?></h2>
<div style="padding-top: 10px">
    <table cellpadding="0" cellspacing="0" border="0" style="border: none;">
        <?php 
        foreach ($pp->AllProducts as $product)
        {
            ?>
                <tr>
                    <td style="border: none; width: 300px;">
                        <a href="" >
                        <?php 
                            if ($product->Merchant->LogoUrl)
                            {
                                echo '<img src="' . $product->Merchant->LogoUrl . '" alt="' . $product->Merchant->Name . '" width="150px"/>';                                
                            }
                            else
                            {
                                echo '<h4 class="sfpp-merchant-name">' . $product->Merchant->Name . '</h4>'; 
                            }
                        ?>
                        </a>
                    </td>                    
                    <td style="border: none" class="sfpp-merchant-price">
                        <?php jem_sf_renderPrice($product)  ?>
                    </td>                           
                    <td style="border: none; padding-left: 10px">
                        <a href="<?php echo $product->Url ?>" style="white-space: nowrap" class="sfpp-secondary-button">
                            <?php echo $button_text ?>
                        </a>
                    </td>                       
                </tr>
            <?php
        }
        ?>
    </table>
</div>

<hr/>


<h2 class="sfpp-header"><?php echo $xsell_header ?></h2>
<div style="padding-top: 10px">
    <table  cellpadding="0" cellspacing="0" border="0" id="sfpp-cross-sells">
        <?php 
        $cross_sell_row = array();
        for ($i = 0; $i < $max_cross_sells; $i++)
        {
            $product = $pp->CrossSells[$i];
            $cross_sell_row[] = $product;
            if (sizeof($cross_sell_row) == $cross_sell_columns || $i == ($max_cross_sells - 1))
            {
                jem_sf_render_crosssell_row($cross_sell_row, $xsell_img, $i == ($max_cross_sells - 1));
                $cross_sell_row = array();
            }
        }
        ?>
    </table>
</div>

<img src="http://www.sfafflinks.com/StoreDisplay/LogProductPageView?id=<?php echo $pp->Id ?>" width="1px" height="1px"/>
     
<?php

function jem_sf_render_crosssell_row($products, $xsell_img, $last_row){    
    echo '<tr class="sfpp-xsell-name-row">';
    $product_count = sizeof($products);
    $i = 0;
    foreach ($products as $product)
    {
        $i++;
        $cell_class = 'sf-inner-xsell';
        if ($i == $product_count)
        {
            $cell_class = 'sf-outer-xsell';
        }        
        ?>
        <td style="vertical-align: top;  text-align: center; padding-top: 10px;" class="<?php echo $cell_class ?>">
            <a href="<?php echo $product->Url ?>" class="sfpp-xsell-name">
                <?php echo $product->Name ?>    
            </a>                    
        </td>                                  
        <?php        
    }
    echo '</tr>';
    echo '<tr class="sfpp-xsell-image-row">';
    $i = 0;
    foreach ($products as $product)
    {
        $i++;
        $cell_class = 'sf-inner-xsell';
        if ($i == $product_count)
        {
            $cell_class = 'sf-outer-xsell';
        }          
        ?>
        <td style="vertical-align: top; text-align: center; padding-bottom: 10px;" class="<?php echo $cell_class ?>">
            <a href="<?php echo $product->Url ?>" style="border: none">
                <img src="<?php echo $product->SmallImageUrl ?>" border="0" width="<?php echo $xsell_img ?>px" style="border: none;" class="sfpp-xsell-image"/>                    
            </a>                                        
        </td>                                
        <?php        
    }
    echo '</tr>';    
    echo '<tr clas="sfpp-xsell-price-row">';
    $i = 0;
    foreach ($products as $product)
    {
        $i++;
        $cell_class = 'sf-inner-xsell';
        if ($i == $product_count)
        {
            $cell_class = 'sf-outer-xsell';
        }          
        if (!$last_row)
        {
            $cell_class = $cell_class . ' sf-xsell-bottom';
        }
        ?>
        <td style="vertical-align: top; text-align: right" class="<?php echo $cell_class ?>">
            <?php jem_sf_renderPrice($product) ?>
        </td>   
        <?php        
    }
    echo '</tr>';       
}

function jem_sf_renderPrice($product) 
{
    if ($product->DiscountAmount > 0) 
    {
        echo '<span class="sfpp-cross-out">' . $product->FormattedListPrice . '</span><span class="sfpp-sale-price">' . $product->FormattedPrice. '</span>';
    } 
    else 
    {    
        echo '<span class="sfpp-nonsale-price">' . $product->FormattedPrice . '</span>';
    }    
}
?>

 
                
                
   