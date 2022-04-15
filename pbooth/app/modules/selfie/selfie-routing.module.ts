import { NgModule }	from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { PerfumeSelfieComponent } from './perfume-selfie/perfume-selfie.component';
import { DetailComponent } from './detail/detail.component';

const modRoutes: Routes = [
    {path: 'products', component: PerfumeSelfieComponent, data: {id: 0, title: 'PerfumeBooth: Perfuem Selfie Offer!'} },
    {path: '', 		   component: DetailComponent, 		  data: {id: 0, title: 'PerfumeBooth: Selfie Details!'} }
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class SelfieRoutingModule { }
