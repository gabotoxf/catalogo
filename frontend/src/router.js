import { createRouter, createWebHistory } from 'vue-router'
import Home from './pages/Home.vue'
import Products from './pages/Products.vue'
import ProductDetail from './pages/ProductDetail.vue'
import Login from './pages/Login.vue'
import Checkout from './pages/Checkout.vue'
import DashboardLayout from './components/layout/DashboardLayout.vue'
import DashboardHome from './pages/DashboardHome.vue'
import DashboardProducts from './pages/DashboardProducts.vue'
import DashboardCategories from './pages/DashboardCategories.vue'
import DashboardUsers from './pages/DashboardUsers.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/productos',
    name: 'products',
    component: Products
  },
  {
    path: '/producto/:id',
    name: 'product-detail',
    component: ProductDetail
  },
  {
    path: '/login',
    name: 'login',
    component: Login
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: Checkout
  },
  {
    path: '/categoria/:id',
    name: 'category-products',
    component: Products
  },
  {
    path: '/dashboard',
    component: DashboardLayout,
    children: [
      {
        path: '',
        name: 'dashboard-home',
        component: DashboardHome
      },
      {
        path: 'productos',
        name: 'dashboard-products',
        component: DashboardProducts
      },
      {
        path: 'categorias',
        name: 'dashboard-categories',
        component: DashboardCategories
      },
      {
        path: 'usuarios',
        name: 'dashboard-usuarios',
        component: DashboardUsers
      }
    ]
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
