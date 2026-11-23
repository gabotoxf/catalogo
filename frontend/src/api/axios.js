import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
})

const CACHE_TTL = 2 * 60 * 1000
const cache = new Map()

const cacheKey = (config) =>
  (config.baseURL || '') + (config.url || '') + JSON.stringify(config.params || {})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  if ((config.method || 'get').toLowerCase() === 'get' && !config.skipCache) {
    const entry = cache.get(cacheKey(config))
    if (entry && Date.now() - entry.ts < CACHE_TTL) {
      config.adapter = async () => ({
        data: entry.data,
        status: 200,
        statusText: 'OK',
        headers: {},
        config,
        request: {},
      })
    }
  }
  return config
})

api.interceptors.response.use((res) => {
  const method = (res.config.method || 'get').toLowerCase()
  if (method === 'get' && !res.config.skipCache && res.status >= 200 && res.status < 300) {
    cache.set(cacheKey(res.config), { data: res.data, ts: Date.now() })
  } else if (method !== 'get') {
    cache.clear()
  }
  return res
})

export default api