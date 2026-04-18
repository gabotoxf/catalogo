<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ShoppingCart, ArrowLeft, ShieldCheck, Truck, RotateCcw } from 'lucide-vue-next'
import api from '../api/axios'
import { useCartStore } from '../stores/cart'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const product = ref(null)
const loading = ref(true)
const quantity = ref(1)

const fetchProduct = async () => {
  try {
    const response = await api.get('/productos')
    const products = response.data.productos.data || response.data.productos || []
    const found = products.find(p => p.id_producto == route.params.id)
    product.value = found
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const addToCart = () => {
  if (product.value) {
    for (let i = 0; i < quantity.value; i++) {
      cartStore.addToCart({
        id: product.value.id_producto,
        name: product.value.nombre_producto,
        price: product.value.precio_producto,
        image: product.value.imagen_producto
      })
    }
  }
}

const getImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  return `http://localhost:8000/assets/img/Productos/${img}`
}

onMounted(fetchProduct)
</script>

<template>
  <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 py-12 md:py-20">
    <button @click="router.back()" class="inline-flex items-center gap-2 text-brand-500 hover:text-brand-900 font-bold mb-10 transition-colors group">
      <ArrowLeft :size="20" class="group-hover:-translate-x-1 transition-transform" /> Volver a productos
    </button>

    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-pulse flex flex-col md:flex-row gap-16 w-full">
        <div class="bg-brand-50 aspect-square rounded-[2.5rem] flex-1"></div>
        <div class="flex-1 space-y-8">
          <div class="h-4 bg-brand-50 rounded w-1/4"></div>
          <div class="h-12 bg-brand-50 rounded w-3/4"></div>
          <div class="h-6 bg-brand-50 rounded w-1/3"></div>
          <div class="h-32 bg-brand-50 rounded w-full"></div>
        </div>
      </div>
    </div>

    <div v-else-if="product" class="flex flex-col md:flex-row gap-16 items-start">
      <!-- Image -->
      <div class="flex-1 w-full">
        <div class="aspect-square bg-white rounded-[3rem] overflow-hidden border border-brand-100 shadow-xl shadow-brand-900/5 group">
          <img 
            :src="getImageUrl(product.imagen_producto)" 
            :alt="product.nombre_producto" 
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
          />
        </div>
      </div>

      <!-- Info -->
      <div class="flex-1 space-y-10 w-full">
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <span class="px-4 py-1.5 bg-brand-50 text-brand-600 font-black uppercase text-[10px] tracking-[0.2em] rounded-full border border-brand-100">
              {{ product.categoria?.nombre_categoria || 'Orgánico' }}
            </span>
            <span v-if="product.cantidad_producto > 0" class="flex items-center gap-1.5 text-green-600 text-xs font-bold">
              <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
              En Stock
            </span>
          </div>
          
          <h1 class="text-4xl md:text-5xl font-black text-brand-900 uppercase tracking-tight leading-tight">
            {{ product.nombre_producto }}
          </h1>
          
          <div class="flex items-baseline gap-4">
            <p class="text-4xl font-black text-brand-900">${{ Math.floor(product.precio_producto).toLocaleString('es-CO') }}</p>
            <span class="text-brand-400 font-bold text-sm">Precio por unidad</span>
          </div>
        </div>

        <div class="bg-brand-50/50 p-8 rounded-[2rem] border border-brand-100/50">
          <p class="text-brand-600 font-medium leading-relaxed italic">
            "{{ product.descripcion_producto || 'Nuestros productos son cultivados bajo los más altos estándares de calidad orgánica. Garantizamos frescura máxima y un sabor auténtico directamente desde el campo colombiano.' }}"
          </p>
        </div>

        <div class="space-y-6">
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
            <div class="flex items-center bg-white border-2 border-brand-100 rounded-2xl overflow-hidden shadow-sm">
              <button @click="quantity = Math.max(1, quantity - 1)" class="px-6 py-4 hover:bg-brand-50 text-brand-900 font-black transition-colors">-</button>
              <span class="px-6 font-black text-lg text-brand-900 w-16 text-center">{{ quantity }}</span>
              <button @click="quantity++" class="px-6 py-4 hover:bg-brand-50 text-brand-900 font-black transition-colors">+</button>
            </div>
            <button 
              @click="addToCart" 
              class="flex-1 py-5 bg-brand-900 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-brand-900/20 flex items-center justify-center gap-3 hover:bg-brand-800 transition-all active:scale-95"
            >
              <ShoppingCart :size="22" /> Agregar al Carrito
            </button>
          </div>
        </div>

        <!-- Trust Badges -->
        <div class="pt-10 border-t border-brand-100 grid grid-cols-3 gap-6">
          <div class="text-center space-y-3 group">
            <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto text-brand-500 group-hover:bg-brand-900 group-hover:text-white transition-all duration-500">
              <Truck :size="24" />
            </div>
            <p class="text-[9px] font-black uppercase tracking-widest text-brand-900">Envío Hoy</p>
          </div>
          <div class="text-center space-y-3 group">
            <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto text-brand-500 group-hover:bg-brand-900 group-hover:text-white transition-all duration-500">
              <ShieldCheck :size="24" />
            </div>
            <p class="text-[9px] font-black uppercase tracking-widest text-brand-900">100% Fresco</p>
          </div>
          <div class="text-center space-y-3 group">
            <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto text-brand-500 group-hover:bg-brand-900 group-hover:text-white transition-all duration-500">
              <RotateCcw :size="24" />
            </div>
            <p class="text-[9px] font-black uppercase tracking-widest text-brand-900">Pago Seguro</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@reference "../style.css";
</style>
