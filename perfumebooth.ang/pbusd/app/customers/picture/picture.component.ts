import { Component, OnInit, ElementRef } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { ToastrService } 										from 'ngx-toastr';

@Component({
  selector: 'cutomer-picture',
  templateUrl: './picture.component.html',
  styleUrls: [
		'./../../../assets/css/user-dashboard.css',
		'./picture.component.css'
	]
})
export class PictureComponent implements OnInit {
  name:any = '';
  picUrl:string = '';
  saveAction:boolean=false;
  constructor(private toastr:ToastrService, private titleService: Title, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService, private elem:ElementRef) {
    route.data.subscribe(res =>{
      titleService.setTitle(res.title);
    });
  }

  ngOnInit() {
	  this.name = this.auth.getName();
	  this.picUrl = this.auth.getImage();
  }
  
  openBrowse(){
	  this.elem.nativeElement.querySelector('#selectFile').click();
  }

  choosePicture(event){
    this.saveAction = false;
    let file = event.target.files[0];
    if(file.size > 0){
      let maxSize: number = 2; // 5MB
      let fileExt:any = ['JPG','GIF','PNG'];
      let ext = file.name.toUpperCase().split('.').pop() || file.name;
      if ( !fileExt.includes(ext) ){
		    this.toastr.error("Please choose jpg,gif or png file!");
        return true;
      }
      let fileSizeinMB = file.size / (1024 * 1000);
      let size:number = Math.round(fileSizeinMB * 100) / 100; // convert upto 2 decimal place
      if( fileSizeinMB == 0 || (size > maxSize) ){
		  this.toastr.error("File size should be less than 2 MB!");
        return true;
      }
      this.saveAction = true;
      this.savePicture(file);
      return false;
    }
  }

  savePicture(file){
    if(file.size > 0){
      let formData:FormData = new FormData();
      formData.append('fileToUpload',file,file.name);
      this.auth.updatePicture(formData).subscribe(
        res => {
          if(res.status){
            localStorage.setItem('usduser', JSON.stringify(res.data));
            this.picUrl = res.data.image;
            this.saveAction = false;
            this.toastr.success(res.message);
          }else{
            this.toastr.error(res.message);
          }
        },
        (err: HttpErrorResponse) => {
			    this.toastr.error("Server Isse!");
        }
      );
    }
  }

  cancelPicture(){
    this.saveAction = false;
  }

}
