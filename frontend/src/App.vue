<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Navbar from './components/layout/Navbar.vue'
import Footer from './components/layout/Footer.vue'
import RouteLoader from './components/layout/RouteLoader.vue'
import AppSplash from './components/layout/AppSplash.vue'
import { useAuthStore } from './stores/auth'

const route = useRoute()
const router = useRouter()
const isDashboard = computed(() => route.path.startsWith('/dashboard'))
const appReady = ref(false)

// El splash se queda hasta que el router resolvió la ruta inicial
// y cargaron los assets (hero incluido), con topes anti-bloqueo.
onMounted(async () => {
  const authStore = useAuthStore()
  if (authStore.token) authStore.fetchUser()

  const minDelay = new Promise((r) => setTimeout(r, 500))
  const pageLoaded = document.readyState === 'complete'
    ? Promise.resolve()
    : Promise.race([
        new Promise((r) => window.addEventListener('load', r, { once: true })),
        new Promise((r) => setTimeout(r, 3500)),
      ])
  await Promise.all([router.isReady(), minDelay, pageLoaded])
  appReady.value = true
})
</script>

<template>
  <div class="min-h-screen bg-white">
    <AppSplash :show="!appReady" />
    <RouteLoader />
    <Navbar v-if="!isDashboard" />
    <main class="">
      <router-view></router-view>
    </main>
    <Footer v-if="!isDashboard" />
  </div>
</template>
