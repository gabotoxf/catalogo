<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from './components/layout/Navbar.vue'
import Footer from './components/layout/Footer.vue'
import Skeleton from './components/layout/Skeleton.vue'

const route = useRoute()
const isDashboard = computed(() => route.path.startsWith('/dashboard'))
const isPageLoading = ref(false)

// Watch for route changes to show a brief loading state if needed
watch(() => route.path, () => {
  isPageLoading.value = true
  setTimeout(() => {
    isPageLoading.value = false
  }, 400)
})

onMounted(() => {
  // Initial load
  isPageLoading.value = true
  setTimeout(() => {
    isPageLoading.value = false
  }, 600)
})
</script>

<template>
  <div class="min-h-screen bg-white flex flex-col">
    <Navbar v-if="!isDashboard" />
    
    <main class="flex-1">
      <div v-if="isPageLoading && !isDashboard" class="max-w-[var(--max-width)] mx-auto px-4 py-8 space-y-8 animate-in fade-in duration-300">
        <div class="space-y-4">
          <Skeleton width="60%" height="40px" borderRadius="12px" />
          <Skeleton width="40%" height="20px" borderRadius="8px" />
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <Skeleton v-for="i in 4" :key="i" height="300px" borderRadius="24px" />
        </div>
      </div>
      <router-view v-else></router-view>
    </main>

    <Footer v-if="!isDashboard" />
  </div>
</template>
