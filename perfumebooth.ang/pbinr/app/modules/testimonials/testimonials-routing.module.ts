import { NgModule }	from '@angular/core';
import { RouterModule, Routes }	from '@angular/router';
import { TestimonialsComponent } from './testimonials/testimonials.component';

const modRoutes: Routes = [
    {path: '', 	component: TestimonialsComponent, data: {id: 0, title: 'PerfumeBooth: Testimonials!'} }
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class TestimonialsRoutingModule { }
