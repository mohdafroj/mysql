import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { RouterModule } from '@angular/router';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatAutocompleteModule} 				from '@angular/material/autocomplete';
import { MatFormFieldModule} 					from '@angular/material/form-field';
import { MatInputModule} 						from '@angular/material/input';
import { HttpClientModule } 	from '@angular/common/http';
import { NoopAnimationsModule } from '@angular/platform-browser/animations';
import { SearchComponent } from './search.component';

describe('SearchComponent', () => {
  let component: SearchComponent;
  let fixture: ComponentFixture<SearchComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SearchComponent ],
	  imports: [NoopAnimationsModule, RouterModule.forRoot([]), ReactiveFormsModule, FormsModule, HttpClientModule, MatInputModule, MatAutocompleteModule, MatFormFieldModule ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SearchComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
