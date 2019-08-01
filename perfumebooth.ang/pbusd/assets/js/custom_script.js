jQuery(function ($) {

	/* start of scrolltop button js */
		$(window).scroll(function() {
		if ($(this).scrollTop() > 50 ) {
			$('.scrollUp:hidden').stop(true, true).fadeIn();
		} else {
			$('.scrollUp').stop(true, true).fadeOut();
		}
		});

		$(function(){$(".scrollUp").click(function(){$("html,body").animate({scrollTop:$(".thetop").offset().top},"500");return false})})
	 /*end of scrolltop button js */

	/* start modal popup show and hide js */
	var modalUniqueClass = ".modal";
	$('.modal').on('show.bs.modal', function(e) {
	  var $element = $(this);
	  var $uniques = $(modalUniqueClass + ':visible').not($(this));
	  if ($uniques.length) {
		$uniques.modal('hide');
		$uniques.one('hidden.bs.modal', function(e) {
		  $element.modal('show');
		});
		return false;
	  }
	});
	/* end modal popup show and hide js */

}); /* end of jequery */

