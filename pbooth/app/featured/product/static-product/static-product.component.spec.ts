import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { DebugElement } from '@angular/core';
import { NguCarouselModule } from '@ngu/carousel';
import { RouterModule } from '@angular/router';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { HttpClientModule } 	from '@angular/common/http';
import { ToastrModule } 	from 'ngx-toastr';

import { StaticProductComponent } from './static-product.component';
import 'hammerjs';

describe('StaticProductComponent', () => {
  let component: StaticProductComponent;
  let fixture: ComponentFixture<StaticProductComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StaticProductComponent ],
	  imports: [ NguCarouselModule, RouterModule.forRoot([]), HttpClientModule, ToastrModule.forRoot()]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StaticProductComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
    
});
