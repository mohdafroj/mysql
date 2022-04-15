/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';
import { RouterModule } from '@angular/router';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { HttpClientModule } 	from '@angular/common/http';
import { ToastrModule } 	from 'ngx-toastr';
import { ProductModule } from './../../../featured/product/product.module';
import { BrandPerfumesComponent } from './perfume.component';

describe('BrandPerfumesComponent', () => {
  let component: BrandPerfumesComponent;
  let fixture: ComponentFixture<BrandPerfumesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BrandPerfumesComponent ],
	  imports: [ RouterModule.forRoot([]), HttpClientModule, ReactiveFormsModule, FormsModule, ProductModule, ToastrModule.forRoot()]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BrandPerfumesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
