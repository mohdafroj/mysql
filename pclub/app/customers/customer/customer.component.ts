import { Component, ViewEncapsulation, ElementRef, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { CustomerService } from '../../_services/pb/customer.service';
import { ToastrService } from 'ngx-toastr';

@Component({
	selector: 'customer',
	templateUrl: './customer.component.html',
	encapsulation: ViewEncapsulation.None,
	styleUrls: [
		'./customer.component.css'
	]
})
export class CustomerComponent implements OnInit {
	name = '';
	picUrl = '';
	menuOpen = false;
	serverRequest: boolean = false;

	constructor(
		private toastr: ToastrService,
		private router: Router, 
		private route: ActivatedRoute, 
		private customer: CustomerService,
		private elem: ElementRef
	) {
		
	}
	ngOnInit() {
		this.name = this.customer.getName();
		this.picUrl = this.customer.getImage();
	}
	
	choosePicture (event) {
		let file = event.target.files[0];
		if ( file.size > 0 ) {
			let maxSize = 2; // 5MB
			let fileExt = ['JPG','GIF','PNG'];
			let ext = file.name.toUpperCase().split('.').pop() || file.name;
			if ( !fileExt.includes(ext) ) {
				this.toastr.error("Please choose jpg,gif or png file!");
				return true;
			}
			let fileSizeinMB = file.size / (1024 * 1000);
			let size = Math.round(fileSizeinMB * 100) / 100; // convert upto 2 decimal place
			if( fileSizeinMB == 0 || (size > maxSize) ) {
				this.toastr.error("File size should be less than 2 MB!");
				return true;
			}
			this.savePicture(file);
		}	
		return false;
    }
	
	savePicture (file) {
		if ( file.size > 0 ) {
			this.serverRequest = true;
			let formData:FormData = new FormData();
			formData.append('fileToUpload',file,file.name);
			this.customer.updatePicture(formData).subscribe(
				res => {
					if(res.status){
						this.picUrl = res.data.image;
						this.customer.setImage(this.picUrl);
						this.toastr.success(res.message);
					} else {
						this.toastr.error(res.message);
					}
					this.serverRequest = false;
					this.elem.nativeElement.querySelector("#inputGroupFile01").value = '';
				},
				(err: HttpErrorResponse) => {
					this.serverRequest = false;
					this.elem.nativeElement.querySelector("#inputGroupFile01").value = '';
					this.toastr.error("Server Isse!");
				}
			);
		}
	}

	removePicture () {
		this.serverRequest = true;
		let formData:FormData = new FormData();
		formData.append('removePic', '1');
		this.customer.updatePicture(formData).subscribe(
			res => {
				if(res.status){
					this.customer.setImage('');
					this.picUrl = this.customer.getImage();
					this.toastr.success(res.message);
				} else {
					this.toastr.error(res.message);
				}
				this.serverRequest = false;
			},
			(err: HttpErrorResponse) => {
				this.serverRequest = false;
				this.toastr.error("Server Isse!");
			}
		);
	}
	
	customerLogout () {
		this.customer.customerLogout();
	}
}
