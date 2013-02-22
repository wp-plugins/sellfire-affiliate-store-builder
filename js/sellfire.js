jQuery.fn.extend({
insertAtCaret: function(myValue){
  return this.each(function(i) {
    if (document.selection) {
      //For browsers like Internet Explorer
      this.focus();
      sel = document.selection.createRange();
      sel.text = myValue;
      this.focus();
    }
    else if (this.selectionStart || this.selectionStart == '0') {
      //For browsers like Firefox and Webkit based
      var startPos = this.selectionStart;
      var endPos = this.selectionEnd;
      var scrollTop = this.scrollTop;
      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
      this.focus();
      this.selectionStart = startPos + myValue.length;
      this.selectionEnd = startPos + myValue.length;
      this.scrollTop = scrollTop;
    } else {
      this.value += myValue;
      this.focus();
    }
  })
}
});

function jemSfAddShortCode() {
    var shortCodeValue = '[sellfire id="' + jQuery("#jem_sf_store").val() + 
        '" name="' + jQuery("#jem_sf_store option:selected").text() + '"]';
    
    jemSfInsertEditorText(shortCodeValue);
    
    return false;
}

function jemSfInsertEditorText(text) {
    var rich;
    rich = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();

    if (rich) {
        tinyMCE.activeEditor.focus();
        tinyMCE.execCommand("mceInsertContent", false, text);
    } else {                
        jQuery("#content").insertAtCaret(text)
    }    
    
    return false;    
}

function jemSfAddQuickShortCode() {
    var shortCodeValue, theme, name, nameNeg, cat, catNeg, minPrice, minDiscount, maxPrice, keyword, amazonSite, amazonCategory;
    
    storeType =  jQuery("#sltSfQuickStoreType").val();    
    
    shortCodeValue = "[sellFireQuick type='" + storeType + "'";
    
    theme = jQuery('#sltSfQuickStoreProductTheme').val();    
    couponTheme = jQuery('#sltSfQuickStoreCouponTheme').val();   
    keyword = jQuery('#txtSfQuickStoreKeyword').val();    
    amazonSite = jQuery('#sltSfQuickStoreAmazonSite').val();  
    amazonCategory = jQuery('#sltSfQuickStoreSearchCategory').val();  
    name = jQuery('#txtSfQuickStoreName').val();    
    nameNeg = jQuery('#txtSfQuickStoreNameNeg').val();
    cat = jQuery('#txtSfQuickStoreCat').val();
    catNeg = jQuery('#txtSfQuickStoreCatNeg').val();
    minPrice = jQuery('#txtSfQuickStorePrice').val();
    maxPrice = jQuery('#txtSfQuickStoreMaxPrice').val();
    minDiscount = jQuery('#txtSfQuickStoreDiscount').val();
    merchant = jQuery('#txtSfQuickStoreMerchant').val();
    storeType =  jQuery("#sltSfQuickStoreType").val();    
    
    if (storeType === 'product')
    {
        if (theme) shortCodeValue += " themeId='" + theme + "'";     
        if (name) shortCodeValue += " name='" + name + "'"; 
        if (nameNeg) shortCodeValue += " nameneg='" + nameNeg + "'"; 
        if (cat) shortCodeValue += " category='" + cat + "'"; 
        if (catNeg) shortCodeValue += " categoryneg='" + catNeg + "'"; 
        if (merchant) shortCodeValue += " merchant='" + merchant + "'"; 
        if (minPrice) shortCodeValue += " minPrice='" + minPrice + "'"; 
        if (maxPrice) shortCodeValue += " maxPrice='" + maxPrice + "'"; 
        if (minDiscount) shortCodeValue += " minDiscount='" + minDiscount + "'";
    }
    else if (storeType === 'amazon')        
    {
        if (theme) shortCodeValue += " themeId='" + theme + "'";   
        if (keyword) shortCodeValue += " keyword='" + keyword + "'"; 
        if (minPrice) shortCodeValue += " minPrice='" + minPrice + "'"; 
        if (maxPrice) shortCodeValue += " maxPrice='" + maxPrice + "'"; 
        shortCodeValue += " amazonSite='" + amazonSite + "'"; 
        shortCodeValue += " amazonCat='" + amazonCategory + "'"; 
    }
    else if (storeType === 'coupon')
    {
        if (theme) shortCodeValue += " themeId='" + couponTheme + "'";       
        if (keyword) shortCodeValue += " keyword='" + keyword + "'"; 
        if (merchant) shortCodeValue += " merchant='" + merchant + "'"; 
    }           
 
    shortCodeValue += "]";
    jemSfInsertEditorText(shortCodeValue);
    jQuery("#divSfQuickStoreDialog").dialog("close");    
    return false;
}

function jemSfShowQuickStoreDialog(){
    
    jQuery("#sltSfQuickStoreType").val("product")
    jemQuickStoreTypeChange();
    jQuery("#divSfQuickStoreDialog").dialog("open");    
}

function jemQuickStoreTypeChange() {
    var jElement = jQuery("#divSfQuickStoreDialog");    
    jElement.find("input[type='text']").val("");    
    jElement.find(".quick-store-element").toggle(false);
    jElement.find("." + jQuery("#sltSfQuickStoreType").val()).toggle(true).each(
        function (index, value)
        {
            if (index % 2 == 1)
            {
                jQuery(value).addClass("alt")
            }
            else
            {
                jQuery(value).removeClass("alt")
            }            
        });    
}

function jemSfQuickStoreDialogOnEnter(event)
{
    if (event.which === 13)
    {
        return jemSfAddQuickShortCode();
    }
    return true;
}
        
/*
 * Issues an AJAX request to flush cache
 */
function jemSfFlushCache() {
    var data = {
        action: 'jem_sf_flush_cache'
    };
    jQuery("#jem_sf_flush_message").html("");
    jQuery.get(jem_sf.ajaxurl, data, jemSfShowFlushMessage);
    return false;
}

function jemSfShowFlushMessage() {
    jQuery("#jem_sf_flush_message").html("Cache has been flushed");
}

var lastPPPageNumber = 0;
 
function importWooCommerceStore(data) {
    if (data.pageNumber == 1)
    {
        jQuery('#divSfPPID').dialog('open');
        jQuery('#divSfPPIDImportInProgress').toggle(true);
        jQuery('#divSfPPIDImportComplete').toggle(false);
    }            
    jQuery('#divSfPPIDCurrentPage').text(data.pageNumber);
    var data = {
        action: 'jem_sf_import_woocommerce_action',
        storeId: data.storeId,
        pageNumber: data.pageNumber
    };
    jQuery.get(jem_sf.ajaxurl, data, importWooCommerceResponse);    
    return false;
    
}

function importWooCommerceResponse(data) {
    data = jQuery.parseJSON(data);
    if (data.hasMore)
    {
        jQuery('#divSfPPIDImportCount').text(data.importedCount + parseInt(jQuery('#divSfPPIDImportCount').text(),10));
        jQuery('#divSfPPIDTotalPages').text(data.totalPages);
        data.pageNumber = parseInt(data.pageNumber, 10) + 1;
        importWooCommerceStore(data);
    }
    else
    {        
        jQuery('#divSfPPIDImportInProgress').toggle(false);
        jQuery('#divSfPPIDImportComplete').toggle(true);        
    }
} 

var lastStoreType = '';
function importPremiumPressStore(data) {
    if (data.pageNumber == 1)
    {
        jQuery('#divSfPPID').dialog('open');
        jQuery('#divSfPPIDImportInProgress').toggle(true);
        jQuery('#divSfPPIDImportComplete').toggle(false);
    }            


    jQuery('#divSfPPIDCurrentPage').text(data.pageNumber);    
    data = {
        action: 'jem_sf_store_export_pp',
        storeId: data.storeId,
        storeType: data.storeType,
        pageNumber: data.pageNumber
    };
    jQuery.get(jem_sf.ajaxurl, data, importPremiumPressResponse);    
    return false;    
} 

function importPremiumPressResponse(data) {
    data = jQuery.parseJSON(data);
    if (data.hasMore)
    {
        jQuery('#divSfPPIDImportCount').text(data.importedCount + parseInt(jQuery('#divSfPPIDImportCount').text(),10));
        jQuery('#divSfPPIDTotalPages').text(data.totalPages);        
        data.pageNumber = parseInt(data.pageNumber, 10) + 1;
        importPremiumPressStore(data);
    }
    else
    {        
        jQuery('#divSfPPIDImportInProgress').toggle(false);
        jQuery('#divSfPPIDImportComplete').toggle(true);        
    }
}        

function jemSfOptionsPageOnReady() {
    var jElement = jQuery('#divSfPPID');
    jElement.dialog({ autoOpen: false, modal: true, title: "Import from SellFire", minWidth: 600, dialogClass: 'wp-dialog' });
}

jQuery(document).ready(function() {
    var jElement = jQuery('#divSfQuickStoreDialog');
    if (jElement.length > 0)
    {
        jElement.dialog({ autoOpen: false, modal: true, title: "Add Quick Store", minWidth: 600, dialogClass: 'wp-dialog' });
        jQuery("#divSfQuickStoreDialog input[type='text']").keypress(jemSfQuickStoreDialogOnEnter);            
    }   
});