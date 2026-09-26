// Número único de WhatsApp para toda la tienda.
// Configurable con VITE_WHATSAPP_NUMBER en .env (ver .env.production).
export const WHATSAPP_NUMBER =
  import.meta.env.VITE_WHATSAPP_NUMBER || '573115140908'

export const waLink = (message) =>
  `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`
