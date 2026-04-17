<script setup>
import { 
  Dialog, 
  DialogPanel, 
  DialogTitle, 
  TransitionChild, 
  TransitionRoot 
} from '@headlessui/vue'
import { ShoppingBag, X, Trash2, Plus, Minus, ArrowRight } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import { useCartStore } from '../../stores/cart'
import Skeleton from './Skeleton.vue'
import { getProductImageUrl } from '../../utils/helpers'

const props = defineProps({
  isOpen: Boolean
})

const emit = defineEmits(['close'])

const cartStore = useCartStore()
const router = useRouter()

const handleClose = () => {
  emit('close')
}

const updateQuantity = (id, q) => {
  cartStore.updateQuantity(id, q)
}

const removeFromCart = (id) => {
  cartStore.removeFromCart(id)
}

const goToCheckout = () => {
  router.push('/checkout')
  handleClose()
}

const getImageUrl = getProductImageUrl
</script>

<template>
  <TransitionRoot as="template" :show="isOpen">
    <Dialog as="div" class="relative z-[100]" @close="handleClose">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-brand-900/40 backdrop-blur-sm" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <TransitionChild
              as="template"
              enter="transform transition duration-300 ease-out"
              enter-from="translate-x-full"
              enter-to="translate-x-0"
              leave="transform transition duration-200 ease-in"
              leave-from="translate-x-0"
              leave-to="translate-x-full"
            >
              <DialogPanel class="pointer-events-auto w-screen max-w-md">
                <div class="flex h-full flex-col bg-white shadow-xl">
                  <!-- Header -->
                  <div class="px-6 py-4 border-b border-brand-100 flex items-center justify-between">
                    <DialogTitle class="text-lg font-bold text-brand-900 flex items-center gap-2">
                      <ShoppingBag :size="20" />
                      Tu Carrito
                    </DialogTitle>
                    <button @click="handleClose" class="p-2 text-brand-400 hover:text-brand-900">
                      <X :size="20" />
                    </button>
                  </div>

                  <!-- Items -->
                  <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <div v-if="cartStore.cartItems.length > 0" class="divide-y divide-brand-50">
                      <div v-for="product in cartStore.cartItems" :key="product.id" class="flex gap-4 py-4 first:pt-0">
                        <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded border border-brand-100">
                          <img :src="getImageUrl(product.image)" :alt="product.name" class="h-full w-full object-cover" />
                        </div>

                        <div class="flex flex-1 flex-col justify-between">
                          <div>
                            <div class="flex justify-between items-start">
                              <h3 class="font-semibold text-brand-900 text-sm">{{ product.name }}</h3>
                              <button @click="removeFromCart(product.id)" class="p-1.5 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-colors group">
                                <Trash2 :size="14" />
                              </button>
                            </div>
                            <p class="text-brand-500 text-sm mt-1 font-bold">${{ Math.floor(product.price).toLocaleString('es-CO') }}</p>
                          </div>

                          <div class="flex items-center justify-between">
                            <div class="flex items-center border border-brand-200 rounded-lg">
                              <button @click="updateQuantity(product.id, product.quantity - 1)" class="p-1.5 hover:bg-brand-50">
                                <Minus :size="14" />
                              </button>
                              <span class="px-4 text-xs font-black text-brand-900">{{ product.quantity }}</span>
                              <button @click="updateQuantity(product.id, product.quantity + 1)" class="p-1.5 hover:bg-brand-50">
                                <Plus :size="14" />
                              </button>
                            </div>
                            <span class="text-brand-900 font-black text-sm">${{ Math.floor(product.price * product.quantity).toLocaleString('es-CO') }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-else class="h-full flex flex-col items-center justify-center text-center">
                      <ShoppingBag :size="48" class="text-brand-100 mb-4" />
                      <h3 class="text-lg font-bold text-brand-900 mb-1">Carrito vacío</h3>
                      <p class="text-brand-400 text-sm mb-6 font-medium">Aún no has agregado productos.</p>
                      <button @click="handleClose" class="px-6 py-2 bg-brand-900 text-white rounded-full text-sm font-bold">Seguir comprando</button>
                    </div>
                  </div>

                  <!-- Footer -->
                  <div v-if="cartStore.cartItems.length > 0" class="p-6 border-t border-brand-100 bg-brand-50/50 space-y-4">
                    <div class="flex justify-between items-center px-2">
                      <span class="text-brand-500 font-medium">Subtotal</span>
                      <span class="text-xl font-bold text-brand-900">${{ Math.floor(cartStore.totalPrice).toLocaleString('es-CO') }}</span>
                    </div>
                    <button 
                      @click="goToCheckout"
                      class="w-full py-4 bg-brand-900 text-white rounded-2xl font-black shadow-lg flex items-center justify-center gap-2 hover:scale-105 transition-transform uppercase tracking-widest text-sm"
                    >
                      Continuar al Pago <ArrowRight :size="18" />
                    </button>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style scoped>
@reference "../../style.css";
</style>
