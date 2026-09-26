import { ref } from 'vue'

// Flag global de navegación: true entre beforeEach y afterEach.
// La escribe el router y la lee RouteLoader (con retardo anti-parpadeo).
export const routeLoading = ref(false)
