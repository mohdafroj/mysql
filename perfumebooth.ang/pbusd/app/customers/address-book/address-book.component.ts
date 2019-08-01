import { Component, OnInit } from '@angular/core';
//import { Title } from '@angular/platform-browser';
import { MatDialog, MatDialogConfig} from "@angular/material";
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { DeleteDialogComponent } from './delete-dialog/delete-dialog.component';

@Component({
  selector: 'app-address-book',
  templateUrl: './address-book.component.html',
  styleUrls: [
		'./../../../assets/css/user-dashboard.css',
		'./address-book.component.css'
	]
})
export class AddressBookComponent implements OnInit {
	rForm:FormGroup;
	msg:string		= '';
	response:any		= {};
	addressAction:number;
	country:string = 'USA';
	constructor(private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService,private dialog: MatDialog) {
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
	    this.rForm = new FormGroup ({
			id: new FormControl("0"),
			firstname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP), Validators.minLength(3)]) ),
			lastname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
			address: new FormControl("", Validators.compose([Validators.required,Validators.minLength(3)]) ),
			city: new FormControl("", Validators.compose([Validators.required,Validators.minLength(3)]) ),
			state: new FormControl("", Validators.compose([Validators.required]) ),
			country: new FormControl(this.country, Validators.compose([Validators.required]) ),
			pincode: new FormControl("", Validators.compose([Validators.required]) ),
			email: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.EMAIL_REGEXP)]) ),
			mobile: new FormControl(""),
			setdefault: new FormControl("0")
		});
		this.addressAction = 0;
	}
	
	//get All addresses related to current user
	getAddresses(){
		this.auth.getAddresses().subscribe(
			res => {
				if(res.status){
					this.response = res.data;
					this.country = this.response.locations[0].title ? this.response.locations[0].title: 'USA';
					(<FormControl>this.rForm.controls['country']).setValue(this.country, {});
				}else{
					console.log(res.message);
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	
	newAddress(){
		this.initAddressForm();
		this.addressAction = 1;
		(<FormControl>this.rForm.controls['country']).setValue(this.country, {});
		this.msg = '';
	}	
	addAddress(address){
		this.msg = 'Wait...';
		this.auth.addAddresses(address).subscribe(
			res => {
				this.msg = res.message;
				if( res.status ){
					this.initAddressForm();
					this.getAddresses();
				}
			},
			(err: HttpErrorResponse) => {
				if(err.error instanceof Error){
					this.msg = 'Client error: '+err.error.message;
				}else{
					this.msg = 'Server error: '+JSON.stringify(err.error);
				}
			}
		);
	}
    
	editAddress(item){
		this.addressAction = 1;
	    this.rForm = new FormGroup ({
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
	
	deleteAddress(address){
		this.msg = 'Wait...';
		this.auth.addAddresses(address).subscribe(
			res => {
				this.msg = res.message;
				if( res.status ){
					this.initAddressForm();
					this.getAddresses();
				}
			},
			(err: HttpErrorResponse) => {
				if(err.error instanceof Error){
					this.msg = 'Client error: '+err.error.message;
				}else{
					this.msg = 'Server error: '+JSON.stringify(err.error);
				}
			}
		);
	}
    
	setDefaultAddress(id){
		this.msg = 'Wait...';
		let formData = {
			id: id
		};
		this.auth.setDefaultAddress(formData).subscribe(
			res => {
				this.msg = res.message;
				if( res.status ){
					this.initAddressForm();
					this.getAddresses();
				}
			},
			(err: HttpErrorResponse) => {
				if(err.error instanceof Error){
					this.msg = 'Client error: '+err.error.message;
				}else{
					this.msg = 'Server error: '+JSON.stringify(err.error);
				}
			}
		);
	}
    
	upperToLower(event, fieldName){
	    (<FormControl>this.rForm.controls[fieldName]).setValue(event.target.value.toLowerCase(), {});
	}
}
