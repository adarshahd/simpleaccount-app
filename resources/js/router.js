import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store'

import Guest from "@/views/layouts/Guest";
import Authenticated from "./views/layouts/Authenticated";

import Login from "@/views/auth/Login";
import Logout from "./components/Logout";
import Dashboard from "@/views/dashboard/Dashboard";

import PageNotFound from "@/components/PageNotFound";

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
