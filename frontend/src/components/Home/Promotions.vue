<script setup>
import { ShoppingBasket, ArrowRight, Tag } from 'lucide-vue-next'
import { getAssetUrl } from '../../utils/helpers'
import { waLink } from '../../utils/whatsapp'

const whatsappLink = (promo) =>
  waLink(`Hola Chaparro, quiero la promo *${promo.title}* ($${promo.price}). ¿Sigue disponible?`)

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
            class="w-full h-44 object-cover rounded-2xl mb-6 sm:mb-0 sm:rounded-none sm:absolute sm:top-0 sm:right-0 sm:w-1/2 sm:h-full opacity-80 sm:opacity-60 group-hover:opacity-80 transition-opacity duration-700 pointer-events-none" 
            alt="Promo Decor"
          />
          
          <div class="absolute top-8 left-8 right-8 h-44 rounded-2xl bg-brand-900/40 pointer-events-none sm:top-0 sm:left-auto sm:right-0 sm:w-1/2 sm:h-full sm:rounded-none sm:bg-transparent sm:bg-gradient-to-l sm:from-brand-900/50 sm:via-brand-900/20 sm:to-transparent"></div>
          
          <div class="relative z-10 flex flex-col h-full w-full sm:w-1/2">
            <span class="inline-block px-4 py-1 bg-white/50 backdrop-blur-sm rounded-full text-xs font-black uppercase tracking-widest mb-6 border border-white/50 w-fit">
              Ahorra hoy
            </span>
            <h3 class="text-3xl md:text-4xl font-black mb-2">{{ promo.title }}</h3>
            <p class="text-lg opacity-80 mb-8 font-medium">{{ promo.subtitle }}</p>
            
            <ul class="space-y-3 mb-8">
              <li v-for="item in promo.items" :key="item" class="flex items-center gap-3 font-bold">
                <div class="w-2 h-2 rounded-full bg-current opacity-30"></div>
                {{ item }}
              </li>
            </ul>
            <div>
              <span class="text-sm opacity-60 line-through block mb-1">${{ promo.oldPrice }}</span>
              <span class="text-4xl font-black">${{ promo.price }}</span>
            </div>
          </div>
            
          <a :href="whatsappLink(promo)" target="_blank" rel="noopener" class="z-10 mt-8 w-full justify-center bg-white text-brand-900 px-8 py-4 rounded-2xl font-black shadow-xl hover:scale-105 transition-all flex items-center gap-2 sm:absolute sm:mt-0 sm:w-fit sm:right-8 sm:bottom-8 lg:right-12 lg:bottom-12">
            Lo quiero
            <ArrowRight :size="18" />
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
