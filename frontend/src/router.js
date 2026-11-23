import { createRouter, createWebHistory } from 'vue-router'
import { routeLoading } from './utils/routeLoading'

const Home = () => import('./pages/Home.vue')
const Products = () => import('./pages/Products.vue')
const ProductDetail = () => import('./pages/ProductDetail.vue')
const Login = () => import('./pages/Login.vue')
const Checkout = () => import('./pages/Checkout.vue')
const DashboardLayout = () => import('./components/layout/DashboardLayout.vue')
const DashboardHome = () => import('./pages/DashboardHome.vue')
const DashboardProducts = () => import('./pages/DashboardProducts.vue')
const DashboardCategories = () => import('./pages/DashboardCategories.vue')
const DashboardUsers = () => import('./pages/DashboardUsers.vue')

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
