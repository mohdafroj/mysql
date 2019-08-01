/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { PerfumeSelfieComponent } from './perfume-selfie.component';

describe('PerfumeSelfieComponent', () => {
  let component: PerfumeSelfieComponent;
  let fixture: ComponentFixture<PerfumeSelfieComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PerfumeSelfieComponent ]
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
