jQuery(document).ready(function($){

  var r=$('<input/>').attr({
        type: "button",
        id: "upload-button",
        
        class: "button-primary",
        value: 'Select/Upload SSL Image'
    });
    $(r).insertAfter("#wcsealdomain_image"); 

 
  var mediaUploader;

  $('#upload-button').click(function(e) {
    e.preventDefault();
    // If the uploader object has already been created, reopen the dialog
      if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: 'Choose SSL Image',
      button: {
      text: 'Choose SSL Image'
    }, multiple: false });

    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on('select', function() {
      attachment = mediaUploader.state().get('selection').first().toJSON();
      $('#wcsealdomain_image').val(attachment.url);
    });
    // Open the uploader dialog
    mediaUploader.open();
  });

});