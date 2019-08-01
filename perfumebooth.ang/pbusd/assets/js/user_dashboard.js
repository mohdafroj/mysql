console.log("user_dashboard js called");
jQuery(function ($) {
	/* start of toggle_left_effect on mobile device */
	$(document).ready(function() {
        $(".account_toggle").click(function() {
            $(".account_toggle").toggleClass("open");
			$(".profile_list").toggleClass("toggle_open");
        });
    });
	/* end of toggle_left_effect on mobile device */
	
	/* start of browser_image js */
	$(document).on('click', '.browse', function(){
	  var file = $(this).parent().parent().parent().find('.uploadfile');
	  file.trigger('click');
	});
	$(document).on('change', '.uploadfile', function(){
	  $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
	});
	/* end of browser_image js */
					  
}); /* end of jequery */