var basePath = "https://www.perfumeoffer.com/pb/";
$(document).ready(function(){
	console.log($("#customer_email_for_review").length);
	if( $("#customer_email_for_review" ).length > 0 )
	{
		$( "#customer_email_for_review" ).autocomplete({
			source: basePath+"api/customers/search",
			minLength: 2,
			select: function( event, ui )
			{
				$('#customer_new').val(ui.item.id);
				$('#customerEmail span').html("selected: "+ui.item.email);
			}
		});	
	}else{
		$('#customer_new').val("");
		$('#customerEmail span').html("");
	}
	
	if( $("#product_for_review" ).length > 0 )
	{
		$( "#product_for_review" ).autocomplete({
			source: basePath+"api/products/search",
			minLength: 2,
			select: function( event, ui )
			{
				$('#product_new').val(ui.item.id);
				$('#productName span').html("selected: "+ui.item.title);
			}
		});	
	}else{
		$('#customer_new').val("");
		$('#customerEmail span').html("");
	}
	/*
	$('#submitReview').click(function(event){
		event.preventDefault();
		if($('#customer_new').val() == '')
		{
			alert('Please select a customer');
		}
		else if($('#product_new').val() == '')
		{
			alert('Please select a product');
		}
		else if($('#review_description').val() == '')
		{
			alert('Please enter Shipping Last Name.');
		}else{
			$('#submit_review_form').submit();
		}
		
	});
	
	*/
});