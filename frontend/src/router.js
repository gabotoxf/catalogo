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
    component: Home,
    meta: { title: 'Inicio' }
  },
  {
    path: '/productos',
    name: 'products',
    component: Products,
    meta: { title: 'Productos' }
  },
  {
    path: '/producto/:id',
    name: 'product-detail',
    component: ProductDetail,
    meta: { title: 'Detalle del Producto' }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { title: 'Iniciar Sesión' }
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: Checkout,
    meta: { title: 'Finalizar Pedido' }
  },
  {
    path: '/categoria/:id',
    name: 'category-products',
    component: Products,
    meta: { title: 'Categoría' }
  },
  {
    path: '/dashboard',
    component: DashboardLayout,
    children: [
      {
        path: '',
        name: 'dashboard-home',
        component: DashboardHome,
        meta: { title: 'Dashboard - Inicio' }
      },
      {
        path: 'productos',
        name: 'dashboard-products',
        component: DashboardProducts,
        meta: { title: 'Dashboard - Productos' }
      },
      {
        path: 'categorias',
        name: 'dashboard-categories',
        component: DashboardCategories,
        meta: { title: 'Dashboard - Categorías' }
      },
      {
        path: 'usuarios',
        name: 'dashboard-usuarios',
        component: DashboardUsers,
        meta: { title: 'Dashboard - Usuarios' }
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

router.beforeEach((to, from, next) => {
  const baseTitle = 'Chaparro Ecommerce'
  const pageTitle = to.meta.title ? `${to.meta.title} | ${baseTitle}` : baseTitle
  document.title = pageTitle
  next()
})

export default router
