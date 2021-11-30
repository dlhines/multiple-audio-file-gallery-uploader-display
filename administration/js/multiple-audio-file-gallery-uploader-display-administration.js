(function($) {

  $.fn.save_post_types = function () {
    $.ajax({
        url: mafgud_set_post_types.ajax_url,
        type: 'POST',
        data: {
          action: 'mafgud_set_post_types',
          post_types: post_types,
          security: mafgud_set_post_types.ajax_nonce
        },
        success: function(response) {
            alert(response);
            window.location.reload();
        }
    });
  }

  $(".set_post_types #set_post_types").on('click', function(){
    var post_types = $("#MAFGUD_post_types").val();
    if(post_types.length !== 0) {
      $.ajax({
          url: mafgud_set_post_types.ajax_url,
          type: 'POST',
          data: {
            action: 'mafgud_set_post_types',
            post_types: post_types,
            security: mafgud_set_post_types.ajax_nonce
          },
          success: function(response) {
              alert(response);
              window.location.reload();
          }
      });
    } else {
      if(confirm("You have '" + post_types.length + "' Post Types selected. You will be clearing any and all Post Types of using MAFGUD.\n\nAre you sure you want to clear all Post Types?\n")) {
        post_types = "";
        $.ajax({
            url: mafgud_set_post_types.ajax_url,
            type: 'POST',
            data: {
              action: 'mafgud_set_post_types',
              post_types: post_types,
              security: mafgud_set_post_types.ajax_nonce
            },
            success: function(response) {
                alert(response);
                window.location.reload();
            }
        });
      }
    }
  });

  $(".set_post_types #settings #MAFGUD_post_types option").dblclick(function(){
    $(this).prop('selected', false);
  });
})(jQuery);
