jQuery(document).ready(function($){

  

  //The button in the editor
  $('.pt_link_button').on('click', function(event) {

    jQuery.data(document.body, 'prevElement', $(this).prev());

    wpActiveEditor = true;	// We need to override this var as the link dialogue is expecting an actual wp_editor instance
    wpLink.open();	// Open the link popup
    return false;
  });

  //The submit button in the link field
  $('#wp-link-submit').on('click', function(event) {

    var field_value = jQuery.data(document.body, 'prevElement');

    var linkAtts = wpLink.getAttrs();	// The links attributes (href, target) are stored in an object, which can be access via  wpLink.getAttrs()
    $(field_value).val(linkAtts.href);	// Get the href attribute and add to a textfield, or use as you see fit
    wpLink.textarea = $(field_value);	// To close the link dialogue, it is again expecting an wp_editor instance, so you need to give it something to set focus back to.
    wpLink.close();	// Close the dialogue
    event.preventDefault ? event.preventDefault() : event.returnValue = false;  // Trap any events
    event.stopPropagation();  // Trap any events
    return false;
  });

  //The cancel button in the link field
  $('#wp-link-cancel').on('click', function(event) {

    var field_value = jQuery.data(document.body, 'prevElement');

    wpLink.textarea = $(field_value);
    wpLink.close();
    event.preventDefault ? event.preventDefault() : event.returnValue = false;    // Trap any events
    event.stopPropagation();  // Trap any events
    return false;
  });

  //The close button in the link field 
  $('#wp-link-close').on('click', function(event) {

    var field_value = jQuery.data(document.body, 'prevElement');

    wpLink.textarea = $(field_value);
    wpLink.close();
    event.preventDefault ? event.preventDefault() : event.returnValue = false;    // Trap any events
    event.stopPropagation();  // Trap any events
    return false;
  });
});