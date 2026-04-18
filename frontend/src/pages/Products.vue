<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api/axios'
import ProductCard from '../components/products/ProductCard.vue'
import Skeleton from '../components/layout/Skeleton.vue'
import { Filter, Search, Loader2, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const route = useRoute()
const products = ref([])
const categories = ref([])
const loading = ref(true)
const selectedCategory = ref(route.params.id || '')
const searchQuery = ref(route.query.search || '')
const priceMin = ref('')
const priceMax = ref('')
const sortOption = ref('')
const currentPage = ref(1)
const totalPages = ref(1)

const fetchCategories = async () => {
  try {
    const res = await api.get('/categorias')
    categories.value = res.data || []
  } catch (err) {
    console.error('Error fetching categories', err)
  }
}

const fetchData = async (page = 1) => {
  loading.value = true
  currentPage.value = page
  try {
    const params = {
      categoria: selectedCategory.value,
      query: searchQuery.value,
      minPrecio: priceMin.value || 0,
      maxPrecio: priceMax.value || 99999999,
      sort: sortOption.value,
      page: page
    }
    
    const prodRes = await api.post('/productos/filtrar', params)
    
    products.value = prodRes.data.productos || []
    totalPages.value = prodRes.data.totalPaginas || 1
  } catch (err) {
    console.error('Error fetching data', err)
  } finally {
    loading.value = false
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const filterByCategory = (id) => {
  selectedCategory.value = id
  fetchData()
}

const applyPriceFilter = () => {
  fetchData()
}

const applySort = () => {
  fetchData()
}

const resetFilters = () => {
  selectedCategory.value = ''
  searchQuery.value = ''
  priceMin.value = ''
  priceMax.value = ''
  sortOption.value = ''
  fetchData()
}

onMounted(() => {
  fetchCategories()
  fetchData()
})

watch(() => route.params.id, (newId) => {
  selectedCategory.value = newId || ''
  fetchData()
})

watch(() => route.query.search, (newQuery) => {
  searchQuery.value = newQuery || ''
  fetchData()
})
</script>

<template>
  <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 py-12">
    <div class="flex flex-col md:flex-row gap-8">
      <!-- Sidebar Filters -->
      <aside class="w-full md:w-72 space-y-6">

        <!-- CONTENEDOR -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-5 space-y-6">

          <!-- HEADER -->
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-neutral-900 uppercase tracking-wider flex items-center gap-2">
              <Filter :size="16" /> Filtros
            </h3>
            <button @click="resetFilters" class="text-xs text-neutral-400 hover:text-neutral-700 transition">
              Limpiar
            </button>
          </div>

          <!-- CATEGORÍAS -->
          <div>
            <p class="text-xs font-semibold text-neutral-500 mb-3 uppercase tracking-wider">
              Categorías
            </p>

            <!-- CONTENEDOR CON SCROLL -->
            <div class="max-h-44 overflow-y-auto pr-1 space-y-1 custom-scroll">

              <button @click="resetFilters" :class="[
                'w-full text-left px-3 py-2 rounded-lg text-sm transition-all',
                !selectedCategory
                  ? 'bg-brand-900 text-white'
                  : 'text-neutral-600 hover:bg-neutral-100'
              ]">
                Todas
              </button>

              <button v-for="cat in categories" :key="cat.id_categoria" @click="filterByCategory(cat.id_categoria)"
                :class="[
                  'w-full text-left px-3 py-2 rounded-lg text-sm transition-all',
                  selectedCategory == cat.id_categoria
                    ? 'bg-brand-900 text-white'
                    : 'text-neutral-600 hover:bg-neutral-100'
                ]">
                {{ cat.nombre_categoria }}
              </button>

            </div>
          </div>

          <!-- PRECIO -->
          <div>
            <p class="text-xs font-semibold text-neutral-500 mb-3 uppercase tracking-wider">
              Precio
            </p>

            <div class="flex gap-2">
              <input type="number" placeholder="Min" v-model="priceMin"
                class="w-full border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-900 outline-none" />
              <input type="number" placeholder="Max" v-model="priceMax"
                class="w-full border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-900 outline-none" />
            </div>

            <button @click="applyPriceFilter"
              class="mt-2 w-full bg-brand-900 text-white text-sm py-2 rounded-lg hover:bg-brand-800 transition shadow-sm font-bold uppercase tracking-wider">
              Aplicar
            </button>
          </div>

          <!-- ORDEN -->
          <div>
            <p class="text-xs font-semibold text-neutral-500 mb-3 uppercase tracking-wider">
              Ordenar por
            </p>

            <select v-model="sortOption" @change="applySort"
              class="w-full border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-900 outline-none">
              <option value="">Relevancia</option>
              <option value="price_asc">Precio: menor a mayor</option>
              <option value="price_desc">Precio: mayor a menor</option>
              <option value="name">Nombre</option>
            </select>
          </div>

          <!-- STOCK / DISPONIBILIDAD -->
          <div>
            <label class="flex items-center gap-2 text-sm text-neutral-600 cursor-pointer">
              <input type="checkbox" v-model="inStockOnly" @change="applyStockFilter" class="accent-brand-900" />
              Solo disponibles
            </label>
          </div>

        </div>

      </aside>

      <!-- Main Content -->
      <main class="flex-1 space-y-8">
        <div
          class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-brand-100 shadow-sm">
          <div>
            <h1 class="text-2xl font-black text-brand-900 uppercase tracking-tight">
              {{ searchQuery ? `Resultados para "${searchQuery}"` : 'Nuestros Productos' }}
            </h1>
            <p class="text-brand-500 text-sm font-medium mt-1">{{ products.length }} productos encontrados</p>
          </div>
          <div class="hidden sm:block">
            <span
              class="px-4 py-2 bg-brand-50 text-brand-600 rounded-full text-xs font-black uppercase tracking-widest border border-brand-100">
              Directo del Campo
            </span>
          </div>
        </div>

        <!-- Grid -->
        <div v-if="loading" class="space-y-12">
          <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
            <div v-for="i in 8" :key="i" class="space-y-4 bg-white p-4 rounded-2xl border border-brand-50 shadow-sm">
              <Skeleton height="200px" borderRadius="1.5rem" />
              <Skeleton width="40%" height="12px" />
              <Skeleton width="80%" height="20px" />
              <div class="flex justify-between items-center pt-2">
                <Skeleton width="30%" height="24px" />
                <Skeleton width="32px" height="32px" borderRadius="50%" />
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="products.length > 0" class="space-y-12">
          <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
            <ProductCard v-for="product in products" :key="product.id_producto" :product="{
              id: product.id_producto,
              name: product.nombre_producto,
              price: product.precio_producto,
              image: product.imagen_producto,
              category_name: product.categoria?.nombre_categoria || 'Orgánico'
            }" />
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex justify-end items-center gap-2 pt-8">
            <button 
              @click="fetchData(currentPage - 1)" 
              :disabled="currentPage === 1"
              class="p-2 rounded-xl bg-white border border-neutral-200 text-neutral-500 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-neutral-50 transition-all"
            >
              <ChevronLeft :size="20" />
            </button>
            
            <div class="flex gap-1">
              <button 
                v-for="page in totalPages" 
                :key="page"
                @click="fetchData(page)"
                :class="[
                  'w-10 h-10 rounded-xl font-black text-xs transition-all',
                  currentPage === page 
                    ? 'bg-brand-900 text-white shadow-lg shadow-brand-900/20' 
                    : 'bg-white border border-neutral-200 text-neutral-500 hover:bg-neutral-100'
                ]"
              >
                {{ page }}
              </button>
            </div>

            <button 
              @click="fetchData(currentPage + 1)" 
              :disabled="currentPage === totalPages"
              class="p-2 rounded-xl bg-white border border-neutral-200 text-neutral-500 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-neutral-50 transition-all"
            >
              <ChevronRight :size="20" />
            </button>
          </div>
        </div>

        <div v-else class="text-center py-20 bg-brand-50 rounded-lg">
          <p class="text-brand-500">No se encontraron productos que coincidan con tu búsqueda.</p>
          <button @click="fetchData" class="mt-4 text-brand-900 font-bold hover:underline">Ver todos los
            productos</button>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
@reference "../style.css";
</style>
