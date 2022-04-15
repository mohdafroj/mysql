import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { DebugElement } from '@angular/core';
import { NguCarouselModule } from '@ngu/carousel';
import { PopupProductComponent } from './popup-product.component';
import 'hammerjs';

describe('PopupProductComponent', () => {
  let component: PopupProductComponent;
  let fixture: ComponentFixture<PopupProductComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PopupProductComponent ],
	  imports: [ NguCarouselModule ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PopupProductComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('Should create', () => {
    expect(component).toBeTruthy();
  });
  
  it('Should render the ngu carousel', async(() => {
    fixture.detectChanges();
    let compiled = fixture.debugElement.nativeElement;
    expect(compiled.querySelector('ngu-carousel')).not.toBeNull();
  }));
  
});
