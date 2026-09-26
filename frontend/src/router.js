import { createRouter, createWebHistory } from 'vue-router'
import { routeLoading } from './utils/routeLoading'
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
    path: '/producto/:slug',
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
    path: '/categoria/:slug',
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
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  routeLoading.value = true
  const baseTitle = 'Chaparro Ecommerce'
  const pageTitle = to.meta.title ? `${to.meta.title} | ${baseTitle}` : baseTitle
  document.title = pageTitle

  if (to.path.startsWith('/dashboard')) {
    const token = localStorage.getItem('token')
    const user = JSON.parse(localStorage.getItem('user') || 'null')
    if (!token) return next('/login')
    if (user?.rol_usuario != 1) return next('/')
  }
  if (to.path === '/login' && localStorage.getItem('token')) {
    const user = JSON.parse(localStorage.getItem('user') || 'null')
    return next(user?.rol_usuario == 1 ? '/dashboard' : '/')
  }

  next()
})

router.afterEach(() => (routeLoading.value = false))
router.onError(() => (routeLoading.value = false))

export default router
