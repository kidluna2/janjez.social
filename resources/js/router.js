import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

axios.defaults.baseURL = window.location.origin;

const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

import Home from './pages/Home.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import Dashboard from './pages/Dashboard.vue';
import Services from './pages/Services.vue';
import Orders from './pages/Orders.vue';
import Deposit from './pages/Deposit.vue';
import Tickets from './pages/Tickets.vue';
import AdminDashboard from './pages/admin/Dashboard.vue';
import AdminServices from './pages/admin/Services.vue';
import AdminOrders from './pages/admin/Orders.vue';
import AdminUsers from './pages/admin/Users.vue';

const routes = [
    { path: '/', name: 'home', component: Home },
    { path: '/login', name: 'login', component: Login, meta: { guest: true } },
    { path: '/register', name: 'register', component: Register, meta: { guest: true } },
    { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { auth: true } },
    { path: '/services', name: 'services', component: Services, meta: { auth: true } },
    { path: '/orders', name: 'orders', component: Orders, meta: { auth: true } },
    { path: '/deposit', name: 'deposit', component: Deposit, meta: { auth: true } },
    { path: '/tickets', name: 'tickets', component: Tickets, meta: { auth: true } },
    { path: '/admin', name: 'admin.dashboard', component: AdminDashboard, meta: { auth: true, admin: true } },
    { path: '/admin/services', name: 'admin.services', component: AdminServices, meta: { auth: true, admin: true } },
    { path: '/admin/orders', name: 'admin.orders', component: AdminOrders, meta: { auth: true, admin: true } },
    { path: '/admin/users', name: 'admin.users', component: AdminUsers, meta: { auth: true, admin: true } },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    if (to.meta.auth && !token) {
        next('/login');
    } else if (to.meta.guest && token) {
        next('/dashboard');
    } else if (to.meta.admin && user.role !== 'admin') {
        next('/dashboard');
    } else {
        next();
    }
});
