<script setup>
import { ref, onMounted } from 'vue'
import api from '../api/axios'
import Skeleton from '../components/layout/Skeleton.vue'
import { getProductImageUrl } from '../utils/helpers'
import { 
  Package, 
  Layers, 
  AlertTriangle, 
  TrendingUp,
  Clock,
  ArrowRight
} from 'lucide-vue-next'

const stats = ref({
  totalProductos: 0,
  totalCategorias: 0,
  stockBajo: 0,
  valorInventario: 0
})
const ultimosProductos = ref([])
const categoriasPopulares = ref([])
const loading = ref(true)

const fetchDashboardData = async () => {
  try {
    const response = await api.get('/dashboard')
    stats.value = response.data.stats
    ultimosProductos.value = response.data.ultimosProductos
    categoriasPopulares.value = response.data.categoriasPopulares
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const getImageUrl = getProductImageUrl

onMounted(fetchDashboardData)
</script>

<template>
  <div class="space-y-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <h1 class="text-xl font-bold text-neutral-900 tracking-tight">Panel de Control</h1>
      <p class="text-sm text-neutral-500 font-medium">Resumen general de tu tienda y catálogo.</p>
    </div>

    <!-- Stats Grid -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="h-28 bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm space-y-4">
        <div class="flex justify-between items-center">
          <Skeleton width="60px" height="12px" />
          <Skeleton width="32px" height="32px" borderRadius="8px" />
        </div>
        <Skeleton width="100px" height="32px" />
      </div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Total Products -->
      <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm flex flex-col justify-between hover:border-brand-500 transition-colors">
        <div class="flex justify-between items-center">
          <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Productos</p>
          <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
            <Package :size="18" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <h3 class="text-2xl font-bold text-neutral-900">{{ stats.totalProductos }}</h3>
          <span class="text-[10px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded">+12%</span>
        </div>
      </div>

      <!-- Total Categories -->
      <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm flex flex-col justify-between hover:border-brand-500 transition-colors">
        <div class="flex justify-between items-center">
          <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Categorías</p>
          <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
            <Layers :size="18" />
          </div>
        </div>
        <div class="mt-2">
          <h3 class="text-2xl font-bold text-neutral-900">{{ stats.totalCategorias }}</h3>
        </div>
      </div>

      <!-- Low Stock -->
      <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm flex flex-col justify-between hover:border-brand-500 transition-colors">
        <div class="flex justify-between items-center">
          <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Stock Bajo</p>
          <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
            <AlertTriangle :size="18" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <h3 class="text-2xl font-bold text-neutral-900">{{ stats.stockBajo }}</h3>
          <span v-if="stats.stockBajo > 0" class="text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">Revisar</span>
        </div>
      </div>

      <!-- Inventory Value -->
      <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm flex flex-col justify-between hover:border-brand-500 transition-colors">
        <div class="flex justify-between items-center">
          <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Valor Total</p>
          <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
            <TrendingUp :size="18" />
          </div>
        </div>
        <div class="mt-2">
          <h3 class="text-2xl font-bold text-neutral-900">${{ Math.floor(stats.valorInventario).toLocaleString('es-CO') }}</h3>
        </div>
      </div>
    </div>

    <!-- Main Section: Recent Items & Categories -->
    <div class="grid lg:grid-cols-12 gap-6">
      <!-- Recent Products -->
      <div class="lg:col-span-8 space-y-4">
        <div class="flex justify-between items-center">
          <h2 class="text-sm font-bold text-neutral-900 uppercase tracking-wider flex items-center gap-2">
            <Clock :size="16" class="text-neutral-400" /> Últimos Productos
          </h2>
          <RouterLink to="/dashboard/productos" class="text-[11px] font-bold text-brand-600 hover:underline uppercase tracking-wider flex items-center gap-1">
            Ver catálogo <ArrowRight :size="12" />
          </RouterLink>
        </div>

        <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm overflow-hidden">
          <table class="w-full text-left text-xs">
            <thead class="bg-neutral-50 border-b border-neutral-100 text-neutral-400 uppercase text-[10px] font-bold tracking-widest">
              <tr>
                <th class="px-6 py-4">Producto</th>
                <th class="px-6 py-4">Categoría</th>
                <th class="px-6 py-4 text-right">Precio</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-50">
              <tr v-for="prod in ultimosProductos" :key="prod.id_producto" class="hover:bg-neutral-50/50 transition-colors">
                <td class="px-6 py-3">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg overflow-hidden bg-neutral-100 border border-neutral-200 shrink-0">
                      <img :src="getImageUrl(prod.imagen_producto)" class="h-full w-full object-cover" />
                    </div>
                    <span class="font-semibold text-neutral-900">{{ prod.nombre_producto }}</span>
                  </div>
                </td>
                <td class="px-6 py-3">
                  <span class="px-2 py-0.5 bg-neutral-100 text-neutral-500 rounded text-[10px] font-bold uppercase tracking-wider">
                    {{ prod.categoria?.nombre_categoria || 'S/C' }}
                  </span>
                </td>
                <td class="px-6 py-3 font-bold text-neutral-900 text-right">${{ Math.floor(prod.precio_producto).toLocaleString('es-CO') }}</td>
              </tr>
              <tr v-if="ultimosProductos.length === 0" class="text-center">
                <td colspan="3" class="py-12 text-neutral-400 font-medium">No hay registros recientes</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Popular Categories -->
      <div class="lg:col-span-4 space-y-4">
        <h2 class="text-sm font-bold text-neutral-900 uppercase tracking-wider">Categorías Populares</h2>
        <div class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm space-y-5">
          <div v-for="cat in categoriasPopulares" :key="cat.id_categoria" class="space-y-1.5">
            <div class="flex justify-between items-center text-xs">
              <span class="font-semibold text-neutral-900">{{ cat.nombre_categoria }}</span>
              <span class="text-[10px] font-bold text-neutral-400">{{ cat.productos_count }} items</span>
            </div>
            <div class="h-1.5 bg-neutral-100 rounded-full overflow-hidden">
              <div 
                class="h-full bg-brand-600 rounded-full" 
                :style="{ width: `${(cat.productos_count / stats.totalProductos) * 100}%` }"
              ></div>
            </div>
          </div>
          <div v-if="categoriasPopulares.length === 0" class="py-8 text-center text-neutral-400 text-xs font-medium">
            Sin datos suficientes
          </div>
        </div>

        <!-- Help Card -->
        <div class="bg-brand-900 rounded-2xl p-6 text-white relative overflow-hidden">
          <div class="relative z-10">
            <h3 class="text-sm font-bold uppercase tracking-tight mb-1">Soporte Técnico</h3>
            <p class="text-xs text-brand-400 font-medium mb-4">¿Tienes dudas con el sistema?</p>
            <button class="w-full py-2 bg-white text-brand-900 rounded-lg font-bold text-xs hover:bg-neutral-100 transition-colors">Contactar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
