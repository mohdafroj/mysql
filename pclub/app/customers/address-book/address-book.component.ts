import { Component, OnInit, ViewEncapsulation } from '@angular/core';
//import { Title } from '@angular/platform-browser';
import { MatDialog, MatDialogConfig } from "@angular/material/dialog";
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { DeleteDialogComponent } from './delete-dialog/delete-dialog.component';

@Component({
  selector: 'pc-address-book',
  templateUrl: './address-book.component.html',
  encapsulation: ViewEncapsulation.None,
  styleUrls: [
		'./address-book.component.css'
	]
})
export class AddressBookComponent implements OnInit {
	addressForm:FormGroup;
	resObject = {};
	response = {};
	serverRequest = true;
	setDefaultId = 0;
	country = 'India';
	constructor (
		private router: Router, 
		private route: ActivatedRoute, 
		private config:Myconfig, 
		private customer: CustomerService,
		private dialog: MatDialog
	) {
	}

	ngOnInit() {
		this.initAddressForm();
		this.getAddresses();
		this.response = {
			  address:[],
			  states: [],
			  locations:[]
		}    
	}

	//init address form
	initAddressForm() {
	    this.addressForm = new FormGroup ({
			id: new FormControl("0"),
			firstname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP), Validators.minLength(3)]) ),
			lastname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
			address: new FormControl("", Validators.compose([Validators.required,Validators.minLength(3)]) ),
			city: new FormControl("", Validators.compose([Validators.required,Validators.minLength(3)]) ),
			state: new FormControl("Delhi", Validators.compose([Validators.required]) ),
			country: new FormControl(this.country, Validators.compose([Validators.required]) ),
			pincode: new FormControl("", Validators.compose([Validators.required]) ),
			email: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.EMAIL_REGEXP)]) ),
			mobile: new FormControl(""),
			setdefault: new FormControl("0")
		});
	}
	
	//get All addresses related to current user
	getAddresses(){
		this.customer.getAddresses().subscribe(
			res => {
				if(res.status){
					this.response = res.data;
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
		return false;
	}
	
	newAddress(){
		this.resObject = {'textClass': 'text-danger', 'message': 'Please fill required'};
		this.initAddressForm();
	}	
	
	addAddress(address){
		this.resObject['message'] = 'Wait...';
		this.resObject['textClass'] = 'text-warning';
		if ( this.serverRequest ) {
			this.serverRequest = false;
			this.customer.addAddresses(address).subscribe(
				res => {
					this.serverRequest = true;
					this.resObject['message'] = res.message;
					if( res.status ){
						this.resObject['textClass'] = 'text-success';
						this.initAddressForm();
						this.getAddresses();
					} else {
						this.resObject['textClass'] = 'text-danger';
					}
				},
				(err: HttpErrorResponse) => {
					if(err.error instanceof Error){
						this.resObject['message'] = 'Client error: '+err.error.message;
					}else{
						this.resObject['message'] = 'Server side error';
					}
					this.serverRequest = true;
					this.resObject['textClass'] = 'text-danger';
				}
			);
		}	
	}
    
	editAddress(item){
	    this.addressForm = new FormGroup ({
			id: new FormControl(item.id),
			firstname: new FormControl(item.firstname),
			lastname: new FormControl(item.lastname),
			address: new FormControl(item.address),
			city: new FormControl(item.city),
			state: new FormControl(item.state),
			country: new FormControl(item.country),
			pincode: new FormControl(item.pincode),
			email: new FormControl(item.email),
			mobile: new FormControl(item.mobile),
			setdefault: new FormControl(item.set_default)
		});
	}
    
	openDeleteDialog(item){
		const dialogConfig = new MatDialogConfig();

		dialogConfig.disableClose = true;
		dialogConfig.autoFocus = true;
		dialogConfig.data = item;
		const dialogRef = this.dialog.open(DeleteDialogComponent, dialogConfig);
		dialogRef.afterClosed().subscribe(
			res => {
				if( res != 0 ){
					this.getAddresses();
				}
			}
		);    
	}
	    
	setDefaultAddress(id){
		this.setDefaultId = id;
		let formData = {
			id: id
		};
		if ( this.serverRequest ) {
			this.serverRequest = false;
			this.customer.setDefaultAddress(formData).subscribe(
				res => {
					if( res.status ){
						this.getAddresses();
					}
					this.serverRequest = true;
					this.setDefaultId = 0;
				},
				(err: HttpErrorResponse) => {
					this.setDefaultId = 0;
					this.serverRequest = true;
				}
			);
		}	
	}
    
	upperToLower(event, fieldName){
	    (<FormControl>this.addressForm.controls[fieldName]).setValue(event.target.value.toLowerCase(), {});
	}
}
