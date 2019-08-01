jQuery(function ($) {
	
	/* start of on scroll animation js */
	$(function(){
		function onScrollInit( items, trigger ) {
			items.each( function() {
			var osElement = $(this),
				osAnimationClass = osElement.attr('data-os-animation'),
				osAnimationDelay = osElement.attr('data-os-animation-delay');
			  
				osElement.css({
					'-webkit-animation-delay':  osAnimationDelay,
					'-moz-animation-delay':     osAnimationDelay,
					'animation-delay':          osAnimationDelay
				});
	
				var osTrigger = ( trigger ) ? trigger : osElement;
				
				osTrigger.waypoint(function() {
					osElement.addClass('animated').addClass(osAnimationClass);
					},{
						triggerOnce: true,
						offset: '90%'
				});
			});
		}
	
		onScrollInit( $('.os-animation') );
		onScrollInit( $('.staggered-animation'), $('.staggered-animation-container') );
	});
	
	/* start of our_brands thumbnail js */
	$(document).ready(function() {
		$('.touch_slider_1').lightSlider({
			item:6,
			//auto:true,
			loop:false,
			//pauseOnHover: true,
			slideMove:1,
			easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
			speed:1500,
			responsive : [
				{
					breakpoint:900,
					settings: {
						item:5,
						slideMove:1,
						slideMargin:6,
					  }
				},
				{
					breakpoint:767,
					settings: {
						item:4.5,
						slideMove:1
					  }
				}
			]
		});
	});
	/* end of our_brands thumbnail js */
	
	/* start of our_products thumbnail js */
	$(document).ready(function() {
		$('.touch_slider_2').lightSlider({
			item:4,
			//auto:true,
			loop:false,
			//pauseOnHover: true,
			slideMove:1,
			easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
			speed:1500,
			responsive : [
				{
					breakpoint:900,
					settings: {
						item:3,
						slideMove:1,
						slideMargin:6,
					  }
				},
				{
					breakpoint:767,
					settings: {
						item:1.05,
						slideMove:1
					  }
				}
			]
		});
	});
	/* end of our_products js */
	
	/* start design_specs slider js */
	$(document).ready(function(){
    
		var clickEvent = false;
		$('#myCarousel').carousel({
			//interval:   false
		}).on('click', '.carousel_list .list-group li', function() {
				clickEvent = true;
				$('.carousel_list .list-group li').removeClass('active');
				$(this).addClass('active');		
		}).on('slid.bs.carousel', function(e) {
			if(!clickEvent) {
				var count = $('.carousel_list .list-group').children().length -1;
				var current = $('.carousel_list .list-group li.active');
				current.removeClass('active').next().addClass('active');
				var id = parseInt(current.data('slide-to'));
				if(count == id) {
					$('.carousel_list .list-group li').first().addClass('active');	
				}
			}
			clickEvent = false;
		});
    
		var clickEvent = false;
		$('#myCarousel_1').carousel({
			interval:   false	
		}).on('click', '.carousel_1_list .list-group li', function() {
				clickEvent = true;
				$('.carousel_1_list .list-group li').removeClass('active');
				$(this).addClass('active');		
		}).on('slid.bs.carousel', function(e) {
			if(!clickEvent) {
				var count = $('.carousel_1_list .list-group').children().length -1;
				var current = $('.carousel_1_list .list-group li.active');
				current.removeClass('active').next().addClass('active');
				var id = parseInt(current.data('slide-to'));
				if(count == id) {
					$('.carousel_1_list .list-group li').first().addClass('active');	
				}
			}
			clickEvent = false;
		});
		
	})
	/* end design_specs slider js */
					  
}); /* end of jequery */