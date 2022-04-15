import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { DashboardComponent } from './dashboard/dashboard.component';
import { BuynowRoutingModule } from './buynow-routing.module';

@NgModule({
	imports: [
		CommonModule,
		ReactiveFormsModule,
		FormsModule,
		BuynowRoutingModule
	],
	declarations: [
		DashboardComponent
	],
	exports: [
		DashboardComponent
	]
})
export class BuynowModule { }
