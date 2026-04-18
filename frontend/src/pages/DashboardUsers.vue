<script setup>
import { ref, onMounted } from 'vue'
import api from '../api/axios'
import Skeleton from '../components/layout/Skeleton.vue'
import Toastify from 'toastify-js'
import 'toastify-js/src/toastify.css'
import { 
  Plus, 
  Trash2, 
  Edit,
  Loader2,
  X,
  Search,
  Users,
  Shield,
  Mail,
  User,
  AlertTriangle
} from 'lucide-vue-next'

const users = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDeleteModal = ref(false)
const itemToDelete = ref(null)
const isEditing = ref(false)
const currentItem = ref(null)

const showToast = (message, type = 'success') => {
  const toastNode = document.createElement("div");
  const isSuccess = type === 'success';
  
  toastNode.innerHTML = `
    <div class="flex items-center gap-4">
      <div class="relative">
        <div class="absolute inset-0 ${isSuccess ? 'bg-emerald-400' : 'bg-red-400'} blur-md opacity-30 rounded-full"></div>
        <div class="relative bg-gradient-to-br ${isSuccess ? 'from-emerald-400 to-emerald-600' : 'from-red-400 to-red-600'} p-2.5 rounded-xl shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            ${isSuccess ? '<polyline points="20 6 9 17 4 12"></polyline>' : '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>'}
          </svg>
        </div>
      </div>
      <div class="flex flex-col leading-tight">
        <span class="text-[10px] uppercase tracking-[0.25em] ${isSuccess ? 'text-emerald-300' : 'text-red-300'} font-semibold">
          ${isSuccess ? 'Operación Exitosa' : 'Error'}
        </span>
        <span class="text-sm font-semibold text-white">
          ${message}
        </span>
      </div>
    </div>
  `;

  Toastify({
    node: toastNode,
    duration: 3000,
    gravity: "bottom",
    position: "right",
    style: {
      background: isSuccess ? "linear-gradient(to right, #1a2e05, #628f2c)" : "linear-gradient(to right, #450a0a, #991b1b)",
      borderRadius: "20px",
      padding: "16px 24px",
    }
  }).showToast();
}

const form = ref({
  nombre_usuario: '',
  apellido_usuario: '',
  usuario_usuario: '',
  password_usuario: '',
  rol_usuario: 2,
  estado_usuario: 'activo'
})

const resetForm = () => {
  form.value = {
    nombre_usuario: '',
    apellido_usuario: '',
    usuario_usuario: '',
    password_usuario: '',
    rol_usuario: 2,
    estado_usuario: 'activo'
  }
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await api.get('/dashboard/usuarios')
    users.value = response.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const openModal = (item = null) => {
  isEditing.value = !!item
  currentItem.value = item
  if (item) {
    form.value = { ...item, password_usuario: '' }
  } else {
    resetForm()
  }
  showModal.value = true
}



const handleSubmit = async () => {
  try {
    if (isEditing.value) {
      await api.post(`/dashboard/usuarios/${currentItem.value.id_usuario}`, form.value)
      showToast('Usuario actualizado correctamente')
    } else {
      await api.post('/dashboard/usuarios', form.value)
      showToast('Usuario creado correctamente')
    }
    showModal.value = false
    fetchUsers()
  } catch (err) {
    showToast(err.response?.data?.error || 'Error al guardar', 'error')
  }
}

const confirmDelete = (id) => {
  itemToDelete.value = id
  showDeleteModal.value = true
}

const deleteItem = async () => {
  if (!itemToDelete.value) return
  try {
    await api.delete(`/dashboard/usuarios/${itemToDelete.value}`)
    showToast('Usuario eliminado correctamente')
    fetchUsers()
    showDeleteModal.value = false
    itemToDelete.value = null
  } catch (err) {
    showToast(err.response?.data?.error || 'Error al eliminar', 'error')
    showDeleteModal.value = false
  }
}

onMounted(fetchUsers)
</script>

<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex flex-col gap-0.5">
        <h1 class="text-xl font-bold text-neutral-900 tracking-tight">Usuarios</h1>
        <p class="text-sm text-neutral-500 font-medium">Gestiona el equipo administrativo.</p>
      </div>
      <button @click="openModal()" class="bg-brand-900 text-white px-6 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-brand-800 transition-colors shadow-sm active:scale-95">
        <Plus :size="16" /> Nuevo Usuario
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm overflow-hidden relative min-h-[400px]">
      <div v-if="loading" class="p-6 space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center gap-4 py-3 border-b border-neutral-50 last:border-0">
          <Skeleton width="36px" height="36px" borderRadius="8px" />
          <div class="flex-1 space-y-2">
            <Skeleton width="40%" height="14px" />
            <Skeleton width="20%" height="10px" />
          </div>
          <Skeleton width="15%" height="20px" />
          <Skeleton width="10%" height="20px" />
          <Skeleton width="80px" height="32px" borderRadius="8px" />
        </div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-neutral-50 border-b border-neutral-100 text-neutral-400 uppercase text-[10px] font-bold tracking-widest">
            <tr>
              <th class="px-6 py-4">Usuario</th>
              <th class="px-6 py-4">Rol</th>
              <th class="px-6 py-4 text-center">Estado</th>
              <th class="px-6 py-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-neutral-50">
            <tr v-for="user in users" :key="user.id_usuario" class="hover:bg-neutral-50/50 transition-colors group">
              <td class="px-6 py-3">
                <div class="flex items-center gap-3">
                  <div class="h-9 w-9 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs border border-brand-100 shrink-0">
                    {{ user.nombre_usuario?.charAt(0) }}{{ user.apellido_usuario?.charAt(0) }}
                  </div>
                  <div class="flex flex-col">
                    <span class="font-bold text-neutral-900 text-sm">{{ user.nombre_usuario }} {{ user.apellido_usuario }}</span>
                    <span class="text-[10px] text-neutral-400 font-medium">{{ user.usuario_usuario }}</span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-3">
                <span class="text-[11px] font-bold text-neutral-600 flex items-center gap-1.5">
                  <Shield :size="12" class="text-neutral-400" />
                  {{ user.rol_usuario == 1 ? 'Super Admin' : 'Admin' }}
                </span>
              </td>
              <td class="px-6 py-3 text-center">
                <span :class="[
                  'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                  user.estado_usuario === 'activo' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'
                ]">
                  {{ user.estado_usuario }}
                </span>
              </td>
              <td class="px-6 py-3 text-right">
                <div class="flex justify-end gap-1">
                  <button @click="openModal(user)" class="p-2 text-neutral-400 hover:text-brand-900 hover:bg-brand-50 rounded-lg transition-colors"><Edit :size="16" /></button>
                  <button @click="confirmDelete(user.id_usuario)" class="p-2 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"><Trash2 :size="16" /></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="showModal = false"></div>
      
      <div class="relative bg-white w-full max-w-lg max-h-[85vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar">
          <header class="flex justify-between items-center mb-6">
            <div class="flex flex-col gap-0.5">
              <h2 class="text-lg font-bold text-neutral-900 tracking-tight">
                {{ isEditing ? 'Editar' : 'Nuevo' }} Usuario
              </h2>
              <p class="text-xs text-neutral-500 font-medium">Gestiona los permisos de acceso.</p>
            </div>
            <button @click="showModal = false" class="p-2 text-neutral-400 hover:text-neutral-900 rounded-lg transition-colors"><X :size="20" /></button>
          </header>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Nombre *</label>
                <input v-model="form.nombre_usuario" type="text" required class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" />
              </div>
              <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Apellido *</label>
                <input v-model="form.apellido_usuario" type="text" required class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" />
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Email / Usuario *</label>
              <input v-model="form.usuario_usuario" type="text" required class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" />
            </div>

            <div class="space-y-1.5">
              <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Contraseña {{ isEditing ? '(dejar vacío para no cambiar)' : '*' }}</label>
              <input v-model="form.password_usuario" type="password" :required="!isEditing" class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Rol</label>
                <select v-model="form.rol_usuario" class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none">
                  <option :value="1">Administrador</option>
                  <option :value="2">Usuario Cliente</option>
                </select>
              </div>
              <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Estado</label>
                <select v-model="form.estado_usuario" class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none">
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>
            </div>

            <div class="pt-4 sticky bottom-0 bg-white">
              <button type="submit" class="w-full py-3 bg-brand-900 text-white rounded-xl font-bold text-sm shadow-sm hover:bg-brand-800 transition-colors active:scale-95">
                {{ isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showDeleteModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>
        
        <div class="relative bg-white rounded-[2rem] w-full max-w-md shadow-2xl border border-neutral-100 overflow-hidden">
          <div class="p-8 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <AlertTriangle class="text-red-500" :size="32" />
            </div>
            <h3 class="text-xl font-bold text-neutral-900 mb-2">¿Confirmar eliminación?</h3>
            <p class="text-sm text-neutral-500 font-medium mb-8">
              Esta acción no se puede deshacer. El usuario será eliminado permanentemente.
            </p>
            
            <div class="flex gap-3">
              <button 
                @click="showDeleteModal = false"
                class="flex-1 px-6 py-3 rounded-xl bg-neutral-100 text-neutral-600 font-bold text-xs hover:bg-neutral-200 transition-colors"
              >
                Cancelar
              </button>
              <button 
                @click="deleteItem"
                class="flex-1 px-6 py-3 rounded-xl bg-red-500 text-white font-bold text-xs hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20"
              >
                Sí, eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>