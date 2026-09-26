<script setup>
import { ref, watch } from 'vue'
import { routeLoading } from '../../utils/routeLoading'

const visible = ref(false)
let t = null
watch(routeLoading, (v) => {
  clearTimeout(t)
  // Solo muestra si la navegación tarda >200ms (evita parpadeos)
  t = v ? setTimeout(() => (visible.value = true), 200) : (visible.value = false, null)
})
</script>

<template>
  <div v-show="visible" class="fixed top-0 left-0 right-0 z-[200] h-[3px] overflow-hidden bg-brand-100">
    <div class="route-bar h-full w-1/3 bg-brand-600 rounded-r-full"></div>
  </div>
</template>

<style scoped>
.route-bar {
  animation: route-slide 1s ease-in-out infinite;
}
@keyframes route-slide {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(400%); }
}
</style>
