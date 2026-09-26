<script setup>
defineProps({ show: { type: Boolean, default: true } })
</script>

<template>
  <Transition name="splash-fade">
    <div v-if="show" class="fixed inset-0 z-[300] bg-white flex flex-col items-center justify-center gap-4">
      <div class="splash-ring"></div>
      <p class="splash-text">Cargando</p>
    </div>
  </Transition>
</template>

<style scoped>
.splash-ring {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 4px solid #E3EAE2;
  border-top-color: #1B5E20;
  animation: splash-spin 0.8s linear infinite;
}
.splash-text {
  font-family: system-ui, sans-serif;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #7A8F78;
}
@keyframes splash-spin { to { transform: rotate(360deg); } }
.splash-fade-leave-active { transition: opacity 0.35s ease; }
.splash-fade-leave-to { opacity: 0; }
</style>
