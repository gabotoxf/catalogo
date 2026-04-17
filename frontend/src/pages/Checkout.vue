<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { 
  User, 
  Phone, 
  MapPin, 
  Building, 
  StickyNote, 
  MessageCircle, 
  ArrowLeft, 
  ShoppingBag,
  ShieldCheck,
  Truck,
  CreditCard
} from 'lucide-vue-next'

const cartStore = useCartStore()
const router = useRouter()

const userData = ref({
  name: '',
  phone: '',
  address: '',
  city: '',
  notes: ''
})

const isSubmitting = ref(false)

onMounted(() => {
  if (cartStore.cartItems.length === 0) {
    router.push('/productos')
  }
})

const handleCheckout = () => {
  if (!userData.value.name || !userData.value.phone || !userData.value.address) {
    alert('Por favor, completa los campos obligatorios para procesar tu pedido.')
    return
  }
  
  isSubmitting.value = true
  cartStore.checkoutWhatsApp(userData.value)
  // Optionally clear cart or wait for user to return
}

const getImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  return `http://localhost:8000/assets/img/Productos/${img}`
}
</script>

<template>
  <div class="min-h-screen bg-brand-50/30 py-12 md:py-20">
    <div class="max-w-[var(--max-width)] mx-auto px-4 md:px-6">
      
      <!-- Back Link -->
      <button 
        @click="router.back()" 
        class="flex items-center gap-2 text-brand-500 hover:text-brand-900 font-bold mb-8 transition-colors group"
      >
        <ArrowLeft :size="20" class="group-hover:-translate-x-1 transition-transform" />
        Volver a la tienda
      </button>

      <div class="grid lg:grid-cols-12 gap-12 items-start">
        
        <!-- Left Column: Form -->
        <div class="lg:col-span-7 space-y-8">
          <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-sm border border-brand-100">
            <h1 class="text-3xl font-black text-brand-900 mb-2 uppercase tracking-tight">Finalizar Pedido</h1>
            <p class="text-brand-500 font-medium mb-10">Completa tus datos para coordinar la entrega por WhatsApp.</p>

            <form @submit.prevent="handleCheckout" class="space-y-6">
              <div class="grid md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Nombre Completo *</label>
                  <div class="relative">
                    <User class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-300" :size="20" />
                    <input 
                      v-model="userData.name" 
                      type="text" 
                      required
                      placeholder="Ej: Juan Pérez" 
                      class="w-full pl-14 pr-6 py-4 bg-brand-50/50 border-2 border-transparent focus:border-brand-500 focus:bg-white rounded-2xl outline-none transition-all font-bold text-brand-900"
                    />
                  </div>
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">WhatsApp / Celular *</label>
                  <div class="relative">
                    <Phone class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-300" :size="20" />
                    <input 
                      v-model="userData.phone" 
                      type="tel" 
                      required
                      placeholder="Ej: 300 123 4567" 
                      class="w-full pl-14 pr-6 py-4 bg-brand-50/50 border-2 border-transparent focus:border-brand-500 focus:bg-white rounded-2xl outline-none transition-all font-bold text-brand-900"
                    />
                  </div>
                </div>
              </div>

              <!-- Address -->
              <div class="space-y-2">
                <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Dirección de Entrega *</label>
                <div class="relative">
                  <MapPin class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-300" :size="20" />
                  <input 
                    v-model="userData.address" 
                    type="text" 
                    required
                    placeholder="Calle, número, conjunto, apartamento..." 
                    class="w-full pl-14 pr-6 py-4 bg-brand-50/50 border-2 border-transparent focus:border-brand-500 focus:bg-white rounded-2xl outline-none transition-all font-bold text-brand-900"
                  />
                </div>
              </div>

              <!-- City -->
              <div class="space-y-2">
                <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Ciudad / Municipio</label>
                <div class="relative">
                  <Building class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-300" :size="20" />
                  <input 
                    v-model="userData.city" 
                    type="text" 
                    placeholder="Ej: Bogotá, Chía, Cajicá..." 
                    class="w-full pl-14 pr-6 py-4 bg-brand-50/50 border-2 border-transparent focus:border-brand-500 focus:bg-white rounded-2xl outline-none transition-all font-bold text-brand-900"
                  />
                </div>
              </div>

              <!-- Notes -->
              <div class="space-y-2">
                <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Notas o Instrucciones</label>
                <div class="relative">
                  <StickyNote class="absolute left-5 top-5 text-brand-300" :size="20" />
                  <textarea 
                    v-model="userData.notes" 
                    rows="3"
                    placeholder="¿Alguna instrucción especial para la entrega?" 
                    class="w-full pl-14 pr-6 py-4 bg-brand-50/50 border-2 border-transparent focus:border-brand-500 focus:bg-white rounded-2xl outline-none transition-all font-bold text-brand-900 resize-none"
                  ></textarea>
                </div>
              </div>

              <div class="pt-6">
                <button 
                  type="submit"
                  class="w-full py-5 bg-[#25D366] text-white rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-green-500/20 flex items-center justify-center gap-3 hover:bg-[#21bd5b] hover:-translate-y-1 transition-all active:scale-95"
                >
                  <MessageCircle :size="24" />
                  Confirmar Pedido por WhatsApp
                </button>
                <p class="text-center text-xs text-brand-400 mt-4 font-medium italic">
                  Al hacer clic, se generará un mensaje automático con tu pedido para que un asesor te atienda.
                </p>
              </div>
            </form>
          </div>

          <!-- Trust Badges -->
          <div class="grid grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-3xl border border-brand-100 flex flex-col items-center text-center">
              <ShieldCheck class="text-brand-500 mb-3" :size="32" />
              <p class="text-[10px] font-black uppercase text-brand-900">Compra Segura</p>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-brand-100 flex flex-col items-center text-center">
              <Truck class="text-brand-500 mb-3" :size="32" />
              <p class="text-[10px] font-black uppercase text-brand-900">Envío Express</p>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-brand-100 flex flex-col items-center text-center">
              <CreditCard class="text-brand-500 mb-3" :size="32" />
              <p class="text-[10px] font-black uppercase text-brand-900">Pago Contraentrega</p>
            </div>
          </div>
        </div>

        <!-- Right Column: Summary -->
        <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-32">
          <div class="bg-brand-900 text-white rounded-[2.5rem] p-8 md:p-10 shadow-2xl shadow-brand-900/20">
            <div class="flex items-center gap-3 mb-8 pb-6 border-b border-white/10">
              <ShoppingBag :size="24" class="text-brand-400" />
              <h2 class="text-xl font-black uppercase tracking-tight">Resumen de tu compra</h2>
            </div>

            <!-- Items List -->
            <div class="space-y-6 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar mb-8">
              <div 
                v-for="item in cartStore.cartItems" 
                :key="item.id"
                class="flex gap-4 items-center"
              >
                <div class="h-20 w-20 shrink-0 rounded-2xl overflow-hidden border border-white/10 bg-white/5">
                  <img :src="getImageUrl(item.image)" :alt="item.name" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1">
                  <h4 class="font-bold text-sm leading-tight mb-1">{{ item.name }}</h4>
                  <p class="text-xs text-brand-400 font-medium">Cantidad: {{ item.quantity }}</p>
                  <p class="text-brand-400 font-black mt-1 text-sm">${{ Math.floor(item.price * item.quantity).toLocaleString('es-CO') }}</p>
                </div>
              </div>
            </div>

            <!-- Totals -->
            <div class="space-y-4 pt-6 border-t border-white/10">
              <div class="flex justify-between items-center text-brand-400 font-bold uppercase tracking-widest text-[10px]">
                <span>Subtotal</span>
                <span>${{ Math.floor(cartStore.totalPrice).toLocaleString('es-CO') }}</span>
              </div>
              <div class="flex justify-between items-center text-brand-400 font-bold uppercase tracking-widest text-[10px]">
                <span>Envío</span>
                <span class="text-brand-400">Calculado por WhatsApp</span>
              </div>
              <div class="flex justify-between items-center pt-4">
                <span class="text-xl font-black uppercase tracking-tighter">Total</span>
                <span class="text-3xl font-black text-brand-400">${{ Math.floor(cartStore.totalPrice).toLocaleString('es-CO') }}</span>
              </div>
            </div>
          </div>

          <!-- Guarantee -->
          <div class="bg-brand-50 p-6 rounded-3xl border border-brand-100 flex gap-4 items-start">
            <div class="bg-brand-100 p-2 rounded-xl text-brand-600">
              <ShieldCheck :size="24" />
            </div>
            <div>
              <p class="text-xs font-black text-brand-900 uppercase mb-1">Garantía de Frescura</p>
              <p class="text-xs text-brand-500 font-medium leading-relaxed">Si algún producto no llega en perfectas condiciones, lo reemplazamos sin costo adicional.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
}
</style>
