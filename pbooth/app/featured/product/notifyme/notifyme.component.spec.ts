import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { RouterModule } from '@angular/router';
import { HttpClientModule } 	from '@angular/common/http';
import { NotifymeComponent } from './notifyme.component';

describe('NotifymeComponent', () => {
  let component: NotifymeComponent;
  let fixture: ComponentFixture<NotifymeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NotifymeComponent ],
	  imports: [RouterModule.forRoot([]), HttpClientModule]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NotifymeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
