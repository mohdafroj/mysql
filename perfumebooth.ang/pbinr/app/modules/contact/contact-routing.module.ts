import { NgModule }						 from '@angular/core';
import { RouterModule, Routes }			 from '@angular/router';
import { ContactComponent }			 from './contact/contact.component';

const modRoutes: Routes = [
    {path:'', 					component: ContactComponent, 			data:{id:0,title:'PerfumeBooth: Contact Us!'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class ContactRoutingModule { }
