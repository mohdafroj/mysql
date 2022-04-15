import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { RouterModule } from '@angular/router';
import { HttpClientModule } 	from '@angular/common/http';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatAutocompleteModule} 				from '@angular/material/autocomplete';
import { MatFormFieldModule} 					from '@angular/material/form-field';
import { MatInputModule} 						from '@angular/material/input';
import { ToastrModule } 	from 'ngx-toastr';
import { NoopAnimationsModule } from '@angular/platform-browser/animations';
import { MiniCartComponent } from './../mini-cart/mini-cart.component';
import { SearchComponent } from './../search/search.component';
import { HeaderComponent } from './header.component';

describe('HeaderComponent', () => {
  let component: HeaderComponent;
  let fixture: ComponentFixture<HeaderComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ HeaderComponent, MiniCartComponent, SearchComponent ],
	  imports: [NoopAnimationsModule, RouterModule.forRoot([]), HttpClientModule, ReactiveFormsModule, FormsModule, MatAutocompleteModule, MatFormFieldModule, MatInputModule, ToastrModule.forRoot()]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HeaderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
