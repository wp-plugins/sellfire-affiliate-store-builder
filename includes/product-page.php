<?php

    if (!function_exists('jem_sf_render_crosssell_row'))
    {
        function jem_sf_render_crosssell_row($products, $xsell_img, $last_row, $num_columns, $targetText){    
            $pageText = '<tr class="sfpp-xsell-name-row">';
            $product_count = sizeof($products);
            $i = 0;
            $cell_width = floor(98/$num_columns) . "%";
            foreach ($products as $product)
            {
                $i++;
                $cell_class = 'sf-inner-xsell';
                if ($i == $product_count)
                {
                    $cell_class = 'sf-outer-xsell';
                }        

                $pageText = $pageText . '<td style="vertical-align: top;  text-align: center; padding-top: 10px; width:' . $cell_width .'" class="' . $cell_class .'">
                    <div class="sf-xsell-side-margin">
                        <a href="' . $product->Url .'" class="sfpp-xsell-name"' . $targetText .' >
                            ' . $product->Name . '    
                        </a>  
                    </div>                              
                </td>';                                     
            }
            for ($i = $product_count; $i < $num_columns; $i++)
            {
                $pageText = $pageText . '<td></td>';                
            }
            $pageText = $pageText . '</tr><tr class="sfpp-xsell-image-row">';
            $i = 0;
            foreach ($products as $product)
            {
                $i++;
                $cell_class = 'sf-inner-xsell';
                if ($i == $product_count)
                {
                    $cell_class = 'sf-outer-xsell';
                }          
                $pageText = $pageText . '<td style="vertical-align: top; text-align: center; padding-bottom: 10px;" class="' . $cell_class . '">
                    <div class="sf-xsell-side-margin">
                        <a href="' . $product->Url .'" style="border: none"' . $targetText.'>
                            <img src="' . $product->SmallImageUrl .'" border="0" width="' . $xsell_img . 'px" style="border: none;" class="sfpp-xsell-image"/>                    
                        </a>    
                    </div>
                </td>';                                                  
            }
            for ($i = $product_count; $i < $num_columns; $i++)
            {
                $pageText = $pageText . '<td></td>';                
            }
            $pageText = $pageText . '</tr>';    
            $pageText = $pageText . '<tr clas="sfpp-xsell-price-row">';
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
                $pageText = $pageText . '<td style="vertical-align: top; text-align: right" class="' . $cell_class .'">            
                    <div class="sf-xsell-side-margin sf-xsell-bottom-margin">' .
                        jem_sf_renderPrice($product) . '
                    </div>
                </td>';     
            }
            for ($i = $product_count; $i < $num_columns; $i++)
            {
                $pageText = $pageText . '<td></td>';         
            }
            $pageText = $pageText . '</tr>';       
            return $pageText;
        }

        function jem_sf_renderPrice($product) 
        {
            if ($product->DiscountAmount > 0) 
            {
                return '<span class="sfpp-crossout-price">' . $product->FormattedListPrice . '</span><span class="sfpp-sale-price">' . $product->FormattedPrice. '</span>';
            } 
            else 
            {    
                return '<span class="sfpp-nonsale-price">' . $product->FormattedPrice . '</span>';
            }    
        }   
        
        function jem_sf_renderProductPage($product_page_var)
        {
            $options = get_option('jem_sf_options');
            $pp = $product_page_var;
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
            $call_to_action_img = $options['pp_call_to_action_img'];
            $new_window = $options['pp_new_window'];
            $targetText = $new_window ? " target='_BLANK'" : "";

            $pageText = '<div id="divSfProductPage" class="sfpp-outer-container">    
            <table cellspacing="0" cellpadding="0" border="0" style="border: none; vertical-align: top; padding-top: 10px; padding-bottom: 10px" class="sfpp-product-overview">
                <tr>
                    <td style="padding-right: 20px; border: none; vertical-align: top; min-width: ' .  $image_width . 'px">
                        <a href="' .  $pp->MainProduct->Url . '" style="border: none"' .  $targetText. '>
                            <img src="' .  $pp->PrimaryImage . '" alt="' .  $pp->MainProduct->Name . '" width="' . $image_width . 'px" style="min-width: ' .  $image_width . ' px"/>
                        </a>
                    </td>
                    <td style="vertical-align: top; border: none;">
                        ' .  $pp->MainProduct->ShortDescription . '
                        <div style="padding-top: 10px; text-align: right;">
                            <h3 class="sfpp-main-price">' .  $pp->MainProduct->FormattedPrice . '</h3>
                            <div style="padding: 10px 0 10px 0">';
                                    if ($call_to_action_img != null && strlen($call_to_action_img) > 0)
                                    {
                                    $pageText = $pageText . '	
                                        <a href="' .  $pp->MainProduct->Url . '"' .  $targetText. ' style="border: none">
                                            <img src="' .  $call_to_action_img . '" style="border: none" alt="' .  $button_text . '"/>
                                        </a>                      
                                    ';
                                    }
                                    else
                                    {
                                    $pageText = $pageText .  '
                                        <a href="' . $pp->MainProduct->Url . '" id="sfpp-button"' . $targetText. '>
                                            ' . $button_text . '</a>';
                                    }
                                $pageText = $pageText .  '                 
                            </div>
                        </div>                    
                    </td>
                </tr>
            </table>
            <hr/>
            <div style="padding-top: 10px">
                <h2 class="sfpp-header">' . $merchant_header . '</h2>
            </div>
            <div style="padding-top: 10px">
            <table cellpadding="0" cellspacing="0" border="0" style="border: none;">';
                foreach ($pp->AllProducts as $product)
                {
                    $pageText = $pageText .  '
                        <tr>
                            <td style="border: none; width: 300px;">
                                <a href="' .  $product->Url . '"' . $targetText. '>';
                                    if ($product->Merchant->LogoUrl)
                                    {
                                        $pageText = $pageText . '<img src="' . $product->Merchant->LogoUrl . '" alt="' . $product->Merchant->Name . '" width="100px"/>';                                
                                    }
                                    else
                                    {
                                        $pageText = $pageText . '<h4 class="sfpp-merchant-name">' . $product->Merchant->Name . '</h4>'; 
                                    }
                $pageText = $pageText . '
                                </a>
                            </td>                    
                            <td style="border: none" class="sfpp-merchant-price">
                                ' .  jem_sf_renderPrice($product)  . '
                            </td>                           
                            <td style="border: none; padding-left: 10px">
                                <a href="' .  $product->Url . '" style="white-space: nowrap" class="sfpp-secondary-button"' . $targetText. '>
                                    ' .  $button_text . '
                                </a>
                            </td>                       
                        </tr>';
                }
                $pageText = $pageText . '
            </table>
            </div>
            <hr/>
            <div style="padding-top: 10px">
                <h2 class="sfpp-header">' .  $xsell_header . '</h2>
            </div>

            <div style="padding-top: 10px">
                <table  cellpadding="0" cellspacing="0" border="0" id="sfpp-cross-sells" style="width: 100%">';  
                    $cross_sell_row = array();
                    for ($i = 0; $i < $max_cross_sells; $i++)
                    {
                        $product = $pp->CrossSells[$i];
                        $cross_sell_row[] = $product;
                        if (sizeof($cross_sell_row) == $cross_sell_columns || $i == ($max_cross_sells - 1))
                        {
                            $pageText = $pageText . jem_sf_render_crosssell_row($cross_sell_row, $xsell_img, $i == ($max_cross_sells - 1), $cross_sell_columns, $targetText);
                            $cross_sell_row = array();
                        }
                    }
            $pageText = $pageText .  '</table></div>
            <img src="http://www.sfafflinks.com/StoreDisplay/LogProductPageView?id=' .  $pp->Id . '" width="1px" height="1px"/>
            </div>';

            return $pageText;
        }
    }
    




 
                
                
   