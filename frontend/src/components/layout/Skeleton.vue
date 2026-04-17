<script setup>
defineProps({
  width: {
    type: String,
    default: '100%'
  },
  height: {
    type: String,
    default: '20px'
  },
  borderRadius: {
    type: String,
    default: '4px'
  },
  className: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <div 
    class="skeleton-loader animate-pulse bg-neutral-200"
    :class="className"
    :style="{
      width: width,
      height: height,
      borderRadius: borderRadius
    }"
  ></div>
</template>

<style scoped>
.skeleton-loader {
  background: linear-gradient(
    90deg,
    #f3f4f6 25%,
    #e5e7eb 50%,
    #f3f4f6 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite linear;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style>
