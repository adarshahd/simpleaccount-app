import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store'

import Guest from "@/views/layouts/Guest";
import Authenticated from "@/views/layouts/Authenticated";

import Login from "@/views/auth/Login";
import Logout from "@/components/Logout";
import Dashboard from "@/views/dashboard/Dashboard";

import PageNotFound from "@/components/PageNotFound";
import Manufacturers from "@/views/product/Manufacturers";
import NewManufacturer from "@/views/forms/NewManufacturer";
import Manufacturer from "@/views/product/Manufacturer";
import Taxes from "@/views/product/Taxes";
import ProductTypes from "@/views/product/ProductTypes";
import Products from "@/views/product/Products";
import Product from "@/views/forms/Product";
import ProductDetails from "@/views/product/Product";
import Customers from "@/views/customers/Customers";
import Customer from "@/views/forms/Customer";
import CustomerDetails from "@/views/customers/Customer"

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
                component: NewManufacturer
            },
            {
                path: '/manufacturers/:id',
                name: 'manufacturer',
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
