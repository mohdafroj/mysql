jQuery(function ($) {
	
	/* move content another div js */
	var myContent = $(".left-column").html();
	$(window).on("resize load", function() {
		if($(window).width() <=767) {
			$(".right-column").html(myContent);
			$(".left-column").html('');
		}else{
			$(".left-column").html(myContent);
			$(".right-column").html('');
		}
	});
					  
}); /* end of jequery */

