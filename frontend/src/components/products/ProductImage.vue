<script setup>
import { ref, watch } from 'vue'
import { ShoppingBasket } from 'lucide-vue-next'
import { getProductImageUrl } from '../../utils/helpers'

const props = defineProps({
  src: { type: String, default: null },
  alt: { type: String, default: '' },
  imgClass: { type: String, default: '' }
})

const loaded = ref(false)
watch(() => props.src, () => (loaded.value = false))
</script>

<template>
  <div class="relative w-full h-full overflow-hidden bg-brand-50">
    <div v-if="src && !loaded" class="absolute inset-0 animate-pulse bg-brand-100" />
    <img
      v-if="src"
      :src="getProductImageUrl(src)"
      :alt="alt"
      :class="[imgClass, loaded ? 'opacity-100' : 'opacity-0']"
      class="w-full h-full object-cover transition-opacity duration-500"
      @load="loaded = true"
    />
    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-2 text-brand-300">
      <ShoppingBasket :size="48" :stroke-width="1.5" />
      <span class="text-[10px] font-black uppercase tracking-widest">Del campo</span>
    </div>
  </div>
</template>
