import { Injectable } from '@angular/core';
import { StoreService } from './pb/store.service';
import { Myconfig } from './pb/myconfig';
declare let dataLayer: [any];

@Injectable({
	providedIn:'root'
})
export class TrackingService {
	public PBGTAG:string = 'UA-170897597-1';
	currencyCode = 'INR';
	baseUrl;
	evetFire = 1;
	constructor (
		private config: Myconfig,
		private store: StoreService
	) {
		this.baseUrl = config.baseUrl;
		if ( this.baseUrl.indexOf('www.perfumerclub.com') != -1 ) {
			this.evetFire = 1;
		}
	}
	
	clickProduct(items) {
		if( Object.keys(items).length > 0 ){
			let products = [];
			products.push({
				id         : items.id,
				name       : items.name,
				title      : items.title,
				sku        : items.sku,
				size       : items.size +' '+items.unit,
				quantity   : items.quantity,
				price      : items.price,
				description: items.shortDescription,
				gender     : items.gender,
				isStock    : (items.isStock == 'in_stock') ? 'In Stock':'Out Of Stock',
				brand      : (items.brand != undefined) ? items.brand.title : '',
				category   : (items['categories'][0] && items.categories[0]['name'] != undefined) ? items.categories[0]['name'] : '',
				categories : items.categories,
				url 	   : this.baseUrl+'/'+items.urlKey,
				image      : (items.images[0] != undefined) ? items.images[0]['large'] : ''
			}); //console.log(products);
			if ( this.evetFire ) {
				dataLayer.push({
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
				//console.log(products);
			}
		}
	}
	
	addToCart (items) {
		if( Object.keys(items).length > 0 ){
			let products = [];
			products.push({
				'id'		:items.id,
				'name'		:items.name,
				'title'		:items.title,
				'sku'		:items.sku,
				'price'		:items.price,
				'quantity'	:(items.cart_quantity != undefined) ? items.cart_quantity : 1,
				'brand'		:(items.brand != undefined) ? items.brand.title : '',
				'category'	:( items.categories[0] != undefined && items.categories[0]['name'] != undefined) ? items.categories[0]['name'] : '',
				'categories':items.categories
			});
			if ( this.evetFire ) {
				dataLayer.push({
					'event': 'addToCart',
					'ecommerce': {
						'currencyCode': this.currencyCode,
						'add': {
							'products':products
						}
					}
				});
				//console.log(products);
			}
		}
	}
	
	removeFromCart(items){
		if( Object.keys(items).length > 0 ) {
			let products = [];
			products.push({
				'id'		:items.id,
				'name'		:items.name,
				'title'		:items.title,
				'sku'		:items.sku,
				'price'		:items.price,
				'quantity'	:(items.cart_quantity != undefined) ? items.cart_quantity:1,
				'brand'		:(items.brand != undefined) ? items.brand.title : '',
				'category'	:(items.categories[0] != undefined && items.categories[0]['name'] != undefined) ? items.categories[0]['name'] : '',
				'categories':items.categories
			});
			if ( this.evetFire ) {
				dataLayer.push({
					'event': 'removeFromCart',
					'ecommerce': {
						'remove': {   // 'remove' actionFieldObject measures.
							'products': products
						}
					}
				});
				//console.log(products);
			}
		}
	}
	
	storeTrack(pageType){
		var trackData = this.store.getTrackingData();
		if( Object.keys(trackData).length ) {
			let shopping 	=  trackData['shopping'] ? trackData['shopping'] : [];			
			let products = [];
			let shoppingCart = shopping['cart'] ? shopping['cart'] : [];
			//console.log(shopping);
			for( let i = 0; i < shoppingCart.length; i++ ){
				products.push({
					'id'		:shoppingCart[i]['id'],
					'name'		:shoppingCart[i]['name'],
					'title'		:shoppingCart[i]['title'],
					'sku'		:shoppingCart[i]['sku'],
					'price'		:shoppingCart[i]['price'],
					'quantity'	:shoppingCart[i]['cart_quantity'],
					'brand'		:(shoppingCart[i]['brand'] && shoppingCart[i]['brand']['title']) ? shoppingCart[i]['brand']['title'] : '',
					'category'	:(shoppingCart[i]['categories'][0] && shoppingCart[i]['categories'][0]['name']) ? shoppingCart[i]['categories'][0]['name'] : '',
					'categories':shoppingCart[i]['categories'] ? shoppingCart[i]['categories'] : ''
				});
			}
			let shoppingPack = shopping['pack'] ? shopping['pack'] : [];
			for( let i = 0; i < shoppingPack.length; i++ ){
				products.push({
					'id'		:shoppingPack[i]['pack'],
					'name'		:shoppingPack[i]['title'],
					'title'		:shoppingPack[i]['title'],
					'sku'		:shoppingPack[i]['pack'],
					'price'		:shoppingPack[i]['price'],
					'quantity'	:shoppingPack[i]['cart_quantity'],
					'brand'		:(shoppingPack[i]['brand']['title'] != undefined) ? shoppingPack[i]['brand']['title'] : '3 Pack',
					'category'	:shoppingPack[i]['categories'][0]['name'] ? shoppingPack[i]['categories'][0]['name'] : '',
					'categories':shoppingPack[i]['categories'] ? shoppingPack[i]['categories'] : ''
				});
			}
			switch ( pageType ) {
				case 'cart' : 
					if ( this.evetFire ) {
						dataLayer.push({
							'event': 'addToCart',
							'ecommerce': {
								'currencyCode': this.currencyCode,
								'add': {
									'products':products
								}
							}
						});
					}
					break;
				case 'checkout' : 
					if ( this.evetFire ) {
						dataLayer.push({
							'event': 'checkout',
							'ecommerce': {
								'checkout': {
									'actionField': {'step': 1, 'option': ''},
									'products': products
								}
						   },
						   'eventCallback': function() {
							  //document.location = 'checkout.html'; ecommerce.checkout.products.0.brand
						   }
						});
					}
					break;
				case 'purchase':
					let revenue 	=  trackData['grand_final_total'] ? trackData['grand_final_total'] : 0;
					let shipping	=  trackData['shipping_amount'] ? trackData['shipping_amount'] : 0;
					let coupon 		=  trackData['coupon_code'] ? trackData['coupon_code'] : '';
					var orderData 	=  this.store.getSuccessData();
					let orderPrefix =  ( orderData['orderPrefix'] != undefined ) ? trackData['orderPrefix'] : '';
					let orderNumber =  ( orderData['orderNumber'] != undefined ) ? trackData['orderNumber'] : 0;
					if ( this.evetFire ) {
						dataLayer.push({
							'ecommerce': {
								'purchase': {
									'actionField':{
										'id': orderPrefix+orderNumber, // Transaction ID. Required for purchases and refunds.
										'affiliation': 'Online Store',
										'revenue': revenue,            // Total transaction value (incl. tax and shipping)
										'tax': 0,
										'shipping': shipping,
										'coupon': coupon
									},
									'products': products
								}
							},
							'event' : 'transaction'
						});
					}	
					break;
				default:
			}
		}
		return true;
	}
	
}
