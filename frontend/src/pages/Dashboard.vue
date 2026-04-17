<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { 
  LayoutDashboard, 
  Package, 
  Layers, 
  Users, 
  LogOut, 
  Plus, 
  Trash2, 
  Edit,
  Loader2,
  X
} from 'lucide-vue-next'
import api from '../api/axios'

const authStore = useAuthStore()
const router = useRouter()
const activeTab = ref('products')
const products = ref([])
const categories = ref([])
const loading = ref(true)
const showModal = ref(false)
const modalType = ref('products') // 'products' or 'categories'
const isEditing = ref(false)
const currentItem = ref(null)

const form = ref({
  // Product fields
  nombre_producto: '',
  precio_producto: '',
  id_categoria: '',
  imagen_producto: '',
  descripcion_producto: '',
  cantidad_producto: 0,
  // Category fields
  nombre_categoria: '',
  imagen_categoria: ''
})

const fetchData = async () => {
  loading.value = true
  try {
    const [prodRes, catRes] = await Promise.all([
      api.get('/dashboard/productos'),
      api.get('/dashboard/categorias')
    ])
    products.value = prodRes.data.productos || []
    categories.value = catRes.data || []
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const openModal = (type, item = null) => {
  modalType.value = type
  isEditing.value = !!item
  currentItem.value = item
  showModal.value = true

  if (item) {
    if (type === 'products') {
      form.value = { ...item }
    } else {
      form.value = { ...item }
    }
  } else {
    resetForm()
  }
}

const resetForm = () => {
  form.value = {
    nombre_producto: '',
    precio_producto: '',
    id_categoria: '',
    imagen_producto: '',
    descripcion_producto: '',
    cantidad_producto: 0,
    nombre_categoria: '',
    imagen_categoria: ''
  }
}

const handleSubmit = async () => {
  try {
    let response
    if (modalType.value === 'products') {
      if (isEditing.value) {
        response = await api.post(`/dashboard/productos/${currentItem.value.id_producto}`, form.value)
      } else {
        response = await api.post('/dashboard/productos', form.value)
      }
    } else {
      if (isEditing.value) {
        response = await api.post(`/dashboard/categorias/${currentItem.value.id_categoria}`, form.value)
      } else {
        response = await api.post('/dashboard/categorias', form.value)
      }
    }
    
    showModal.value = false
    fetchData()
  } catch (err) {
    alert(err.response?.data?.error || 'Error al guardar')
  }
}

const deleteItem = async (type, id) => {
  if (!confirm('¿Estás seguro de eliminar este elemento?')) return
  
  try {
    if (type === 'products') {
      await api.delete(`/dashboard/productos/${id}`)
    } else {
      await api.delete(`/dashboard/categorias/${id}`)
    }
    fetchData()
  } catch (err) {
    alert(err.response?.data?.error || 'Error al eliminar')
  }
}

const getImageUrl = (img, type) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  const folder = type === 'products' ? 'Productos' : 'Categorias'
  return `http://localhost:8000/assets/img/${folder}/${img}`
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  fetchData()
})
</script>

<template>
  <div class="min-h-screen bg-brand-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-brand-900 text-white flex flex-col">
      <div class="p-6 border-b border-brand-800 flex items-center gap-3">
        <img src="/logo.jfif" alt="Logo" class="h-8 w-8 rounded" />
        <span class="font-bold uppercase tracking-tight">Admin Panel</span>
      </div>

      <nav class="flex-1 p-4 space-y-2">
        <button 
          @click="activeTab = 'products'"
          :class="['w-full flex items-center gap-3 px-4 py-3 rounded-md transition-colors', activeTab === 'products' ? 'bg-brand-800 text-white' : 'text-brand-300 hover:bg-brand-800']"
        >
          <Package :size="18" /> Productos
        </button>
        <button 
          @click="activeTab = 'categories'"
          :class="['w-full flex items-center gap-3 px-4 py-3 rounded-md transition-colors', activeTab === 'categories' ? 'bg-brand-800 text-white' : 'text-brand-300 hover:bg-brand-800']"
        >
          <Layers :size="18" /> Categorías
        </button>
      </nav>

      <div class="p-4 border-t border-brand-800">
        <div class="px-4 py-3 mb-4">
          <p class="text-xs font-bold text-brand-400 uppercase tracking-widest">Usuario</p>
          <p class="text-sm font-semibold truncate">{{ authStore.user?.nombre }}</p>
        </div>
        <button 
          @click="handleLogout"
          class="w-full flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-md transition-colors"
        >
          <LogOut :size="18" /> Cerrar Sesión
        </button>
      </div>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-8 overflow-y-auto">
      <div v-if="loading" class="flex items-center justify-center h-full">
        <Loader2 class="animate-spin text-brand-300" :size="48" />
      </div>

      <div v-else class="max-w-5xl mx-auto space-y-8">
        <header class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-brand-900 uppercase tracking-tight">
            {{ activeTab === 'products' ? 'Gestión de Productos' : 'Gestión de Categorías' }}
          </h1>
          <button @click="openModal(activeTab)" class="bg-brand-900 text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:bg-brand-800 transition-all active:scale-95 shadow-lg shadow-brand-900/10">
            <Plus :size="18" /> {{ activeTab === 'products' ? 'Nuevo Producto' : 'Nueva Categoría' }}
          </button>
        </header>

        <!-- Table -->
        <div class="bg-white border border-brand-100 rounded-3xl shadow-sm overflow-hidden">
          <table class="w-full text-left text-sm">
            <thead class="bg-brand-50 border-b border-brand-100 text-brand-600 uppercase text-[10px] font-black tracking-widest">
              <tr v-if="activeTab === 'products'">
                <th class="px-6 py-5">Producto</th>
                <th class="px-6 py-5">Categoría</th>
                <th class="px-6 py-5 text-right">Precio</th>
                <th class="px-6 py-5 text-right">Acciones</th>
              </tr>
              <tr v-else>
                <th class="px-6 py-5">ID</th>
                <th class="px-6 py-5">Nombre de Categoría</th>
                <th class="px-6 py-5 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-50">
              <template v-if="activeTab === 'products'">
                <tr v-for="prod in products" :key="prod.id_producto" class="hover:bg-brand-50/50 transition-colors group">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                      <div class="h-12 w-12 rounded-xl overflow-hidden bg-brand-50 border border-brand-100 shrink-0">
                        <img :src="getImageUrl(prod.imagen_producto, 'products')" class="h-full w-full object-cover transition-transform group-hover:scale-110" />
                      </div>
                      <span class="font-bold text-brand-900 uppercase tracking-tight">{{ prod.nombre_producto }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-brand-50 text-brand-600 rounded-full text-[10px] font-black uppercase border border-brand-100">
                      {{ prod.categoria?.nombre_categoria || 'S/C' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 font-black text-brand-900 text-right text-base">${{ Math.floor(prod.precio_producto).toLocaleString('es-CO') }}</td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-1">
                      <button @click="openModal('products', prod)" class="p-2 text-brand-400 hover:text-brand-900 hover:bg-brand-50 rounded-lg transition-all"><Edit :size="18" /></button>
                      <button @click="deleteItem('products', prod.id_producto)" class="p-2 text-brand-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"><Trash2 :size="18" /></button>
                    </div>
                  </td>
                </tr>
              </template>
              <template v-else>
                <tr v-for="cat in categories" :key="cat.id_categoria" class="hover:bg-brand-50/50 transition-colors group">
                  <td class="px-6 py-4 font-mono text-brand-300 font-bold">#{{ cat.id_categoria }}</td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                      <div class="h-10 w-10 rounded-lg overflow-hidden bg-brand-50 border border-brand-100 shrink-0">
                        <img v-if="cat.imagen_categoria" :src="getImageUrl(cat.imagen_categoria, 'categories')" class="h-full w-full object-cover" />
                        <div v-else class="h-full w-full flex items-center justify-center text-brand-200"><Layers :size="16" /></div>
                      </div>
                      <span class="font-bold text-brand-900 uppercase tracking-tight">{{ cat.nombre_categoria }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-1">
                      <button @click="openModal('categories', cat)" class="p-2 text-brand-400 hover:text-brand-900 hover:bg-brand-50 rounded-lg transition-all"><Edit :size="18" /></button>
                      <button @click="deleteItem('categories', cat.id_categoria)" class="p-2 text-brand-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"><Trash2 :size="18" /></button>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
          
          <div v-if="(activeTab === 'products' && products.length === 0) || (activeTab === 'categories' && categories.length === 0)" class="p-20 text-center space-y-4">
            <div class="w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mx-auto text-brand-200">
              <Package v-if="activeTab === 'products'" :size="32" />
              <Layers v-else :size="32" />
            </div>
            <p class="text-brand-400 font-bold uppercase tracking-widest text-xs">No hay elementos registrados</p>
          </div>
        </div>

        <!-- MODAL CRUD -->
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-brand-900/60 backdrop-blur-sm" @click="showModal = false"></div>
          
          <div class="relative bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-8 md:p-10">
              <header class="flex justify-between items-center mb-8">
                <div>
                  <h2 class="text-2xl font-black text-brand-900 uppercase tracking-tight">
                    {{ isEditing ? 'Editar' : 'Nuevo' }} {{ modalType === 'products' ? 'Producto' : 'Categoría' }}
                  </h2>
                  <p class="text-brand-500 text-sm font-medium mt-1">Completa los campos para continuar</p>
                </div>
                <button @click="showModal = false" class="p-2 text-brand-400 hover:text-brand-900 transition-colors"><X :size="24" /></button>
              </header>

              <form @submit.prevent="handleSubmit" class="space-y-6">
                <template v-if="modalType === 'products'">
                  <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Nombre del Producto</label>
                      <input v-model="form.nombre_producto" type="text" required class="w-full px-6 py-3.5 bg-brand-50 border-none rounded-2xl focus:ring-2 focus:ring-brand-500 font-bold text-brand-900" />
                    </div>
                    <div class="space-y-2">
                      <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Precio (COP)</label>
                      <input v-model="form.precio_producto" type="number" required class="w-full px-6 py-3.5 bg-brand-50 border-none rounded-2xl focus:ring-2 focus:ring-brand-500 font-bold text-brand-900" />
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Categoría</label>
                    <select v-model="form.id_categoria" required class="w-full px-6 py-3.5 bg-brand-50 border-none rounded-2xl focus:ring-2 focus:ring-brand-500 font-bold text-brand-900">
                      <option value="" disabled>Seleccionar...</option>
                      <option v-for="cat in categories" :key="cat.id_categoria" :value="cat.id_categoria">{{ cat.nombre_categoria }}</option>
                    </select>
                  </div>

                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Nombre de la Imagen (ej: tomate.jpg)</label>
                    <input v-model="form.imagen_producto" type="text" class="w-full px-6 py-3.5 bg-brand-50 border-none rounded-2xl focus:ring-2 focus:ring-brand-500 font-bold text-brand-900" />
                  </div>
                </template>

                <template v-else>
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Nombre de la Categoría</label>
                    <input v-model="form.nombre_categoria" type="text" required class="w-full px-6 py-3.5 bg-brand-50 border-none rounded-2xl focus:ring-2 focus:ring-brand-500 font-bold text-brand-900" />
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-400 uppercase tracking-widest ml-4">Imagen (opcional)</label>
                    <input v-model="form.imagen_categoria" type="text" class="w-full px-6 py-3.5 bg-brand-50 border-none rounded-2xl focus:ring-2 focus:ring-brand-500 font-bold text-brand-900" />
                  </div>
                </template>

                <div class="pt-4">
                  <button type="submit" class="w-full py-4 bg-brand-900 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-brand-900/20 hover:bg-brand-800 transition-all active:scale-95">
                    Guardar Cambios
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
@reference "../style.css";
</style>
