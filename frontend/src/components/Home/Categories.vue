<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/axios'
import { RouterLink } from 'vue-router'
import { ArrowRight, Leaf, Apple, Milk, Beef, Wheat, Egg, Package } from 'lucide-vue-next'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, FreeMode } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/free-mode'

const categories = ref([])
const loading = ref(true)

const fetchCategories = async () => {
  try {
    const response = await api.get('/home')
    categories.value = response.data.categorias || []
  } catch (err) {
    console.error('Error fetching categories', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCategories()
})

const getCategoryIcon = (name) => {
  const icons = {
    'Verduras': Leaf,
    'Frutas': Apple,
    'Lácteos': Milk,
    'Carnes': Beef,
    'Granos': Wheat,
    'Huevos': Egg
  }
  return icons[name] || Package
}

const getImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  return `http://localhost:8000/assets/img/Categorias/${img}`
}
</script>

<template>
  <div id="categorias" class="py-12">

    <!-- Header -->
    <div class="flex items-end justify-between mb-10">
      <div>
        <h2 class="text-3xl md:text-4xl font-black text-brand-900 leading-tight">
          Nuestras Categorías
        </h2>
        <p class="text-brand-500 mt-2 font-medium">
          Encuentra lo mejor del campo organizado para ti
        </p>
      </div>
      <RouterLink
        to="/productos"
        class="hidden sm:flex items-center gap-2 text-brand-600 font-bold hover:text-brand-900 transition-all group"
      >
        Ver todas
        <ArrowRight :size="18" class="group-hover:translate-x-1 transition-transform" />
      </RouterLink>
    </div>

    <!-- Skeleton loader -->
    <div v-if="loading" class="grid grid-cols-3 md:grid-cols-6 gap-4">
      <div v-for="i in 6" :key="i" class="flex flex-col items-center gap-2">
        <div class="w-20 h-20 rounded-full bg-brand-100 animate-pulse"></div>
        <div class="w-16 h-3 rounded-full bg-brand-100 animate-pulse"></div>
      </div>
    </div>

    <!-- Swiper -->
    <Swiper
      v-else-if="categories.length > 0"
      :key="categories.length"
      :modules="[Autoplay, FreeMode]"
      :slides-per-view="2.5"
      :space-between="16"
      :loop="true"
      :free-mode="true"
      :autoplay="{ delay: 4000, disableOnInteraction: false }"
      :breakpoints="{
        640:  { slidesPerView: 3.5, spaceBetween: 20 },
        768:  { slidesPerView: 4.5, spaceBetween: 24 },
        1024: { slidesPerView: 6,   spaceBetween: 30 }
      }"
      class="pb-8"
    >
      <SwiperSlide
        v-for="category in categories"
        :key="category.id_categoria"
      >
        <RouterLink
          :to="{ name: 'category-products', params: { id: category.id_categoria } }"
          class="category-card block group"
        >
          <!-- Contenedor de Imagen -->
          <div class="relative aspect-square rounded-2xl overflow-hidden bg-brand-50 border border-brand-100/50 transition-all duration-300 group-hover:shadow-lg group-hover:border-brand-200">
            <!-- Imagen de BD -->
            <img
              v-if="category.imagen_categoria"
              :src="getImageUrl(category.imagen_categoria)"
              :alt="category.nombre_categoria"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
            <!-- Fallback Professional Icon (Only if no image) -->
            <div v-else class="absolute inset-0 flex items-center justify-center bg-brand-50">
              <component 
                :is="getCategoryIcon(category.nombre_categoria)" 
                :size="40" 
                class="text-brand-200"
              />
            </div>
          </div>

          <!-- Nombre -->
          <div class="mt-4 text-center">
            <h3 class="text-sm md:text-base font-bold text-brand-900 group-hover:text-brand-600 transition-colors duration-200 uppercase tracking-tight">
              {{ category.nombre_categoria }}
            </h3>
          </div>
        </RouterLink>
      </SwiperSlide>
    </Swiper>

    <div v-else class="text-center py-12 text-brand-400 font-medium">
      No hay categorías disponibles por el momento.
    </div>

    <!-- Ver todas en móvil -->
    <RouterLink
      to="/productos"
      class="sm:hidden flex items-center justify-center gap-2 text-brand-600 font-bold hover:text-brand-900 transition-all mt-6"
    >
      Ver todas las categorías
      <ArrowRight :size="18" />
    </RouterLink>
  </div>
</template>

<style scoped>
.category-card {
  text-decoration: none;
}

/* Subtle image brightness change on hover for professional feel */
.category-card:hover img {
  filter: brightness(1.02);
}
</style>