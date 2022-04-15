import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { ClipboardService } from 'ngx-clipboard';
import { FormGroup, FormControl, Validators, RequiredValidator } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { ToastrService } from 'ngx-toastr';
import { CustomerService } from './../../_services/pb/customer.service';
import { Myconfig } from './../../_services/pb/myconfig';

@Component({
  selector: 'pc-share-earn',
  templateUrl: './share-earn.component.html',
  styleUrls: ['./share-earn.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class ShareEarnComponent implements OnInit {
	referForm: FormGroup;  
	copyLink = '';
	refersData = {'earned':[], 'redeemed':[], 'holding':[], 'pending':[], 'expired':[]};
    constructor( private toastr: ToastrService, private config: Myconfig, private customer: CustomerService, private clipboardService: ClipboardService ) { }

	ngOnInit() {
		this.getReferEarn();
	    this.referForm = new FormGroup ({
			email : new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
		});
	}
	getReferEarn () {
		this.customer.getRferEarn().subscribe(
			res => {
				this.refersData = res.data.refer;
				this.copyLink = res.data.referLink;
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	sendReferEmail (formData) {
		formData.referlink = this.copyLink;
		if ( this.config.EMAIL_REGEXP.test(formData['email']) ) {
			this.customer.sendRferEarn(formData).subscribe(
				res => {
					if ( res.status ) {
						this.referForm.controls.email.setValue('', {});
						this.toastr.success('Email sent successfully!');
					} else {
						this.toastr.error(res.message);
					}
				},
				(err: HttpErrorResponse) => {
					this.toastr.error("Server Isse!");
				}
			);
		} else {
			this.toastr.error('Please enter valid email id!');
		}
	}
	copy(text: string){
	  this.clipboardService.copyFromContent(text)
	}
}
