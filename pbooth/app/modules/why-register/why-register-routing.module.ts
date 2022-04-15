import { NgModule }	from '@angular/core';
import { RouterModule, Routes }	from '@angular/router';
import { WhyRegisterComponent } from './why-register/why-register.component';

const modRoutes: Routes = [
    {path: '', component: WhyRegisterComponent, data: {id: 0, title: 'PerfumeBooth: Prive Member!'} }
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class WhyRegisterRoutingModule { }
