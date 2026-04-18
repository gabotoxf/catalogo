<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Pagination, EffectFade } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/effect-fade'
import { ArrowRight, Play } from 'lucide-vue-next'
import { getAssetUrl } from '../../utils/helpers'
</script>

<template>
  <div class="relative w-full h-[500px] md:h-[600px] lg:h-[700px] overflow-hidden">
    <Swiper
        :modules="[Autoplay, Pagination, EffectFade]"
        :slides-per-view="1"
        :loop="true"
        :effect="'fade'"
        :speed="1000"
        :pagination="{ clickable: true }"
        :autoplay="{
            delay: 5000,
            disableOnInteraction: false
        }"
        class="h-full"
    >
        <!-- SLIDE 1: General Value Proposition -->
        <SwiperSlide>
            <div class="relative w-full h-full">
                <img :src="getAssetUrl('img/Banners/Verduras.jpg')" alt="Campo Real" class="absolute inset-0 w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="relative h-full max-w-[var(--max-width)] mx-auto px-4 md:px-6 flex flex-col justify-center items-start text-white">
                    <span class="inline-block px-4 py-1 bg-brand-500 text-white text-xs font-bold rounded-full mb-4 animate-fade-in uppercase tracking-widest">Directo del campo</span>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.1] max-w-3xl">
                        Del campo a tu mesa en <span class="text-brand-400">menos de 24 horas</span>
                    </h1>
                    <p class="text-lg md:text-xl mb-8 max-w-xl text-brand-50 opacity-90 font-medium">
                        Productos frescos, sin intermediarios, directo de productores locales que cuidan la tierra.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <RouterLink to="/productos" class="px-8 py-4 bg-brand-600 hover:bg-brand-700 text-white rounded-full font-bold transition-all shadow-lg hover:shadow-brand-500/20 flex items-center gap-2 group">
                            Comprar ahora
                            <ArrowRight class="group-hover:translate-x-1 transition-transform" :size="20" />
                        </RouterLink>
                        <RouterLink to="#categorias" class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/30 rounded-full font-bold transition-all">
                            Ver categorías
                        </RouterLink>
                    </div>
                </div>
            </div>
        </SwiperSlide>

        <!-- SLIDE 2: Authenticity / Farmers -->
        <SwiperSlide>
            <div class="relative w-full h-full">
                <img :src="getAssetUrl('img/Banners/Papas2.jpg')" alt="Productores Locales" class="absolute inset-0 w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="relative h-full max-w-[var(--max-width)] mx-auto px-4 md:px-6 flex flex-col justify-center items-center text-center text-white">
                    <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight max-w-4xl">
                        Apoya al comercio local y recibe <span class="text-brand-400">lo mejor de la cosecha</span>
                    </h1>
                    <p class="text-lg md:text-xl mb-10 max-w-2xl text-brand-50 opacity-90">
                        Cada compra ayuda a familias campesinas y garantiza que consumas alimentos 100% naturales.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <RouterLink to="/productores" class="px-10 py-4 bg-white text-brand-900 rounded-full font-bold transition-all shadow-xl hover:scale-105">
                            Con conocer productores
                        </RouterLink>
                        <button class="px-8 py-4 bg-brand-500/20 hover:bg-brand-500/40 border border-brand-500/50 rounded-full font-bold transition-all flex items-center gap-2">
                            <Play :size="18" fill="currentColor" />
                            Ver nuestra historia
                        </button>
                    </div>
                </div>
            </div>
        </SwiperSlide>
    </Swiper>
  </div>
</template>

<style scoped>
:deep(.swiper-pagination-bullet) {
    background: white;
    opacity: 0.5;
    width: 12px;
    height: 12px;
    transition: all 0.3s ease;
}

:deep(.swiper-pagination-bullet-active) {
    opacity: 1;
    background: #628f2c; /* brand-500 equivalent */
    width: 30px;
    border-radius: 6px;
}

.animate-fade-in {
    animation: fadeIn 0.8s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>