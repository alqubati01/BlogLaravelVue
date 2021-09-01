import Router from 'vue-router'
import Vue from 'vue'
Vue.use(Router)
import hooks from './components/pages/basic/hooks.vue'
import methods from './components/pages/basic/methods.vue'
import usecom from './vuex/usecom.vue'

// admin project pages
import home from './components/pages/Home'
import tags from './admin/pages/Tags'
import categories from './admin/pages/Categories'
import adminusers from './admin/pages/AdminUsers'
import login from './admin/pages/Login'
import role from './admin/pages/Role'
import assignRole from './admin/pages/AssignRole'
import createBlog from './admin/pages/CreateBlog'
import blogs from './admin/pages/Blogs'


const routes = [
    {
        path: '/',
        component: home,
        name: 'home'
    },
    {
        path: '/tags',
        component: tags,
        name: 'tags'
    },
    {
        path: '/categories',
        component: categories,
        name: 'categories'
    },
    {
        path: '/adminusers',
        component: adminusers,
        name: 'adminusers'
    },
    {
        path: '/login',
        component: login,
        name: 'login'
    },
    {
        path: '/role',
        component: role,
        name: 'role'
    },
    {
        path: '/assignRole',
        component: assignRole,
        name: 'assignRole'
    },
    {
        path: '/createBlog',
        component: createBlog,
        name: 'createBlog'
    },
    {
        path: '/blogs',
        component: blogs,
        name: 'blogs'
    },
    
    {
        path: '/hooks',
        component: hooks
    },
    {
        path: '/methods',
        component: methods
    },
    {
        path: '/testvuex',
        component: usecom
    }
]

export default new Router({
    mode: 'history',
    routes
})