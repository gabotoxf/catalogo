<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../api/axios'
import { Mail, Lock, Eye, EyeOff, Loader2, ArrowLeft, User } from 'lucide-vue-next'

const authStore = useAuthStore()
const router = useRouter()

const mode = ref('login')
const email = ref('')
const password = ref('')
const regNombre = ref('')
const regApellido = ref('')
const regUsuario = ref('')
const regContra = ref('')
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')
const success = ref('')

const redirectByRole = () => {
  router.push(authStore.isAdmin ? '/dashboard' : '/')
}

const handleLogin = async () => {
  if (!email.value || !password.value) {
    error.value = 'Por favor complete todos los campos'
    return
  }
  
  loading.value = true
  error.value = ''
  
  try {
    await authStore.login({
      usuario: email.value,
      contra: password.value
    })
    redirectByRole()
  } catch (err) {
    error.value = err.response?.data?.message || 'Credenciales incorrectas.'
  } finally {
    loading.value = false
  }
}

const handleRegister = async () => {
  if (!regNombre.value || !regApellido.value || !regUsuario.value || !regContra.value) {
    error.value = 'Por favor complete todos los campos'
    return
  }

  loading.value = true
  error.value = ''
  success.value = ''

  try {
    await api.post('/registrar', {
      nombre: regNombre.value,
      apellido: regApellido.value,
      usuario: regUsuario.value,
      contra: regContra.value
    })
    await authStore.login({ usuario: regUsuario.value, contra: regContra.value })
    redirectByRole()
  } catch (err) {
    error.value = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : err.response?.data?.message || 'No se pudo crear la cuenta.'
  } finally {
    loading.value = false
  }
}

const switchMode = (m) => {
  mode.value = m
  error.value = ''
  success.value = ''
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-brand-50 px-4">
    <div class="max-w-md w-full">
      <div class="text-center mb-8">
        <RouterLink to="/" class="inline-flex items-center gap-2 text-brand-400 hover:text-brand-900 text-xs font-bold uppercase tracking-wider mb-6 transition-colors">
          <ArrowLeft :size="14" /> Volver a la tienda
        </RouterLink>
        <div class="bg-white p-4 rounded-lg inline-block shadow-sm mb-4">
          <img src="/logo.jfif" alt="Logo" class="h-12 w-12 object-cover" />
        </div>
        <h1 class="text-2xl font-bold text-brand-900">{{ mode === 'login' ? 'Iniciar Sesión' : 'Crear cuenta' }}</h1>
        <p class="text-brand-500 text-sm mt-1">{{ mode === 'login' ? 'Accede a tu panel administrativo' : 'Compra directo del campo' }}</p>
      </div>

      <div class="grid grid-cols-2 bg-brand-100 rounded-lg p-1 mb-6 text-sm font-bold">
        <button @click="switchMode('login')" :class="mode === 'login' ? 'bg-white shadow-sm text-brand-900' : 'text-brand-400 hover:text-brand-900'" class="py-2 rounded-md transition-all">Entrar</button>
        <button @click="switchMode('registro')" :class="mode === 'registro' ? 'bg-white shadow-sm text-brand-900' : 'text-brand-400 hover:text-brand-900'" class="py-2 rounded-md transition-all">Crear cuenta</button>
      </div>

      <div class="bg-white p-8 rounded-lg shadow-sm border border-brand-100">
        <form v-if="mode === 'login'" class="space-y-5" @submit.prevent="handleLogin">
          <div class="space-y-1">
            <label class="text-xs font-bold text-brand-600 uppercase ml-1">Usuario</label>
            <div class="relative">
              <Mail class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-300" :size="18" />
              <input 
                v-model="email"
                type="text" 
                required 
                class="input-field pl-12" 
                placeholder="Ej: admin"
              />
            </div>
          </div>

          <div class="space-y-1">
            <label class="text-xs font-bold text-brand-600 uppercase ml-1">Contraseña</label>
            <div class="relative">
              <Lock class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-300" :size="18" />
              <input 
                v-model="password"
                :type="showPassword ? 'text' : 'password'" 
                required 
                class="input-field pl-12 pr-12" 
                placeholder="••••••••"
              />
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-brand-300 hover:text-brand-900"
              >
                <Eye v-if="!showPassword" :size="18" />
                <EyeOff v-else :size="18" />
              </button>
            </div>
          </div>

          <div v-if="error" class="text-red-600 text-xs font-medium bg-red-50 p-3 rounded border border-red-100">
            {{ error }}
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="btn-primary w-full py-4 flex items-center justify-center gap-2"
          >
            <Loader2 v-if="loading" class="animate-spin" :size="18" />
            <span v-else>Entrar al Panel</span>
          </button>
        </form>

        <form v-else class="space-y-5" @submit.prevent="handleRegister">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="text-xs font-bold text-brand-600 uppercase ml-1">Nombre</label>
              <input v-model="regNombre" type="text" required class="input-field" placeholder="Ej: María" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-brand-600 uppercase ml-1">Apellido</label>
              <input v-model="regApellido" type="text" required class="input-field" placeholder="Ej: Pérez" />
            </div>
          </div>

          <div class="space-y-1">
            <label class="text-xs font-bold text-brand-600 uppercase ml-1">Usuario</label>
            <div class="relative">
              <User class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-300" :size="18" />
              <input v-model="regUsuario" type="text" required class="input-field pl-12" placeholder="Ej: maria_p" />
            </div>
          </div>

          <div class="space-y-1">
            <label class="text-xs font-bold text-brand-600 uppercase ml-1">Contraseña</label>
            <div class="relative">
              <Lock class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-300" :size="18" />
              <input 
                v-model="regContra"
                :type="showPassword ? 'text' : 'password'" 
                required 
                minlength="6"
                class="input-field pl-12 pr-12" 
                placeholder="Mínimo 6 caracteres"
              />
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-brand-300 hover:text-brand-900"
              >
                <Eye v-if="!showPassword" :size="18" />
                <EyeOff v-else :size="18" />
              </button>
            </div>
          </div>

          <div v-if="error" class="text-red-600 text-xs font-medium bg-red-50 p-3 rounded border border-red-100">
            {{ error }}
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="btn-primary w-full py-4 flex items-center justify-center gap-2"
          >
            <Loader2 v-if="loading" class="animate-spin" :size="18" />
            <span v-else>Crear mi cuenta</span>
          </button>
        </form>
      </div>
      
      <p class="text-center mt-6 text-brand-400 text-xs font-medium">
        ¿Olvidaste tu contraseña? <a href="#" class="text-brand-900 hover:underline">Soporte</a>
      </p>
    </div>
  </div>
</template>

<style scoped>
@reference "../style.css";
</style>
