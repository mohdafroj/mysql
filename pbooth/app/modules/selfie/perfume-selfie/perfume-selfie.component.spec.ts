/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';
import { NguCarouselModule } from '@ngu/carousel';
import { NoopAnimationsModule } from '@angular/platform-browser/animations';
import { RouterModule } from '@angular/router';
import { HttpClientModule } 	from '@angular/common/http';
import { ProductModule } from './../../../featured/product/product.module';
import { ToastrModule } 	from 'ngx-toastr';
import { PerfumeSelfieComponent } from './perfume-selfie.component';

describe('PerfumeSelfieComponent', () => {
  let component: PerfumeSelfieComponent;
  let fixture: ComponentFixture<PerfumeSelfieComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PerfumeSelfieComponent ],
	  imports: [NoopAnimationsModule, NguCarouselModule, ProductModule, RouterModule.forRoot([]), ToastrModule.forRoot(), HttpClientModule ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PerfumeSelfieComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
