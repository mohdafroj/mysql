import { NgModule }						 from '@angular/core';
import { RouterModule, Routes }			 from '@angular/router';
import { AboutUsComponent }				 from './about-us/about-us.component';

const modRoutes: Routes = [
    {path:'', 					component: AboutUsComponent, 			data:{id:0,title:'Perfumer\'s Club: About Us!'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class AboutUsRoutingModule { }
