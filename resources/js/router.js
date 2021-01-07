import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store'

import Guest from "@/views/layouts/Guest";
import Authenticated from "@/views/layouts/Authenticated";

import Login from "@/views/auth/Login";
import Logout from "@/components/auth/Logout";
import Dashboard from "@/views/dashboard/Dashboard";

import PageNotFound from "@/views/errors/PageNotFound";
import Manufacturers from "@/views/product/Manufacturers";
import Manufacturer from "@/views/forms/Manufacturer";
import ManufacturerDetails from "@/views/product/Manufacturer";
import Taxes from "@/views/product/Taxes";
import ProductTypes from "@/views/product/ProductTypes";
import Products from "@/views/product/Products";
import Product from "@/views/forms/Product";
import ProductDetails from "@/views/product/Product";
import Customers from "@/views/customers/Customers";
import Customer from "@/views/forms/Customer";
import CustomerDetails from "@/views/customers/Customer"
import Vendors from "@/views/vendors/Vendors";
import Vendor from "@/views/forms/Vendor";
import VendorDetails from '@/views/vendors/Vendor'
import Sales from "@/views/sales/Sales";
import Sale from "@/views/forms/Sale";
import SaleDetails from "@/views/sales/Sale"
import Purchases from "@/views/purchases/Purchases";
import Purchase from "@/views/forms/Purchase";
import PurchaseDetails from "@/views/purchases/Purchase"
import Credits from "@/views/credits/Credits";
import Credit from "@/views/forms/Credit";
import CreditDetails from "@/views/credits/Credit"
import Debits from "@/views/debits/Debits";
import Debit from "@/views/forms/Debit";
import DebitDetails from "@/views/debits/Debit"
import Receipts from "@/views/receipts/Receipts";
import Receipt from "@/views/forms/Receipt";
import Vouchers from "@/views/vouchers/Vouchers";
import Voucher from "@/views/forms/Voucher";
import Accounts from "@/views/accounts/Accounts";
import Account from "@/views/forms/Account";
import AccountDetails from "@/views/accounts/Account"
import Incomes from "@/views/accounts/Incomes";
import Expenses from "@/views/accounts/Expenses";
import Support from "@/views/Support";
import Overview from "@/views/settings/Overview";
import Business from "@/views/forms/Business";

Vue.use(Router);

const routes =  [
    // Auth Routes
    {
        path: '/',
        component: Guest,
        meta: {
            redirectIfAuthenticated: true
        },
        children: [
            {
                path: '/',
                redirect: '/login'
            },
            {
                path: '/login',
                name: 'login',
                component: Login
            }
        ]
    },

    // Application Routes
    {
        path: '/admin',
        component: Authenticated,
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: '/dashboard',
                name: 'dashboard',
                component: Dashboard
            },

            /* Manufacturers */
            {
                path: '/manufacturers',
                name: 'manufacturers',
                component: Manufacturers
            },
            {
                path: '/manufacturers/new',
                name: 'manufacturers-new',
                component: Manufacturer
            },
            {
                path: '/manufacturers/:id/details',
                name: 'manufacturer-details',
                component: ManufacturerDetails
            },
            {
                path: '/manufacturers/:id/edit',
                name: 'manufacturer-edit',
                component: Manufacturer
            },

            /* Tax */
            {
                path: '/taxes',
                name: 'taxes',
                component: Taxes
            },

            /* Product Types */
            {
                path: '/product-types',
                name: 'product-types',
                component: ProductTypes
            },

            /* Products */
            {
                path: '/products',
                name: 'products',
                component: Products
            },
            {
                path: '/products/new',
                name: 'products-new',
                component: Product
            },
            {
                path: '/products/:id/details',
                name: 'product-details',
                component: ProductDetails
            },
            {
                path: '/products/:id/edit',
                name: 'product-edit',
                component: Product
            },

            /* Customers */
            {
                path: '/customers',
                name: 'customers',
                component: Customers
            },
            {
                path: '/customers/new',
                name: 'customers-new',
                component: Customer
            },
            {
                path: '/customers/:id/edit',
                name: 'customer-edit',
                component: Customer
            },
            {
                path: '/customers/:id/details',
                name: 'customer-details',
                component: CustomerDetails
            },

            /* Vendors */
            {
                path: '/vendors',
                name: 'vendors',
                component: Vendors
            },
            {
                path: '/vendors/new',
                name: 'vendors-new',
                component: Vendor
            },
            {
                path: '/vendors/:id/edit',
                name: 'vendor-edit',
                component: Vendor
            },
            {
                path: '/vendors/:id/details',
                name: 'vendor-details',
                component: VendorDetails
            },

            /* Sales */
            {
                path: '/sales',
                name: 'sales',
                component: Sales
            },
            {
                path: '/sales/new',
                name: 'sales-new',
                component: Sale
            },
            {
                path: '/sales/:id/edit',
                name: 'sale-edit',
                component: Sale
            },
            {
                path: '/sales/:id/details',
                name: 'sale-details',
                component: SaleDetails
            },

            /* Purchases */
            {
                path: '/purchases',
                name: 'purchases',
                component: Purchases
            },
            {
                path: '/purchases/new',
                name: 'purchases-new',
                component: Purchase
            },
            {
                path: '/purchases/:id/edit',
                name: 'purchase-edit',
                component: Purchase
            },
            {
                path: '/purchases/:id/details',
                name: 'purchase-details',
                component: PurchaseDetails
            },

            /* Credits */
            {
                path: '/credits',
                name: 'credits',
                component: Credits
            },
            {
                path: '/credits/new',
                name: 'credits-new',
                component: Credit
            },
            {
                path: '/credits/:id/edit',
                name: 'credit-edit',
                component: Credit
            },
            {
                path: '/credits/:id/details',
                name: 'credit-details',
                component: CreditDetails
            },

            /* Debits */
            {
                path: '/debits',
                name: 'debits',
                component: Debits
            },
            {
                path: '/debits/new',
                name: 'debits-new',
                component: Debit
            },
            {
                path: '/debits/:id/edit',
                name: 'debit-edit',
                component: Debit
            },
            {
                path: '/debits/:id/details',
                name: 'debit-details',
                component: DebitDetails
            },

            /* Receipts */
            {
                path: '/receipts',
                name: 'receipts',
                component: Receipts
            },
            {
                path: '/receipts/new',
                name: 'receipts-new',
                component: Receipt
            },
            {
                path: '/receipts/:id/edit',
                name: 'receipt-edit',
                component: Receipt
            },

            /* Vouchers */
            {
                path: '/vouchers',
                name: 'vouchers',
                component: Vouchers
            },
            {
                path: '/vouchers/new',
                name: 'vouchers-new',
                component: Voucher
            },
            {
                path: '/vouchers/:id/edit',
                name: 'voucher-edit',
                component: Voucher
            },

            /* Accounts */
            {
                path: '/accounts',
                name: 'accounts',
                component: Accounts
            },
            {
                path: '/accounts/new',
                name: 'accounts-new',
                component: Account
            },
            {
                path: '/accounts/:id/edit',
                name: 'account-edit',
                component: Account
            },
            {
                path: '/accounts/:id/details',
                name: 'account-details',
                component: AccountDetails
            },

            /* Incomes */
            {
                path: '/incomes',
                name: 'incomes',
                component: Incomes
            },

            /* Expenses */
            {
                path: '/expenses',
                name: 'expenses',
                component: Expenses
            },

            /* Settings */
            {
                path: 'settings/business',
                name: 'business',
                component: Business
            },
            {
                path: '/settings',
                name: 'settings',
                component: Overview
            },

            /* Misc Routes */
            {
                path: '/support',
                name: 'support',
                component: Support
            },





            {
                path: '/logout',
                name: 'logout',
                component: Logout
            }
        ]
    },

    /* All other pages to be redirected to 404*/
    {
        path: '*',
        name: '404',
        component: PageNotFound
    }
]

const router = new Router({
    routes,
    mode: 'history',
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return {x: 0, y: 0}
        }
    }
})

router.beforeEach((to, from, next) => {
    if(to.matched.some(match => match.meta.requiresAuth)) {
        if(store.getters.getCurrentUser.id == null) {
            return next({
                name: 'login'
            })
        }
    }

    if(to.matched.some(match => match.meta.redirectIfAuthenticated) && store.getters.getCurrentUser.id != null) {
        return next({
            name: 'dashboard'
        })
    }

    return next()
})

export default router;
