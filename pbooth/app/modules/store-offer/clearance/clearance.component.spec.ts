import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { RouterModule } from '@angular/router';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { HttpClientModule } 	from '@angular/common/http';
import { ToastrModule } 	from 'ngx-toastr';
import { ProductModule } from './../../../featured/product/product.module'
import { ClearanceComponent } from './clearance.component';

describe('SaleComponent', () => {
  let component: ClearanceComponent;
  let fixture: ComponentFixture<ClearanceComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ClearanceComponent ],
	  imports: [ RouterModule.forRoot([]), HttpClientModule, ReactiveFormsModule, FormsModule, ProductModule, ToastrModule.forRoot()]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ClearanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
