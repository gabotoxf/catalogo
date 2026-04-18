<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from './components/layout/Navbar.vue'
import Footer from './components/layout/Footer.vue'

const route = useRoute()
const isDashboard = computed(() => route.path.startsWith('/dashboard'))
</script>

<template>
  <div class="min-h-screen bg-white">
    <Navbar v-if="!isDashboard" />
    <main class="">
      <router-view></router-view>
    </main>
    <Footer v-if="!isDashboard" />
  </div>
</template>
