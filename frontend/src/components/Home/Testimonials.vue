<script setup>
import { Star, User } from 'lucide-vue-next'

const testimonials = [
  {
    id: 1,
    name: 'Carolina Gómez',
    role: 'Cliente Frecuente',
    text: 'La calidad de las verduras es increíble. Se nota que son recién cosechadas y el servicio por WhatsApp es súper rápido.',
    stars: 5
  },
  {
    id: 2,
    name: 'Andrés Felipe',
    role: 'Restaurante Local',
    text: 'Compro para mi negocio y la frescura es inigualable. Apoyar a los productores locales es lo mejor de esta plataforma.',
    stars: 5
  },
  {
    id: 3,
    name: 'Lucía Méndez',
    role: 'Madre de Familia',
    text: 'Mis hijos aman las frutas de aquí. Todo llega muy bien empacado y a tiempo. ¡Recomendadísimo!',
    stars: 5
  }
]
</script>

<template>
  <section class="py-24 bg-brand-50/50">
    <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-black text-brand-900 mb-16">Lo que dicen <span class="text-brand-500">nuestros clientes</span></h2>
      
      <div class="grid md:grid-cols-3 gap-8">
        <div 
          v-for="testimonial in testimonials" 
          :key="testimonial.id"
          class="bg-white p-8 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-500 text-left border border-brand-100 flex flex-col"
        >
          <div class="flex gap-1 mb-6 text-yellow-400">
            <Star v-for="i in testimonial.stars" :key="i" :size="18" fill="currentColor" />
          </div>
          
          <p class="text-brand-700 font-medium italic mb-8 leading-relaxed flex-grow">
            "{{ testimonial.text }}"
          </p>
          
          <div class="flex items-center gap-4 border-t border-brand-50 pt-6">
            <div class="w-12 h-12 bg-brand-100 rounded-full flex items-center justify-center text-brand-600">
              <User :size="24" />
            </div>
            <div>
              <h4 class="font-black text-brand-900 leading-tight">{{ testimonial.name }}</h4>
              <span class="text-xs font-bold text-brand-400 uppercase tracking-widest">{{ testimonial.role }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
