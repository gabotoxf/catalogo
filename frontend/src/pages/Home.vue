<script setup>
import { ref, onMounted } from 'vue'
import api from '../api/axios'
import ProductCard from '../components/products/ProductCard.vue'
import Skeleton from '../components/layout/Skeleton.vue'
import { ArrowRight, ShoppingBag } from 'lucide-vue-next'
import Hero from '../components/Home/Hero.vue'
import Features from '../components/Home/Features.vue'
import Categories from '../components/Home/Categories.vue'
import Producers from '../components/Home/Producers.vue'
import HowItWorks from '../components/Home/HowItWorks.vue'
import Promotions from '../components/Home/Promotions.vue'
import Testimonials from '../components/Home/Testimonials.vue'

const categories = ref([])
const featuredProducts = ref([])
const loading = ref(true)

const fetchHomeData = async () => {
  try {
    const response = await api.get('/home')
    categories.value = response.data.categorias || []
    featuredProducts.value = response.data.productosConCategoria || []
  } catch (err) {
    console.error('Error fetching home data', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchHomeData()
})
</script>

<template>
  <div class="space-y-0 pb-0 overflow-x-hidden">
    <!-- Hero Section (Above the fold) -->
    <Hero />

    <!-- Main Categories (Quick Navigation) -->
    <section class="max-w-[var(--max-width)] mx-auto px-4 md:px-6">
      <Categories />
    </section>

    <!-- Featured Products (High-conversion block) -->
    <section class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 py-20">
      <div class="flex items-end justify-between mb-12">
        <div class="max-w-xl">
          <h2 class="text-3xl md:text-4xl font-black text-brand-900 leading-tight">Más Vendidos de la Semana</h2>
          <p class="text-brand-500 mt-2 font-medium">Los favoritos de nuestra comunidad directos a tu mesa</p>
        </div>
        <RouterLink to="/productos" class="hidden sm:flex items-center gap-2 text-brand-600 font-bold hover:text-brand-900 transition-all group">
          Ver catálogo completo
          <ArrowRight :size="18" class="group-hover:translate-x-1 transition-transform" />
        </RouterLink>
      </div>

      <div v-if="loading" class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
        <div v-for="i in 4" :key="i" class="space-y-4 bg-white p-4 rounded-[2rem] border border-brand-50 shadow-sm">
          <Skeleton height="240px" borderRadius="1.5rem" />
          <Skeleton width="40%" height="12px" />
          <Skeleton width="80%" height="20px" />
          <div class="flex justify-between items-center pt-2">
            <Skeleton width="30%" height="24px" />
            <Skeleton width="32px" height="32px" borderRadius="50%" />
          </div>
        </div>
      </div>

      <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
        <ProductCard v-for="product in featuredProducts" :key="product.id_producto" :product="{
          id: product.id_producto,
          name: product.nombre_producto,
          price: product.precio_producto,
          image: product.imagen_producto,
          category_name: product.categoria?.nombre_categoria || 'Orgánico',
          is_organic: product.id_categoria == 1 // Just for visual example
        }" />
      </div>

      <RouterLink to="/productos" class="sm:hidden flex items-center justify-center gap-2 text-brand-600 font-bold hover:text-brand-900 transition-all mt-10">
        Ver todos los productos
        <ArrowRight :size="18" />
      </RouterLink>
    </section>

    <!-- Trust Section (Differentiator) -->
    <Producers id="productores" />

    <!-- Benefits (Proof + Confidence) -->
    <section class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 py-12" id="beneficios">
      <Features />
    </section>

    <!-- How it Works (WhatsApp Flow) -->
    <HowItWorks id="como-funciona" />

    <!-- Promotions / Offers -->
    <Promotions id="ofertas" />

    <!-- Testimonials -->
    <Testimonials id="testimonios" />

    <!-- Final CTA -->
    <section class="relative py-24 overflow-hidden bg-brand-50">
      <!-- Background Decor -->
      <div class="absolute top-0 right-0 w-1/3 h-full bg-brand-900 skew-x-12 translate-x-1/2 pointer-events-none opacity-5"></div>
      
      <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 relative z-10 text-center">
        <div class="max-w-3xl mx-auto">
          <h2 class="text-4xl md:text-6xl font-black text-brand-900 mb-8 leading-tight">
            Empieza a comprar productos <span class="text-brand-500">frescos hoy mismo</span>
          </h2>
          <p class="text-xl text-brand-600 mb-12 font-medium opacity-80 leading-relaxed">
            Apoya a los campesinos locales y recibe la mejor calidad en la puerta de tu casa. Sin intermediarios, sin demoras.
          </p>
          <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <RouterLink 
              to="/productos" 
              class="w-full sm:w-auto px-12 py-5 bg-brand-900 text-white rounded-full text-xl font-black hover:bg-brand-800 transition-all shadow-2xl hover:scale-105 active:scale-95 flex items-center justify-center gap-3"
            >
              <ShoppingBag :size="24" />
              Ver productos
            </RouterLink>
            <a 
              href="https://wa.me/tu-numero" 
              target="_blank"
              class="w-full sm:w-auto px-12 py-5 bg-white border-2 border-brand-100 text-brand-900 rounded-full text-xl font-black hover:border-brand-500 transition-all flex items-center justify-center gap-3"
            >
              Chat de WhatsApp
            </a>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
@reference "../style.css";
</style>
