<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { 
  LayoutDashboard, 
  Package, 
  Layers, 
  Users, 
  Settings, 
  LogOut, 
  Menu, 
  X,
  Bell,
  Search,
  ChevronRight,
  Store
} from 'lucide-vue-next'
import Skeleton from './Skeleton.vue'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const isSidebarOpen = ref(true)
const dashboardSearchQuery = ref('')
const isLoadingInitial = ref(true)

const handleDashboardSearch = () => {
  if (dashboardSearchQuery.value.trim()) {
    router.push({ path: '/dashboard/productos', query: { search: dashboardSearchQuery.value } })
  }
}

const menuItems = [
  { name: 'Inicio', path: '/dashboard', icon: LayoutDashboard },
  { name: 'Productos', path: '/dashboard/productos', icon: Package },
  { name: 'Categorías', path: '/dashboard/categorias', icon: Layers },
  { name: 'Usuarios', path: '/dashboard/usuarios', icon: Users },
]

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

onMounted(async () => {
  if (!authStore.isAuthenticated || !authStore.isAdmin) {
    router.push('/login')
    return
  }

  // Simulate initial load for smooth skeleton transition
  setTimeout(() => {
    isLoadingInitial.value = false
  }, 600)
})
</script>

<template>
  <div class="layout-root">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar--open': isSidebarOpen }">
      <div class="sidebar__inner">
        <!-- Logo -->
        <div class="sidebar__logo">
          <div class="sidebar__logo-icon">
            <Store :size="20" />
          </div>
          <span class="sidebar__logo-text">Chaparro Admin</span>
        </div>

        <!-- Navigation -->
        <nav class="sidebar__nav">
          <RouterLink
            v-for="item in menuItems"
            :key="item.path"
            :to="item.path"
            class="sidebar__nav-item"
            active-class="sidebar__nav-item--active"
            :exact-active-class="'sidebar__nav-item--active'"
          >
            <component :is="item.icon" :size="18" />
            <span>{{ item.name }}</span>
            <ChevronRight v-if="route.path === item.path || (item.path !== '/dashboard' && route.path.startsWith(item.path))" :size="14" class="sidebar__nav-chevron" />
          </RouterLink>
        </nav>

        <!-- Footer: User + Logout -->
        <div class="sidebar__footer">
          <div class="sidebar__user">
            <div class="sidebar__avatar">
              {{ authStore.user?.nombre?.charAt(0)?.toUpperCase() || 'A' }}
            </div>
            <div class="sidebar__user-info">
              <p class="sidebar__user-name">{{ authStore.user?.nombre || 'Admin' }}</p>
              <p class="sidebar__user-role">Administrador</p>
            </div>
          </div>

          <button class="sidebar__logout" @click="handleLogout">
            <LogOut :size="18" />
            <span>Cerrar sesión</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- Mobile overlay -->
    <div
      v-if="isSidebarOpen"
      class="layout-overlay"
      @click="isSidebarOpen = false"
    />

    <!-- Right column: navbar + content -->
    <div class="layout-main">
      <!-- Navbar fijo -->
      <header class="navbar">
        <div class="navbar__left">
          <button class="navbar__menu-btn" @click="isSidebarOpen = !isSidebarOpen">
            <Menu v-if="!isSidebarOpen" :size="20" />
            <X v-else :size="20" />
          </button>

          <div class="navbar__search">
            <Search :size="14" class="text-neutral-400" />
            <input 
              v-model="dashboardSearchQuery"
              @keyup.enter="handleDashboardSearch"
              type="text" 
              placeholder="Buscar productos..." 
              class="text-xs font-medium"
            />
          </div>
        </div>

        <div class="navbar__right">
          <button class="navbar__icon-btn">
            <Bell :size="18" />
            <span class="navbar__badge"></span>
          </button>

          <RouterLink to="/" class="navbar__store-btn">
            <Store :size="16" />
            <span>Ver tienda</span>
          </RouterLink>
        </div>
      </header>

      <!-- Content area -->
      <main class="layout-content">
        <div v-if="isLoadingInitial" class="space-y-8 animate-in fade-in duration-500">
          <div class="flex flex-col gap-2">
            <Skeleton width="200px" height="28px" />
            <Skeleton width="300px" height="16px" />
          </div>
          <div class="grid grid-cols-4 gap-4">
            <Skeleton v-for="i in 4" :key="i" height="100px" borderRadius="16px" />
          </div>
          <div class="grid grid-cols-12 gap-6 mt-8">
            <div class="col-span-8">
              <Skeleton height="400px" borderRadius="24px" />
            </div>
            <div class="col-span-4">
              <Skeleton height="400px" borderRadius="24px" />
            </div>
          </div>
        </div>
        <router-view v-else v-slot="{ Component }">
          <transition name="page" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* ─── Reset & root ────────────────────────────────────────────── */
.layout-root {
  display: flex;
  height: 100vh;        /* ocupa exactamente la ventana */
  overflow: hidden;     /* impide que el body scrollee */
  background: #f5f5f4;
  font-family: 'Inter', sans-serif;
}

/* ─── Sidebar ─────────────────────────────────────────────────── */
.sidebar {
  width: 272px;
  min-width: 272px;
  height: 100%;         /* 100% del padre que ya es 100vh */
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  z-index: 50;

  /* escritorio: siempre visible */
  position: sticky;
  top: 0;
  left: 0;
}

.sidebar__inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 24px 16px;
}

/* Logo */
.sidebar__logo {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 8px 32px;
}

.sidebar__logo-icon {
  width: 40px;
  height: 40px;
  background: var(--color-brand-900);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  flex-shrink: 0;
}

.sidebar__logo-text {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--color-brand-900);
  letter-spacing: -0.025em;
}

/* Nav */
.sidebar__nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
  overflow-y: auto;     /* si hay muchos items, solo el nav scrollea */
}

.sidebar__nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s;
}

.sidebar__nav-item:hover {
  background: var(--color-brand-50);
  color: var(--color-brand-900);
}

.sidebar__nav-item--active {
  background: var(--color-brand-900);
  color: #ffffff;
}

.sidebar__nav-chevron {
  margin-left: auto;
  opacity: 0.5;
}

/* Footer */
.sidebar__footer {
  margin-top: auto;
  padding-top: 20px;
  border-top: 1px solid var(--color-brand-100);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sidebar__user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px;
}

.sidebar__avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: var(--color-brand-50);
  border: 1px solid var(--color-brand-100);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--color-brand-900);
  flex-shrink: 0;
}

.sidebar__user-name {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--color-brand-900);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}

.sidebar__user-role {
  font-size: 0.6875rem;
  color: var(--color-brand-500);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.sidebar__logout {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 10px 14px;
  border-radius: 10px;
  border: none;
  background: transparent;
  font-size: 0.8125rem;
  font-weight: 600;
  color: #ef4444;
  cursor: pointer;
  transition: all 0.2s;
}

.sidebar__logout:hover {
  background: #fef2f2;
}

/* ─── Layout main (columna derecha) ───────────────────────────── */
.layout-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;     /* evita que esta columna scrollee */
  min-width: 0;
}

/* ─── Navbar ──────────────────────────────────────────────────── */
.navbar {
  height: 56px;
  background: #ffffff;
  border-bottom: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  position: sticky;
  top: 0;
  z-index: 40;
}

.navbar__left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.navbar__menu-btn {
  display: none;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  color: #6b7280;
  transition: all 0.2s;
}

.navbar__search {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f9fafb;
  border: 1px solid #f3f4f6;
  padding: 6px 14px;
  border-radius: 10px;
  width: 280px;
  transition: all 0.2s;
}

.navbar__search:focus-within {
  background: #ffffff;
  border-color: var(--color-brand-900);
  box-shadow: 0 0 0 2px var(--color-brand-100);
}

.navbar__search input {
  background: transparent;
  border: none;
  outline: none;
  width: 100%;
  font-size: 0.8125rem;
  color: var(--color-brand-900);
  font-weight: 500;
}

/* ─── Right side ──────────────────────────────────────────────── */
.navbar__right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.navbar__icon-btn {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  color: var(--color-brand-500);
  position: relative;
  transition: all 0.2s;
}

.navbar__icon-btn:hover {
  background: var(--color-brand-50);
  color: var(--color-brand-900);
}

.navbar__badge {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 6px;
  height: 6px;
  background: #ef4444;
  border-radius: 50%;
  border: 2px solid #ffffff;
}

.navbar__store-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  background: var(--color-brand-900);
  color: #ffffff;
  border-radius: 10px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.025em;
  transition: all 0.2s;
}

.navbar__store-btn:hover {
  background: var(--color-brand-800);
  transform: translateY(-1px);
}

/* ─── Content ─────────────────────────────────────────────────── */
.layout-content {
  flex: 1;
  padding: 32px;
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
}

/* ─── Transición de páginas ───────────────────────────────────── */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(12px);
}

.page-enter-to {
  opacity: 1;
  transform: translateY(0);
}

.page-leave-from {
  opacity: 1;
  transform: translateY(0);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* ─── Mobile overlay ──────────────────────────────────────────── */
.layout-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 45;
  display: none;
}

/* ─── Responsive ──────────────────────────────────────────────── */
@media (max-width: 1024px) {
  /* En móvil el sidebar sale del flujo */
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }

  .sidebar--open {
    transform: translateX(0);
  }

  .layout-overlay {
    display: block;
  }

  .navbar__menu-btn {
    display: flex;
  }

  .navbar__search {
    width: 200px;
  }

  .layout-content {
    padding: 20px 16px;
  }
}

@media (max-width: 640px) {
  .navbar__search {
    display: none;
  }

  .navbar__store-btn span {
    display: none;
  }

  .navbar {
    padding: 0 16px;
  }
}
</style>

<style scoped>
@reference "../../style.css";
.router-link-active {
  @apply bg-brand-900 text-white shadow-xl shadow-brand-900/20;
}
</style>
