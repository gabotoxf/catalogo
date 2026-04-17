<script setup>
import { Truck, Leaf, Handshake, Lock } from 'lucide-vue-next'

const features = [
  {
    icon: Truck,
    title: 'Entrega rápida',
    desc: 'En menos de 24h en tu puerta'
  },
  {
    icon: Leaf,
    title: '100% Fresco',
    desc: 'Cosechado hoy mismo'
  },
  {
    icon: Handshake,
    title: 'Comercio Justo',
    desc: 'Apoyo directo al productor'
  },
  {
    icon: Lock,
    title: 'Pago Seguro',
    desc: 'Contra entrega o WhatsApp'
  }
]
</script>

<template>
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8 w-full">
    <div 
      v-for="feature in features" 
      :key="feature.title"
      class="flex flex-col md:flex-row items-center md:items-start gap-4 p-6 bg-white border border-brand-100 rounded-[2rem] hover:shadow-xl hover:shadow-brand-900/5 transition-all duration-300 group"
    >
      <div class="bg-brand-50 text-brand-600 p-4 rounded-2xl group-hover:bg-brand-600 group-hover:text-white transition-colors duration-300">
        <component :is="feature.icon" :size="24" />
      </div>
      <div class="text-center md:text-left">
        <h3 class="font-black text-brand-900 text-base">{{ feature.title }}</h3>
        <p class="text-xs text-brand-400 mt-1 font-bold uppercase tracking-tighter">{{ feature.desc }}</p>
      </div>
    </div>
  </div>
</template>