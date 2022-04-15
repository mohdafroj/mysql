import { Component, OnInit,Inject, ViewEncapsulation } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef} from "@angular/material/dialog";
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { CustomerService } from '../../../_services/pb/customer.service';

@Component({
  selector: 'pc-address-delete-dialog',
  templateUrl: './delete-dialog.component.html',
  styleUrls: ['./delete-dialog.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class DeleteDialogComponent implements OnInit {
	rForm:FormGroup;
	item:any = {};
	userId:number = 0;
	resObject = {};
	serverRequest = true;
	constructor( private customer: CustomerService, private dialogRef: MatDialogRef<DeleteDialogComponent>, @Inject(MAT_DIALOG_DATA) data) {
		this.item = data;
		this.userId = data.customer_id;
	}
	
	ngOnInit() {
	    this.rForm = new FormGroup ({
			id: new FormControl(this.item.id),
			item: new FormControl(""),
		});
	}

	processAction() {
		this.resObject['message'] = 'Wait...';
		this.resObject['textClass'] = 'text-warning';
		if ( this.serverRequest ) {
			this.serverRequest = false;
			this.customer.deleteAddress(this.rForm.value).subscribe(
				res => {
					this.serverRequest = true;
					if( res.status ) {
						this.dialogRef.close(this.rForm.value.id);
						this.resObject = {};
					} else {
						this.resObject['message'] = res.message;
						this.resObject['textClass'] = 'text-danger';
					}
				},
				(err: HttpErrorResponse) => {
					this.resObject['textClass'] = 'text-danger';
					if(err.error instanceof Error){
						this.resObject['message'] = 'App error, contact to customer care';
					}else{
						this.resObject['message'] = 'Server error, try later';
					}
				}
			);
		}	
    }

    closePopup() {
		this.resObject = {};
        this.dialogRef.close(0);
    }
}
