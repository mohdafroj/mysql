/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { PlacedComponent } from './placed.component';

describe('PlacedComponent', () => {
  let component: PlacedComponent;
  let fixture: ComponentFixture<PlacedComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PlacedComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PlacedComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
