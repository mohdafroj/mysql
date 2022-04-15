jQuery(function ($) {
	
	/* start of wishlist js */
	$(document).ready(function() {
        $(".wishlist").click(function() {
            $(this).toggleClass("wishlist_color");
        });
    });
	/* end of wishlist js */
	
	/* start accordion tab */
		$(".panel-collapse.in").each(function(){
			$(this).siblings(".panel-heading").find(".fa").addClass("fa-angle-up").removeClass("fa-angle-down");
		});
		
		// Toggle plus minus icon on show hide of collapse element
		$(".panel-collapse").on('show.bs.collapse', function(){
			$(this).parent().find(".fa").removeClass("fa-angle-down").addClass("fa-angle-up");
		}).on('hide.bs.collapse', function(){
			$(this).parent().find(".fa").removeClass("fa-angle-up").addClass("fa-angle-down");
		});
	/* end accordion tab */
	
	/* start open/close filter js */
	$('.cd-filter-trigger').on('click', function(){
		triggerFilter(true);
	});
	$('.cd-filter .cd-close').on('click', function(){
		triggerFilter(false);
	});

	function triggerFilter($bool) {
		var elementsToTrigger = $([$('.cd-filter-trigger'), $('.cd-filter'), $('.cd-tab-filter'), $('.cd-gallery')]);
		elementsToTrigger.each(function(){
			$(this).toggleClass('filter-is-visible', $bool);
		});
	}
	/* end open/close filter js */
	
	/* start of tooltip js */
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
	})
	/* end of tooltip js */
					  
}); /* end of jequery */