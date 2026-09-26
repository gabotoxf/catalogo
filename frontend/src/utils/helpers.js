// Bump al cambiar imágenes en el repo: nginx las cachea 1 año como inmutables
const IMG_VERSION = '3'

export const getProductImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  const baseUrl = import.meta.env.VITE_IMAGE_BASE_URL || 'http://localhost:8000/assets/img/Productos'
  return `${baseUrl}/${img}?v=${IMG_VERSION}`
}

export const getCategoryImageUrl = (img) => {
  if (!img) return null
  if (img.startsWith('http')) return img
  const baseUrl = import.meta.env.VITE_CAT_IMAGE_BASE_URL || 'http://localhost:8000/assets/img/Categorias'
  return `${baseUrl}/${img}?v=${IMG_VERSION}`
}

export const getAssetUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  // Remove /src/assets if present to normalize path
  const cleanPath = path.replace(/^\/src\/assets\//, '').replace(/^src\/assets\//, '')
  // In development (Vite), we might need the full path if it's an internal asset,
  // but for production, these should be in the public folder or served by backend.
  // If the path starts with /assets/, it's likely a public asset.
  const apiBase = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(/\/api$/, '')
  return `${apiBase}/assets/${cleanPath}?v=${IMG_VERSION}`
}
