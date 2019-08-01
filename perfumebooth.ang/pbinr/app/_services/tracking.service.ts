import { Injectable } from '@angular/core';
declare let dataLayer: [any];

@Injectable({
	providedIn:'root'
})
export class TrackingService {
	public PBGTAG:string = 'UA-92767815-1';
	constructor() {
		
	}
	
	trackProductClick(items) {
		if( Object.keys(items).length > 0 ){
			let products:any = [];
			products.push({
				id         : items.id,
				name       : items.name,
				title      : items.title,
				sku        : items.skuCode,
				size       : items.size +' '+items.sizeUnit,
				quantity   : items.qty,
				price      : items.price,
				description: items.shortDescription,
				gender     : items.price,
				isStock    : (items.isStock == 'in_stock') ? 'In Stock':'Out Of Stock',
				brand      : items.brand.title,
				category   : items.category[0]['name'],
				categories : items.category,
				url 	   : 'https://www.perfumebooth.com/'+items.urlKey,
				image      : items.images[0]['imgLarge']
			});
			dataLayer.push({/*
				'content_type': 'product',
				'content_ids': productObj.skuCode,
				'content_name': productObj.name,
				'content_brand': productObj.brand.title,
				'content_category': productObj.category[0]['name'],
				'value': productObj.price, */
				'event': 'productClick',
				'ecommerce': {
					'click': {
						'actionField': {'list': 'Search Results'},      // Optional list property.
						'products': products
					}
				},
				'eventCallback': function() {
				}
			});
			//console.log(dataLayer);
		}
	}
	
	trackRemoveItemFromCart(items){
		//console.log(typeof Object.keys(items).length);
		if( Object.keys(items).length > 0 ){
			let products:any = [];
			products.push({
				'id'		:items.id,
				'name'		:items.name,
				'title'		:items.title,
				'sku'		:items.sku_code,
				'price'		:items.price,
				'quantity'	:items.cart_qty,
				'brand'		:items.brand.title,
				'category'	:items.category[0]['name'],
				'categories':items.category
			});
			
			dataLayer.push({
				'event': 'removeFromCart',
				'ecommerce': {
					'remove': {                               // 'remove' actionFieldObject measures.
						'products': products
					}
				}
			});
			//console.log(dataLayer);
		}
	}
	
	addToCart(items){
		if( Object.keys(items).length > 0 ){
			let products:any = [];
			products.push({
				'id'		:items.id,
				'name'		:items.name,
				'title'		:items.title,
				'sku'		:items.sku_code,
				'price'		:items.price,
				'quantity'	:items.cart_qty,
				'brand'		:items.brand.title,
				'category'	:items.category[0]['name'],
				'categories':items.category
			});
			dataLayer.push({
				'event': 'addToCart',
				'ecommerce': {
					'currencyCode': 'INR',
					'add': {
						'products':products
					}
				}
			});
			//console.log(dataLayer);
		}
	}
	
	trackCart(){
		let trackData:any = localStorage.getItem('trackingData');  //console.log(JSON.parse(trackData));
		if( trackData !== null ){
			let trackDataObj:any = JSON.parse(trackData); 
			//console.log(trackDataObj.cart.cart);
			let cart:any =  (trackDataObj.cart.cart != null) ? trackDataObj.cart.cart:[];
			let products:any = [];
			for( let i=0; i < cart.length; i++ ){
				products.push({
					'id'		:cart[i]['id'],
					'name'		:cart[i]['name'],
					'title'		:cart[i]['title'],
					'sku'		:cart[i]['sku_code'],
					'price'		:cart[i]['price'],
					'quantity'	:cart[i]['cart_qty'],
					'brand'		:cart[i]['brand']['title'],
					'category'	:cart[i]['category'][0]['name'],
					'categories':cart[i]['category']
				});
			}
			dataLayer.push({
				'event': 'addToCart',
				'ecommerce': {
					'currencyCode': 'INR',
					'add': {
						'products':products
					}
				}
			});
			//console.log(dataLayer);
		}
	}
	
	trackCheckout(){
		let trackData:any = localStorage.getItem('trackingData');
		if( trackData != null ){
			let trackDataObj:any = JSON.parse(trackData); 
			let cart:any =  (trackDataObj.cart.cart != null) ? trackDataObj.cart.cart:[];
			let products:any = [];
			for( let i=0; i < cart.length; i++ ){
				products.push({
					'id'		:cart[i]['id'],
					'name'		:cart[i]['name'],
					'title'		:cart[i]['title'],
					'sku'		:cart[i]['sku_code'],
					'price'		:cart[i]['price'],
					'quantity'	:cart[i]['cart_qty'],
					'brand'		:cart[i]['brand']['title'],
					'category'	:cart[i]['category'][0]['name'],
					'categories':cart[i]['category']
				});
			}
			dataLayer.push({
				'event': 'checkout',
				'ecommerce': {
					'checkout': {
						'actionField': {'step': 1, 'option': ''},
						'products': products
					}
			   },
			   'eventCallback': function() {
				  //document.location = 'checkout.html';
			   }
			});
			
		}
	}
	
	trackPurchase(){
		let trackData:any = localStorage.getItem('trackingData');
		if( trackData != null ){
			let trackDataObj:any 	= JSON.parse(trackData);
			let orderNumber:any 	=  (trackDataObj.order_number != null) 		? trackDataObj.order_number:0;
			let taxAmount:any		=  (trackDataObj.tax_amount != null) 		? trackDataObj.tax_amount:0;
			let revenue:number 		=  (trackDataObj.grand_final_total != null) ? trackDataObj.grand_final_total:0;
			let shipping:number		=  (trackDataObj.shipping_amount != null) 	? trackDataObj.shipping_amount:0;
			let coupon:any 			=  (trackDataObj.coupon_code != null) 		? trackDataObj.coupon_code:'';			
			let cart:any 			=  (trackDataObj.cart.cart != null) 		? trackDataObj.cart.cart:[];
			
			let products:any = [];
			for( let i=0; i < cart.length; i++ ){
				products.push({
					'id'		:cart[i]['id'],
					'name'		:cart[i]['name'],
					'title'		:cart[i]['title'],
					'sku'		:cart[i]['sku_code'],
					'price'		:cart[i]['price'],
					'quantity'	:cart[i]['cart_qty'],
					'brand'		:cart[i]['brand']['title'],
					'category'	:cart[i]['category'][0]['name'],
					'categories':cart[i]['category']
				});
			}
			//console.log(orderNumber); ecommerce.purchase.actionField.revenue
			dataLayer.push({
				'ecommerce': {
					'purchase': {
						'actionField':{
							'id': orderNumber,                         // Transaction ID. Required for purchases and refunds.
							'affiliation': 'Online Store',
							'revenue': revenue,                     // Total transaction value (incl. tax and shipping)
							'tax':taxAmount,
							'shipping': shipping,
							'coupon': coupon
						},
						'products': products
					}
				},
				'event' : 'transaction'
			});			
		}
	}
	
	setOrderNumberToTrack(orderNumber){
		let trackData:any = localStorage.getItem('trackingData');
		if( trackData != null ){
			let trackDataObj:any = JSON.parse(trackData); 
			trackDataObj.order_number = orderNumber;
			localStorage.removeItem('trackingData');
			localStorage.setItem('trackingData', JSON.stringify(trackDataObj));
		}	
		return false;
  }

}
