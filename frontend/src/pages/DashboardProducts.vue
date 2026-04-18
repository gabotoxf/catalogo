<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
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
  Filter,
  ChevronLeft,
  ChevronRight,
  AlertTriangle
} from 'lucide-vue-next'

const products = ref([])
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
  nombre_producto: '',
  precio_producto: '',
  categoria_id: '',
  imagen_producto: null,
  descripcion_producto: '',
  cantidad_producto: 0
})

watch(searchQuery, () => {
  fetchProducts(1)
})

const imagePreview = ref(null)

watch(() => form.value.imagen_producto, (newVal) => {
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
    form.value.imagen_producto = file
  }
}

const fetchProducts = async (page = 1) => {
  loading.value = true
  try {
    const response = await api.get(`/dashboard/productos?page=${page}&per_page=${pagination.value.per_page}&search=${searchQuery.value}`)
    products.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
      per_page: response.data.per_page
    }
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/dashboard/categorias')
    // Since getCategorias is paginated now, we might need all for the select
    // For simplicity, let's assume we fetch them all for the modal
    // In a real scenario, we might need a separate endpoint for non-paginated categories
    categories.value = response.data.data || response.data
  } catch (err) {
    console.error(err)
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
    nombre_producto: '',
    precio_producto: '',
    categoria_id: '',
    imagen_producto: null,
    descripcion_producto: '',
    cantidad_producto: 0
  }
}

const handleSubmit = async () => {
  try {
    const formData = new FormData()
    formData.append('nombre_producto', form.value.nombre_producto)
    formData.append('precio_producto', form.value.precio_producto)
    formData.append('categoria_id', form.value.categoria_id)
    formData.append('descripcion_producto', form.value.descripcion_producto || '')
    formData.append('cantidad_producto', form.value.cantidad_producto || 0)
    
    if (form.value.imagen_producto instanceof File) {
      formData.append('imagen_producto', form.value.imagen_producto)
    } else if (form.value.imagen_producto) {
      formData.append('imagen_producto_actual', form.value.imagen_producto)
    }

    if (isEditing.value) {
      await api.post(`/dashboard/productos/${currentItem.value.id_producto}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      showToast('Producto actualizado correctamente')
    } else {
      await api.post('/dashboard/productos', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      showToast('Producto creado correctamente')
    }
    showModal.value = false
    fetchProducts(pagination.value.current_page)
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
    await api.delete(`/dashboard/productos/${itemToDelete.value}`)
    showToast('Producto eliminado correctamente')
    fetchProducts(pagination.value.current_page)
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
  return `http://localhost:8000/assets/img/Productos/${img}`
}

const route = useRoute()

onMounted(() => {
  if (route.query.search) {
    searchQuery.value = route.query.search
  }
  fetchProducts()
  fetchCategories()
})
</script>

<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex flex-col gap-0.5">
        <h1 class="text-xl font-bold text-neutral-900 tracking-tight">Productos</h1>
        <p class="text-sm text-neutral-500 font-medium">Gestiona el inventario de tu tienda.</p>
      </div>
      <button @click="openModal()" class="bg-brand-900 text-white px-6 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-brand-800 transition-colors shadow-sm active:scale-95">
        <Plus :size="16" /> Nuevo Producto
      </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-3 rounded-2xl border border-neutral-200 shadow-sm flex flex-col md:flex-row gap-3 items-center">
      <div class="relative flex-1 w-full">
        <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400" :size="16" />
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Buscar productos..." 
          class="w-full pl-10 pr-4 py-2 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" 
        />
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm overflow-hidden relative min-h-[400px]">
      <div v-if="loading" class="p-6 space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center gap-4 py-3 border-b border-neutral-50 last:border-0">
          <Skeleton width="40px" height="40px" borderRadius="8px" />
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
              <th class="px-6 py-4">Producto</th>
              <th class="px-6 py-4">Categoría</th>
              <th class="px-6 py-4 text-right">Precio</th>
              <th class="px-6 py-4 text-center">Stock</th>
              <th class="px-6 py-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-neutral-50">
            <tr v-for="prod in products" :key="prod.id_producto" class="hover:bg-neutral-50/50 transition-colors group">
              <td class="px-6 py-3">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-lg overflow-hidden bg-neutral-100 border border-neutral-200 shrink-0">
                    <img :src="getImageUrl(prod.imagen_producto)" class="h-full w-full object-cover" />
                  </div>
                  <div class="flex flex-col">
                    <span class="font-bold text-neutral-900 text-sm">{{ prod.nombre_producto }}</span>
                    <span class="text-[10px] text-neutral-400 font-medium">Ref: #{{ prod.id_producto }}</span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-3">
                <span class="px-2 py-0.5 bg-brand-50 text-brand-700 rounded text-[10px] font-bold uppercase tracking-wider border border-brand-100">
                  {{ prod.categoria?.nombre_categoria || 'S/C' }}
                </span>
              </td>
              <td class="px-6 py-3 font-bold text-neutral-900 text-right text-sm">${{ Math.floor(prod.precio_producto).toLocaleString('es-CO') }}</td>
              <td class="px-6 py-3 text-center">
                <span :class="[
                  'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                  prod.cantidad_producto < 10 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'
                ]">
                  {{ prod.cantidad_producto || 0 }}
                </span>
              </td>
              <td class="px-6 py-3 text-right">
                <div class="flex justify-end gap-1">
                  <button @click="openModal(prod)" class="p-2 text-neutral-400 hover:text-brand-900 hover:bg-brand-50 rounded-lg transition-colors"><Edit :size="16" /></button>
                  <button @click="confirmDelete(prod.id_producto)" class="p-2 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"><Trash2 :size="16" /></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-neutral-100 flex items-center justify-between bg-neutral-50/30">
        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">
          {{ products.length }} de {{ pagination.total }}
        </p>
        <div class="flex gap-1.5">
          <button 
            @click="fetchProducts(pagination.current_page - 1)" 
            :disabled="pagination.current_page === 1"
            class="p-1.5 rounded-lg bg-white border border-neutral-200 text-neutral-500 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-neutral-50 transition-colors"
          >
            <ChevronLeft :size="16" />
          </button>
          <div class="flex gap-1">
            <button 
              v-for="page in pagination.last_page" 
              :key="page"
              @click="fetchProducts(page)"
              :class="[
                'w-8 h-8 rounded-lg font-bold text-[11px] transition-colors',
                pagination.current_page === page ? 'bg-brand-900 text-white shadow-sm' : 'bg-white border border-neutral-200 text-neutral-500 hover:bg-neutral-50'
              ]"
            >
              {{ page }}
            </button>
          </div>
          <button 
            @click="fetchProducts(pagination.current_page + 1)" 
            :disabled="pagination.current_page === pagination.last_page"
            class="p-1.5 rounded-lg bg-white border border-neutral-200 text-neutral-500 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-neutral-50 transition-colors"
          >
            <ChevronRight :size="16" />
          </button>
        </div>
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
                {{ isEditing ? 'Editar' : 'Nuevo' }} Producto
              </h2>
              <p class="text-xs text-neutral-500 font-medium">Completa los datos del producto.</p>
            </div>
            <button @click="showModal = false" class="p-2 text-neutral-400 hover:text-neutral-900 rounded-lg transition-colors"><X :size="20" /></button>
          </header>

          <form @submit.prevent="handleSubmit" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Nombre *</label>
                <input v-model="form.nombre_producto" type="text" required class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" placeholder="Nombre del producto" />
              </div>
              <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Precio (COP) *</label>
                <input v-model="form.precio_producto" type="number" required class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" placeholder="0" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Categoría *</label>
                <select v-model="form.categoria_id" required class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none appearance-none">
                  <option value="" disabled>Seleccionar...</option>
                  <option v-for="cat in categories" :key="cat.id_categoria" :value="cat.id_categoria">{{ cat.nombre_categoria }}</option>
                </select>
              </div>
              <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Stock</label>
                <input v-model="form.cantidad_producto" type="number" class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none" placeholder="0" />
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Imagen</label>
              <div class="flex flex-col gap-3">
                <div v-if="imagePreview || (form.imagen_producto && typeof form.imagen_producto === 'string')" class="h-24 w-24 rounded-xl overflow-hidden border border-neutral-200 bg-neutral-50 relative group mx-auto sm:mx-0">
                  <img :src="imagePreview || getImageUrl(form.imagen_producto)" class="h-full w-full object-cover" />
                  <button type="button" @click="form.imagen_producto = null; imagePreview = null" class="absolute inset-0 bg-red-500/80 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
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
                  <Plus :size="14" /> {{ form.imagen_producto ? 'Cambiar Imagen' : 'Subir Imagen' }}
                </button>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider ml-1">Descripción</label>
              <textarea v-model="form.descripcion_producto" rows="2" class="w-full px-4 py-2 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium text-sm text-neutral-900 outline-none resize-none" placeholder="Breve descripción..."></textarea>
            </div>

            <div class="pt-2 sticky bottom-0 bg-white">
              <button type="submit" class="w-full py-3 bg-brand-900 text-white rounded-xl font-bold text-sm shadow-sm hover:bg-brand-800 transition-colors active:scale-95">
                {{ isEditing ? 'Guardar Cambios' : 'Registrar Producto' }}
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
              Esta acción no se puede deshacer. El producto será eliminado permanentemente.
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
