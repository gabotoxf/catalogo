<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useCartStore } from '../../stores/cart'
import { useAuthStore } from '../../stores/auth'
import { ShoppingCart, User, Search, Menu, X, ChevronDown, MapPin, Truck, Tag, Users, Info, Loader2, ArrowRight } from 'lucide-vue-next'
import CartModal from './CartModal.vue'
import api from '../../api/axios'

const cartStore = useCartStore()
const authStore = useAuthStore()
const router = useRouter()

const isMenuOpen = ref(false)
const isCartOpen = ref(false)
const searchQuery = ref('')
const isScrolled = ref(false)
const categories = ref([])
const showCategoriesDropdown = ref(false)

// Real-time search states
const searchResults = ref([])
const isSearching = ref(false)
const showSearchResults = ref(false)
let searchTimeout = null

const totalItems = computed(() => cartStore.totalItems)

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'products', query: { search: searchQuery.value } })
    isMenuOpen.value = false
    showSearchResults.value = false
  }
}

const performRealTimeSearch = async () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = []
    showSearchResults.value = false
    return
  }

  isSearching.value = true
  showSearchResults.value = true

  try {
    const response = await api.post('/productos/filtrar', { query: searchQuery.value })
    // Limit to 5 results for the dropdown
    searchResults.value = (response.data.productos || []).slice(0, 5)
  } catch (err) {
    console.error('Error in real-time search', err)
  } finally {
    isSearching.value = false
  }
}

watch(searchQuery, (newVal) => {
  clearTimeout(searchTimeout)
  if (!newVal.trim()) {
    searchResults.value = []
    showSearchResults.value = false
    return
  }
  searchTimeout = setTimeout(performRealTimeSearch, 300)
})

const selectSearchResult = (product) => {
  router.push({ name: 'product-detail', params: { id: product.id_producto } })
  showSearchResults.value = false
  searchQuery.value = ''
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/home')
    categories.value = response.data.categorias || []
  } catch (err) {
    console.error('Error fetching categories', err)
  }
}

const getImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  return `http://localhost:8000/assets/img/Productos/${img}`
}

onMounted(() => {
  fetchCategories()
  window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 10
  })
  // Close search results when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-container')) {
      showSearchResults.value = false
    }
  })
})
</script>

<template>
  <div class="w-full z-[100] sticky top-0">
    <!-- Top Bar / Indicator -->
    <div class="bg-brand-900 text-white py-2 px-4 text-center text-xs md:text-sm font-medium">
      <div class="max-w-[var(--max-width)] mx-auto flex items-center justify-center gap-2">
        <Truck :size="16" />
        <span>Envíos hoy en tu ciudad - ¡Pide antes de las 11 AM!</span>
      </div>
    </div>

    <header :class="[
      'w-full transition-all duration-300 border-b bg-white py-4',
      isScrolled ? 'shadow-md border-brand-100' : 'border-transparent'
    ]">
      <nav class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 flex items-center justify-between gap-4">
        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2 shrink-0">
          <img src="/logo.jfif" alt="Logo" class="h-10 w-10 object-cover rounded shadow-sm" />
          <span class="text-xl font-black tracking-tighter text-brand-900 uppercase hidden sm:block">Chaparro</span>
        </RouterLink>

        <!-- Search (Prominent) -->
        <div class="hidden md:flex flex-1 max-w-xl relative search-container">
          <div class="relative w-full">
            <input v-model="searchQuery" @keyup.enter="handleSearch" @focus="showSearchResults = searchQuery.length > 0"
              type="text" placeholder="Busca frutas, verduras, lácteos..."
              class="w-full pl-11 pr-4 py-2.5 bg-brand-50 border border-brand-100 rounded-full text-sm focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all outline-none" />
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-400" :size="18" />

            <!-- Real-time Results Dropdown -->
            <div v-if="showSearchResults"
              class="absolute top-full left-0 right-0 mt-2 bg-white border border-brand-100 shadow-2xl rounded-2xl overflow-hidden z-[110] animate-in fade-in slide-in-from-top-2 duration-200 max-h-[70vh] overflow-y-auto">
              <div v-if="isSearching" class="p-8 text-center">
                <Loader2 class="animate-spin text-brand-500 mx-auto mb-2" :size="24" />
                <p class="text-xs font-bold text-brand-400 uppercase tracking-widest">Buscando...</p>
              </div>

              <div v-else-if="searchResults.length > 0" class="divide-y divide-brand-50">
                <button v-for="product in searchResults" :key="product.id_producto" @click="selectSearchResult(product)"
                  class="w-full flex items-center gap-4 p-4 hover:bg-brand-50 transition-colors text-left group">
                  <div class="h-12 w-12 rounded-xl overflow-hidden bg-brand-50 border border-brand-100 shrink-0">
                    <img :src="getImageUrl(product.imagen_producto)"
                      class="h-full w-full object-cover group-hover:scale-110 transition-transform" />
                  </div>
                  <div class="flex-1">
                    <h4 class="font-bold text-brand-900 text-sm uppercase tracking-tight">{{ product.nombre_producto }}
                    </h4>
                    <p class="text-xs text-brand-400 font-bold tracking-widest uppercase">{{
                      product.categoria?.nombre_categoria || 'Campo' }}</p>
                  </div>
                  <div class="text-right">
                    <p class="font-black text-brand-900">${{ Math.floor(product.precio_producto).toLocaleString('es-CO')
                      }}</p>
                    <span class="text-[10px] font-black text-brand-500 uppercase flex items-center gap-1">Ver
                      <ArrowRight :size="10" />
                    </span>
                  </div>
                </button>

                <button @click="handleSearch"
                  class="w-full p-4 bg-brand-50/50 text-brand-600 text-xs font-black uppercase tracking-widest hover:bg-brand-50 transition-colors flex items-center justify-center gap-2">
                  Ver todos los resultados
                  <ArrowRight :size="14" />
                </button>
              </div>

              <div v-else class="p-8 text-center">
                <Search class="text-brand-100 mx-auto mb-3" :size="32" />
                <p class="text-sm font-bold text-brand-900">No encontramos resultados</p>
                <p class="text-xs text-brand-400 mt-1 font-medium">Prueba con otra palabra clave</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-1 md:gap-3">
          <RouterLink :to="authStore.isAuthenticated ? '/dashboard' : '/login'"
            class="flex items-center gap-2 px-3 py-2 text-brand-700 hover:text-brand-900 hover:bg-brand-50 rounded-lg transition-colors">
            <User :size="20" />
            <span class="text-sm font-semibold hidden lg:block">{{ authStore.isAuthenticated ? 'Mi Cuenta' : 'Entrar'
              }}</span>
          </RouterLink>

          <button @click="isCartOpen = true"
            class="flex items-center gap-2 px-3 py-2 text-brand-700 hover:text-brand-900 hover:bg-brand-50 rounded-lg transition-colors relative">
            <ShoppingCart :size="20" />
            <span class="text-sm font-semibold hidden lg:block">Carrito</span>
            <span v-if="totalItems > 0"
              class="absolute top-1 left-6 bg-brand-600 text-white text-[10px] font-bold h-4 w-4 flex items-center justify-center rounded-full shadow-sm">
              {{ totalItems }}
            </span>
          </button>

          <button @click="isMenuOpen = !isMenuOpen" class="md:hidden p-2 text-brand-600">
            <Menu v-if="!isMenuOpen" :size="24" />
            <X v-else :size="24" />
          </button>
        </div>
      </nav>

      <!-- Bottom Nav (Desktop) -->
      <div class="hidden md:block border-t border-brand-50 mt-2 pt-2">
        <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 flex items-center justify-between">
          <div class="flex items-center gap-8 text-sm font-bold text-brand-700">
            <!-- Categories Dropdown -->
            <div class="relative group" @mouseenter="showCategoriesDropdown = true"
              @mouseleave="showCategoriesDropdown = false">
              <button class="flex items-center gap-1 hover:text-brand-900 py-2 transition-colors">
                <Menu :size="16" />
                Categorías
                <ChevronDown :size="14" class="transition-transform group-hover:rotate-180" />
              </button>

              <div v-show="showCategoriesDropdown"
                class="absolute top-full left-0 w-64 bg-white border border-brand-100 shadow-xl rounded-b-xl py-4 z-50 max-h-[50vh] overflow-y-auto">
                <div v-if="categories.length === 0" class="px-4 py-2 text-brand-400 font-normal">Cargando...</div>
                <RouterLink v-for="cat in categories" :key="cat.id_categoria"
                  :to="{ name: 'category-products', params: { id: cat.id_categoria } }"
                  class="block px-6 py-2.5 hover:bg-brand-50 hover:text-brand-900 transition-colors font-medium">
                  {{ cat.nombre_categoria }}
                </RouterLink>
              </div>
            </div>

            <RouterLink to="/#ofertas"
              class="hover:text-brand-900 py-2 border-b-2 border-transparent hover:border-brand-500 transition-all flex items-center gap-1">
              <Tag :size="14" class="text-brand-500" />
              Ofertas
            </RouterLink>
            <RouterLink to="/#productores"
              class="hover:text-brand-900 py-2 border-b-2 border-transparent hover:border-brand-500 transition-all flex items-center gap-1">
              <Users :size="14" class="text-brand-500" />
              Productores
            </RouterLink>
            <RouterLink to="/#como-funciona"
              class="hover:text-brand-900 py-2 border-b-2 border-transparent hover:border-brand-500 transition-all flex items-center gap-1">
              <Info :size="14" class="text-brand-500" />
              Cómo funciona
            </RouterLink>
          </div>

          <RouterLink to="/productos"
            class="bg-brand-900 text-white px-5 py-1.5 rounded-full text-sm font-bold hover:bg-brand-800 transition-all shadow-md active:scale-95">
            Comprar ahora
          </RouterLink>
        </div>
      </div>
    </header>

    <!-- Mobile Menu -->
    <div v-if="isMenuOpen" class="md:hidden fixed inset-0 z-[60] bg-white overflow-y-auto pt-20">
      <div class="p-6 space-y-8">
        <div class="relative">
          <input v-model="searchQuery" @keyup.enter="handleSearch" type="text" placeholder="¿Qué buscas hoy?"
            class="w-full pl-12 pr-4 py-4 bg-brand-50 rounded-xl text-base font-medium border-none focus:ring-2 focus:ring-brand-500" />
          <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-400" :size="20" />
        </div>

        <nav class="flex flex-col gap-2">
          <span class="text-xs font-bold text-brand-400 uppercase tracking-widest px-2 mb-2">Menú Principal</span>
          <RouterLink to="/" class="flex items-center gap-3 p-3 rounded-lg hover:bg-brand-50 font-bold text-brand-900"
            @click="isMenuOpen = false">Inicio</RouterLink>
          <RouterLink to="/productos"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-brand-50 font-bold text-brand-900"
            @click="isMenuOpen = false">Todos los Productos</RouterLink>
          <RouterLink to="/#ofertas"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-brand-50 font-bold text-brand-900"
            @click="isMenuOpen = false">Ofertas del día</RouterLink>
          <RouterLink to="/#productores"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-brand-50 font-bold text-brand-900"
            @click="isMenuOpen = false">Nuestros Productores</RouterLink>
          <RouterLink to="/#como-funciona"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-brand-50 font-bold text-brand-900"
            @click="isMenuOpen = false">¿Cómo funciona?</RouterLink>
        </nav>

        <div class="space-y-4">
          <span class="text-xs font-bold text-brand-400 uppercase tracking-widest px-2">Categorías</span>
          <div class="grid grid-cols-2 gap-2">
            <RouterLink v-for="cat in categories" :key="cat.id_categoria"
              :to="{ name: 'category-products', params: { id: cat.id_categoria } }"
              class="p-3 bg-brand-50 rounded-lg text-sm font-semibold text-brand-700" @click="isMenuOpen = false">
              {{ cat.nombre_categoria }}
            </RouterLink>
          </div>
        </div>
      </div>
    </div>

    <CartModal :is-open="isCartOpen" @close="isCartOpen = false" />
  </div>
</template>

<style scoped>
/* No more complex animations */
</style>
