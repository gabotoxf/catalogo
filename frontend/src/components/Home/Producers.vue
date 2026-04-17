<script setup>
import { MapPin, Quote } from 'lucide-vue-next'

const producers = [
  {
    id: 1,
    name: 'Don Ricardo',
    location: 'Vereda El Hato',
    specialty: 'Papas y Tubérculos',
    story: 'Más de 30 años cultivando la tierra con métodos tradicionales y orgánicos.',
    image: '/src/assets/img/Banners/Papas2.jpg'
  },
  {
    id: 2,
    name: 'Doña Martha',
    location: 'Finca La Esperanza',
    specialty: 'Verduras de Hoja',
    story: 'Líder de la asociación local de mujeres productoras de hortalizas frescas.',
    image: '/src/assets/img/Banners/Verduras.jpg'
  }
]
</script>

<template>
  <section class="py-20 bg-brand-900 text-white rounded-[3rem] overflow-hidden my-16">
    <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6">
      <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
        <div class="max-w-xl">
          <span class="text-brand-400 font-black uppercase tracking-[0.2em] text-sm">Nuestra Gente</span>
          <h2 class="text-4xl md:text-5xl font-black mt-4 leading-tight">Conoce a quienes cultivan <span class="text-brand-400">tus alimentos</span></h2>
        </div>
        <p class="text-brand-100 opacity-80 max-w-sm font-medium">
          Eliminamos intermediarios para que el productor reciba un pago justo y tú recibas frescura real.
        </p>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <div 
          v-for="producer in producers" 
          :key="producer.id"
          class="group relative bg-white/5 border border-white/10 p-8 rounded-[2rem] hover:bg-white/10 transition-all duration-500"
        >
          <Quote class="absolute top-8 right-8 text-brand-500/20" :size="64" />
          
          <div class="flex flex-col sm:flex-row gap-8 items-center sm:items-start relative z-10">
            <div class="w-32 h-32 shrink-0 rounded-2xl overflow-hidden border-4 border-brand-500/20">
              <img :src="producer.image" :alt="producer.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
            </div>
            
            <div class="text-center sm:text-left">
              <div class="flex items-center justify-center sm:justify-start gap-2 text-brand-400 mb-2">
                <MapPin :size="16" />
                <span class="text-xs font-bold uppercase tracking-widest">{{ producer.location }}</span>
              </div>
              <h3 class="text-2xl font-black mb-3">{{ producer.name }}</h3>
              <p class="text-brand-100 opacity-70 text-sm leading-relaxed mb-6 font-medium italic">
                "{{ producer.story }}"
              </p>
              <div class="inline-block px-4 py-2 bg-brand-500/20 rounded-lg text-brand-400 text-xs font-bold border border-brand-500/30">
                Experto en: {{ producer.specialty }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
