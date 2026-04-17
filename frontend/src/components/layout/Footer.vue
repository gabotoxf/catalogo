<script setup>
import { Mail, Phone, MapPin, Facebook, Instagram, Twitter, MessageCircle, CreditCard, Truck } from 'lucide-vue-next'
</script>

<template>
  <footer class="bg-brand-900 text-white pt-24 pb-12">
    <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 mb-20">
        <!-- Brand & Mission -->
        <div class="space-y-8">
          <RouterLink to="/" class="flex items-center gap-3">
            <img src="/logo.jfif" alt="Logo" class="h-12 w-12 object-cover rounded-xl shadow-lg shadow-black/20" />
            <span class="text-2xl font-black tracking-tighter uppercase">Chaparro</span>
          </RouterLink>
          <p class="text-brand-100/70 text-base leading-relaxed font-medium">
            Conectamos el campo directamente con tu mesa. Productos frescos, precios justos y apoyo real a nuestros campesinos.
          </p>
          <div class="flex gap-4">
            <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-brand-500 hover:text-white transition-all duration-300 border border-white/10"><Facebook :size="20" /></a>
            <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-brand-500 hover:text-white transition-all duration-300 border border-white/10"><Instagram :size="20" /></a>
            <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-brand-500 hover:text-white transition-all duration-300 border border-white/10"><Twitter :size="20" /></a>
          </div>
        </div>

        <!-- Quick Navigation -->
        <div>
          <h4 class="text-lg font-black mb-8 border-l-4 border-brand-500 pl-4">Nuestra Tienda</h4>
          <ul class="space-y-4 text-brand-100/70 font-bold">
            <li><RouterLink to="/productos" class="hover:text-brand-400 transition-colors">Todos los productos</RouterLink></li>
            <li><RouterLink to="/#ofertas" class="hover:text-brand-400 transition-colors">Ofertas del día</RouterLink></li>
            <li><RouterLink to="/#productores" class="hover:text-brand-400 transition-colors">Conoce a los productores</RouterLink></li>
            <li><RouterLink to="/#como-funciona" class="hover:text-brand-400 transition-colors">¿Cómo funciona?</RouterLink></li>
          </ul>
        </div>

        <!-- Support & Policies -->
        <div>
          <h4 class="text-lg font-black mb-8 border-l-4 border-brand-500 pl-4">Soporte</h4>
          <ul class="space-y-4 text-brand-100/70 font-bold">
            <li><a href="#" class="hover:text-brand-400 transition-colors">Preguntas frecuentes</a></li>
            <li><a href="#" class="hover:text-brand-400 transition-colors">Políticas de envío</a></li>
            <li><a href="#" class="hover:text-brand-400 transition-colors">Términos y condiciones</a></li>
            <li><a href="#" class="hover:text-brand-400 transition-colors">Zonas de cobertura</a></li>
          </ul>
        </div>

        <!-- Contact & WhatsApp -->
        <div>
          <h4 class="text-lg font-black mb-8 border-l-4 border-brand-500 pl-4">Contacto Directo</h4>
          <ul class="space-y-6 text-brand-100/70 font-bold mb-8">
            <li class="flex items-start gap-3"><MapPin :size="20" class="text-brand-500 shrink-0" /> Bogotá y Sabana, Colombia</li>
            <li class="flex items-center gap-3"><Mail :size="20" class="text-brand-500 shrink-0" /> contacto@chaparro.com</li>
          </ul>
          <a 
            href="https://wa.me/tu-numero" 
            target="_blank"
            class="flex items-center justify-center gap-3 bg-[#25D366] text-white py-4 px-6 rounded-2xl font-black shadow-xl hover:scale-105 transition-all w-full"
          >
            <MessageCircle :size="24" />
            Pide por WhatsApp
          </a>
        </div>
      </div>

      <!-- Payment & Trust -->
      <div class="border-t border-white/5 pt-12 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="flex flex-wrap justify-center md:justify-start gap-8 opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
          <div class="flex items-center gap-2"><CreditCard :size="24" /> <span class="text-xs font-bold uppercase tracking-widest">Pago Seguro</span></div>
          <div class="flex items-center gap-2"><Truck :size="24" /> <span class="text-xs font-bold uppercase tracking-widest">Entrega Local</span></div>
          <div class="flex items-center gap-2"><MessageCircle :size="24" /> <span class="text-xs font-bold uppercase tracking-widest">Asistencia 24/7</span></div>
        </div>
        
        <div class="text-brand-100/30 text-xs font-bold uppercase tracking-[0.2em] text-center md:text-right">
          &copy; {{ new Date().getFullYear() }} Chaparro Ecommerce. 
          <span class="block mt-1">Directo del campo a tu hogar.</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
/* Professional and clean footer */
</style>
