(function($) {

    // On page load to check empty gallery
    if( $("#audio-uploader-display #gallery_load").is(":empty")){
      $("#audio-uploader-display #gallery_load").html('<h4>Click "Select Audio File(s)"</h4>');
    }

    // Toggle instructions
    $("#audio-uploader-display").on('click', '#instructions p.instructions_header', function(){
      // Toggles instructions
      $("#audio-uploader-display #instructions section").toggle();

      // Changes contents of p tag
      if($("#instructions section").is(":visible")) {
        $("#audio-uploader-display #instructions p.instructions_header").html("MAFGUD Instructions (Hide)")
      } else {
        $("#audio-uploader-display #instructions p.instructions_header").html("MAFGUD Instructions (Show)")
      }
    })


    // var for WP Media Uploader
    var mediaUploader;

    // jQuery UI sortable functions
    // Custom namespace SORT for jQuery Sortable. This needs to be called again after ajax
    // https://jqueryui.com/sortable/
    ;AUDIO_SORT = {
      sort: function () {
        $( "#audio_sortable" ).sortable({
          revert: true,
          update: function( event, ui ) {
            var audio_ids = new Array();
            var postID = $("#select_audio").attr("data-id");
            $("#audio-uploader-display #gallery_load ul li span").map(function(index){
              audio_ids.push(this.id);
            });
            $.ajax({
              url: mafgud_sort_audio.ajax_url,
              type: 'post',
              data: { action: 'mafgud_sort_audio', audio_ids: audio_ids, postID: postID },
              success: function(response) {
                audio_ids.splice(0, audio_ids.length);
                var value = JSON.parse(response);
                if(value.updated !== false && value.audio !== "") {
                  $("#audio-uploader-display #gallery_load").html(value.audio);
                  AUDIO_SORT.sort(); // Re-establish jQuery Sortable after ajax calls
                } else {
                  $("#audio-uploader-display #gallery_load").html('<h4>Click "Select Audio File(s)"</h4>');
                }
              }
            });
          }
        });
      }
    }

    // Call the Sort function
    AUDIO_SORT.sort();

    //
    // Select Audio
    //
    $("#audio-uploader-display").on('click', '#select_audio', function(){

      // Array to save audio ids, urls, and get the contents to the gallery load div
      var postID = $(this).attr("data-id");
      var audio_ids = new Array();
      var nonce = $("#audiouploader_display_meta_box_nonce").val()
      var display = $("#audio-uploader-display #gallery_load").html();

      if (mediaUploader) {
         mediaUploader.open();
         return;
       }
       mediaUploader = wp.media.frames.file_frame = wp.media({
         title: 'Select Audio(s)',
         button: {
         text: 'Select Audio(s)'
       }, multiple: true });
       mediaUploader.on('select', function() {
         var selection = mediaUploader.state().get('selection');
         selection.map( function( attachment ) {
             attachment = attachment.toJSON();
             audio_ids.push(attachment.id);
         });
        $.ajax({
          url: mafgud_save_audio.ajax_url,
          type: 'post',
          data: { action: 'mafgud_save_audio', security: mafgud_save_audio.ajax_nonce, audio_ids: audio_ids, postID: postID, nonce: nonce
          },
          success: function(response) {
            audio_ids.splice(0, audio_ids.length);
            var value = JSON.parse(response);
            if(value.updated !== false && value.audio !== "") {
              $("#audio-uploader-display #gallery_load").html(value.audio);
              AUDIO_SORT.sort();
            } else {
              $("#audio-uploader-display #gallery_load").html('<h4>Click "Select Audio"</h4>');
            }
          }
        });
       });
       mediaUploader.open();
    });

    //
    // Double click audio for deletion
    //
    $("#audio-uploader-display").on('dblclick', '#gallery_load ul li span',function() {
      if($(this).hasClass("remove")) {
        $(this).removeClass("remove");
      } else {
        $(this).addClass("remove");
      }
    });

    //
    // Delete Audio
    //
    $("#audio-uploader-display").on('click', '#delete_audio', function(e){
      e.preventDefault();
      var postID = $(this).attr("data-id");
      var audio_ids = new Array();

      $("#audio-uploader-display #gallery_load .remove").map(function(index){
        audio_ids.push(this.id);
      });
      if(audio_ids.length !== 0) {
        $.ajax({
          url: mafgud_delete_audio.ajax_url,
          type: 'post',
          data: { action: 'mafgud_delete_audio', postID: postID, audio_ids: audio_ids },
          success: function(response) {
            audio_ids.splice(0, audio_ids.length);
            var value = JSON.parse(response);
            // Check to make sure value.updated is not false
            if(value.updated !== false) {
              $("#audio-uploader-display #gallery_load").html(value.audio);
              SORT.sort();
            } else {
              alert("Error: Did not save selected audio(s).");
            }
          }
        });
      } else {
        alert("You have not selected any audio to delete.");
      }

      // End jQuery
    });
})(jQuery);
