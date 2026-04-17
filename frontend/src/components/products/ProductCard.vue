<script setup>
import { ShoppingCart, Eye, Plus } from 'lucide-vue-next'
import { useCartStore } from '../../stores/cart'
import { getProductImageUrl } from '../../utils/helpers'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const cartStore = useCartStore()

const addToCart = () => {
  cartStore.addToCart(props.product)
}

const getImageUrl = getProductImageUrl
</script>

<template>
  <div class="group relative bg-white rounded-3xl border border-brand-100 hover:border-brand-300 transition-all duration-300 hover:shadow-xl hover:shadow-brand-900/5 overflow-hidden">
    <!-- Image & Badge Container -->
    <div class="relative aspect-[1/1] overflow-hidden bg-brand-50">
      <img 
        :src="getImageUrl(product.image)" 
        :alt="product.name" 
        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
      />
      
      <!-- Badges -->
      <div class="absolute top-3 left-3 flex flex-col gap-2">
        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-brand-900 text-[10px] font-black uppercase tracking-tighter rounded-full shadow-sm border border-brand-100">
          Fresco
        </span>
        <span v-if="product.is_organic" class="px-3 py-1 bg-brand-500 text-white text-[10px] font-black uppercase tracking-tighter rounded-full shadow-sm">
          Orgánico
        </span>
      </div>

      <!-- Quick Actions (Hover) -->
      <div class="absolute inset-0 bg-brand-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
        <button 
          @click="addToCart"
          class="w-12 h-12 bg-white text-brand-900 rounded-full shadow-xl flex items-center justify-center hover:bg-brand-900 hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-300 delay-75"
          title="Agregar al carrito"
        >
          <Plus :size="24" />
        </button>
        <RouterLink 
          :to="{ name: 'product-detail', params: { id: product.id }}"
          class="w-12 h-12 bg-white text-brand-900 rounded-full shadow-xl flex items-center justify-center hover:bg-brand-900 hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-300"
          title="Ver detalle"
        >
          <Eye :size="22" />
        </RouterLink>
      </div>
    </div>
    
    <!-- Info -->
    <div class="p-5">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] font-black text-brand-400 uppercase tracking-widest">{{ product.category_name }}</span>
        <div class="flex gap-0.5">
          <div v-for="i in 5" :key="i" class="w-1.5 h-1.5 rounded-full" :class="i <= 4 ? 'bg-brand-500' : 'bg-brand-100'"></div>
        </div>
      </div>
      
      <h3 class="font-bold text-brand-900 text-base mb-1 line-clamp-1 group-hover:text-brand-600 transition-colors">
        {{ product.name }}
      </h3>
      
      <div class="flex items-end justify-between mt-4">
        <div>
          <span class="text-[10px] text-brand-400 font-bold block">Precio por unidad</span>
          <p class="text-xl font-black text-brand-900">${{ Math.floor(product.price).toLocaleString('es-CO') }}</p>
        </div>
        
        <button 
          @click="addToCart"
          class="p-2.5 bg-brand-50 text-brand-600 rounded-xl hover:bg-brand-900 hover:text-white transition-all active:scale-90"
        >
          <ShoppingCart :size="18" />
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Clean and modern */
</style>
