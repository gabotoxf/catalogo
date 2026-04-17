export const getProductImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  const baseUrl = import.meta.env.VITE_IMAGE_BASE_URL || 'http://localhost:8000/assets/img/Productos'
  return `${baseUrl}/${img}`
}

export const getCategoryImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  const baseUrl = import.meta.env.VITE_CAT_IMAGE_BASE_URL || 'http://localhost:8000/assets/img/Categorias'
  return `${baseUrl}/${img}`
}
