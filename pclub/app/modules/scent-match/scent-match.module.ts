import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { DashboardComponent } from './dashboard/dashboard.component';
import { ScentMatchRoutingModule } from './scent-match-routing.module';
import {MatAutocompleteModule} from '@angular/material/autocomplete';

@NgModule({
	declarations: [
		DashboardComponent
	],
	imports: [
		CommonModule,
		ReactiveFormsModule,
		FormsModule,
		MatAutocompleteModule,
		ScentMatchRoutingModule
	]
})
export class ScentMatchModule { }
