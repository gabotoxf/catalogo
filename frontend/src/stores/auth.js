import { defineStore } from 'pinia'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.rol_usuario === 1 || state.user?.rol_usuario === '1'
  },
  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/login', credentials)
        
        if (response.data.status === 'success') {
          this.token = response.data.access_token
          this.user = response.data.user
          
          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))
          
          return response.data
        } else {
          throw new Error(response.data.message || 'Error en el login')
        }
      } catch (err) {
        this.error = err.response?.data?.message || err.message || 'Error al iniciar sesión'
        throw err
      } finally {
        this.loading = false
      }
    },
    async logout() {
      try {
        await api.post('/logout')
      } catch (err) {
        console.error('Error logging out', err)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    },
    async fetchUser() {
      if (!this.token) return
      try {
        const response = await api.get('/user')
        this.user = response.data
        localStorage.setItem('user', JSON.stringify(this.user))
      } catch (err) {
        this.logout()
      }
    }
  }
})
