import { Component, OnInit } from '@angular/core';
//import { Title } from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';

@Component({
  selector: 'app-account-detail',
  templateUrl: './account-detail.component.html',
  styleUrls: [
		'./../../../assets/css/user-dashboard.css',
		'./account-detail.component.css'
	]
})
export class AccountDetailComponent implements OnInit {
  rForm:FormGroup;
  msg:string		= '';
  response:any		= '';
  constructor(private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
  }

  ngOnInit() {
    this.rForm = new FormGroup ({
      firstname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP), Validators.minLength(3)]) ),
      lastname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
      dob: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.DATE_MM_DD_YYYY_REGEXP)]) ),
      gender: new FormControl("", Validators.required),
      mobile: new FormControl("", Validators.required)
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
        if(res.status){
          this.response = res.data;
        }else{
          console.log(res.message);
        }
        this.rForm = new FormGroup ({
          firstname: new FormControl(this.response.firstname, Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
          lastname: new FormControl(this.response.lastname, Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
          dob: new FormControl(this.response.dob.substring(0,10), Validators.compose([Validators.required,Validators.pattern(this.config.DATE_MM_DD_YYYY_REGEXP)]) ),
          gender: new FormControl(this.response.gender, Validators.required),
          mobile: new FormControl(this.response.mobile, Validators.required)
        });
      },
      (err: HttpErrorResponse) => {
        console.log("Server Isse!");
      }
    );

  }
  customerDetail(detail){
    this.msg = 'Wait...';
    this.auth.updateProfile(detail).subscribe(
      res => {
        //console.log(res);
        if(res.status){
          this.router.navigate(['/customer/profile']);
        }else{
          this.msg = res.message;
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
}
