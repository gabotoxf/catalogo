<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import {
  LayoutDashboard,
  Package,
  Layers,
  Users,
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

// En desktop el sidebar siempre está "abierto" visualmente (posición estática),
// este ref solo controla el drawer móvil.
const isMobileDrawerOpen = ref(false)
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

const toggleMobileDrawer = () => {
  isMobileDrawerOpen.value = !isMobileDrawerOpen.value
}

const closeMobileDrawer = () => {
  isMobileDrawerOpen.value = false
}

const isActiveRoute = (item) =>
  route.path === item.path ||
  (item.path !== '/dashboard' && route.path.startsWith(item.path))

onMounted(() => {
  if (!authStore.isAuthenticated || authStore.user?.rol_usuario != 1) {
    router.push('/login')
    return
  }
  setTimeout(() => { isLoadingInitial.value = false }, 600)
})
</script>

<template>
  <!-- Root: flex row, full viewport height -->
  <div class="flex h-screen overflow-hidden bg-neutral-100 font-sans">

    <!-- ═══════════════════════════════════════════
         SIDEBAR — desktop: always visible (static)
                   mobile:  drawer (fixed, z-50)
         ═══════════════════════════════════════════ -->
    <aside class="
        flex-shrink-0 w-68 h-screen bg-white border-r border-neutral-200
        flex flex-col
        transition-transform duration-300 ease-[cubic-bezier(.4,0,.2,1)]

        /* Desktop: posición normal en el flujo, siempre visible */
        lg:relative lg:translate-x-0

        /* Mobile: fixed drawer, oculto por defecto */
        max-lg:fixed max-lg:top-0 max-lg:left-0 max-lg:z-50
      " :class="isMobileDrawerOpen ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-full'">
      <div class="flex flex-col h-full px-4 py-6">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-2 pb-8">
          <div class="w-10 h-10 bg-brand-900 rounded-xl flex items-center justify-center text-white flex-shrink-0">
            <Store :size="20" />
          </div>
          <span class="text-[1.1rem] font-extrabold text-brand-900 tracking-tight">
            Chaparro Admin
          </span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 flex flex-col gap-1 overflow-y-auto">
          <RouterLink v-for="item in menuItems" :key="item.path" :to="item.path" @click="closeMobileDrawer" class="
              flex items-center gap-3 px-3.5 py-2.5 rounded-xl
              text-sm font-semibold text-slate-500
              transition-all duration-200
              hover:bg-brand-50 hover:text-brand-900
            " :class="isActiveRoute(item) ? 'bg-brand-900 !text-white shadow-lg shadow-brand-900/20' : ''">
            <component :is="item.icon" :size="18" />
            <span>{{ item.name }}</span>
            <ChevronRight v-if="isActiveRoute(item)" :size="14" class="ml-auto opacity-50" />
          </RouterLink>
        </nav>

        <!-- Footer: user info + logout -->
        <div class="mt-auto pt-5 border-t border-brand-100 flex flex-col gap-3">
          <div class="flex items-center gap-2.5 px-2">
            <div
              class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-100 flex items-center justify-center text-xs font-bold text-brand-900 flex-shrink-0">
              {{ authStore.user?.nombre?.charAt(0)?.toUpperCase() || 'A' }}
            </div>
            <div class="min-w-0">
              <p class="text-[0.8125rem] font-bold text-brand-900 truncate max-w-[140px]">
                {{ authStore.user?.nombre || 'Admin' }}
              </p>
              <p class="text-[0.6875rem] text-brand-500 font-medium uppercase tracking-wide">
                Administrador
              </p>
            </div>
          </div>

          <button @click="handleLogout"
            class="flex items-center gap-2.5 w-full px-3.5 py-2.5 rounded-xl text-[0.8125rem] font-semibold text-red-500 hover:bg-red-50 transition-all duration-200 cursor-pointer">
            <LogOut :size="18" />
            <span>Cerrar sesión</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- Mobile overlay -->
    <Transition name="fade">
      <div v-if="isMobileDrawerOpen" class="fixed inset-0 bg-black/45 z-40 lg:hidden" @click="closeMobileDrawer" />
    </Transition>

    <!-- ═══════════════════════════════════════════
         MAIN COLUMN: navbar + content
         ═══════════════════════════════════════════ -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto bg-slate-50">

      <!-- Sticky navbar -->
      <header class="sticky top-0 z-40 bg-white border-b border-neutral-200 shadow-sm">
        <div class="flex items-center justify-between px-4 sm:px-8 py-4 gap-4">

          <!-- Left: toggle (mobile only) + search -->
          <div class="flex items-center gap-3">
            <!-- Toggle: en desktop se puede ocultar o mantener para preferencia del usuario -->
            <button @click="toggleMobileDrawer"
              class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:bg-neutral-200 hover:text-neutral-900 transition-all cursor-pointer flex-shrink-0">
              <Menu v-if="!isMobileDrawerOpen" :size="20" />
              <X v-else :size="20" />
            </button>

            <!-- Search bar -->
            <div
              class="hidden sm:flex items-center gap-3 bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-2.5 w-72 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition-all">
              <Search :size="14" class="text-neutral-400 flex-shrink-0" />
              <input v-model="dashboardSearchQuery" @keyup.enter="handleDashboardSearch" type="text"
                placeholder="Buscar productos..."
                class="bg-transparent border-none outline-none w-full text-sm text-neutral-900 placeholder:text-neutral-400 placeholder:font-normal font-medium" />
            </div>
          </div>

          <!-- Right: bell + store link -->
          <div class="flex items-center gap-2">
            <button
              class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-500 hover:bg-neutral-200 hover:text-neutral-900 transition-all cursor-pointer">
              <Bell :size="18" />
              <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white" />
            </button>

            <RouterLink to="/"
              class="flex items-center gap-2 px-4 py-2.5 bg-brand-900 text-white rounded-xl text-sm font-bold hover:bg-brand-800 transition-all hover:-translate-y-px">
              <Store :size="16" />
              <span class="hidden sm:inline">Ver tienda</span>
            </RouterLink>
          </div>
        </div>
      </header>

      <!-- Content area -->
      <main class="flex-1 p-6 sm:p-10 max-w-[1600px] w-full mx-auto">
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
/* Sidebar width personalizada (no está en Tailwind por defecto) */
.w-68 {
  width: 272px;
}

/* Transición de página */
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

/* Fade para el overlay */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
