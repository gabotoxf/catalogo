<script setup>
import { ShoppingBasket, ArrowRight, Tag } from 'lucide-vue-next'
import { getAssetUrl } from '../../utils/helpers'

const promos = [
  {
    title: 'Canasta Campesina',
    subtitle: 'Todo lo básico para tu semana',
    price: '45.000',
    oldPrice: '55.000',
    items: ['3kg Papas', '1kg Cebolla', '2kg Tomate', '1 Cubeta Huevos'],
    color: 'bg-brand-50',
    textColor: 'text-brand-900',
    image: 'img/Banners/Verduras.jpg'
  },
  {
    title: 'Combo Frutero',
    subtitle: 'Vitaminas directas a tu casa',
    price: '32.000',
    oldPrice: '40.000',
    items: ['1kg Banano', '1kg Manzana', '1kg Papaya', '500g Fresas'],
    color: 'bg-accent-50',
    textColor: 'text-accent-900',
    image: 'img/Banners/Papas2.jpg'
  }
]
</script>

<template>
  <section class="py-20">
    <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6">
      <div class="flex items-center gap-3 mb-10">
        <Tag class="text-brand-500" :size="32" />
        <h2 class="text-3xl md:text-4xl font-black text-brand-900">Ofertas Imperdibles</h2>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <div 
          v-for="promo in promos" 
          :key="promo.title"
          :class="[promo.color, promo.textColor]"
          class="relative overflow-hidden rounded-[3rem] p-8 md:p-12 group"
        >
          <img 
            :src="getAssetUrl(promo.image)" 
            class="absolute top-0 right-0 w-1/2 h-full object-cover opacity-10 group-hover:opacity-20 transition-opacity duration-700 pointer-events-none" 
            alt="Promo Decor"
          />
          
          <div class="relative z-10 flex flex-col h-full">
            <span class="inline-block px-4 py-1 bg-white/50 backdrop-blur-sm rounded-full text-xs font-black uppercase tracking-widest mb-6 border border-white/50 w-fit">
              Ahorra hoy
            </span>
            <h3 class="text-3xl md:text-4xl font-black mb-2">{{ promo.title }}</h3>
            <p class="text-lg opacity-80 mb-8 font-medium">{{ promo.subtitle }}</p>
            
            <ul class="space-y-3 mb-10">
              <li v-for="item in promo.items" :key="item" class="flex items-center gap-3 font-bold">
                <div class="w-2 h-2 rounded-full bg-current opacity-30"></div>
                {{ item }}
              </li>
            </ul>
            
            <div class="mt-auto flex items-end justify-between">
              <div>
                <span class="text-sm opacity-60 line-through block mb-1">${{ promo.oldPrice }}</span>
                <span class="text-4xl font-black">${{ promo.price }}</span>
              </div>
              <button class="bg-white text-brand-900 px-8 py-4 rounded-2xl font-black shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                Lo quiero
                <ArrowRight :size="18" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
