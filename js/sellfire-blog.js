function jemSfMoveStores()
{
    jQuery.each(jQuery('.sf-temp-store-holder'), function(index, value)
    {
      jElement = jQuery(value);      
      jElement.appendTo('#' + jElement.attr("id").replace('Temp', '')).toggle(true);
    });       
}

jQuery(document).ready(jemSfMoveStores);