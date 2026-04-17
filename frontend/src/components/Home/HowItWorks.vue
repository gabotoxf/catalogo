<script setup>
import { ShoppingBasket, ShoppingCart, MessageCircle, Home } from 'lucide-vue-next'

const steps = [
  {
    icon: ShoppingBasket,
    title: 'Elige tus productos',
    desc: 'Navega por nuestras categorías y selecciona lo más fresco.'
  },
  {
    icon: ShoppingCart,
    title: 'Agrégalos al carrito',
    desc: 'Revisa tu pedido y asegúrate de tener todo lo necesario.'
  },
  {
    icon: MessageCircle,
    title: 'Confirma por WhatsApp',
    desc: 'Envíanos tu pedido. Coordinamos entrega y pago al instante.'
  },
  {
    icon: Home,
    title: 'Recibe en casa',
    desc: '¡Listo! Disfruta de la frescura del campo en tu mesa.'
  }
]
</script>

<template>
  <section class="py-20 bg-white">
    <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <h2 class="text-3xl md:text-4xl font-black text-brand-900 mb-4">¿Cómo funciona?</h2>
        <p class="text-brand-500 font-medium">Comprar productos del campo nunca fue tan fácil y rápido.</p>
      </div>

      <div class="relative">
        <!-- Connecting Line (Desktop) -->
        <div class="hidden lg:block absolute top-1/2 left-0 w-full h-0.5 bg-brand-100 -translate-y-1/2 z-0"></div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 relative z-10">
          <div 
            v-for="(step, index) in steps" 
            :key="step.title"
            class="flex flex-col items-center text-center group"
          >
            <div class="w-20 h-20 bg-white border-4 border-brand-50 text-brand-600 rounded-full flex items-center justify-center mb-6 group-hover:border-brand-500 group-hover:bg-brand-500 group-hover:text-white transition-all duration-500 shadow-xl">
              <component :is="step.icon" :size="32" />
              <span class="absolute -top-2 -right-2 w-8 h-8 bg-brand-900 text-white rounded-full flex items-center justify-center text-sm font-black border-4 border-white">
                {{ index + 1 }}
              </span>
            </div>
            <h3 class="text-xl font-black text-brand-900 mb-3">{{ step.title }}</h3>
            <p class="text-brand-500 text-sm font-medium leading-relaxed max-w-[200px]">
              {{ step.desc }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
