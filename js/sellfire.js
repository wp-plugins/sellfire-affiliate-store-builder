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
    var rich, shortCodeValue;
    rich = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
    shortCodeValue = '[sellfire id="' + jQuery("#jem_sf_store").val() + 
        '" name="' + jQuery("#jem_sf_store option:selected").text() + '"]';
    
    
    if (rich) {
        tinyMCE.activeEditor.focus();
        tinyMCE.execCommand("mceInsertContent", false, shortCodeValue);
    } else {                
        jQuery("#content").insertAtCaret(shortCodeValue)
    }    
    
    return false;
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
