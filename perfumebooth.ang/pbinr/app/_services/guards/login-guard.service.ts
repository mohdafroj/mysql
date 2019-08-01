import { Injectable } from '@angular/core';
import { CanActivate, Router, ActivatedRouteSnapshot, RouterStateSnapshot }    from '@angular/router';
import { CustomerService } from './../pb/customer.service'

@Injectable({
	providedIn:'root'
})
export class LoginGuard implements CanActivate{
  constructor(private customer:CustomerService, private router:Router){    
  }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    let id:number = this.customer.getId();
    if( id == 0 || undefined || null ){
      return true;
    }
    this.router.navigate(['/customer/profile'], {
      queryParams: {
        return: state.url
      }
    });
    return false;
  }

}
