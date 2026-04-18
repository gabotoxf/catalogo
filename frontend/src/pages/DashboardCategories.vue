<script setup>
import { ref, onMounted, watch } from 'vue'
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
  Layers,
  ChevronLeft,
  ChevronRight,
  ImageIcon,
  AlertTriangle
} from 'lucide-vue-next'

const categories = ref([])
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 10
})
const loading = ref(true)
const showModal = ref(false)
const showDeleteModal = ref(false)
const itemToDelete = ref(null)
const isEditing = ref(false)
const currentItem = ref(null)
const searchQuery = ref('')
const fileInput = ref(null)

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
  nombre_categoria: '',
  imagen_categoria: null
})

const fetchCategories = async (page = 1) => {
  loading.value = true
  try {
    const response = await api.get(`/dashboard/categorias?page=${page}&per_page=${pagination.value.per_page}&search=${searchQuery.value}`)
    // Check if response is paginated or just an array
    if (response.data.data) {
      categories.value = response.data.data
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
        per_page: response.data.per_page
      }
    } else {
      categories.value = response.data
      pagination.value.last_page = 1
    }
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
    form.value = { ...item }
  } else {
    resetForm()
  }
  showModal.value = true
}

const resetForm = () => {
  form.value = {
    nombre_categoria: '',
    imagen_categoria: null
  }
}

watch(searchQuery, () => {
  fetchCategories(1)
})

const imagePreview = ref(null)

watch(() => form.value.imagen_categoria, (newVal) => {
  if (newVal instanceof File) {
    if (imagePreview.value) URL.revokeObjectURL(imagePreview.value)
    imagePreview.value = URL.createObjectURL(newVal)
  } else {
    imagePreview.value = null
  }
})

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.imagen_categoria = file
  }
}

const handleSubmit = async () => {
  try {
    const formData = new FormData()
    formData.append('nombre_categoria', form.value.nombre_categoria)
    
    if (form.value.imagen_categoria instanceof File) {
      formData.append('imagen_categoria', form.value.imagen_categoria)
    } else if (form.value.imagen_categoria) {
      formData.append('imagen_categoria_actual', form.value.imagen_categoria)
    }

    if (isEditing.value) {
      await api.post(`/dashboard/categorias/${currentItem.value.id_categoria}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      showToast('Categoría actualizada correctamente')
    } else {
      await api.post('/dashboard/categorias', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      showToast('Categoría creada correctamente')
    }
    showModal.value = false
    fetchCategories(pagination.value.current_page)
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
    await api.delete(`/dashboard/categorias/${itemToDelete.value}`)
    showToast('Categoría eliminada correctamente')
    fetchCategories(pagination.value.current_page)
    showDeleteModal.value = false
    itemToDelete.value = null
  } catch (err) {
    showToast(err.response?.data?.error || 'Error al eliminar', 'error')
    showDeleteModal.value = false
  }
}

const getImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  return `http://localhost:8000/assets/img/Categorias/${img}`
}

onMounted(fetchCategories)
</script>

<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex flex-col gap-0.5">
        <h1 class="text-xl font-bold text-neutral-900 tracking-tight">Categorías</h1>
        <p class="text-sm text-neutral-500 font-medium">Organiza los productos de tu tienda.</p>
      </div>
      <button @click="openModal()" class="bg-brand-900 text-white px-6 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-brand-800 transition-colors shadow-sm active:scale-95">
        <Plus :size="16" /> Nueva Categoría
      </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-3 rounded-2xl border border-neutral-200 shadow-sm flex flex-col md:flex-row gap-3 items-center">
      <div class="relative flex-1 w-full">
        <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400" :size="16" />
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Buscar categorías..." 
          class="w-full pl-10 pr-4 py-2 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" 
        />
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm overflow-hidden relative min-h-[400px]">
      <div v-if="loading" class="p-6 space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center gap-4 py-3 border-b border-neutral-50 last:border-0">
          <Skeleton width="40px" height="40px" borderRadius="8px" />
          <Skeleton width="40%" height="14px" />
          <Skeleton width="10%" height="14px" />
          <Skeleton width="15%" height="20px" />
          <Skeleton width="80px" height="32px" borderRadius="8px" />
        </div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-neutral-50 border-b border-neutral-100 text-neutral-400 uppercase text-[10px] font-bold tracking-widest">
            <tr>
              <th class="px-6 py-4">Categoría</th>
              <th class="px-6 py-4 text-center">ID</th>
              <th class="px-6 py-4 text-center">Productos</th>
              <th class="px-6 py-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-neutral-50">
            <tr v-for="cat in categories" :key="cat.id_categoria" class="hover:bg-neutral-50/50 transition-colors group">
              <td class="px-6 py-3">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-lg overflow-hidden bg-neutral-100 border border-neutral-200 shrink-0 flex items-center justify-center">
                    <img v-if="cat.imagen_categoria" :src="getImageUrl(cat.imagen_categoria)" class="h-full w-full object-cover" />
                    <Layers v-else :size="18" class="text-neutral-300" />
                  </div>
                  <span class="font-bold text-neutral-900 text-sm">{{ cat.nombre_categoria }}</span>
                </div>
              </td>
              <td class="px-6 py-3 text-center font-mono text-neutral-400 text-[10px] font-bold">#{{ cat.id_categoria }}</td>
              <td class="px-6 py-3 text-center">
                <span class="px-2 py-0.5 bg-brand-50 text-brand-700 rounded text-[10px] font-bold uppercase tracking-wider border border-brand-100">
                  {{ cat.productos_count || 0 }} items
                </span>
              </td>
              <td class="px-6 py-3 text-right">
                <div class="flex justify-end gap-1">
                  <button @click="openModal(cat)" class="p-2 text-neutral-400 hover:text-brand-900 hover:bg-brand-50 rounded-lg transition-colors"><Edit :size="16" /></button>
                  <button @click="confirmDelete(cat.id_categoria)" class="p-2 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"><Trash2 :size="16" /></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-neutral-100 flex items-center justify-between bg-neutral-50/30">
        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">
          {{ categories.length }} categorías
        </p>
        <div class="flex gap-1.5">
          <button 
            @click="fetchCategories(pagination.current_page - 1)" 
            :disabled="pagination.current_page === 1"
            class="p-1.5 rounded-lg bg-white border border-neutral-200 text-neutral-500 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-neutral-50 transition-colors"
          >
            <ChevronLeft :size="16" />
          </button>
          <div class="flex gap-1">
            <button 
              v-for="page in pagination.last_page" 
              :key="page"
              @click="fetchCategories(page)"
              :class="[
                'w-8 h-8 rounded-lg font-bold text-[11px] transition-colors',
                pagination.current_page === page ? 'bg-brand-900 text-white shadow-sm' : 'bg-white border border-neutral-200 text-neutral-500 hover:bg-neutral-50'
              ]"
            >
              {{ page }}
            </button>
          </div>
          <button 
            @click="fetchCategories(pagination.current_page + 1)" 
            :disabled="pagination.current_page === pagination.last_page"
            class="p-1.5 rounded-lg bg-white border border-neutral-200 text-neutral-500 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-neutral-50 transition-colors"
          >
            <ChevronRight :size="16" />
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="showModal = false"></div>
      
      <div class="relative bg-white w-full max-w-md max-h-[85vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar">
          <header class="flex justify-between items-center mb-6">
            <div class="flex flex-col gap-0.5">
              <h2 class="text-lg font-bold text-neutral-900 tracking-tight">
                {{ isEditing ? 'Editar' : 'Nueva' }} Categoría
              </h2>
              <p class="text-xs text-neutral-500 font-medium">Organiza tus productos.</p>
            </div>
            <button @click="showModal = false" class="p-2 text-neutral-400 hover:text-neutral-900 rounded-lg transition-colors"><X :size="20" /></button>
          </header>

          <form @submit.prevent="handleSubmit" class="space-y-5">
            <div class="space-y-1.5">
              <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Nombre *</label>
              <div class="relative">
                <Layers class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-300" :size="16" />
                <input v-model="form.nombre_categoria" type="text" required class="w-full pl-10 pr-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" placeholder="Nombre de la categoría" />
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Imagen</label>
              <div class="flex flex-col gap-3">
                <div v-if="imagePreview || (form.imagen_categoria && typeof form.imagen_categoria === 'string')" class="h-24 w-24 rounded-xl overflow-hidden border border-neutral-200 bg-neutral-50 relative group mx-auto">
                  <img :src="imagePreview || getImageUrl(form.imagen_categoria)" class="h-full w-full object-cover" />
                  <button type="button" @click="form.imagen_categoria = null; imagePreview = null" class="absolute inset-0 bg-red-500/80 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <Trash2 :size="16" />
                  </button>
                </div>
                <input 
                  type="file" 
                  ref="fileInput" 
                  @change="handleFileUpload" 
                  accept="image/*"
                  class="hidden" 
                />
                <button 
                  type="button"
                  @click="$refs.fileInput.click()"
                  class="flex items-center justify-center gap-2 px-4 py-2 bg-neutral-50 text-neutral-500 rounded-xl font-bold text-xs hover:bg-neutral-100 transition-colors border border-dashed border-neutral-300"
                >
                  <Plus :size="14" /> {{ form.imagen_categoria ? 'Cambiar Imagen' : 'Subir Imagen' }}
                </button>
              </div>
            </div>

            <div class="pt-2 sticky bottom-0 bg-white">
              <button type="submit" class="w-full py-3 bg-brand-900 text-white rounded-xl font-bold text-sm shadow-sm hover:bg-brand-800 transition-colors active:scale-95">
                {{ isEditing ? 'Guardar Cambios' : 'Crear Categoría' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    </Transition>

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
              Esta acción no se puede deshacer. La categoría será eliminada permanentemente.
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
