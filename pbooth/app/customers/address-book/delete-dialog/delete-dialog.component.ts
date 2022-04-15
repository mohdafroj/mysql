import { Component, OnInit,Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef} from "@angular/material/dialog";
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { CustomerService } from '../../../_services/pb/customer.service';

@Component({
  selector: 'address-delete-dialog',
  templateUrl: './delete-dialog.component.html',
  styleUrls: ['./delete-dialog.component.css']
})
export class DeleteDialogComponent implements OnInit {
	rForm:FormGroup;
	item:any = {};
	userId:number = 0;
	message:string = '';
	errorStatus:number = 0;
	errClassObject:any = {};
	constructor(private auth:CustomerService,private dialogRef: MatDialogRef<DeleteDialogComponent>, @Inject(MAT_DIALOG_DATA) data) {
		this.item 	= data;
		this.userId 	= data.customer_id;
		this.errClassObject = {'':(this.errorStatus == 0),'text-success':(this.errorStatus == 1),'text-warning':(this.errorStatus == 2),'text-danger':(this.errorStatus == 3)};
	}
	
	ngOnInit() {
	    this.rForm = new FormGroup ({
			id: new FormControl(this.item.id),
			item: new FormControl(""),
		});
	}

	save() {
		this.errorStatus = 2;
		this.message = 'Wait...'
		this.auth.deleteAddress(this.rForm.value).subscribe(
			res => {
				this.message = res.message;
				if( res.status ){
					this.dialogRef.close(this.rForm.value.id);
				}else{
					this.errorStatus = 3;
				}
			},
			(err: HttpErrorResponse) => {
				this.errorStatus = 3;
				if(err.error instanceof Error){
					this.message = 'App error, contact to customer care';
				}else{
					this.message = 'Server error, try later';
				}
			}
		);
    }

    close() {
		this.message = ''
        this.dialogRef.close(0);
    }
}
