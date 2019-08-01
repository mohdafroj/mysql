//console.log("addcart js called");
jQuery(function ($) {
	
	/* start of add-on or remove number js */
	$(document).on('click', '.number-spinner button', function () {    
		var btn = $(this),
			oldValue = btn.closest('.number-spinner').find('input').val().trim(),
			newVal = 0;
		
		if (btn.attr('data-dir') == 'up') {
			newVal = parseInt(oldValue) + 1;
		} else {
			if (oldValue > 1) {
				newVal = parseInt(oldValue) - 1;
			} else {
				newVal = 1;
			}
		}
		btn.closest('.number-spinner').find('input').val(newVal);
	});
	/* end of add-on or remove number js */
	
$(".pop").popover({ trigger: "manual" , html: true, animation:false})
		.on("mouseenter", function () {
			
			var _this = this;
			$(this).popover("show");
			$(".popover").on("mouseleave", function () {
				$(_this).popover('hide');
			});
		}).on("mouseleave", function () {
			var _this = this;
			setTimeout(function () {
				if (!$(".popover:hover").length) {
					$(_this).popover("hide");
				}
			}, 300);
	});
	
}); /* end of jequery */


