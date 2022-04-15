import { Component, OnInit, ViewChild, ElementRef, HostListener } from '@angular/core';
import { FormGroup, FormControl, Validators, RequiredValidator } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { TrackingService } from './../../_services/tracking.service';
import { SeoService } from './../../_services/seo.service';

@Component({
  selector: 'app-checkout',
  templateUrl: './checkout.component.html',
  styleUrls: [
     './checkout.component.css'
  ]
})
export class CheckoutComponent implements OnInit {
  @ViewChild('hideDeleteModal', {static: false}) hideDeleteModal: ElementRef;
  rForm: FormGroup;
  methodForm: FormGroup;
  addresses: any = [];
  selectedAddress: any = {};
  initAddress: any = {};
  editedAddressId: any = -1;
  addressIndex: any = -1;
  addressResponse: any  = {};
  tabIndex: any = {};
  userId: any = 0;
  inputData: any = [];

  paymentMethodData: any = [];
  myCart: any = [];
  customer: any = [];
  credits: any = [];
  discounts: any = [];
  prive: any = [];

  couponCode: any = '';
  voucherMessage: any = '';
  shippingAmount: any = 0;
  codAmount: any = 0;
  grandFinalTotal: any = 0;
  pincodeStatus: any	= 0;

  finalStatus: any	= false;
  finalMessage: any = '';
  otpResponse: any = {};
  enterOtpNumber: any = '';
  summaryClass: any = '';
  paymentGatewayUrl = '';
  orderNumber = 1;
  deliveryMessage = '';
  paymentOffer: any = {};
  constructor (
    private seo: SeoService,
    private router: Router,
    private route: ActivatedRoute,
    public auth: CustomerService,
    private store: StoreService,
    private config: Myconfig,
    private elem: ElementRef,
    private track: TrackingService
    ) {
        this.tabIndex.third = 0;
        let profileFirstName = this.auth.getFirstName();
        let profileEmail = this.auth.getEmail();
        let profileMobile = this.auth.getMobile();
        this.initAddress = {
            id: 0,
            firstname: profileFirstName,
            lastname: '',
            address: '',
            city: '',
            state: 'Delhi',
            country: 'India',
            pincode: '',
            email: profileEmail,
            mobile: profileMobile,
            set_default: 0
        };
    }

    ngOnInit () {
        this.config.scrollToTop(0, 0);
        this.userId = this.auth.getId();
        let cartInfo: any = JSON.parse(localStorage.getItem('cartInfo'));
        cartInfo = (cartInfo != null) ? cartInfo : {};
        cartInfo.trackPage = 'checkout';
        this.inputData = cartInfo;
        this.getMyCart();
        this.initAddressForm(this.initAddress);
        this.getAddresses();
        this.methodForm = new FormGroup ({
            paymentMethod: new FormControl(this.inputData.paymentMethod, Validators.required)
        });
        this.seo.ogMetaTag('Checkout Page', 'Checkout page description');

        // this.otpResponse.message = 'Waiting...<i class="fa fa-spinner fa-spin"></i>';
        // this.otpResponse.class = 'loader_msz';
        // this.elem.nativeElement.querySelector('#getOtpPopup').click();
    }

    // init address form
    initAddressForm (value) {
        this.rForm = new FormGroup ({
            id: new FormControl(value.id),
            firstname: new FormControl(
                value.firstname,
                Validators.compose([Validators.required, Validators.pattern(this.config.ALPHA_SPACE_REGEXP), Validators.minLength(3)])
            ),
            lastname: new FormControl(
                value.lastname,
                Validators.compose([Validators.required, Validators.pattern(this.config.ALPHA_SPACE_REGEXP), Validators.minLength(3)])
            ),
            address: new FormControl(value.address, Validators.compose([Validators.required, Validators.minLength(3)]) ),
            city: new FormControl(value.city, Validators.compose([Validators.required, Validators.minLength(3)]) ),
            state: new FormControl(value.state, Validators.compose([Validators.required]) ),
            country: new FormControl({value: value.country, disabled: true}, Validators.required),
            pincode: new FormControl(value.pincode, Validators.compose([Validators.required, Validators.pattern(/^\d{6}$/)]) ),
            email: new FormControl(value.email, Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
            mobile: new FormControl(
                value.mobile,
                Validators.compose([Validators.required, Validators.pattern(this.config.MOBILE_REGEXP)])
            ),
            setdefault: new FormControl(value.id)
        });
    }

    getAddresses () {
        this.auth.getAddresses().subscribe(
            res => {
                this.addresses = res.data; // this.addresses.address = [];
                if ( this.addresses.address.length > 0 ) {
                    let item: any;
                    for ( item of this.addresses.address ) {
                        if ( item.set_default === '1' ) {
                            this.selectedAddress = item;
                            this.rForm.patchValue({setdefault: item.id});
                            // this.elem.nativeElement.querySelector('#RevieworederSelected').click();
                            break;
                        }
                    }
                } else {
                    this.editedAddressId = 0;
                    this.initAddressForm(this.initAddress);
                }
            },
            (err: HttpErrorResponse) => {
                console.log('Server Isse!');
            }
        );
    }

    editAddress(item) {
        this.editedAddressId = item.id;
        this.initAddressForm(item);
    }

    cancelAddress() {
        this.editedAddressId = -1;
        this.initAddressForm(this.initAddress);
    }

    addressMessageClear() {
        // this.addressResponse = {};
    }

    addNewAddress() {
        this.editedAddressId = 0;
        this.initAddressForm(this.initAddress);
    }

    saveAddress(formData, addressIndex) {
        this.addressIndex 		= addressIndex;
        this.addressResponse 	= {
            id: this.editedAddressId,
            addressIndex: this.addressIndex,
            message: '<i class="fa fa-spinner fa-spin"></i>',
            class: 'loader_msz'
        };
        this.store.checkPincode(formData.pincode).subscribe(
            res => {
                this.pincodeStatus = res.status; // 1 both, 2 prepaid, 3 postpaid, 0 not
                if ( this.pincodeStatus > 0 ) {
					this.deliveryMessage = res.message;
                    this.addressResponse = {};
                    formData.setdefault = 1;
                    this.auth.addAddresses(formData).subscribe(
                        res1 => {
                            if ( res1.status ) {
                                // this.getAddresses();
                                this.config.scrollToTop(0, 0);
                                this.addresses = res1.data;
                                if ( this.addresses.address.length > 0 ) {
                                    let item: any;
                                    for ( item of this.addresses.address ) {
                                        if ( item.set_default === '1' ) {
                                            this.selectedAddress = item;
                                            this.rForm.patchValue({setdefault: item.id});
                                            // this.elem.nativeElement.querySelector('#RevieworederSelected').click();
                                            break;
                                        }
                                    }
                                }
                                this.initAddressForm(this.initAddress);
                                this.addressResponse = {id: this.editedAddressId, addressIndex: this.addressIndex, class: 'success_msz'};
                                this.editedAddressId = -1;
                                this.tabIndex.third = 1;
                                this.elem.nativeElement.querySelector('#RevieworederSelected').click();
                            } else {
                                this.addressResponse = {
                                    id: this.editedAddressId,
                                    addressIndex: this.addressIndex,
                                    message: res1.message,
                                    class: 'error_msz'
                                };
                            }
                        },
                        (err: HttpErrorResponse) => {
                            let message = '';
                            if (err.error instanceof Error ) {
                                message = 'Client error: ' + err.error.message;
                            } else {
                                message = 'Server error: ' + JSON.stringify(err.error);
                            }
                            this.addressResponse = {
                                id: this.editedAddressId,
                                addressIndex: this.addressIndex,
                                message: message,
                                class: 'error_msz'
                            };
                        }
                    );
                } else {
                    this.addressResponse = {
                        id: this.editedAddressId,
                        addressIndex: this.addressIndex,
                        message: res.message,
                        class: 'error_msz'
                    };
                }
            },
            (err: HttpErrorResponse) => {
                /* if(err.error instanceof Error) {
                    message = 'Client error: '+err.error.message;
                } else {
                    message = 'Server error: '+JSON.stringify(err.error);
                } */
                this.addressResponse = {
                    id: this.editedAddressId,
                    addressIndex: this.addressIndex,
                    message: 'Sorry, may be network issue, please refresh page!',
                    class: 'error_msz'
                };
            }
        );
    }

    reviewedCartContinue() {
        this.tabIndex.fourth = 1;
        this.elem.nativeElement.querySelector('#PaymentoptionSelected').click();
    }

    getMyCart() {
        // this.tabIndex.fourth = 1;
        this.store.getCart(this.inputData).subscribe(
            res => {
                if (res.status && res.data.cart !== undefined) {
                    this.myCart 					= res.data.cart;
                    this.paymentMethodData     		= res.data.payment_method_data;
                    this.customer 					= res.data.customer;
                    this.credits 					= res.data.credits;
                    this.discounts 					= res.data.discounts;
                    this.prive 						= res.data.prive;
					this.paymentOffer               = res.data.payment_offer;
                    this.couponCode			 		= res.data.coupon_code;
                    this.shippingAmount		 		= res.data.shipping_amount;
                    this.codAmount			 		= res.data.payment_fees;
                    this.grandFinalTotal		 	= res.data.grand_final_total;
                    // console.log(res);
                    if ( this.inputData.paymentMethod !== res.data.payment_method ) {
                        this.inputData.paymentMethodSelected = '';
                        this.methodForm.patchValue({paymentMethod: ''});
                    }
                    if (this.inputData.paymentMethod !== 1) {
                        this.inputData.otpStatus = true;
                    } else {
                        this.inputData.otpStatus = (this.inputData.mobile === this.selectedAddress.mobile) ? true : false;
                    }
                    localStorage.setItem('trackingData', JSON.stringify(res.data));
                    this.track.trackCheckout();
                } else {
                    this.router.navigate(['/checkout/cart'], {});
                }
            },
            (err: HttpErrorResponse) => {
                if (err.error instanceof Error) {
                    console.log('Client Error: ' + err.error.message);
                } else {
                    console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
                }
            }
        );
    }

    onSelectionChange(value: number) {
        this.inputData.paymentMethod = value;
        this.methodForm.patchValue({paymentMethod: value});
        let i: any;
        for (i of this.paymentMethodData) {
            if ( i.id === value ) {
                this.inputData.paymentMethodSelected = i.title;
                break;
            }
        }
        this.finalMessage = '';
        localStorage.setItem('cartInfo', JSON.stringify(this.inputData));
        this.getMyCart();
        return false;
    }

    getOtp() {
        this.finalMessage = '';
        this.otpResponse.message = 'Waiting...<i class="fa fa-spinner fa-spin"></i>';
        this.otpResponse.class = 'loader_msz';
        const formData = {
            userId: this.userId,
            name  : this.selectedAddress.firstname + ' ' + this.selectedAddress.lastname,
            email : this.selectedAddress.email,
            mobile: this.selectedAddress.mobile,
            amount: this.grandFinalTotal
        };
        this.store.getOtp(formData).subscribe(
            res => {
                if ( res.status ) {
                    this.otpResponse.class = 'success_msz';
                } else {
                    this.otpResponse.class = 'error_msz';
                }
                this.otpResponse.message = res.message;
            }, (err: HttpErrorResponse) => {
                this.otpResponse.class = 'error_msz';
                this.otpResponse.message = 'Sorry, there are some app issue!';
            }
        );
        return true;
    }

    verifyOtp () {
        this.otpResponse.message = 'Waiting...<i class="fa fa-spinner fa-spin"></i>';
        this.otpResponse.class = 'loader_msz';
        if ( this.enterOtpNumber !== 0 ) {
            const formData = {
                userId: this.userId,
                otp: this.enterOtpNumber
            };
            this.store.verifyOtp(formData).subscribe(
                res => {
                    if ( res.status ) {
                        this.otpResponse.class = 'success_msz';
                        this.inputData.otpStatus = true;
                        this.inputData.mobile = this.selectedAddress.mobile;
						this.elem.nativeElement.querySelector('#closeOtpPopup').click();
                        this.placeOrder(); // place order just after otp verification
                    } else {
                        this.otpResponse.class = 'error_msz';
                    }
                    this.otpResponse.message = res.message;
                }, (err: HttpErrorResponse) => {
                    this.otpResponse.class = 'error_msz';
                    this.otpResponse.message = 'Sorry, there are some app issue!';
                }
            );
        } else {
            this.otpResponse.class = 'error_msz';
            this.otpResponse.message = 'Please enter otp number!';
        }
        return false;
    }

    checkData() {
        this.finalMessage = '';
        this.finalStatus = true;

        if (
            this.selectedAddress.firstname === '' ||
            this.selectedAddress.lastname === '' ||
            this.selectedAddress.address === '' ||
            this.selectedAddress.pincode === '' ||
            this.selectedAddress.city === '' ||
            this.selectedAddress.email === '' ||
            this.selectedAddress.mobile === ''
        ) {
            this.finalMessage = 'Please select shipping address!';
            this.finalStatus = false;
        }
        if ( this.methodForm.value.paymentMethod === '' || this.methodForm.value.paymentMethod === 0 ) {
            this.methodForm.controls.paymentMethod.markAsDirty();
            this.finalStatus = false;
        }
        // console.log(this.methodForm.value.paymentMethod);
        if ( this.finalStatus ) {
            if ( this.pincodeStatus > 0 ) {
                this.finalMessage = 'Wait...';
                if ( this.inputData.paymentMethod === 1 ) {
                    this.getOtp();
                    this.elem.nativeElement.querySelector('#getOtpPopup').click();
                } else {
                    this.placeOrder();
                }
            } else {
				this.deliveryMessage = '';
                this.finalStatus = false;
                this.finalMessage = 'Sorry, service not available at pincode: ' + this.selectedAddress.pincode;
            }
        }
        return true;
    }

    placeOrder () {
        if ( this.inputData.paymentMethod === 1) {
            this.otpResponse.message = 'Waiting...<i class="fa fa-spinner fa-spin"></i>';
            this.otpResponse.class = 'loader_msz';
        } else {
            this.finalMessage = 'Waiting...';
        }
        // shipping address
        this.inputData.shipping_firstname 	= this.selectedAddress.firstname;
        this.inputData.shipping_lastname 	= this.selectedAddress.lastname;
        this.inputData.shipping_address 	= this.selectedAddress.address;
        this.inputData.shipping_city 		= this.selectedAddress.city;
        this.inputData.shipping_state 		= this.selectedAddress.state;
        this.inputData.shipping_country 	= this.selectedAddress.country;
        this.inputData.shipping_pincode 	= this.selectedAddress.pincode;
        this.inputData.shipping_email 		= this.selectedAddress.email;
        this.inputData.shipping_mobile 		= this.selectedAddress.mobile;

        // console.log(this.inputData);
        this.store.saveOrderDetails(this.inputData).subscribe(
           res => {
            if ( res.status ) {
				this.orderNumber = res.data.orderNumber;
				this.paymentGatewayUrl = res.data.paymentGatewayUrl;
				this.track.setOrderNumberToTrack(this.orderNumber);			  
				localStorage.setItem('successData', JSON.stringify({'orderNumber': this.orderNumber, 'trackFlag': 1}));			  
				setTimeout( () => {
					this.elem.nativeElement.querySelector('#paymentGatewayForm').submit();
				}, 2000);
            } else {
				this.finalMessage = res.message;
            }
          },
          (err: HttpErrorResponse) => {
            if (err.error instanceof Error) {
              console.log('Client Error: ' + err.error.message);
            } else {
              console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
            }
          }
        );
        return false;
    }

    upperToLower (event, fieldName) {
        (<FormControl>this.rForm.controls[fieldName]).setValue(event.target.value.toLowerCase().trim(), {});
    }

    showSummary () {
        this.summaryClass = 'filter-is-visible';
    }

    hideSummary () {
        this.summaryClass = '';
    }

    @HostListener('window:click', ['$event'])
    checkClick () {
      const componentPosition = this.elem.nativeElement.offsetTop;
      const scrollPosition = window.pageYOffset;
    }
}
