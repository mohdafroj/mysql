import { Component, Input, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';

@Component({
  selector: 'app-security',
  templateUrl: './security.component.html',
  styleUrls: [
		'./../../../assets/css/user-dashboard.css',
		'./security.component.css'
	]
})
export class SecurityComponent implements OnInit {
  rForm:FormGroup;
  msg:string;
  response:any;
  constructor(private titleService: Title, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
    route.data.subscribe(res =>{
      titleService.setTitle(res.title);

    });
  }
  ngOnInit() {
      this.rForm = new FormGroup ({
        currentPassword: new FormControl("", Validators.compose([Validators.required]) ),
        newPassword: new FormControl("", Validators.compose([Validators.required]) ),
        confirmPassword: new FormControl("", this.confirmPasswordValidator),
      });
    this.response = {
      firstname:'',
      lastname:'',
      email:'',
      gender:'',
      dob:'',
      mobile:'',
      image:'',
      created:'',
      modified:''
    }
    this.auth.getProfile().subscribe(
      res => {
        //console.log(res);
        if(res.status){
          this.response = res.data;
        }else{
          console.log(res.message);
        }
      },
      (err: HttpErrorResponse) => {
        console.log("Server Isse!");
      }
    );

  }
  confirmPasswordValidator(control){
    if( control.value == "" ) {
      return {'confirmPassword': true};
    }
  }

  updateSecurity(formData) {
    this.msg = 'Wait...';
    this.auth.updateSecurity(formData).subscribe(
      res => {
        this.msg = res.message;
        if (res.status) {
          this.rForm = new FormGroup ({
            currentPassword: new FormControl(""),
            newPassword: new FormControl(""),
            confirmPassword: new FormControl(""),
          });
        }
      },
      (err: HttpErrorResponse) => {
        if (err.error instanceof Error) {
          this.msg = 'Client error: ' + err.error.message;
        } else {
          this.msg = 'Server error: ' + JSON.stringify(err.error);
        }
      }
    );
  }

}
