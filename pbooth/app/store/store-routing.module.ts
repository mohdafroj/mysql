import { NgModule }	from '@angular/core';
import { RouterModule, Routes }	from '@angular/router';
import { CartComponent } from './cart/cart.component';
import { CheckoutComponent } from './checkout/checkout.component';
import { PlacedComponent } from './placed/placed.component';
import { SuccessComponent } from './success/success.component';
import { FailureComponent } from './failure/failure.component';
import { UnauthorizedComponent } from './unauthorized/unauthorized.component';
import { AuthGuard } from './../_services/guards/auth-guard.service';

const storeRoutes: Routes = [
    { path: '', 						component: CartComponent, 			canActivate: [AuthGuard], data: {id: 0, title: 'Shopping Cart'}},
    { path: 'cart', 					component: CartComponent, 			canActivate: [AuthGuard], data: {id: 0, title: 'Shopping Cart'}},
    { path: 'unauthorized', 			component: UnauthorizedComponent, 	canActivate: [AuthGuard], data: {id: 0, title: 'Unauthorized Access'}},
    { path: 'onepage', 					component: CheckoutComponent, 		canActivate: [AuthGuard], data: {id: 0, title: 'Store Checkout'}},
    { path: 'onepage/confirmation', 	component: PlacedComponent, 		canActivate: [AuthGuard], data: {id: 0, title: 'Order Confirmation'}},
    { path: 'onepage/success', 			component: SuccessComponent, 		canActivate: [AuthGuard], data: {id: 0, title: 'Order Success'}},
    { path: 'onepage/failure', 			component: FailureComponent, 		canActivate: [AuthGuard], data: {id: 0, title: 'Order Failure'}},
];

@NgModule({
  imports: [RouterModule.forChild(storeRoutes)],
  exports: [RouterModule]
})
export class StoreRoutingModule { }
