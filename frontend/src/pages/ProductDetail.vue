<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { ShoppingCart, Check, ShieldCheck, Truck, RotateCcw } from 'lucide-vue-next'
import api from '../api/axios'
import { useCartStore } from '../stores/cart'
import ProductImage from '../components/products/ProductImage.vue'
import ProductCard from '../components/products/ProductCard.vue'

const route = useRoute()
const cartStore = useCartStore()
const product = ref(null)
const relacionados = ref([])
const loading = ref(true)
const quantity = ref(1)

const fetchProduct = async () => {
  try {
    const response = await api.get(`/productos/${route.params.slug}`)
    product.value = response.data
    relacionados.value = response.data.relacionados || []
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

onMounted(fetchProduct)

// Al navegar entre relacionados (misma vista, distinto slug) recarga
watch(() => route.params.slug, () => {
  loading.value = true
  quantity.value = 1
  relacionados.value = []
  fetchProduct()
})
</script>

<template>
  <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 py-8 md:py-12">

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-pulse flex flex-col md:flex-row gap-10 w-full">
        <div class="bg-brand-50 aspect-square rounded-[2rem] flex-1"></div>
        <div class="flex-1 space-y-6">
          <div class="h-3 bg-brand-50 rounded w-1/4"></div>
          <div class="h-9 bg-brand-50 rounded w-3/4"></div>
          <div class="h-5 bg-brand-50 rounded w-1/3"></div>
          <div class="h-24 bg-brand-50 rounded w-full"></div>
        </div>
      </div>
    </div>

    <div v-else-if="product" class="flex flex-col md:flex-row gap-10 items-start w-full md:max-w-5xl lg:max-w-6xl md:mx-auto">
      <!-- Image -->
      <div class="flex-1 w-full">
        <div class="aspect-square bg-white rounded-[2rem] overflow-hidden border border-brand-100 shadow-lg shadow-brand-900/5 group">
          <ProductImage
            :src="product.imagen_producto"
            :alt="product.nombre_producto"
            img-class="transition-transform duration-700 group-hover:scale-105"
          />
        </div>
      </div>

      <!-- Info -->
      <div class="flex-1 space-y-6 w-full self-center">
        <div class="space-y-3">
          <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-brand-50 text-brand-600 font-black uppercase text-[10px] tracking-[0.2em] rounded-full border border-brand-100">
              {{ product.categoria?.nombre_categoria || 'Orgánico' }}
            </span>
            <span v-if="product.cantidad_producto > 0" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-600 text-white text-[11px] font-black uppercase tracking-wider shadow-sm">
              <Check :size="14" stroke-width="3" /> En Stock
            </span>
          </div>
          
          <h1 class="text-3xl md:text-4xl font-black text-brand-900 uppercase tracking-tight leading-tight">
            {{ product.nombre_producto }}
          </h1>
          
          <div class="flex items-baseline gap-3">
            <p class="text-3xl font-black text-brand-900">${{ Math.floor(product.precio_producto).toLocaleString('es-CO') }}</p>
            <span class="text-brand-400 font-bold text-xs">Precio por unidad</span>
          </div>
        </div>

        <div class="bg-brand-50/50 p-6 rounded-3xl border border-brand-100/50">
          <p class="text-brand-600 font-medium leading-relaxed italic text-sm">
            "{{ product.descripcion_producto || 'Nuestros productos son cultivados bajo los más altos estándares de calidad orgánica. Garantizamos frescura máxima y un sabor auténtico directamente desde el campo colombiano.' }}"
          </p>
        </div>

        <div class="space-y-4">
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex items-center bg-brand-50 border-2 border-brand-200 rounded-2xl overflow-hidden shadow-sm">
              <button @click="quantity = Math.max(1, quantity - 1)" class="px-5 py-3 hover:bg-brand-100 text-brand-900 font-black transition-colors">-</button>
              <span class="px-5 font-black text-base text-brand-900 w-14 text-center">{{ quantity }}</span>
              <button @click="quantity++" class="px-5 py-3 hover:bg-brand-100 text-brand-900 font-black transition-colors">+</button>
            </div>
            <button 
              @click="addToCart" 
              class="flex-1 py-3 bg-brand-900 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-md shadow-brand-900/20 flex items-center justify-center gap-2 hover:bg-brand-800 transition-all active:scale-95"
            >
              <ShoppingCart :size="18" /> Agregar al Carrito
            </button>
          </div>
        </div>

        <!-- Trust Badges -->
        <div class="pt-6 border-t border-brand-100 grid grid-cols-3 gap-4">
          <div class="text-center space-y-2 group">
            <div class="w-10 h-10 bg-brand-50 rounded-xl flex items-center justify-center mx-auto text-brand-500 group-hover:bg-brand-900 group-hover:text-white transition-all duration-500">
              <Truck :size="20" />
            </div>
            <p class="text-[9px] font-black uppercase tracking-widest text-brand-900">Envío Hoy</p>
          </div>
          <div class="text-center space-y-2 group">
            <div class="w-10 h-10 bg-brand-50 rounded-xl flex items-center justify-center mx-auto text-brand-500 group-hover:bg-brand-900 group-hover:text-white transition-all duration-500">
              <ShieldCheck :size="20" />
            </div>
            <p class="text-[9px] font-black uppercase tracking-widest text-brand-900">100% Fresco</p>
          </div>
          <div class="text-center space-y-2 group">
            <div class="w-10 h-10 bg-brand-50 rounded-xl flex items-center justify-center mx-auto text-brand-500 group-hover:bg-brand-900 group-hover:text-white transition-all duration-500">
              <RotateCcw :size="20" />
            </div>
            <p class="text-[9px] font-black uppercase tracking-widest text-brand-900">Pago Seguro</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Relacionados -->
    <div v-if="!loading && relacionados.length" class="mt-16">
      <h2 class="text-2xl md:text-3xl font-black text-brand-900 mb-2">Productos relacionados</h2>
      <p class="text-brand-500 font-medium mb-8">Más de {{ product?.categoria?.nombre_categoria || 'la cosecha' }} para tu mercado</p>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
        <ProductCard v-for="rel in relacionados" :key="rel.id_producto" :product="{
          id: rel.id_producto,
          slug: rel.slug_producto,
          name: rel.nombre_producto,
          price: rel.precio_producto,
          image: rel.imagen_producto,
          category_name: rel.categoria?.nombre_categoria || 'Orgánico'
        }" />
      </div>
    </div>
  </div>
</template>

<style scoped>
@reference "../style.css";
</style>
